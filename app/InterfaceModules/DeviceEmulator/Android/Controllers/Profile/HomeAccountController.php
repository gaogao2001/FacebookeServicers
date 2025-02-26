<?php

namespace App\InterfaceModules\DeviceEmulator\Android\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use MongoDB\BSON\ObjectId;
use HoangquyIT\FacebookAccount;
use HoangquyIT\Weather\SamsungWeather;
use HoangquyIT\Helper\Common;
use HoangquyIT\ModelFacebook\Android\CheckConnect;
use HoangquyIT\ModelFacebook\Android\MainControler;
use HoangquyIT\ModelFacebook\Android\Profile\ProfileManager;
use HoangquyIT\ModelFacebook\Android\Accounts\AccountManager;
use App\Modules\Fanpage\Repositories\FanpageManagerRepositoryInterface;
use HoangquyIT\NetworkControler\NetworkControler;
use Illuminate\Pagination\LengthAwarePaginator;
use HoangquyIT\ModelFacebook\Android\Groups\GroupsManager;
use HoangquyIT\ModelFacebook\Android\Marketplace\MainMarketplace;
use HoangquyIT\ModelFacebook\Android\Message\MessageControler;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\DeviceEmulatorController;


class HomeAccountController extends Controller
{
    protected $accountRepository;
    protected $fanpageManagerRepository;
    protected $account; // day la luu giu thong tin goc tu mongodb tra ve khi connect
    protected $uid; // tai day chua thong tin facebook id
    protected $_id; // tai day chua thong tin facebook id
    protected $FacebookUse; // tai day luu tru thong tin account khi da chuyen doi sang array
    protected $InterfaceUse; // tai day luu tru thong tin account khi da chuyen doi sang array
    public $Message = null; // chua thong bao loi
    public $ConnectData = false; // ghi nhan status

    public function vailidateUids($uid)
    {
        // Kiểm tra sự tồn tại của uid
        if (!empty($uid)) {
            // Tìm kiếm tài khoản dựa trên uid
            $account = $this->accountRepository->findByUid(trim($uid));
            // Kiểm tra sự tồn tại của tài khoản
            if (!empty($account)) {
                // Gán các thuộc tính cho controller
                $this->account = $account;
                $this->uid = $account->uid;
                $this->_id = $account->_id;
                if (!empty($this->account->MultiAccount)) {
                    foreach ($this->account->MultiAccount as $_account) {
                        if (trim($_account->profile->id) == trim($uid)) {
                            $cookies = '';
                            // Xây dựng chuỗi cookie
                            foreach ($_account->session_info->session_cookies as $cookieJson) {
                                $cookie = json_decode($cookieJson);
                                $cookieString = "{$cookie->name}={$cookie->value}; path={$cookie->path}; domain={$cookie->domain}; ";
                                $cookieString .= ($cookie->secure ? 'Secure; ' : '') . ($cookie->httponly ? 'HttpOnly; ' : '') . ($cookie->samesite ? "SameSite={$cookie->samesite}; " : '');
                                $cookies .= $cookieString . "\n";
                            }
                            $this->account->uid = $uid;
                            $this->account->android_device->cookies = $cookies;
                            $this->account->android_device->access_token = $_account->session_info->access_token;
                        }
                    }
                }
                $this->InterfaceUse = $this->account->networkuse->interface ?? null;
                if (empty($this->account->networkuse->interface)) {
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
                    $this->InterfaceUse = $selectedSetting;
                }
                // kiểm tra kết nối của phần network
                $_Network = new NetworkControler();
                $CheckConnect = $_Network->TestConnect($this->account->networkuse);
                if (!$CheckConnect) {
                    if ($this->account->networkuse->type == 'interfaces') {
                        $resultCreate = $_Network->CreateInterface($this->InterfaceUse);
                        if (!$resultCreate->status) {
                            die('Lỗi khởi tạo Interface, tạo tay xem lại lỗi');
                        }
                        $this->account->networkuse->ip = $resultCreate->ip;
                        $updatData = array('networkuse' => iterator_to_array($this->account->networkuse));
                        $this->accountRepository->updateByUid($this->uid, $updatData);
                    }
                }
                // chuyển đổi mảng cho dữ liệu từ mongodb
                $this->FacebookUse = iterator_to_array($this->account);
                unset($this->FacebookUse["_id"]);
                //
                $uids = array_map(function ($account) {
                    return $account->uid;
                }, [$account]);

                //
                $this->ConnectData = true;
            } else {
                $this->Message = 'Không tìm thấy tài khoản';
            }
        } else {
            $this->Message = 'Thiếu dữ liệu cần thiết: uid';
        }
    }
    public function getFacebookUse()
    {
        return $this->FacebookUse;
    }

