<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

require '/var/www/FacebookService/vendor/Phphelper/autoload.php';

use HoangquyIT\ModelFacebook\Android\AdsManager\AdsControler;
use HoangquyIT\ModelFacebook\Android\CheckConnect;
use HoangquyIT\FacebookAccount;
use App\Modules\Facebook\Repositories\Account\AccountRepository;
use HoangquyIT\NetworkControler\NetworkControler;
use HoangquyIT\ModelFacebook\Android\Accounts\AccountManager;





// Kết nối MongoDB để lấy thông tin tài khoản (nếu cần cập nhật lại)
$database = new HoangquyIT\MongoDB\Client("mongodb://localhost:27017");

// File chứa danh sách uid cần đồng bộ ADS (được tạo bởi hàm syncAllAds)

$fileData = '/tmp/listCheckAds.txt';

$fileExists = file_exists($fileData);

// Số uid cần xử lý trong mỗi lần cron (ví dụ 50)
$linesToRead = 50;

if (file_exists($fileData)) {
    // Đọc tất cả các dòng, bỏ qua dòng trống
    $lines = file($fileData, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $totalLines = count($lines);

    if ($totalLines > 0) {
        // Số dòng cần đọc là min($linesToRead, tổng số dòng hiện có)
        $readCount = min($linesToRead, $totalLines);
        $selectedUids = array_slice($lines, 0, $readCount);

        // Nếu còn uid chưa xử lý, cập nhật lại file; nếu không, xóa file
        if ($totalLines > $readCount) {
            file_put_contents($fileData, implode(PHP_EOL, array_slice($lines, $readCount)) . PHP_EOL);
        } else {
            unlink($fileData);
        }
        // Với mỗi uid, tiến hành đồng bộ ADS
        foreach ($selectedUids as $uid) {
            echo "Đồng bộ ADS cho UID: $uid\n";
            try {
                // Lấy dữ liệu account từ MongoDB (hoặc qua repository, ví dụ như findByUid)
                // Ở đây ta dùng cách đơn giản: lấy bản ghi trên collection FacebookData.Account
                $collection = $database->FacebookData->Account;
                $account = $collection->findOne(['uid' => $uid]);
                if (!$account) {
                    echo "UID $uid không tìm thấy trong database.\n";
                    continue;
                }

                
                // Khởi tạo đối tượng FacebookAccount với dữ liệu account
                if (!empty($account)) {
                    // Gán các thuộc tính cho controller
                    $accountData = $account;
                    $accountUid = $account->uid;
                    $accountId = $account->_id;

                    if (!empty($accountData->MultiAccount)) {
                        foreach ($accountData->MultiAccount as $_account) {
                            if (trim($_account->profile->id) == trim($uid)) {
                                $cookies = '';
                                // Xây dựng chuỗi cookie
                                foreach ($_account->session_info->session_cookies as $cookieJson) {
                                    $cookie = json_decode($cookieJson);
                                    $cookieString = "{$cookie->name}={$cookie->value}; path={$cookie->path}; domain={$cookie->domain}; ";
                                    $cookieString .= ($cookie->secure ? 'Secure; ' : '') . ($cookie->httponly ? 'HttpOnly; ' : '') . ($cookie->samesite ? "SameSite={$cookie->samesite}; " : '');
                                    $cookies .= $cookieString . "\n";
                                }
                                $accountData->uid = $uid;
                                $accountData->android_device->cookies = $cookies;
                                $accountData->android_device->access_token = $_account->session_info->access_token;
                            }
                        }
                    }
                    //
                    $interfaceUse = $accountData->networkuse->interface ?? null;
                    if (empty($accountData->networkuse->interface)) {
                        $selectedSetting = null;
                        $network_settings = config('network_settings.settings');
                        foreach ($network_settings as $document) {
                            $selectedSetting = collect($document)
                                ->filter(function ($value) {
                                    return isset($value->status) && $value->status === 'on' && !empty($value->pppoe_username);
                                })
                                ->keys()
                                ->first();

                            if ($selectedSetting) {
                                break;
                            }
                        }
                        $interfaceUse = $selectedSetting;
                    }
                    // kiểm tra kết nối của phần network
                    $_Network = new NetworkControler();
                    $CheckConnect = $_Network->TestConnect($accountData->networkuse);
                    if (!$CheckConnect) {
                        if ($accountData->networkuse->type == 'interfaces') {
                            $resultCreate = $_Network->CreateInterface($interfaceUse);
                            if (!$resultCreate->status) {
                                echo "Lỗi khởi tạo Interface, tạo tay xem lại lỗi\n";
                                continue;
                            }
                            // Cập nhật trực tiếp vào MongoDB thay vì dùng repository
                            $accountData->networkuse->ip = $resultCreate->ip;
                            $updatData = array('networkuse' => iterator_to_array($accountData->networkuse));
                            $collection->updateOne(['uid' => $accountUid], ['$set' => $updatData]);
                        }
                    }
                    $facebookUse = json_decode(json_encode($accountData), true);
                    unset($facebookUse['_id']);
                    //
                    $uids = array_map(function ($account) {
                        return $account->uid;
                    }, [$account]);

                    $connectData = true;
                } else {
                    echo "$message\n";
                    continue;
                }

                if ($connectData) {
                    $Accountuse = new FacebookAccount(iterator_to_array($account));
                    if (!$Accountuse->Connect) {
                        echo "UID $uid: Không thể thiết lập kết nối Device Android\n";
                        continue;
                    }
                    $CheckConnect = new CheckConnect($Accountuse);
                    if (!$CheckConnect->ConnectAccount) {
                        echo "UID $uid: Account đang bị CHECKPOINT\n";
                        continue;
                    }
                    // Use the aliased FBAccountManager
                    $_account = new AccountManager($Accountuse);
                    $resultConvert = $_account->ConverSession('438142079694454');
                    
                    if ($resultConvert->status && !empty($resultConvert->session)) {
                        $Accountuse->AccountInfo['android_device_ads'] = $resultConvert->session;
                        $ArrayUpdate = [
                            'android_device_ads' => $resultConvert->session
                        ];
                        $updatData = array('android_device_message' => $resultConvert->session);
                        $collection->updateOne(['uid' => $accountUid], ['$set' => $updatData]);

                        // Thực hiện đồng bộ ADS
                        $_ads = new AdsControler($Accountuse);
                        $ResultLoad = $_ads->AdsManagerOnboardingAccountSelectionScreen_Query();
                        if ($ResultLoad->status) {
                            foreach ($ResultLoad->account_list as $_slectAds) {
                                $adData = [
                                    'insights' => $_slectAds->uid ?? null,
                                    'account_type' => $_slectAds->account_type ?? null,
                                    'total_spending' => $_slectAds->total_spending ?? null,
                                    'act_id' => $_slectAds->act_id ?? null,
                                    'name' => $_slectAds->act_name ?? null,
                                    'legacy_account_id' => $_slectAds->legacy_account_id ?? null,
                                    'currency' => $_slectAds->currency ?? null,
                                    'created_time' => $_slectAds->created_time ?? null,
                                    'next_bill_date' => $_slectAds->next_bill_date ?? null,
                                    'timezone' => $_slectAds->timezone->difference ?? null,
                                    'timezone_name' => $_slectAds->timezone->timezone ?? null,
                                    'account_status' => $_slectAds->account_status ?? null,
                                    'admin_list' => [],
                                    'admin_hidden' => $_slectAds->admin_hidden ?? null,
                                    'user_roles' => $_slectAds->user_roles ?? null,
                                    'nguong_tt' => $_slectAds->nguong_tt ?? null,
                                    'admin_hidden' => $_slectAds->admin_hidden ?? null,
                                    'nguong_tt_hientai' => $_slectAds->nguong_tt_hientai ?? null
                                ];
                                $collections = $database->FacebookData->Adsmanager;
                                // Kiểm tra xem dữ liệu đã tồn tại chưa dựa trên insights và act_id  
                                $filter = [
                                    'insights' => $_slectAds->uid,
                                    'act_id' => $_slectAds->act_id,
                                ];
                                $update = ['$set' => $adData];
                                // Cập nhật nếu có, chèn mới nếu không tìm thấy
                                $result = $collections->updateOne($filter, $update, ['upsert' => true]);
                            }
                            echo "UID $uid: Đã đồng bộ ADS\n";
                        } else {
                            echo "UID $uid: Không có ADS để đồng bộ\n";
                        }
                    } else {
                        echo "UID $uid: Chuyển đổi session không thành công\n";
                    }
                    // Kiểm tra kết nối
                } else {
                    echo "UID $uid: $message\n";
                }
            } catch (\Exception $e) {
                echo "UID $uid gặp lỗi: " . $e->getMessage() . "\n";
            }
        }
    }
}
