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
use HoangquyIT\ModelFacebook\Android\Profile\ProfileManager;
use HoangquyIT\ModelFacebook\FacebookFind;
use HoangquyIT\ModelFacebook\FbMediaDownloader;
use HoangquyIT\VideoFrameExtractor;



// Kết nối MongoDB
$database = new HoangquyIT\MongoDB\Client("mongodb://localhost:27017");

// Hàm kiểm tra xem Facebook UID đã tồn tại trong thư mục Video chưa
function isUidAlreadyProcessed($uid)
{
    $basePath = '/var/www/FacebookService/public/FileData/';
    $videoDir = $basePath . 'Video/' . $uid;

    // Nếu thư mục tồn tại và có ít nhất 1 file video thì coi như đã tải
    if (file_exists($videoDir)) {
        $files = scandir($videoDir);
        $videoFiles = array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) == 'mp4';
        });
        return count($videoFiles) > 0;
    }

    return false;
}

// UID để kết nối - lấy random từ account LIVE
$uid = $database->FacebookData->Account->aggregate([
    ['$match' => ['status' => 'LIVE']],
    ['$sample' => ['size' => 1]]
])->toArray()[0]->uid ?? null;

if (!$uid) {
    echo "Không tìm thấy tài khoản LIVE để kết nối.\n";
    exit(1);
}

// Lấy danh sách URLs từ ContentManager.Links
$urls = $database->ContentManager->Links->find(
    ['url' => ['$regex' => 'www.facebook.com']],
    ['projection' => ['url' => 1, '_id' => 0], 'limit' => 10] // Lấy 10 URLs để có dự phòng
)->toArray();

if (empty($urls)) {
    echo "Không tìm thấy URL nào phù hợp.\n";
    exit(1);
}

// Xử lý từng URL, bỏ qua URL đã tải
foreach ($urls as $urlObj) {
    $url = $urlObj->url;
    echo "Đang kiểm tra URL: $url\n";

    // Trích xuất Facebook UID từ URL
    $findUid = new FacebookFind($url);
    $facebookUid = $findUid->GetFacebookID();

    if (!$facebookUid) {
        echo "Không thể trích xuất Facebook UID từ URL: $url\n";
        continue;
    }

    // Kiểm tra xem UID này đã được tải chưa
    if (isUidAlreadyProcessed($facebookUid)) {
        echo "Facebook UID $facebookUid đã được tải trước đó, bỏ qua.\n";
        continue;
    }

    // Nếu chưa tải, thực hiện tải video
    echo "Bắt đầu tải videos cho Facebook UID: $facebookUid\n";
    $result = GetVideoByUrl($url, $uid);

    // Sau khi tải xong một URL, thoát khỏi vòng lặp để không tải quá nhiều videos trong một lần chạy cron
    if ($result) {
        echo "Hoàn thành tải videos cho URL: $url\n";
        break;
    }
}