    //ở file này thực hiện các tác vụ hiển thị trang chủ của tài khoản facebook; Trả ra các biến cần thiết để hiển thị trang chủ; tướng tác với Home main
    public function __construct(Request $request, AccountRepositoryInterface $accountRepository, FanpageManagerRepositoryInterface $fanpageManagerRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->fanpageManagerRepository = $fanpageManagerRepository;
        // Lấy uid từ route parameters
        $uid = $request->route('uid');

        // Kiểm tra sự tồn tại của uid
        if (!empty($uid)) {
            // Tìm kiếm tài khoản dựa trên uid
            $account = $this->accountRepository->findByUid(trim($uid));
            // Kiểm tra sự tồn tại của tài khoản
            if (!empty($account)) {
                // Gán các thuộc tính cho controller
                $this->account = $account;
                $this->uid = $account->uid;
                $this->_id = $account->_id;
                if (!empty($this->account->MultiAccount)) {
                    foreach ($this->account->MultiAccount as $_account) {
                        if (trim($_account->profile->id) == trim($uid)) {
                            $cookies = '';
                            // Xây dựng chuỗi cookie
                            foreach ($_account->session_info->session_cookies as $cookieJson) {
                                $cookie = json_decode($cookieJson);
                                $cookieString = "{$cookie->name}={$cookie->value}; path={$cookie->path}; domain={$cookie->domain}; ";
                                $cookieString .= ($cookie->secure ? 'Secure; ' : '') . ($cookie->httponly ? 'HttpOnly; ' : '') . ($cookie->samesite ? "SameSite={$cookie->samesite}; " : '');
                                $cookies .= $cookieString . "\n";
                            }
                            $this->account->uid = $uid;
                            $this->account->android_device->cookies = $cookies;
                            $this->account->android_device->access_token = $_account->session_info->access_token;
                        }
                    }
                }
                $this->InterfaceUse = $this->account->networkuse->interface ?? null;
                if (empty($this->account->networkuse->interface)) {
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
                    $this->InterfaceUse = $selectedSetting;
                }
                // kiểm tra kết nối của phần network
                $_Network = new NetworkControler();
                $CheckConnect = $_Network->TestConnect($this->account->networkuse);
                if (!$CheckConnect) {
                    if ($this->account->networkuse->type == 'interfaces') {
                        $resultCreate = $_Network->CreateInterface($this->InterfaceUse);
                        if (!$resultCreate->status) {
                            die('Lỗi khởi tạo Interface, tạo tay xem lại lỗi');
                        }
                        $this->account->networkuse->ip = $resultCreate->ip;
                        $updatData = array('networkuse' => iterator_to_array($this->account->networkuse));
                        $this->accountRepository->updateByUid($this->uid, $updatData);
                    }
                }
                // chuyển đổi mảng cho dữ liệu từ mongodb
                $this->FacebookUse = iterator_to_array($this->account);
                unset($this->FacebookUse["_id"]);
                //
                $uids = array_map(function ($account) {
                    return $account->uid;
                }, [$account]);
                //
                $friendsData = $this->accountRepository->getMultipleFriendsData($uids);
                $groupsData = $this->accountRepository->getMultipleGroupsData($uids);
                $postData = $this->accountRepository->getMultiplePostsData($uids);
                //
                $this->account->groups = $groupsData;
                $this->account->friends = $friendsData;
                $this->account->post_data = $postData;
                //
                $this->ConnectData = true;
            } else {
                $this->Message = 'Không tìm thấy tài khoản';
            }
        } else {
            $this->Message = 'Thiếu dữ liệu cần thiết: uid';
        }
    }

