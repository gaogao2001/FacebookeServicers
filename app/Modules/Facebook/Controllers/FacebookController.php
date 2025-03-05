<?php

namespace App\Modules\Facebook\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface;
use App\Modules\Network\Repositories\ProxyV6\ProxyV6RepositoryInterface;
use Illuminate\Http\Request;
use HoangquyIT\FacebookAccount;
use HoangquyIT\ModelFacebook\FacebookApi;
use Illuminate\Support\Facades\Validator;
use QRcode;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Symfony\Component\HttpFoundation\StreamedResponse;
use HoangquyIT\Helper\Common;
use HoangquyIT\DeviceControler\DeviceInfoCollector;
use HoangquyIT\Encryption\Php7x\AES256\HqitEncryption;
use MongoDB\BSON\ObjectID;


class FacebookController extends Controller
{
    protected $accountRepository;
    protected $proxyV6Repository;

    public function __construct(AccountRepositoryInterface $accountRepository, ProxyV6RepositoryInterface $proxyV6Repository)
    {
        $this->accountRepository = $accountRepository;
        $this->proxyV6Repository = $proxyV6Repository;
    }

    public function facebookPage()
    {
        // Lấy bộ lọc từ session (nếu có)
        $filters = session('fb_filters', []);

        $InterFaceControler = new \HoangquyIT\NetworkControler\NetworkControler();
        $interfaces = $InterFaceControler->GetAllInterface();
        //print_r(json_encode($interfaces));
        //exit();
        // Tổng số tài khoản
        $totalAccounts = $this->accountRepository->countAccounts([]);
        $dieAccounts = $this->accountRepository->countAccounts(['status' => 'CHECKPOINT']);
        $liveAccounts = $this->accountRepository->countAccounts(['status' => 'LIVE']);
        $kxdAccounts = $this->accountRepository->countAccounts(['status' => 'KXD']);

        //var_dump($filters);
        //		die();
        // Áp dụng bộ lọc cho phân trang
        $page = 1;
        $perPage = 1000;
        $paginationData = $this->accountRepository->paginate($filters, $perPage, $page);
        // Lấy danh sách tài khoản từ kết quả lọc
        $accounts = $paginationData['data'];
        // Lấy danh sách UID
        $uids = array_map(function ($account) {
            return $account->uid;
        }, $accounts);
        // Lấy dữ liệu friends và groups
        $friendsData = $this->accountRepository->getMultipleFriendsData($uids);
        $groupsData = $this->accountRepository->getMultipleGroupsData($uids);
        // $historyData = $this->accountRepository->getHistoryData($uids);
        // Ghép dữ liệu friends và groups vào từng tài khoản
        foreach ($accounts as $account) {
            $uid = $account->uid;

            // Gán dữ liệu bạn bè
            if (isset($friendsData[$uid]) && is_array($friendsData[$uid])) {
                $account->friends = (object)[
                    'count' => count($friendsData[$uid]),
                    'friends_list' => $friendsData[$uid]
                ];
            } else {
                $account->friends = (object)[
                    'count' => 0,
                    'friends_list' => []
                ];
            }

            // Gán dữ liệu nhóm
            if (isset($groupsData[$uid]) && is_array($groupsData[$uid])) {
                $account->groups = (object)[
                    'count' => count($groupsData[$uid]),
                    'groups_list' => $groupsData[$uid]
                ];
            } else {
                $account->groups = (object)[
                    'count' => 0,
                    'groups_list' => []
                ];
            }

            // // Gán dữ liệu lịch sử tương tác
            // if (isset($historyData[$uid]) && is_array($historyData[$uid])) {
            //     $account->history = $historyData[$uid];
            // } else {
            //     $account->history = [];
            // }
        }
        // Lấy danh sách nhóm tài khoản và trạng thái
        $accountGroups = $this->accountRepository->getUniqueGroupAccounts();
        $allStatus = $this->accountRepository->getUniqueStatus();


        // Trả về view với dữ liệu đã lọc
        //print_r(json_encode($accounts));
        //exit();
        return view('Facebook::Facebook.facebook_page', [
            'accounts' => $accounts,
            'currentPage' => $paginationData['currentPage'],
            'lastPage' => $paginationData['lastPage'],
            'totalAccounts' => $totalAccounts,
            'dieAccounts' => $dieAccounts,
            'liveAccounts' => $liveAccounts,
            'kxdAccounts' => $kxdAccounts,
            'accountGroups' => $accountGroups,
            'allStatus' => $allStatus,
            'uid' => $uids,
            'interfaces' => $interfaces
        ]);
    }

