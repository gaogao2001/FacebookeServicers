<?php

namespace App\InterfaceModules\DeviceEmulator\Android\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface;
use Illuminate\Http\Request;
use HoangquyIT\FacebookAccount;
use HoangquyIT\ModelFacebook\Android\CheckConnect;
use App\Modules\Fanpage\Repositories\FanpageManagerRepositoryInterface;
use HoangquyIT\NetworkControler\NetworkControler;
use HoangquyIT\ModelFacebook\Android\Marketplace\MainMarketplace;

//ở file này chưa toàn bộ controller tương tác với marketplace của facebook
class MarketPlaceController extends Controller
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

    //hàm này thực hiện việc hiển thị trang marketplace của facebook
    public function showMarket(Request $request, $uid)
    {
        $data = [
            'account' => $this->account,
        ];
        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {

                    $_MainMarket = new MainMarketplace($Accountuse);
                    // Tải dữ liệu thị trường
                    $ResultAllMarketHome = $_MainMarket->LoadMarketplacePlainHome();
                    $categories =  $_MainMarket->LoadCategoriesMarketplace();

                    return view('Android::Market_Place.market_place', [
                        'data' => $data,
                        'uid' => $this->uid,
                        'products' => $ResultAllMarketHome,
                        'categories' => $categories
                    ]);
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

        // return view('user.pages.Market_Place.market_place', [
        //     'data' => $data,
        //     'uid' => $this->uid,

        // ]);
    }

    //hàm này thực hiện việc tìm kiếm sản phẩm trên marketplace
    public function searchProduct(Request $request)
    {
        $KeySearch = $request->input('description');
        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {

                    $_MainMarket = new MainMarketplace($Accountuse);


                    $ResultFin = $_MainMarket->FindLocation($KeySearch);

                    return response()->json(['response' => $ResultFin], 200);
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

    //hàm này thực hiện việc  chọn vị trí trên bản đồ của marketplace
    public function setLocation(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {

                    $_MainMarket = new MainMarketplace($Accountuse);
                    $_MainMarket->SetLocationMarketplace($latitude, $longitude);

                    return response()->json(['response' => 'Đã cập nhật vị trí thành công'], 200);
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


    //hàm này thực hiện việc đăng bài trên marketplace
    public function postMarket(Request $request)
    {
        $Title = $request->input('title');
        $Content = $request->input('content');
        $price = $request->input('price');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $postGroup = filter_var($request->input('post_group'), FILTER_VALIDATE_BOOLEAN);
        $ListImg = $request->file('images');

        if ($request->hasFile('images')) {
            foreach ($ListImg as $image) {
                \Log::info('Uploaded image: ' . $image->getClientOriginalName());
                // Xử lý lưu file nếu cần
            }
        } else {
            \Log::info('No images uploaded.');
        }

        // Debug để kiểm tra dữ liệu
        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {
                    $_MainMarket = new MainMarketplace($Accountuse);

                    $_MainMarket->PostNewMarketplace($Title, $Content, $ListImg, $price, $latitude, $longitude, $postGroup);

                    return response()->json(['response' => 'Đăng bài thành công !'], 200);
                } else {
                    return response()->json(['response' => 'Đăng bài thất bại !'], 200);
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

    //hàm này thực hiện việc hiển thị các bài đăng cá nhân trên marketplace
    public function showMyPost(Request $request)
    {
        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {

                    $_MainMarket = new MainMarketplace($Accountuse);

                    $posts = $_MainMarket->LoadAllMyPostMarketplace();

                    return view('Android::Market_Place.my_post', [
                        'data' => [
                            'account' => $this->account,
                        ],
                        'uid' => $this->uid,
                        'posts' => $posts
                    ]);
                } else {
                    return response()->json(['response' => 'Có lỗi kết nối !!'], 200);
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

        return view('Android::Market_Place.my_post', [
            'data' => [
                'account' => $this->account,
            ],
            'uid' => $this->uid,
        ]);
    }

    //hàm này thực hiện việc xóa bài đăng cá nhân trên marketplace
    public function deletePost(Request $request)
    {
        $item_id = $request->input('id');

        if ($this->ConnectData) {
            $Accountuse = new FacebookAccount($this->FacebookUse);
            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);
                if ($CheckConnect->ConnectAccount) {

                    $_MainMarket = new MainMarketplace($Accountuse);

                    $_MainMarket->DeleteMyPostMarketplace($item_id);

                    return response()->json(['response' => 'Xóa bài thành công !'], 200);
                } else {
                    return response()->json(['response' => 'Có lỗi kết nối !!'], 200);
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