    public function validateInputs(Request $request, array $rules)
    {
        $request->validate($rules);
        $uid = $request->input('uid');
        //
        if (empty($uid)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thiếu dữ liệu cần thiết: uid'
            ], 200);
        }

        return compact('url', 'uid');
    }

    public function Home(Request $request, $uid)
    {
        if ($this->ConnectData) {
            $latitude = $this->account->latitude ?? '11.036';
            $longitude = $this->account->longitude ?? '106.731';
            $Weather = new SamsungWeather();
            $data['Weather'] = $Weather->LoadWeather($latitude . ', ' . $longitude);
            // 
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse); // kết nối facebook
                if ($CheckConnect->ConnectAccount) {
                    $Main = new MainControler($Accountuse); // ket noi class dung de lay thong tin ban tin facebook
                    $profile = new ProfileManager($Accountuse); // ket noi class dung lay thong tin account facebook
                    $arrayUpdate = array('last_ip_connect' => $Accountuse->AccountInfo['last_ip_connect']);
                    $_account = $profile->OpenSecurityTwoFactor();
                    die('ssssssss');
                    $_account = $profile->MyProfile(); // tai day cho ra thong tin profile tai khoan facebook
                    if (!empty($_account->avatar)) {
                        // xu ly update thong tin url avatar nay vao mongodb 
                        $arrayUpdate['avatar'] = $_account->avatar ?? null;
                        $arrayUpdate['fullname'] = $_account->name ?? null;
                        $arrayUpdate['birthday'] = $_account->birthday ?? null;
                        $arrayUpdate['email'] = $_account->email ?? null;
                        $arrayUpdate['friends'] = $_account->friends ?? null;
                        $arrayUpdate['phone'] = $_account->phone ?? null;
                        //
                        $this->account->avatar = $_account->avatar ?? null;
                        $this->account->birthday = $_account->birthday ?? null;
                        $this->account->fullname = $_account->name ?? null;
                        $this->account->email = $_account->email ?? null;
                        $this->account->phone = $_account->phone ?? null;
                    }

                    $MultiAccount = $profile->LoadAccountControl();
                    if (!empty($MultiAccount)) {

                        $arrayUpdate['MultiAccount'] = $MultiAccount;
                    }
                    if (count($arrayUpdate) > 0) {
                        $this->accountRepository->update($this->_id->__toString(), $arrayUpdate);
                    }
                    //die('bo qua');
                    if (!empty($MultiAccount)) {
                        foreach ($MultiAccount as $_select) {
                            $page_id = $_select->profile->id ?? null;
                            $page_name = $_select->profile->name ?? null;
                            $access_token = $_select->session_info->access_token ?? null;
                            if ($_select->AccountType == 'PageAccount') {
                                if ($page_id && $page_name && $access_token) {
                                    $existingPage = $this->fanpageManagerRepository->findOneFanpage([
                                        'uid_controler' => $this->uid,
                                        'page_id' => $page_id
                                    ]);

                                    if ($existingPage) {
                                        $this->fanpageManagerRepository->update($existingPage->_id->__toString(), [
                                            'page_name' => $page_name,
                                            'access_token' => $access_token,
                                            'config_auto' => config('defaultconfigs.defaultConfigFanpage'),
                                        ]);
                                    } else {
                                        $this->fanpageManagerRepository->create([
                                            'uid_controler' => $this->uid,
                                            'page_id' => $page_id,
                                            'page_name' => $page_name,
                                            'access_token' => $access_token,
                                            'config_auto' => config('defaultconfigs.defaultConfigFanpage'),
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                    // lay lai trong db  lan nua  $_account
                    $data['root_uid'] = $this->uid;
                    $data['account'] = $this->account;
                    $data['newfeed'] = $Main->LoadPostNewFeed();
                    $data['Suggestions'] = $Main->LoadSuggestions();
                    $data['MultiAccount'] = $MultiAccount;
                    $data['Stories'] = $Main->LoadStoriesNewFeed();
                    $data['FriendListAccept'] =  $Main->LoadRequestPending();
                    // hủy kết nối mạng để giảm tải cho hệ thống
                    $_Network = new NetworkControler();
                    $_Network->RemoveInterface($this->InterfaceUse, $this->account->networkuse->ip);
                    // gửi dữ liệu ra giao diện
                    return view('Android::Android_home', [
                        'data' => $data,
                        'account' => $this->account,
                        'uid' => $this->uid,
                        'FacebookUse' => $this->FacebookUse,
                    ]);
                } else {
                    $thongTin['status'] = false;
                    $thongTin['message'] = 'Account đang bị CHECKPOINT';
                    return response()->json(['response' => $thongTin], 200);
                }
            } else {
                $thongTin['status'] = false;
                $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                return response()->json(['response' => $thongTin], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $this->Message
            ], 200);
        }
    }

    public function searchResult(Request $request, $uid)
    {
        $KeySearch = $request->input('key-search');

        $end_cursor = $request->input('end_cursor', null);
        $typeFind   = $request->input('typeFind', null);

        $data = [
            'account' => $this->account,
        ];

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {
                    $Main = new MainControler($Accountuse);

                    if ($end_cursor && $typeFind) {

                        $moreData = $Main->PaginationSearchResultsGraphQL($end_cursor, $KeySearch, $typeFind);

                        return response()->json($moreData, 200);
                    } else {
                        // Initial search
                        $users  = $Main->SearchResultsGraphQL($KeySearch, 'user');
                        $groups = $Main->SearchResultsGraphQL($KeySearch, 'group');
                        $pages  = $Main->SearchResultsGraphQL($KeySearch, 'page');

                        $data['FriendListAccept'] =  $Main->LoadRequestPending();

                        return view('Android::Search.Search_result', [
                            'data'      => $data,
                            'uid'       => $this->uid,
                            'keySearch' => $KeySearch,
                            'users'     => $users,
                            'groups'    => $groups,
                            'pages'     => $pages,
                        ]);
                    }
                } else {
                    $thongTin['status'] = false;
                    $thongTin['message'] = 'Account đang bị CHECKPOINT';
                    return response()->json(['response' => $thongTin], 200);
                }
            } else {
                $thongTin['status'] = false;
                $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                return response()->json(['response' => $thongTin], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $this->Message
            ], 200);
        }
    }

    public function syncFanpage(Request $request, $uid)
    {
        // get uid ; check connect
        $this->vailidateUids($uid);
        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);

            if ($Accountuse->Connect) {

                try {
                    $CheckConnect = new CheckConnect($Accountuse); // kết nối facebook
                } catch (\Throwable $ex) {
                    return response()->json([
                        'status'  => 'not_synced',
                        'message' => 'Tài khoản ' . $uid . ' chưa được đồng bộ - Lỗi CheckConnect: ' . $ex->getMessage()
                    ], 200);
                }
                // Nếu không thể kết nối qua CheckConnect, trả về thông báo chưa được đồng bộ
                if (!$CheckConnect->ConnectAccount) {
                    return response()->json([
                        'status'  => 'not_synced',
                        'message' => 'Tài khoản ' . $uid . ' chưa được đồng bộ'
                    ], 200);
                }

                if ($CheckConnect->ConnectAccount) {
                    $profile = new ProfileManager($Accountuse); // kết nối lấy thông tin account facebook
                    $MultiAccount = $profile->LoadAccountControl();

                    if (!empty($MultiAccount)) {
                        foreach ($MultiAccount as $_select) {
                            $page_id = $_select->profile->id ?? null;
                            $page_name = $_select->profile->name ?? null;
                            $access_token = $_select->session_info->access_token ?? null;
                            if ($_select->AccountType == 'PageAccount') {
                                if ($page_id && $page_name && $access_token) {
                                    $existingPage = $this->fanpageManagerRepository->findOneFanpage([
                                        'uid_controler' => $this->uid,
                                        'page_id'      => $page_id
                                    ]);

                                    if ($existingPage) {
                                        $this->fanpageManagerRepository->update($existingPage->_id->__toString(), [
                                            'page_name'    => $page_name,
                                            'access_token' => $access_token,
                                            'config_auto'  => config('defaultconfigs.defaultConfigFanpage'),
                                        ]);
                                    } else {
                                        $this->fanpageManagerRepository->create([
                                            'uid_controler' => $this->uid,
                                            'page_id'      => $page_id,
                                            'page_name'    => $page_name,
                                            'access_token' => $access_token,
                                            'config_auto'  => config('defaultconfigs.defaultConfigFanpage'),
                                        ]);
                                    }
                                }
                            }
                        }

                        return response()->json([
                            'status'  => 'success',
                            'message' => 'Đồng bộ Fanpage thành công cho tài khoản ' . $uid
                        ], 200);
                    } else {
                        // Trường hợp MultiAccount rỗng -> chưa được đồng bộ
                        return response()->json([
                            'status'  => 'not_synced',
                            'message' => 'Tài khoản ' . $uid . ' chưa được đồng bộ'
                        ], 200);
                    }
                } else {
                    $thongTin['status'] = false;
                    $thongTin['message'] = 'Account đang bị CHECKPOINT';
                    return response()->json($thongTin, 200);
                }
            } else {
                $thongTin['status'] = false;
                $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                return response()->json($thongTin, 200);
            }
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => $this->Message
            ], 200);
        }
    }
    // Đồng bộ tất cả Fanpage
    public function syncAllFanpage(Request $request)
    {
        $allUids = $this->accountRepository->getAllUids();
        $results = [];

        foreach ($allUids as $uid) {
            // Gọi syncFanpage cho từng uid và thu thập kết quả trả về (cả thành công và không đồng bộ)
            $response = $this->syncFanpage($request, $uid);
            $result = json_decode($response->getContent(), true);
            $results[$uid] = $result;
        }

        return response()->json([
            'status' => 'success',
            'data'   => $results,
        ], 200);
    }

    protected function startSub(Request $request, $uid)
    {
        $limit =  $request->input('limit');
        $uid_sub = $request->input('uid_sub');

        $this->vailidateUids($uid);

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {

                    $profile = new ProfileManager($Accountuse);

                    $resultUser = $profile->LoadInfoUserByUID($uid_sub);

                    if ($limit->$resultUser->follow) {
                        if ($profile->FollowUser($uid_sub)) {
                            return response()->json([
                                'status' => 'success',
                                'message' => 'Sub thành công cho tài khoản ' . $uid_sub
                            ], 200);
                        } else {

                            return response()->json([
                                'status' => 'error',
                                'message' => 'Sub thất bại cho tài khoản ' . $uid_sub
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Đã vượt giới hạn sub cho tài khoản '
                        ], 200);
                    }
                } else {
                    $thongTin['status'] = false;
                    $thongTin['message'] = 'Account đang bị CHECKPOINT';
                    return response()->json(['response' => $thongTin], 200);
                }
            } else {
                $thongTin['status'] = false;
                $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                return response()->json(['response' => $thongTin], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $this->Message
            ], 200);
        }
    }

    public function SubNow(Request $request)
    {
        $limit = $request->input('limit');
        $uid_sub = $request->input('uid_sub');

        $allUids = $this->accountRepository->getAllUids();

        $results = [];

        foreach ($allUids as $uid) {
            // Gọi hàm startSub và truyền các input từ request
            $response = $this->startSub($request, $uid);

            // Lưu kết quả
            $results[$uid] = json_decode($response->getContent(), true);
        }

        return response()->json([
            'status' => 'success',
            'data' => $results,
        ], 200);
    }

    public function LikeNow(Request $request)
    {
        $limit = $request->input('limit');
        $uid_sub = $request->input('uid_sub');

        dd($limit, $uid_sub);


      
    }

    public function ShareNow(Request $request)
    {
        $limit = $request->input('limit');
        $uid_sub = $request->input('uid_sub');

        dd($limit, $uid_sub);
    }


    public function multiMessageCommentPage(Request $request)
    {
        // Lấy dữ liệu từ request
        $selectedAccounts = $request->input('selected_accounts', []);
        $groupAccount = $request->input('group_account');

        // Nếu có dữ liệu mảng uid được gửi
        if (is_array($selectedAccounts) && count($selectedAccounts) > 0) {
            $uids = $selectedAccounts;
        } elseif (!empty($groupAccount)) {
            // Nếu nhận group thì sử dụng phương thức findByGroup để lấy danh sách uid
            $uids = $this->accountRepository->findByGroup($groupAccount);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng chọn tài khoản hoặc nhóm tài khoản.'
            ], 400);
        }
        // Duyệt mảng uid để lấy thông tin tin nhắn
        $data = [];
        foreach ($uids as $uid) {
            $response = $this->getMessageByUid($uid);
            $messages = json_decode($response->getContent());
           
            $data[] = (object)[
                'messages' => $messages->data,
            ];
        }
        // Trả về view với biến uids chứa mảng uid
        return view('Facebook::Facebook.multi_message_comment_page', compact('uids', 'data'));
    }

    protected function getMessageByUid($uid)
    {
        $this->vailidateUids($uid);

        $requestForRenew = new Request();
        $requestForRenew->replace(['uid' => $uid]);


        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);

                if ($CheckConnect->ConnectAccount) {
                    // Use the aliased FBAccountManager
                    $_account = new AccountManager($Accountuse);

                    if (empty($Accountuse->AccountInfo['android_device_message']['access_token'])) {
                        // Nếu chưa có phiên apk msg, tiến hành chuyển đổi từ apk fb sang apk msg
                        $resultConvert = $_account->ConverSession('256002347743983');
                        if ($resultConvert->status && !empty($resultConvert->session)) {
                            $Accountuse->AccountInfo['android_device_message'] = $resultConvert->session;
                            $this->accountRepository->update($this->_id->__toString(), [
                                'android_device_message' => $resultConvert->session
                            ]);
                        }
                    }

                    // Sau khi đã chuyển đổi (dù thành hay không), gọi renewSession để cập nhật lại phiên
                    $renewSession = new DeviceEmulatorController($this->accountRepository, $this->fanpageManagerRepository);
                    $renewSession->renewSession($requestForRenew);

                    $_Message = new MessageControler($Accountuse);

                    $messages = $_Message->LoadAllMessage();
                   

                    return response()->json([
                        'data' => $messages,
                    ], 200);

                    //print_r(json_encode($_Message->LoadMessageByUid('100028048535913')));
                } else {
                    $thongTin['status'] = false;
                    $thongTin['message'] = 'Account đang bị CHECKPOINT';
                    return response()->json(['response' => $thongTin], 200);
                }
            } else {
                $thongTin['status'] = false;
                $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                return response()->json(['response' => $thongTin], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $this->Message
            ], 200);
        }
    }

    public function showMoreMessage(Request $request, $uid)
    {
        $uid_message = $request->input('uid_message');

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);

                if ($CheckConnect->ConnectAccount) {

                    $_Message = new MessageControler($Accountuse);

                    $result =  $_Message->LoadMessageByUid($uid_message);

                    if ($result) {
                        $collection = app('mongo')->FacebookData->Messagers;

                        $conversationData = [
                            'uid'               => $uid,
                            'uid_message'       => $uid_message,
                            // Nếu muốn lưu trực tiếp dưới dạng mảng thì để $result,
                            // hoặc dùng json_encode($result) nếu muốn lưu dưới dạng chuỗi JSON
                            'conversation_data' => $result,
                            'updated_at'        => new \MongoDB\BSON\UTCDateTime(now()->timestamp * 1000),
                        ];

                        $existingConversation = $collection->findOne([
                            'uid'         => $uid,
                            'uid_message' => $uid_message,
                        ]);

                        if ($existingConversation) {
                            // Nếu cuộc hội thoại đã tồn tại thì cập nhật dữ liệu mới nhất
                            $collection->updateOne(
                                ['_id' => $existingConversation->_id],
                                ['$set' => $conversationData]
                            );
                        } else {
                            // Nếu chưa có, thêm mới
                            $conversationData['created_at'] = new \MongoDB\BSON\UTCDateTime(now()->timestamp * 1000);
                            $collection->insertOne($conversationData);
                        }
                    }


                    return response()->json([
                        'status' => 'success',
                        'data' => $result
                    ], 200);
                } else {
                    $thongTin['status'] = false;
                    $thongTin['message'] = 'Account đang bị CHECKPOINT';
                    return response()->json(['response' => $thongTin], 200);
                }
            } else {
                $thongTin['status'] = false;
                $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                return response()->json(['response' => $thongTin], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $this->Message
            ], 200);
        }
    }
}