    public function loadMoreAccounts(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = 1000;

        if ($page < 1) {
            return response()->json(['error' => 'Invalid page number.'], 400);
        }

        // Lấy bộ lọc từ session hoặc request
        $filters = session('fb_filters', []);

        // Gọi hàm paginate thay vì searchAccounts
        $paginationData = $this->accountRepository->paginate($filters, $perPage, $page);

        if (empty($paginationData['data'])) {
            return response()->json(['message' => 'No more records.'], 200);
        }

        return response()->json([
            'data' => $paginationData['data'],
            'totalRecords' => $paginationData['total'],
            'lastPage' => $paginationData['lastPage'],
            'currentPage' => $paginationData['currentPage'],
        ], 200);
    }

    public function show($id)
    {
        $accounts = $this->accountRepository->findById($id);

        if (!$accounts) {
            return redirect()->back()->with('error', 'Không tìm thấy tài khoản.');
        }

        $friendsData = $this->accountRepository->getMultipleFriendsData([$accounts->uid]);
        $groupsData = $this->accountRepository->getMultipleGroupsData([$accounts->uid]);
        // $historyData = $this->accountRepository->getHistoryData([$accounts->uid]);
        $allStatus = $this->accountRepository->getUniqueStatus();


        // Gắn dữ liệu friends
        if (isset($friendsData) && is_array($friendsData)) {
            $currentUidFriends = $friendsData[$accounts->uid] ?? []; // Dữ liệu friends của UID hiện tại

            $accounts->friends = (object)[
                'count' => count($currentUidFriends),
                'friends_list' => $currentUidFriends
            ];
        } else {
            $accounts->friends = (object)[
                'count' => 0,
                'friends_list' => []
            ];
        }

        // Gắn dữ liệu groups
        if (isset($groupsData) && is_array($groupsData)) {
            $currentUidGroups = $groupsData[$accounts->uid] ?? []; // Dữ liệu groups của UID hiện tại

            $accounts->groups = (object)[
                'count' => count($currentUidGroups),
                'groups_list' => $currentUidGroups
            ];
        } else {
            $accounts->groups = (object)[
                'count' => 0,
                'groups_list' => []
            ];
        }

        $ImgQrcode = null;
        if (!empty($accounts->qrcode)) {
            $ImgQrcode = $this->createQRCode($accounts->uid, $accounts->qrcode);
        }

        // Gắn dữ liệu history
        if (isset($historyData[$accounts->uid]) && is_array($historyData[$accounts->uid])) {
            $accounts->history = $historyData[$accounts->uid];
        } else {
            $accounts->history = [];
        }

        // check live qua uid
        //$FbSdk = new FacebookApi();
        //$accounts->status = $FbSdk->checkFacebookUID($accounts->uid);
        //if (empty($accounts->config_auto)) {
        //    $accounts->config_auto = config('defaultconfigs.defaultConfigFacebook');
        //    $this->accountRepository->update($accounts->_id, array('config_auto' => config('defaultconfigs.defaultConfigFacebook')));
        //}



        return view('Facebook::Facebook.facebook_edit', compact('accounts', 'ImgQrcode', 'allStatus'));
    }

    public function showJson($id)
    {
        $accounts = $this->accountRepository->findById($id);

        return response()->json($accounts);
    }

    public function update(Request $request, $id)
    {

        $data = $request->except('_token');

        $account = $this->accountRepository->findById($id);
        if (!$account) {
            return redirect()->route('facebook.edit', $id)->with('error', 'Không tìm thấy tài khoản.');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }
        if (empty($data['password_email'])) {
            unset($data['password_email']);
        }



        // Cập nhật dữ liệu, tự động thêm trường mới nếu không tồn tại
        foreach ($data as $key => $value) {
            // Nếu giá trị không rỗng, cập nhật
            if (!is_null($value)) {
                $account->$key = $value;
            }
        }
        // Loại bỏ khoảng trắng của từng giá trị trong $data
        $data = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);
        if (!empty($data['password'])) {
            $password = trim($data['password']);
            // Giải mã password nếu extension 'spaceviet' được tải
            $_Encryption = new HqitEncryption();
            if (!$_Encryption->isEncryptedString($password)) {
                $keysEn = md5(trim($account->uid));
                $data['password'] = HqitEncrypt($password, $keysEn);
            }
        }
        if (!empty($data['password_email'])) {
            $password = trim($data['password_email']);
            // Giải mã password nếu extension 'spaceviet' được tải
            $_Encryption = new HqitEncryption();
            if (!$_Encryption->isEncryptedString($password)) {
                $keysEn = md5(trim($account->uid));
                $data['password_email'] = HqitEncrypt($password, $keysEn);
            }
        }
        $updateResult = $this->accountRepository->update($id, $data);


        if ($updateResult) {
            return redirect()->route('facebook.edit', $id)->with('success', 'Cập nhật tài khoản thành công.');
        }

