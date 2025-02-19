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

/// ở file nay thực hiên các tác vụ liên quán đến thông tin cá nhân(info ; timeline ; friend ; group ; fanpage)
class AccountInfoController extends Controller
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

    public function __construct(Request $request, AccountRepositoryInterface $accountRepository, FanpageManagerRepositoryInterface $fanpageManagerRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->fanpageManagerRepository = $fanpageManagerRepository;
        // Lấy uid từ route parameters
        $uid = $request->route('uid') ?? $request->input('uid');


  
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
                //
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
                // $this->FacebookUse = iterator_to_array($this->account);
                // unset($this->FacebookUse["_id"]);

                $this->FacebookUse = json_decode(json_encode($this->account), true);
                unset($this->FacebookUse['_id']);
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

    // Hiển thị trang thông tin cá nhân
    public function showProfile(Request $request, $uid)
    {
        // Khởi tạo mảng $data với key 'account' để tránh lỗi "Undefined array key 'account'"
        $data = [
            'account' => $this->account,
        ];

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);

                if ($CheckConnect->ConnectAccount) {

                    $Main = new MainControler($Accountuse);
                    $profile = new ProfileManager($Accountuse);

                    $_account = $profile->MyProfile();

                    $data['info'] = $_account;
                    $data['Suggestions'] = $Main->LoadSuggestions();
                    $data['FriendListAccept'] =  $Main->LoadRequestPending();
                    // Không dùng Weather, chỉ lấy FaceBook Account đã kết nối
                }
            } else {
                $thongTin['status'] = false;
                $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                return response()->json(['response' => $thongTin], 200);
            }
        }
        return view('Android::Profile.Profile', [
            'data' => $data,
            'uid' => $this->uid,
        ]);
    }

    //hàm này thực hiện việc lấy danh sách bạn bè của tài khoản
    public function getFriend(Request $request, $uid)
    {
        $friends = $this->account->friends ?? [];

        if (empty($friends)) {
            if ($this->ConnectData) {
                $Accountuse = new FacebookAccount($this->FacebookUse);
                if ($Accountuse->Connect) {
                    $CheckConnect = new CheckConnect($Accountuse);
                    if ($CheckConnect->ConnectAccount) {
                        $_profile = new ProfileManager($Accountuse);
                        $_profile->LoadFriendsList();

                        $resultFriends =    $_profile->LoadFriendsList();

                        if (!empty($resultFriends)) {
                            foreach ($resultFriends as $friend) {
                                $friend->friends_of = $this->uid;
                                $collection = app('mongo')->FacebookData->Friends;
                                $existingGroup = $collection->findOne([
                                    'friends_of' => $this->uid,
                                    'uid' => $friend->uid
                                ]);
                                if ($existingGroup) {
                                    $collection->updateOne(
                                        ['_id' => $existingGroup->_id],
                                        ['$set' => $friend]
                                    );
                                } else {
                                    $collection->insertOne($friend);
                                }
                            }
                            // Lấy lại bài đăng sau khi cập nhật
                            $this->account->friends = $this->accountRepository->getMultipleFriendsData([$this->uid]);
                        }
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

        // Thiết lập phân trang
        $perPage = 100;
        $currentPage = (int) $request->input('page', 1);
        $total = is_array($friends) ? count($friends) : 0;
        $offset = ($currentPage - 1) * $perPage;

        if (is_array($friends)) {
            $currentFriends = array_slice($friends, $offset, $perPage);
        } else {
            $currentFriends = [];
        }

        // nếu có trường hợp $currentFriends chứa 1 phần tử duy nhất kiểu mảng con thì flatten
        if (count($currentFriends) === 1 && is_array($currentFriends[0])) {
            $currentFriends = $currentFriends[0];
        }

        // Chuyển từng friend sang dạng mảng
        $currentFriends = array_map(function ($friend) {
            if (is_object($friend) && method_exists($friend, 'getArrayCopy')) {
                return $friend->getArrayCopy();
            } elseif (is_object($friend)) {
                return (array) $friend;
            }
            return $friend;
        }, $currentFriends);

        $hasMore = ($offset + $perPage) < $total;

        if ($request->ajax()) {
            return response()->json([
                'friends' => $currentFriends,
                'hasMore' => $hasMore,
                'totalFriends' => $total
            ]);
        }

        return view('Android::Profile.Friends', [
            'data' => ['account' => $this->account],
            'friends' => $currentFriends,
            'totalFriends' => $total,
            'uid' => $uid,
            'currentPage' => $currentPage,
            'hasMore' => $hasMore,
        ]);
    }

    //hàm này là hàm để hiên thị các bài viết của tài khoản
    public function getTimeLine(Request $request, $uid)
    {
        $page = (int) $request->input('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $data = [
            'account' => $this->account,
        ];

        if (empty($this->account->post_data)) {
            if ($this->ConnectData) {
                $Accountuse = new FacebookAccount($this->FacebookUse);
                if ($Accountuse->Connect) {
                    $CheckConnect = new CheckConnect($Accountuse);
                    if ($CheckConnect->ConnectAccount) {
                        $profile = new ProfileManager($Accountuse);
                        $resultPost = $profile->LoadAllPostAccount();
                        if (!empty($resultPost)) {
                            foreach ($resultPost as $post) {
                                $post->post_of = $this->uid;
                                $collection = app('mongo')->FacebookData->Post;
                                $existingPost = $collection->findOne([
                                    'post_of' => $this->uid,
                                    'post_id' => $post->post_id
                                ]);
                                if ($existingPost) {
                                    $collection->updateOne(
                                        ['_id' => $existingPost->_id],
                                        ['$set' => $post]
                                    );
                                } else {
                                    $collection->insertOne($post);
                                }
                            }
                            // Lấy lại bài đăng sau khi cập nhật
                            $this->account->post_data = $this->accountRepository->getMultiplePostsData([$this->uid]);
                        }
                    }
                } else {
                    $thongTin['status'] = false;
                    $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                    return response()->json(['response' => $thongTin], 200);
                }
            }
        }

        $post = $this->account->post_data ?? [];

        $totalPosts    = count($post);
        $currentPosts  = array_slice($post, $offset, $perPage);
        $hasMore       = ($offset + $perPage) < $totalPosts;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'posts' => $currentPosts,
                'hasMore' => $hasMore,
            ]);
        }

        $latitude = $this->account->latitude;
        $longitude = $this->account->longitude;
        if (empty($latitude) && empty($longitude)) {
            $latitude = '11.036';
            $longitude = '106.731';
        }
        $Weather = new SamsungWeather();
        $data['Weather'] = $Weather->LoadWeather($latitude . ', ' . $longitude);

        return view('Android::Profile.Time_line', [
            'data' => [
                'account' => $this->account,
                'Weather' => $data['Weather'],
            ],

            'posts' => $currentPosts,
            'uid' => $this->uid,
            'hasMore' => $hasMore,
            'currentPage' => $page,
        ]);
    }

    //hàm này để cập nhật các bài viết mới nhất của tài khoản
    public function updatePost(Request $request, $uid)
    {
        $data = [
            'account' => $this->account,
        ];

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {
                    $OldPostId = null;
                    $AllPostAccount = $this->account->post_data = $this->accountRepository->getMultiplePostsData([$this->uid]);

                    if (count($AllPostAccount) > 0) {
                        if (!empty($AllPostAccount[0]->post_id)) {
                            $OldPostId = $AllPostAccount[0]->post_id;
                        }
                    }
                    //
                    $profile = new ProfileManager($Accountuse);
                    $resultPost = $profile->LoadAllPostAccount($OldPostId);

                    if (!empty($resultPost)) {

                        foreach ($resultPost as $post) {
                            $post->post_of = $this->uid;
                            $collection = app('mongo')->FacebookData->Post;
                            $existingPost = $collection->findOne([
                                'post_of' => $this->uid,
                                'post_id' => $post->post_id
                            ]);
                            if ($existingPost) {
                                $collection->updateOne(
                                    ['_id' => $existingPost->_id],
                                    ['$set' => $post]
                                );
                            } else {
                                $collection->insertOne($post);
                            }
                        }
                        // Lấy lại bài đăng sau khi cập nhật
                        $this->account->post_data = $this->accountRepository->getMultiplePostsData([$this->uid]);

                        return response()->json([
                            'response' => true,
                            'message' => 'Đã cập nhật bài đăng thành công.'
                        ], 200);
                    } else {
                        $thongTin['status'] = false;
                        $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                        return response()->json(['response' => $thongTin], 200);
                    }
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $this->Message
                ], 200);
            }
        }
    }

    //hàm này để lấy các fanpage của tài khoản
    public function getFanpage(Request $request, $uid)
    {
        $data = [
            'account' => $this->account,
        ];

        return view('Android::Fanpage.fanpage', [
            'data' => $data,
            'uid' => $this->uid,
        ]);
    }

    //hàm này để hiển thị các nhóm của tài khoản
    public function showGroup(Request $request, $uid)
    {
        $data = [
            'account' => $this->account,
        ];

        if (empty($this->account->groups)) {

            if ($this->ConnectData) {
                $Accountuse = new FacebookAccount($this->FacebookUse);
                if ($Accountuse->Connect) {
                    $CheckConnect = new CheckConnect($Accountuse);
                    if ($CheckConnect->ConnectAccount) {
                        $_Group = new GroupsManager($Accountuse);
                        $resultGroup =  $_Group->LoadGroupsUserJoin();

                        if (!empty($resultGroup)) {
                            foreach ($resultGroup as $group) {
                                $group->groups_of = $this->uid;
                                $collection = app('mongo')->FacebookData->Groups;
                                $existingGroup = $collection->findOne([
                                    'groups_of' => $this->uid,
                                    'uid' => $group->uid
                                ]);
                                if ($existingGroup) {
                                    $collection->updateOne(
                                        ['_id' => $existingGroup->_id],
                                        ['$set' => $group]
                                    );
                                } else {
                                    $collection->insertOne($group);
                                }
                            }
                            // Lấy lại bài đăng sau khi cập nhật
                            $this->account->groups = $this->accountRepository->getMultipleGroupsData([$this->uid]);
                        }
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
        return view('Android::Group.group', [
            'data' => $data,
            'uid' => $this->uid,
        ]);
    }

    //hàm này để cập nhật lại các nhóm mới nhất của tài khoản
    public function updateGroups(Request $request, $uid)
    {
        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {
                    $_Group = new GroupsManager($Accountuse);

                    $resultGroup =  $_Group->LoadGroupsUserJoin();

                    if (!empty($resultGroup)) {
                        foreach ($resultGroup as $group) {
                            $group->groups_of = $this->uid;
                            $collection = app('mongo')->FacebookData->Groups;
                            $existingGroup = $collection->findOne([
                                'groups_of' => $this->uid,
                                'uid' => $group->uid
                            ]);
                            if ($existingGroup) {
                                $collection->updateOne(
                                    ['_id' => $existingGroup->_id],
                                    ['$set' => $group]
                                );
                            } else {
                                $collection->insertOne($group);
                            }
                        }
                        // Lấy lại bài đăng sau khi cập nhật
                        $this->account->groups = $this->accountRepository->getMultipleGroupsData([$this->uid]);

                        return redirect()->back()->with('success', 'Đã cập nhật nhóm thành công');
                    }
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

    //hàm này để hiên thị trang cài đặt thông tin cá nhân
    public function settingProfile(Request $request, $uid)
    {
        $data = [
            'account' => $this->account,
        ];

        return view('Android::Profile.Settings', [
            'data' => $data,
            'uid' => $this->uid,
        ]);
    }

    //hàm này để thực hiện việc thay đổi mật khẩu của tài khoản
    public function changePassword(Request $request, $uid)
    {
        $RequestBody = (object)$request->all();
        if (empty($RequestBody->new_password)) {
            die('Mật khẩu mới không được rỗng !');
        }
        if (empty($RequestBody->confirm_password)) {
            die('Xác nhận mật khẩu mới không được rỗng !');
        }
        if (md5(trim($RequestBody->new_password)) != md5(trim($RequestBody->confirm_password))) {
            die('Xác nhận mật khẩu không chính xác xem lại');
        }
        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {
                    $profile = new AccountManager($Accountuse);
                    $profile->ChangeNewPassword(trim($RequestBody->new_password));
                    die('rrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr');
                } else {
                    return response()->json(['response' => 'Không thể kết nối tới tài khoản'], 200);
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

    //hàm này để hiển thị trang video của tài khoản
    public function showVideo(Request $request, $uid)
    {
        $data = [
            'account' => $this->account,
        ];

        $end_cursor = $request->input('end_cursor', null);

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {
                    $_profile = new ProfileManager($Accountuse);
                    if ($end_cursor) {
                        $moreData  = $_profile->ViewMoreVideoProfile($uid, $end_cursor);

                        return response()->json($moreData, 200);
                    } else {
                        $ResultVideoData = $_profile->GetAllVideoProfile($uid);

                        // dd($ResultVideoData);
                        return view('Android::Profile.Video', [
                            'data' => $data,
                            'uid' => $this->uid,
                            'VideoData' => $ResultVideoData
                        ]);
                    }
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

    public function ReloadLoginAccount()
    {


        $Accountuse = new FacebookAccount($this->FacebookUse);


        if ($Accountuse->Connect) {
            $Connect = new AccountManager($Accountuse);
            $resultLogin = $Connect->LoginAccount();
            //cần phải cập nhật bất kể login thành công hay thất bại để lưu phiên làm việc
            // Cập nhật dữ liệu MongoDB khi đăng nhập thành công
            $this->account->android_device->access_token = $resultLogin->account_info->access_token ?? '';
            $this->account->android_device->device_id = $resultLogin->account_info->device_id ?? '';
            $this->account->android_device->adid = $resultLogin->account_info->adid ?? '';
            $this->account->android_device->family_device_id = $resultLogin->account_info->family_device_id ?? '';
            $this->account->android_device->advertiser_id = $resultLogin->account_info->advertiser_id ?? '';
            $this->account->android_device->UserAgent = $resultLogin->account_info->UserAgent ?? '';
            $this->account->android_device->UserAgentApp = $resultLogin->account_info->UserAgentApp ?? '';
            $this->account->android_device->machine_id = $resultLogin->account_info->machine_id ?? '';
            $this->account->android_device->sim_serials = $resultLogin->account_info->sim_serials ?? '';
            $this->account->android_device->jazoest = $resultLogin->account_info->jazoest ?? '';
            //
            $updatData = array('android_device' => iterator_to_array($this->account->android_device));
            $this->accountRepository->updateByUid($this->uid, $updatData);
            //
            if ($resultLogin->status) {

                return [
                    'status' => 'success',
                    'message' => 'Khởi tạo phiên làm việc Android thành công'
                ];
            } else {
                return  [
                    'status' => 'error',
                    'message' => 'Không thể khởi tạo phiên làm việc Android'
                ];
            }
        } else {
            $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
            return $thongTin;
        }
    }

    public function SetAvatar($path, $Account_uid)
    {
       
        // mac dinh se là upload cho profile cá nhan nếu check ra thấy là page thì se đổi sang page để upload
        $AccountType = 'profile';
     
        if (!empty($this->account)) {
            foreach ($this->account->MultiAccount as $_account) {
                if (trim($_account->profile->id) == trim($Account_uid)) {
                    $cookies = '';
                    $AccountType = 'page';
                    break;
                }
            }
        }
      
        if (!empty($this->FacebookUse)) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
        
            if ($Accountuse->Connect) {
                $profile = new ProfileManager($Accountuse);
                $resultIdPhoto = $profile->UploadPhoto($path);
                if (!is_numeric($resultIdPhoto->message)) {
                    $thongTin['status'] = false;
                    $thongTin['message'] = 'Upload hình ảnh thất bại!';
                    return $thongTin;
                }
                $resultSetAvatar = $profile->SetAvatarAccount($resultIdPhoto->message);
                if ($resultSetAvatar) {
                    $profile = new ProfileManager($Accountuse);
                    $ProfileInfo = $profile->MyProfile();
                    if (!empty($ProfileInfo->avatar)) {
                        $updatData['avatar']  = $ProfileInfo->avatar;
                        $collection = app('mongo')->FacebookData->PageAccount;
                        $result = $collection->updateMany(
                            ['page_id' => $this->account->uid],
                            ['$set' => $updatData]
                        );
                    }
                    $thongTin['status'] = true;
                    $thongTin['message'] = 'Đặt Avatar thành công!';
                    return $thongTin;
                } else {
                    $thongTin['status'] = false;
                    $thongTin['message'] = 'Đặt Avatar thất bại!';
                    return $thongTin;
                }
            } else {
                $thongTin['status'] = false;
                $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                return $thongTin;
            }
        } else {
            $thongTin['status'] = false;
            $thongTin['message'] = 'Có lỗi ngoài ý muốn khi xử lý thông tin tài khoản';
            return $thongTin;
        }
    }

    
	public function UploadReel($videoPath, $Account_uid, $content = 'ffffffffffffffffffffff')
	{
		$thongTin = array('status' => false, 'message' => 'Không xác định');
		$AccountType = 'profile';
		if (!empty($this->account)) {
			foreach ($this->account->MultiAccount as $_account) {
				if (trim($_account->profile->id) == trim($Account_uid)) {
					$cookies = '';
					$AccountType = 'page';
					// Xây dựng chuỗi cookie
					 foreach ($_account->session_info->session_cookies as $cookieJson) {
					     $cookie = json_decode($cookieJson);
					     $cookieString = "{$cookie->name}={$cookie->value}; path={$cookie->path}; domain={$cookie->domain}; ";
					     $cookieString .= ($cookie->secure ? 'Secure; ' : '') . ($cookie->httponly ? 'HttpOnly; ' : '') . ($cookie->samesite ? "SameSite={$cookie->samesite}; " : '');
					     $cookies .= $cookieString . "\n";
					 }
					 $this->account->android_device->cookies = $cookies;
					 $this->account->android_device->access_token = $_account->session_info->access_token;
					 $this->FacebookUse = iterator_to_array($this->account);
				}
			}
		}
		
		if (!empty($this->FacebookUse)) {
			$Accountuse = new FacebookAccount($this->FacebookUse);
			if ($Accountuse->Connect) {
				$profileManager = new ProfileManager($Accountuse);
				$postReelResult = $profileManager->PostReelVideo($videoPath, $content);
				if ($postReelResult->status) {
					$thongTin['status'] = true;
					$thongTin['message'] = json_decode(json_encode($postReelResult));
					return $thongTin;
				} else {
					$thongTin['status'] = false;
					$thongTin['message'] = $postReelResult->message;
					return $thongTin;
				}
				
			} else {
				$thongTin['status'] = false;
				$thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
				return $thongTin;
			}
		}else {
			$thongTin['status'] = false;
			$thongTin['message'] = 'Có lỗi ngoài ý muốn khi xử lý thông tin tài khoản';
			return $thongTin;
		}
	}
}