function GetVideoByUrl($url, $uid)
{
    global $database;

    $collection = $database->FacebookData->Account;
    $account = $collection->findOne(['uid' => $uid]);

    $findUid =  new FacebookFind($url);
    $facebookUid = $findUid->GetFacebookID();


    if (!$account) {
        echo "UID $uid không tìm thấy trong database.\n";
        return;
    }


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
                    return;
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
        return;
    }


    if ($connectData) {
        $Accountuse = new FacebookAccount(iterator_to_array($account));

        if (!$Accountuse->Connect) {
            echo "UID $uid: Không thể thiết lập kết nối Device Android\n";
            return;
        }
        $CheckConnect = new CheckConnect($Accountuse);

        if (!$CheckConnect->ConnectAccount) {
            echo "UID $uid: Account đang bị CHECKPOINT\n";
            return;
        }
        $_profile = new ProfileManager($Accountuse);

        $ResultVideoData = $_profile->GetAllVideoProfile($facebookUid);

        if ($ResultVideoData->status && !empty($ResultVideoData->info)) {
            // Thêm ID video vào mảng kết quả
            foreach ($ResultVideoData->info as $video) {
                $result['video_ids'][] = $video->id;
            }

            // Lấy end cursor nếu có
            if (isset($ResultVideoData->end_cursor)) {
                $result['end_cursor'] = $ResultVideoData->end_cursor;
            } elseif (isset($ResultVideoData->page_info) && isset($ResultVideoData->page_info->end_cursor)) {
                $result['end_cursor'] = $ResultVideoData->page_info->end_cursor;
            }

            // Biến đếm để tránh lặp vô hạn
            $loopCount = 0;
            $maxLoops = 100; // Giới hạn số lần lặp để tránh quá nhiều request

            // Tiếp tục lấy thêm video nếu còn end_cursor
            while (!empty($result['end_cursor']) && $loopCount < $maxLoops) {
                $loopCount++;
                echo "Đang tải trang video thứ " . ($loopCount + 1) . "...\n";

                // Gọi API để lấy thêm video
                $moreData = $_profile->ViewMoreVideoProfile($facebookUid, $result['end_cursor']);

                if (isset($moreData->status) && $moreData->status && !empty($moreData->info)) {
                    // Thêm ID các video mới vào mảng kết quả
                    foreach ($moreData->info as $video) {
                        $result['video_ids'][] = $video->id;
                    }

                    // Cập nhật end_cursor
                    $result['end_cursor'] = null; // Đặt lại mặc định là null

                    if (isset($moreData->end_cursor)) {
                        $result['end_cursor'] = $moreData->end_cursor;
                    } elseif (isset($moreData->page_info) && isset($moreData->page_info->end_cursor)) {
                        $result['end_cursor'] = $moreData->page_info->end_cursor;
                    }

                    echo "Đã tải thêm " . count($moreData->info) . " video.\n";
                } else {
                    // Không còn dữ liệu hoặc có lỗi
                    $result['end_cursor'] = null;
                    break;
                }

                // Thêm delay nhỏ để tránh gửi quá nhiều request
                usleep(500000); // 0.5 giây
            }

            echo "Tổng cộng đã lấy được " . count($result['video_ids']) . " video.\n";
            echo "Bắt đầu tải xuống các video...\n";

            // Đếm số video đã tải thành công
            $successCount = 0;
            $failCount = 0;

            // Xử lý từng video trong mảng
            foreach ($result['video_ids'] as $index => $videoId) {
                // Tạo URL video từ ID
                $videoUrl = "https://www.facebook.com/watch/?v={$videoId}";
                echo "\n" . ($index + 1) . "/" . count($result['video_ids']) . " - Đang tải video ID: {$videoId}\n";

                try {
                    // Gọi function postVideo() đã có sẵn
                    $downloadResult = postVideo($videoUrl, $facebookUid);

                    // Kiểm tra kết quả tải xuống
                    if ($downloadResult) {
                        if (isset($downloadResult['status']) && $downloadResult['status']) {
                            $successCount++;
                            echo "✓ Tải xuống thành công\n";
                        } else {
                            $failCount++;
                            echo "⨯ Tải xuống thất bại: " . ($downloadResult['message'] ?? 'Lỗi không xác định') . "\n";
                        }
                    }
                } catch (Exception $e) {
                    $failCount++;
                    echo "⨯ Lỗi khi tải xuống: " . $e->getMessage() . "\n";
                }

                // Thêm delay nhỏ giữa các lần tải để tránh quá tải
                usleep(800000); // 0.8 giây
            }

            // Thay đoạn hiển thị kết quả tổng kết:
            echo "\n=== Tổng kết ===\n";
            echo "Tổng số video: " . count($result['video_ids']) . "\n";
            echo "Tải thành công: $successCount\n";
            echo "Tải thất bại: $failCount\n";
            echo "Thư mục lưu trữ: /var/www/FacebookService/public/FileData/Video/" . $facebookUid . "\n"; // Đường dẫn cố định

            return $result;
        } else {
            echo "Không tìm thấy video nào hoặc có lỗi xảy ra.\n";
            return $result; // Trả về mảng rỗng
        }
    } else {
        echo "UID $uid: $message\n";
    }
}


function postVideo($url, $uid)
{
    if (empty($url)) {
        return [
            'status' => false,
            'message' => 'URL video trống.'
        ];
    }

    $basePath = '/var/www/FacebookService/public/FileData/';
    $outputDirVideo = $basePath . 'Video/' . $uid;

    if (!file_exists($outputDirVideo)) {
        mkdir($outputDirVideo, 0777, true);
    }

    try {
        $downloader = new FbMediaDownloader();
        $downloader->set_url($url);
        $response = $downloader->generate_data();

        // Kiểm tra xem $response có rỗng hoặc không có dl_urls
        if (!$response || !isset($response->dl_urls)) {
            return [
                'status' => false,
                'message' => 'Không thể lấy dữ liệu video từ URL này.'
            ];
        }

        // Thử lấy URL video từ nhiều nguồn khác nhau (chất lượng cao -> thấp)
        $videoUrl = null;
        if (isset($response->dl_urls->high)) {
            $videoUrl = $response->dl_urls->high;
        } elseif (isset($response->dl_urls->sd)) {
            $videoUrl = $response->dl_urls->sd;
        } elseif (isset($response->dl_urls->low)) {
            $videoUrl = $response->dl_urls->low;
        } elseif (is_string($response->dl_urls)) {
            $videoUrl = $response->dl_urls;
        }

        if (!$videoUrl) {
            return [
                'status' => false,
                'message' => 'Không tìm thấy URL video hợp lệ.'
            ];
        }

        $videoUrl = str_replace('\/', '/', $videoUrl);

        // Kiểm tra URL video có hợp lệ không
        if (filter_var($videoUrl, FILTER_VALIDATE_URL) === false) {
            return [
                'status' => false,
                'message' => 'URL video không hợp lệ: ' . $videoUrl
            ];
        }

        // Đường dẫn đến ffmpeg
        $ffmpegPath = null;

        // Tên tệp
        $fileName = substr(md5($url), 0, 10);

        $_Download = new VideoFrameExtractor($ffmpegPath, $fileName);
        $_Download->setOutputDir($outputDirVideo);

        if ($_Download->downloadVideo($videoUrl)) {
            return [
                'status' => true,
                'message' => 'Video đã được tải xuống thành công.',
                'data' => $response
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Quá trình download video thất bại',
                'data' => $response
            ];
        }
    } catch (Exception $e) {
        return [
            'status' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ];
    } catch (Throwable $e) {
        return [
            'status' => false,
            'message' => 'Lỗi nghiêm trọng: ' . $e->getMessage()
        ];
    }
}