        return redirect()->route('facebook.edit', $id)->with('error', 'Cập nhật tài khoản thất bại.');
    }

    public function destroy($id)
    {

        $deleteResult = $this->accountRepository->delete($id);

        if ($deleteResult) {
            return redirect()->route('facebook.pages')->with('status', 'Xóa tài khoản thành công.');
        }

        return redirect()->route('facebook.pages')->with('error', 'Xóa tài khoản thất bại.');
    }

    public function deleteAllAccounts(Request $request)
    {
        try {
            $this->accountRepository->deleteAll();
            return response()->json(['message' => 'All accounts deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete accounts.'], 500);
        }
    }

    public function searchAccounts(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 50);
        $page = $request->input('page', 1);

        $filters = [];


        if ($search) {
            $regex = new \MongoDB\BSON\Regex($search, 'i');
            $filters = [
                '$or' => [
                    ['uid' => $regex],
                    ['fullname' => $regex],
                    ['email' => $regex],
                    ['status' => $regex],
                    // Thêm các trường khác bạn muốn tìm kiếm
                ]
            ];
        }

        $accounts = $this->accountRepository->paginate($filters, $perPage, $page);

        return response()->json($accounts);
    }

    ///check live
    public function checkLiveUid($uid)
    {
        if (!empty($uid)) {

            $FbSdk = new FacebookApi();
            $resultCheck = $FbSdk->checkFacebookUID($uid);

            // Kiểm tra kết quả trả về
            if (in_array($resultCheck, ['LIVE', 'CHECKPOINT'])) {
                // Tìm tài khoản theo UID thông qua Repository

                // Cập nhật trạng thái tài khoản thông qua Repository
                $this->accountRepository->updateStatus($uid, $resultCheck);


                return response()->json([
                    'response' => [
                        'status' => true,
                        'message' => $resultCheck,
                        'uid' => $uid,
                    ]
                ], 200);
            }

            return response()->json([
                'response' => [
                    'status' => false,
                    'message' => 'UID không hợp lệ hoặc không kiểm tra được.',
                    'uid' => $uid,
                ]
            ], 200);
        }

        return response()->json([
            'response' => [
                'status' => false,
                'message' => 'UID không được cung cấp.'
            ]
        ], 200);
    }

    public function loadAllFacebook(Request $request)
    {
        // Lấy danh sách tất cả tài khoản
        $uids = $this->accountRepository->getAllUids();


        if (count($uids) > 10) {
            // Nếu số tài khoản > 1000, lưu toàn bộ UID vào file tạm
            file_put_contents("/tmp/tempchecklive.txt", implode("\n", $uids));

            return response()->json([
                'response' => [
                    'status' => true,
                    'message' => 'Dữ liệu quá lớn. Đã chuyển qua Check live ngầm'
                ]
            ], 200);
        } else {
            // Nếu dưới 1000, trả về danh sách UID để xử lý check live như thường
            return response()->json([
                'response' => [
                    'status' => true,
                    'uids' => $uids
                ]
            ], 200);
        }
    }

    public function TransferAccountServer(Request $request)
    {
        // Bước 1: Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'target_url' => 'required|url',
            'selected_accounts' => 'array',
            'selected_accounts.*' => 'string', // UID là chuỗi
            'access_token.*' => 'string', // access_token là chuỗi và chính là token của tài khoản admin trên server đích
            'group_account' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ]);
        }

        $targetUrl = $request->input('target_url');
        $access_token = $request->input('access_token');
        if (empty($access_token)) {
            return response()->json([
                'status' => false,
                'message' => 'Access_Token là không thể thiếu.'
            ]);
        }
        $selectedAccounts = $request->input('selected_accounts', []);
        $groupAccount = $request->input('group_account');

        if (empty($selectedAccounts) && empty($groupAccount)) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng chọn tài khoản hoặc chọn nhóm tài khoản.'
            ]);
        }

        if (!empty($groupAccount)) {
            $selectedAccounts = $this->accountRepository->findByGroup($groupAccount);

            if (empty($selectedAccounts)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy tài khoản nào thuộc nhóm đã chọn.'
                ]);
            }
        }

        $accountsData = $this->accountRepository->findByUids($selectedAccounts);

        if (empty($accountsData)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tài khoản nào phù hợp.'
            ]);
        }

        // Bước 3: Tạo phản hồi JSON
        return response()->json([
            'status' => true,
            'data' => $accountsData,
        ], 200);
    }

    public function changeAccountGroup(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'account_group' => 'required|string',
            'selected_accounts' => 'required|array',
            'selected_accounts.*' => 'string' // Giả sử UID là chuỗi
        ]);

        $newGroup = $request->input('account_group');
        $selectedAccounts = $request->input('selected_accounts');

        // Cập nhật trường groups_account cho các tài khoản đã chọn
        $updateResult = $this->accountRepository->updateManyByUids($selectedAccounts, ['groups_account' => $newGroup]);

        if ($updateResult) {
            return response()->json([
                'status' => 'success',
                'message' => 'Đã cập nhật nhóm tài khoản thành công.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Cập nhật nhóm tài khoản thất bại.'
            ]);
        }
    }

    public function updateCoordinates(Request $request, $id)
    {
        $data = $request->only(['latitude', 'longitude']);
        $validator = Validator::make($data, [
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return redirect()->route('facebook.edit', $id)->with('messager', 'Dữ liệu không hợp lệ.');
        }
        // Loại bỏ khoảng trắng của từng giá trị trong $data
        $data = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);

        $updateResult = $this->accountRepository->update($id, $data);

        if ($updateResult) {
            return redirect()->back()->with('success', 'Cập nhật tọa độ thành công.');
        }
        return redirect()->route('facebook.edit', $id)->with('messager', 'Cập nhật tọa độ thất bại.');
    }

    public function proxySplit(Request $request)
    {
        $totalProxies = $this->proxyV6Repository->countProxies([]);
        $totalFacebookAccounts = $this->accountRepository->countAccounts([]);

        $facebookePerProxy =  intval(ceil($totalFacebookAccounts / $totalProxies));

        $allProxies = $this->proxyV6Repository->findAll();
        $facebookAccounts = $this->accountRepository->findAll();

        foreach ($facebookAccounts as $facebookAccount) {
            $isPortAssigned = false;

            while (!$isPortAssigned && count($allProxies) > 0) {
                $radomProxy = $allProxies[array_rand($allProxies)];
                $radomPort = $radomProxy['port'];

                $currentUsageCount = $this->accountRepository->countAccounts(['networkuse.port' => $radomPort]);

                if ($currentUsageCount < $facebookePerProxy) {

                    $this->accountRepository->update(
                        $facebookAccount['_id'],
                        [
                            'networkuse.type' => 'proxy',
                            'networkuse.ip' => '127.0.0.1',
                            'networkuse.port' => $radomPort,
                        ]
                    );

                    $isPortAssigned = true;
                } else {
                    $allProxies = array_filter($allProxies, function ($proxy) use ($radomPort) {
                        return $proxy['port'] !== $radomPort;
                    });

                    $allProxies = array_values($allProxies);
                }
            }
            if (!$isPortAssigned) {
                break;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Đã gán proxy cho các tài khoản Facebook theo giới hạn tính toán.'
        ]);
    }

    public function createQRCode($label, $secret)
    {
        $label = preg_replace('/\s+/', '', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'));
        $secret = preg_replace('/\s+/', '', htmlspecialchars($secret, ENT_QUOTES, 'UTF-8'));

        // Tạo URL chuẩn OTPauth
        $secret_info = sprintf('otpauth://totp/%s?secret=%s', 'FB:' . $label, $secret);
        $qr_code_file = storage_path('app/public/qrcode.png');
        $size = 5;

        QRcode::png($secret_info, $qr_code_file, QR_ECLEVEL_L, $size);

        // Đọc file và hiển thị
        $qr_image = file_get_contents($qr_code_file);
        $base64Qr = base64_encode($qr_image);
        unlink($qr_code_file);

        return  $base64Qr;
    }

    public function importAccount(Request $request)
    {
        $responseData = ['status' => false, 'message' => null];
        $body = $request->all();
        $listInsertData = [];
        // dd($body);
        if (!empty($body)) {
            $account_type = trim($body['account_type']);
            $select_group_account = trim($body['select_group_account']) ?: 'default';


            if ($account_type == 'Zalo') {
                return response()->json(['reponse' => $responseData], 200);
            } elseif ($account_type == 'Facebook') {
                $account_list = preg_split("/\r\n|\n|\r/", $body['account_list']);
                $structureStr = trim($body['chosen_structure']);

                if (empty($structureStr)) {
                    $responseData = [
                        'status' => false,
                        'message' => "Cần chọn cấu trúc cho dữ liệu import!",
                    ];
                    return response()->json(["reponse" => $responseData], 200);
                }


                $chosenStructure = explode('|', $structureStr);
                if (!empty($account_list)) {
                    foreach ($account_list as $_select) {
                        if (!empty($_select)) {
                            $accountImport = explode('|', $_select);
                            $insertData = [];

                            foreach ($chosenStructure as $key => $structure) {
                                $value = $accountImport[$key] ?? null;
                                switch ($structure) {
                                    case 'cookies_pc':
                                        $insertData['windows_device']['cookies'] = $value;
                                        break;
                                    case 'useragent_pc':
                                        $insertData['windows_device']['UserAgent'] = $value;
                                        break;
                                    case 'cookies_mobile':
                                        $insertData['android_mobile']['cookies'] = $value;
                                        break;
                                    case 'useragent_mobile':
                                        $insertData['android_mobile']['UserAgent'] = $value;
                                        break;
                                    default:
                                        $insertData[$structure] = $value;
                                }
                            }
                            $listInsertData[] = $insertData;
                        }
                    }

                    if (count($listInsertData) > 0) {
                        foreach ($listInsertData as $data) {
                            $defaultData = [
                                "uid" => $data['uid'] ?? null,
                                "password" => $data['password'] ?? null,
                                "qrcode" => $data['qrcode'] ?? null,
                                "birthday" => $data['birthday'] ?? null,
                                "fullname" => null,
                                "email" => $data['email'] ?? null,
                                "phone" => null,
                                "gender" => null,
                                "country" => null,
                                "language" => null,
                                "password_email" => $data['password_email'] ?? null,
                                "last_seeding" => null,
                                "created_time" => Carbon::now(),
                                "last_update" => Carbon::now(),
                                "networkuse" => [
                                    "type" => null,
                                    "ip" => null,
                                    "port" => null,
                                    "username" => null,
                                    "password" => null
                                ],
                                "windows_device" => [
                                    "cookies" => $data['windows_device']['cookies'] ?? null,
                                    "UserAgent" => $data['windows_device']['UserAgent'] ?? null,
                                    "Business_Token" => null,
                                    "Ads_Token" => null,
                                    "Intagram_Token" => null
                                ],
                                "android_mobile" => [
                                    "cookies" => $data['android_mobile']['cookies'] ?? null,
                                    "UserAgent" => $data['android_mobile']['UserAgent'] ?? null
                                ],
                                "android_device" => [
                                    "cookies" => null,
                                    "access_token" => null,
                                    "device_id" => null,
                                    "adid" => null,
                                    "family_device_id" => null,
                                    "advertiser_id" => null,
                                    "UserAgent" => null,
                                    "UserAgentApp" => null,
                                    "machine_id" => null,
                                    "sim_serials" => null,
                                    "jazoest" => null
                                ],
                                "groups_account" => $select_group_account,
                                "friends" => null,
                                "groups" => null,
                                "friend_requests" => null,
                                "friend_suggestion" => null,
                                "latitude" => null,
                                "longitude" => null,
                                "last_ip_connect" => null,
                                "config_auto" => null,
                                "notes" => null,
                                "status" => null,
                                "useAccount" => 'NO'
                            ];

                            // Làm sạch dữ liệu
                            foreach ($defaultData as $field => &$value) {
                                if (is_array($value)) {
                                    foreach ($value as $subField => &$subValue) {
                                        if (!is_null($subValue)) {
                                            $subValue = e($subValue);
                                        }
                                    }
                                } else {
                                    if (!is_null($value)) {
                                        $value = e($value);
                                    }
                                }
                            }
                            $Accountuse = new FacebookAccount($defaultData);
                            $Accountuse->AccountInfo['config_auto'] = config('defaultconfigs.defaultConfigFacebook');

                            // Kiểm tra tồn tại
                            $existingAccount = $this->accountRepository->findByUid($Accountuse->AccountInfo['uid']);

                            if ($existingAccount) {
                                $this->accountRepository->update($existingAccount->_id, $Accountuse->AccountInfo);
                                $responseData = [
                                    'status' => true,
                                    'message' => "Cập Nhật Thành Công !",
                                ];
                            } else {
                                $this->accountRepository->create($Accountuse->AccountInfo);
                                $responseData = [
                                    'status' => true,
                                    'message' => "Thêm Mới Thành Công !",
                                ];
                            }
                        }
                    }
                }
            }
        }

        return redirect()->back()->with([
            'message' => $responseData['message'],
            'status' => $responseData['status']
        ]);
    }

    public function filterAccounts(Request $request)
    {
        // Lấy tất cả input từ request (trừ _token)
        $filters = $request->except('_token');
        $query = [];

        session(['fb_filters_input' => $filters]);

        if (!empty($filters['uid_list'])) {
            $uidList = preg_split("/\r\n|\n|\r/", $filters['uid_list']);
            $uidList = array_filter(array_map('trim', $uidList)); // Loại bỏ khoảng trắng và các dòng trống
            if (!empty($uidList)) {
                $query['uid'] = ['$in' => $uidList];
            }
        }

        //port proxy done
        if (!empty($filters['networkuse_port'])) {
            if ($filters['networkuse_port'] === 'has_proxy') {
                $query['networkuse.port'] = ['$ne' => null]; // Lọc các tài khoản có proxy
            } elseif ($filters['networkuse_port'] === 'no_proxy') {
                $query['networkuse.port'] = null; // Lọc các tài khoản không có proxy
            }
        }
        // Lọc giới tính (done)
        if (!empty($filters['gender'])) {
            if ($filters['gender'] === 'male' || $filters['gender'] === 'Nam') {
                $query['gender'] = ['$in' => ['male', 'Nam']];
            } elseif ($filters['gender'] === 'female' || $filters['gender'] === 'Nữ') {
                $query['gender'] = ['$in' => ['female', 'Nữ']];
            }
        }
        // Lọc tuổi (done)
        if (!empty($filters['birthday_from']) && !empty($filters['birthday_to'])) {
            try {
                $currentDate = Carbon::now();
                $minAge = intval($filters['birthday_from']);
                $maxAge = intval($filters['birthday_to']);
                if ($minAge >= $maxAge) {
                    return redirect()->back()->with('error', 'Độ tuổi không hợp lệ.');
                }
                //$minAge = $minAge +1;
                // Tính khoảng thời gian từ độ tuổi
                $minDate = $currentDate->subYears($maxAge)->format('Y-m-d');
                $maxDate = $currentDate->addYears($maxAge - $minAge)->format('Y-m-d');

                // MongoDB pipeline
                $query['$and'] = [
                    [
                        'birthday' => [
                            '$exists' => true,
                            '$ne' => null,
                            '$ne' => '',
                        ],
                    ],
                    [
                        '$expr' => [
                            '$and' => [
                                [
                                    '$gte' => [
                                        [
                                            '$ifNull' => [
                                                [
                                                    '$dateFromString' => [
                                                        'dateString' => '$birthday',
                                                        'format' => '%d/%m/%Y',
                                                        'onError' => null,
                                                        'onNull' => null,
                                                    ],
                                                ],
                                                new \MongoDB\BSON\UTCDateTime(0),
                                            ],
                                        ],
                                        new \MongoDB\BSON\UTCDateTime(strtotime($minDate) * 1000),
                                    ],
                                ],
                                [
                                    '$lte' => [
                                        [
                                            '$ifNull' => [
                                                [
                                                    '$dateFromString' => [
                                                        'dateString' => '$birthday',
                                                        'format' => '%d/%m/%Y',
                                                        'onError' => null,
                                                        'onNull' => null,
                                                    ],
                                                ],
                                                new \MongoDB\BSON\UTCDateTime(0),
                                            ],
                                        ],
                                        new \MongoDB\BSON\UTCDateTime(strtotime($maxDate) * 1000),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Định dạng tuổi không hợp lệ.');
            }
        }

        if (!empty($filters['friends_from']) || !empty($filters['friends_to'])) {
            $friendsRange = [
                'from' => !empty($filters['friends_from']) ? (int)$filters['friends_from'] : null,
                'to' => !empty($filters['friends_to']) ? (int)$filters['friends_to'] : null,
            ];

            // Gọi repository để lọc UID theo số lượng bạn bè
            $friendUIDs = $this->accountRepository->filterFriendsByRange($friendsRange);
        } else {
            $friendUIDs = []; // Nếu không có điều kiện lọc bạn bè, giữ mảng rỗng
        }



        if (!empty($filters['groups_from']) || !empty($filters['groups_to'])) {
            $groupsRange = [
                'from' => !empty($filters['groups_from']) ? (int)$filters['groups_from'] : null,
                'to' => !empty($filters['groups_to']) ? (int)$filters['groups_to'] : null,
            ];
            // Gọi repository để lọc UID theo số lượng nhóm
            $groupUIDs = $this->accountRepository->filterGroupsByRange($groupsRange);
        } else {
            $groupUIDs = []; // Nếu không có điều kiện lọc nhóm, giữ mảng rỗng
        }
        // Kết hợp UID của bạn bè và nhóm
        if (empty($friendUIDs) && empty($groupUIDs)) {
            // Nếu cả friendUIDs và groupUIDs đều rỗng, nghĩa là không có kết quả khớp

        } else {
            // Nếu có UID từ bạn bè hoặc nhóm, kết hợp chúng
            $uids = array_unique(array_merge($friendUIDs, $groupUIDs));
            $query['uid'] = ['$in' => $uids];
        }

        // Kết hợp UID của bạn bè và nhóm vào $query
        if (!empty($friendUIDs) || !empty($groupUIDs)) {
            $uids = array_unique(array_merge($friendUIDs, $groupUIDs));
            $query['uid'] = ['$in' => $uids];
        }
        //tương tác cuối (done)
        if (!empty($filters['last_seeding'])) {
            // Chuyển đổi số giờ thành thời gian ngưỡng (threshold)
            $hours = (int)$filters['last_seeding'];
            $timeThreshold = now()->subHours($hours);
            // Chuyển đổi sang MongoDB UTCDateTime
            $utcDateTime = new \MongoDB\BSON\UTCDateTime($timeThreshold->timestamp * 1000);
            // Tạo pipeline cho last_seeding
            $query['last_seeding_pipeline'] = [
                [
                    '$match' => [
                        'last_seeding' => [
                            '$type' => 'string'
                        ]
                    ]
                ],
                [
                    '$addFields' => [
                        'parsed_last_seeding' => [
                            '$dateFromString' => [
                                'dateString' => '$last_seeding',
                                'format' => '%d/%m/%Y %H:%M:%S',
                                'onError' => null,
                                'onNull' => null
                            ]
                        ]
                    ]
                ],
                [
                    '$match' => [
                        'parsed_last_seeding' => [
                            '$gte' => $utcDateTime
                        ]
                    ]
                ],
                [
                    '$project' => [
                        'parsed_last_seeding' => 0
                    ]
                ]
            ];
        }
        //done
        if (!empty($filters['groups_account'])) {
            $query['groups_account'] = $filters['groups_account'];
        }
        //done
        if (!empty($filters['email'])) {
            $query['email'] = ['$regex' => $filters['email'], '$options' => 'i'];
        }
        //done
        if (!empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }
        //dd($query);
        //exit();
        // Lưu bộ lọc vào session
        session(['fb_filters' => $query]);


        return response()->json(['message' => 'Filters applied successfully.']);
    }

    public function clearFilter(Request $request)
    {
        // Xóa session chứa bộ lọc
        $request->session()->forget(['fb_filters', 'fb_filters_input']);
        return response()->json(['message' => 'Bộ lọc đã được xóa thành công.']);
    }

    public function fixBirthday(Request $request)
    {
        // Gọi phương thức fixBirthday từ repository
        $result = $this->accountRepository->fixBirthday();

        if ($result['status']) {
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật định dạng birthday thành công.',
                'updatedCount' => $result['updatedCount']
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật định dạng birthday thất bại.',
                'error' => $result['error']
            ], 200);
        }
    }

    public function deleteAccounts(Request $request)
    {
        $uids = $request->input('selected_accounts', []);

        if (empty($uids)) {
            return redirect()->back()->with('error', 'Không có UID nào được chọn để xóa.');
        }

        $this->accountRepository->deleteByUids($uids);

        return redirect()->back()->with('status', 'Tài khoản đã được xóa thành công.');
    }

    public function showPassword(Request $request)
    {
        // Lấy dữ liệu password từ input và trim để bỏ khoảng trống
        $passwordInput = trim($request->input('password'));
        $uid = trim($request->input('uid'));
        $_ResultPass = $passwordInput;
        // Kiểm tra xem extension 'spaceviet' có được tải hay không
        if (extension_loaded('spaceviet')) {
            // Giải mã password nếu extension 'spaceviet' được tải
            $_Encryption = new HqitEncryption();
            if ($_Encryption->isEncryptedString($passwordInput)) {
                $keysEn = md5(trim($uid));
                $_ResultPass = HqitDecrypt($passwordInput, $keysEn);
            }
        }
        // Trả về password dưới dạng JSON
        return response()->json(['password' => $_ResultPass]);
    }

    public function CreatePassword(Request $request)
    {
        $_Random = new Common();
        return [
            'status' => 200,
            'password' => $_Random->RandomTextPasword(18),
        ];
    }

    public function exportAccounts(Request $request)
    {
        $uids = $request->input('uids', []);
        $exportGroup = $request->input('export_group', '');

        if (!empty($uids)) {
            // Nếu có UID được gửi, sử dụng chúng để xuất
            $accounts = $this->accountRepository->findByUids($uids);
        } else {
            // Nếu không có UID, tìm UID dựa trên nhóm tài khoản
            $uids = $this->accountRepository->findByGroup($exportGroup);
            $accounts = $this->accountRepository->findByUids($uids);
        }

        if (empty($accounts)) {
            return response()->json(['message' => 'Không tìm thấy tài khoản tương ứng.'], 200);
        }
        // Tạo tên file dựa trên thời gian hiện tại
        $fileName = 'export_accounts_full_' . now()->format('Ymd_His') . '.json';

        // Tạo StreamedResponse để xuất dữ liệu dưới dạng JSON
        $response = new StreamedResponse(function () use ($accounts) {
            $handle = fopen('php://output', 'w');

            if (empty($accounts)) {
                fclose($handle);
                return;
            }
            // Chuẩn bị dữ liệu để xuất
            $data = [];

            foreach ($accounts as $account) {
                // Chuyển đổi đối tượng tài khoản thành mảng
                $accountArray = json_decode(json_encode($account), true);
                $data[] = $accountArray;
            }

            // Ghi dữ liệu JSON vào output
            fwrite($handle, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }

    public function importAccountByFile(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:json',
        ]);

        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');
            $filePath = $file->getRealPath();

            // Đọc nội dung file JSON
            $jsonContent = file_get_contents($filePath);
            $accountsData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'Dữ liệu JSON không hợp lệ.'], 200);
            }

            $added = 0;
            $skipped = 0;

            foreach ($accountsData as $account) {
                // Kiểm tra và chuyển đổi _id nếu có
                if (isset($account['_id']) && isset($account['_id']['$oid'])) {
                    try {
                        $account['_id'] = new ObjectID($account['_id']['$oid']);
                    } catch (\Exception $e) {
                        // Nếu $oid không hợp lệ, bỏ qua hoặc xử lý phù hợp
                        unset($account['_id']);
                    }
                }

                $existing = $this->accountRepository->findByUid($account['uid']);
                if (!$existing) {
                    // Tạo mới tài khoản
                    $this->accountRepository->create($account);
                    $added++;
                } else {
                    $skipped++;
                }
            }

            return response()->json([
                'message' => "Import thành công! Thêm mới: {$added}, Bỏ qua: {$skipped}."
            ], 200);
        }

        return response()->json(['message' => 'Không có file nào được tải lên.'], 200);
    }

    public function updateNetworkUse(Request $request)
    {
        $uids = $request->input('uids', []);
        $type = $request->input('type', null);
        $interface = $request->input('interface', null);

        // Chuẩn bị dữ liệu cập nhật
        $networkUseData = [
            'networkuse.type' => $type,
            'networkuse.ip' => null,
            'networkuse.port' => null,
            'networkuse.username' => null,
            'networkuse.password' => null,
        ];

        if ($type === 'interfaces' && $interface) {
            $networkUseData['networkuse.interface'] = $interface;
        }

        $condition = [];
        if (count($uids) > 0) {
            $condition = ['uid' => ['$in' => $uids]];
        }

        $updateResult = $this->accountRepository->UpdateAll($networkUseData, $condition);

        return response()->json([
            'status' => $updateResult->status,
            'message' => $updateResult->message
        ], 200);
    }

    public function updateNetworkUseByProxyList(Request $request)
    {
        $proxyListInput = $request->input('proxyList', []);
        $number = $request->input('number', 0); // Số lượng proxy cần phân bổ

        if (empty($proxyListInput)) {
            return response()->json([
                'status' => false,
                'message' => 'Danh sách proxy không được bỏ trống.'
            ], 200);
        }

        // Tách danh sách proxy thành mảng, mỗi proxy trên một dòng
        $proxyLines = preg_split('/\r\n|\r|\n/', $proxyListInput);
        $proxyData = [];

        foreach ($proxyLines as $line) {
            $parsedProxy = $this->accountRepository->parseProxy(trim($line));
            if ($parsedProxy) {
                $proxyData[] = $parsedProxy;
            }
        }

        if (empty($proxyData)) {
            return response()->json([
                'status' => false,
                'message' => 'Không có proxy hợp lệ trong danh sách.'
            ], 200);
        }

        // Lấy tất cả UIDs
        $uids = $this->accountRepository->getAllUids();

        if (empty($uids)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tài khoản để cập nhật.'
            ], 200);
        }

        // Xác định số lượng cập nhật tối đa
        $updateCount = min(count($proxyData), count($uids));

        for ($i = 0; $i < $updateCount; $i++) {
            $uid = $uids[$i];
            $proxy = $proxyData[$i];
            $this->accountRepository->updateNetworkUse($uid, $proxy);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật danh sách proxy thành công.'
        ], 200);
    }

    public function changeStatus(Request $request)
    {

        $status = $request->input('status');
        $accountIds = $request->input('account_ids');

        try {
            if ($accountIds) {
                // Cập nhật trạng thái cho các tài khoản được chọn
                $this->accountRepository->updateManyByUids($accountIds, ['status' => $status]);
            } else {
                // Cập nhật trạng thái cho tất cả tài khoản
                $this->accountRepository->UpdateAll(['status' => $status]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Trạng thái tài khoản đã được cập nhật thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật trạng thái: ' . $e->getMessage(),
            ], 500);
        }
    }

    // public function multiMessageCommentPage(Request $request)
    // {

    //     // Lấy dữ liệu từ request
    //     $selectedAccounts = $request->input('selected_accounts', []);
    //     $groupAccount = $request->input('group_account');

    //     // Nếu có dữ liệu mảng uid được gửi
    //     if (is_array($selectedAccounts) && count($selectedAccounts) > 0) {
    //         $uids = $selectedAccounts;
    //     } elseif (!empty($groupAccount)) {
    //         // Nếu nhận group thì sử dụng phương thức findByGroup để lấy danh sách uid
    //         $uids = $this->accountRepository->findByGroup($groupAccount);
    //     } else {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Vui lòng chọn tài khoản hoặc nhóm tài khoản.'
    //         ], 400);
    //     }

    //     // Trả về view với biến uids chứa mảng uid
    //     return view('Facebook::Facebook.multi_message_comment_page', compact('uids'));
    // }
}
