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
use HoangquyIT\ModelFacebook\Android\Fanpage\PageManager;

/// ở file nay thực hiên các tác vụ tương tác với facebook của một tài khoản facebook( như đăng bài, thả cam xúc, xóa bạn bè, thêm bạn bè, lấy comment, lấy thông tin cá nhân, lấy thông tin trang cá nhân, lấy thông tin nhóm, lấy thông tin fanpage, lấy thông tin thời tiết, )
class AndroidController extends Controller
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

    //hàm này là hàm để đăng status lên trang cá nhân
    public function postStatus(Request $request, $uid)
    {
        // Validate the incoming request data
        $message = $request->input('status');
        $longitude = $request->input('longitude') ?? null;
        $latitude = $request->input('latitude') ?? null;

        $data = [
            'account' => $this->account,
        ];

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {
                    $profile = new ProfileManager($Accountuse);

                    $profile->PostStatus($uid, $message, $longitude, $latitude);

                    return redirect()->back()->with('success', 'Đã đăng trạng thái thành công');
                }

                return redirect()->back()->with('error', 'Không thể đăng trạng thái. Vui lòng thử lại.');
            } else {
                $thongTin['status'] = false;
                $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                return response()->json(['response' => $thongTin], 200);
            }
        }


        return response()->json(['response' => 'Lỗi không xác định'], 200);
    }

    //hàm này là hàm để lấy comment của một bài viết(ở timeline và Home main)
    public function getComment(Request $request, $id)
    {
        $body = (object)$request->all();

        if (empty($body->post_id)) {
            return response()->json(['response' => 'Lỗi không có post id thì đọc comment cái quờ quờ !'], 200);
        }

        if ($this->ConnectData) {
            $latitude = $this->account->latitude;
            $longitude = $this->account->longitude;
            if (empty($latitude) && empty($longitude)) {
                $latitude = '11.036';
                $longitude = '106.731';
            }
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse); // kết nối facebook
                if ($CheckConnect->ConnectAccount) {
                    $Main = new MainControler($Accountuse); // ket noi class dung de lay thong tin ban tin facebook
                    $resultComment = $Main->LoadCommentOnPost($body->post_id);


                    return response()->json(['response' => $resultComment], 200);
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


    //hàm này là hàm để thả cam xúc cho một bài viết
    public function addReaction(Request $request, $uid)
    {
        $postInput = $request->input('post_data');
        $camxuc_bot = $request->input('reaction_id');

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {
                    $_Main = new MainControler($Accountuse);

                    if ($_Main->reactionPost(json_decode(json_encode($postInput)), $camxuc_bot)) {
                        return response()->json(['response' => 'Đã thả cam xúc thành công'], 200);
                    } else {

                        return response()->json(['response' => 'Lỗi không xác định'], 200);
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

    //hàm này là hàm để thêm bạn bè
    public function addFriend(Request $request)
    {

        $body = (object)$request->all();
        if (empty($body->uid_invitation)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thiếu dữ liệu cần thiết: uid_invitation'
            ], 200);
        }
        if ($this->ConnectData) {

            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {

                    $Main = new MainControler($Accountuse);

                    $result = $Main->SendRequestAddFriendsByID($body->uid_invitation);

                    return response()->json(['response' => $result], 200);
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

    //hàm này là hàm để xóa bạn bè trong danh sách gợi ý
    public function removeFriend(Request $request)
    {
        $body = (object)$request->all();
        if (empty($body->uid_invitation)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thiếu dữ liệu cần thiết: uid_invitation'
            ], 200);
        }
        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {

                    $Main = new MainControler($Accountuse);
                    $result = $Main->DeleteSuggestions($body->uid_invitation);

                    return response()->json(['response' => $result], 200);
                } else {
                    return response()->json(['response' => 'Đã xóa bạn thất bại'], 200);
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


    public function joinGroup(Request $request, $uid)
    {
        $body = (object)$request->all();

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {

                    $_Group = new GroupsManager($Accountuse);

                    $result = $_Group->JoinGroupUid($body->uidGroup);

                    return response()->json(['response' => $result], 200);
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

    public function acceptFriend(Request $request, $uid)
    {


        $body = (object)$request->all();



        if (empty($body->uid_accept)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thiếu dữ liệu cần thiết: uid_accept !! '
            ], 200);
        }
        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {

                    $Main = new MainControler($Accountuse);


                    $result = $Main->FriendRequestAcceptCoreMutation($body->uid_accept);

                    return response()->json(['response' => $result], 200);
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

    public function likeFollow(Request $request, $uid)
    {
        $body = (object)$request->all();

 

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);

                $_Page = new PageManager($Accountuse);
        

                // Kiểm tra biến action trong request (ví dụ: 'like' hoặc 'follow')
                if (isset($body->action) && $body->action === 'like') {

                    $_Page->LikePageID($body->page_id);

                } elseif (isset($body->action) && $body->action === 'follow') {
                   
                    $_Page->SubscribePageID($body->page_id);
                    
                } else {
                    return response()->json(['response' => 'Hành động không xác định'], 200);
                }

                return response()->json(['response' => 'Thành công'], 200);

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
