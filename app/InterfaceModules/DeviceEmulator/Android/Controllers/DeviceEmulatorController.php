<?php

namespace App\InterfaceModules\DeviceEmulator\Android\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface;
use App\Modules\Fanpage\Repositories\FanpageManagerRepositoryInterface;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\Pages\HomePageController;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\Pages\PageInfoController;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\Profile\AccountInfoController;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\Profile\HomeAccountController;
use HoangquyIT\FacebookAccount;
use HoangquyIT\ModelFacebook\Android\CheckConnect;
use HoangquyIT\ModelFacebook\Android\Profile\ProfileManager;

class DeviceEmulatorController extends Controller
{
    protected $accountRepository;
    protected $fanpageManagerRepository;

    public function __construct(AccountRepositoryInterface $accountRepository, FanpageManagerRepositoryInterface $fanpageManagerRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->fanpageManagerRepository = $fanpageManagerRepository;
    }

    public function validateInputs(Request $request, array $rules)
    {
        $request->validate($rules);

        $url = $request->input('url');
        $uid = $request->input('uid');
        $content = $request->input('content', ''); // Default to an empty string if not provided

        if (empty($url) || empty($uid)) {
            return response()->json([
                'status' => 'error',
                'message' => empty($url) ? 'Thiếu dữ liệu cần thiết: url' : 'Thiếu dữ liệu cần thiết: uid'
            ], 400);
        }

        return compact('url', 'uid', 'content');
    }

    public function validateFilePath($url)
    {

        $parsedUrl = parse_url($url, PHP_URL_PATH);
        $pathParts = explode('/FileData', $parsedUrl);
        if (count($pathParts) < 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'URL không đúng định dạng hoặc không chứa FileData'
            ], 200);
        }

        $fileDataPath = config('file-path.base_path') . $pathParts[1];

        if (!file_exists($fileDataPath) || !is_file($fileDataPath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File không tồn tại hoặc đường dẫn không hợp lệ',
                'fileDataPath' => $fileDataPath
            ], 204);
        }

        return $fileDataPath;
    }

    public function ExportVideo(Request $request)
    {
        $inputs = $this->validateInputs($request, [
            'url' => 'required|url',
            'uid' => 'required'
        ]);
        if ($inputs instanceof \Illuminate\Http\JsonResponse) {
            return $inputs;
        }

        $fileDataPath = $this->validateFilePath($inputs['url']);
        if ($fileDataPath instanceof \Illuminate\Http\JsonResponse) {
            return $fileDataPath;
        }
        $outputDirImg = config('file-path.base_path') . '/Images/' . trim($inputs['uid']);
        $fileName = substr(md5($inputs['url']), 0, 10);

        if (!extract_frames(null, $outputDirImg, $fileName, $fileDataPath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi trong quá trình Export'
            ], 200);
        }

        rename_files($outputDirImg);

        return response()->json([
            'status' => 'success',
            'message' => 'Export Video ra Image hoàn tất'
        ], 200);
    }

    public function postVideo(Request $request)
    {
        return $this->processRequest($request, 'postVideo');
    }

    public function postReels(Request $request)
    {
       
        return $this->processRequest($request, 'postReels');
    }

    public function liveVideo(Request $request)
    {
        $uid = $request->input('uid');
        $videoPath = $request->input('url');
        $content = $request->input('content');

       

        $homeAccount = new HomeAccountController(
            $request,
            $this->accountRepository,
            $this->fanpageManagerRepository
        );

        $homeAccount->vailidateUids($uid);

        if ($homeAccount->ConnectData) {

            $Accountuse = new FacebookAccount($homeAccount->getFacebookUse());

            if ($Accountuse->Connect) {
                $CheckConnect = new CheckConnect($Accountuse);

                $_profile = new ProfileManager($Accountuse);

                $resultStartLive = $_profile->LiveStreaming($videoPath, $content);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Bắt đầu Live Video thành công',
                    'data' => $resultStartLive
                ], 200);

               
            } else {
                $thongTin['status'] = false;
                $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                return response()->json(['response' => $thongTin], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $homeAccount->Message
            ], 200);
        }
    }

    public function stopLive(Request $request)
    {
        $videoId = $request->input('video_id');
        $uid = $request->input('uid');
        // Giả sử bạn đã khởi tạo thông tin FacebookAccount từ uid rồi
        // Và có thể lấy được đối tượng Accountuse
        $homeAccount = new HomeAccountController(
            $request,
            $this->accountRepository,
            $this->fanpageManagerRepository
        );

        $homeAccount->vailidateUids($uid);
       
        if ($homeAccount->ConnectData) {
            $Accountuse = new FacebookAccount($homeAccount->getFacebookUse());
            if ($Accountuse->Connect) {
                $_profile = new ProfileManager($Accountuse);

                $resultStopLive = $_profile->StopLiveStreaming($videoId);
            
             
                if ($resultStopLive) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Live video đã dừng thành công'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Không thể dừng live video'
                    ], 200);
                }
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'Xác thực uid không thành công'
        ], 200);
    }

    private function processRequest(Request $request, $action)
    {
        $inputs = $this->validateInputs($request, [
            'url' => 'required',
            'uid' => 'required',
            'content' => 'nullable' // Make content optional
        ]);


   
        if ($inputs instanceof \Illuminate\Http\JsonResponse) {
            return $inputs;
        }

        $fileDataPath = $this->validateFilePath($inputs['url']);
        if ($fileDataPath instanceof \Illuminate\Http\JsonResponse) {
            return $fileDataPath;
        }
        $account = $this->accountRepository->findByUid(trim($inputs['uid']));
        if (empty($account)) {
            $thongTin['message'] = 'Không tìm thấy dữ liệu phù hợp.';
            return response()->json(['response' => $thongTin], 200);
        }
        $relativePath = parse_url($inputs['url'], PHP_URL_PATH);
    
        $path = $relativePath;

        if (!file_exists($path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File không tồn tại trên hệ thống, vui lòng kiểm tra lại'
            ], 200);
        }
        // kiểm tra xem loại tài khoản chạy đang là page hay profile
        $AccountType = 'profile';
        if (!empty($account)) {
            foreach ($account->MultiAccount as $_account) {
                if (trim($_account->profile->id) == trim($inputs['uid'])) {
                    $cookies = '';
                    $AccountType = 'page';
                    // Xây dựng chuỗi cookie
                    foreach ($_account->session_info->session_cookies as $cookieJson) {
                        $cookie = json_decode($cookieJson);
                        $cookieString = "{$cookie->name}={$cookie->value}; path={$cookie->path}; domain={$cookie->domain}; ";
                        $cookieString .= ($cookie->secure ? 'Secure; ' : '') . ($cookie->httponly ? 'HttpOnly; ' : '') . ($cookie->samesite ? "SameSite={$cookie->samesite}; " : '');
                        $cookies .= $cookieString . "\n";
                    }
                    $account->uid = trim($inputs['uid']);
                    $account->android_device->cookies = $cookies;
                    $account->android_device->access_token = $_account->session_info->access_token;
                    // nếu vào đây xác định nó là dạng tài khoản page cần cập nhật thêm cấu hình auto từ PageConfig vào để dùng
                    $resultFindPage = $this->fanpageManagerRepository->findOneFanpage(['page_id' => $_account->profile->id]);
                    $account->config_auto = $resultFindPage->config_auto;
                }
            }
        }
        //xác dinh phien lam viec de điều hướng
        if ($account->config_auto->session == 'android') {
            // dành cho trường hop account đang cấu hình là phien android APK
            if ($AccountType == 'profile') {
                //profile
                $AccountInfo = new AccountInfoController($request, $this->accountRepository , $this->fanpageManagerRepository);
            
                $result = $AccountInfo->UploadReel($fileDataPath, trim($inputs['uid']), trim($inputs['content']));
                return response()->json($result, 200);
            } else {
                //page
                $PageInfo = new PageInfoController($request, $this->fanpageManagerRepository);
                $ResultUpload = $PageInfo->UploadReel($fileDataPath, trim($inputs['uid']), trim($inputs['content']));
                var_dump($ResultUpload);
                die('rrrrrrrrrrrrrr');
            }
            exit;
        } else if ($account->config_auto->session == 'mobile') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else if ($account->config_auto->session == 'windows') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else {

            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản này chưa thiết lập phiên làm việc'
            ], 200);
        }
    }

    public function uploadAvatar(Request $request)
    {
        // lay ham này lam chuẩn thuật toán cho tiền xử lý account info để làm việc
        $inputs = $this->validateInputs($request, [
            'url' => 'required|url',
            'uid' => 'required'
        ]);

        $account = $this->accountRepository->findByUid(trim($inputs['uid']));

        if (empty($account)) {
            $thongTin['message'] = 'Không tìm thấy dữ liệu phù hợp.';
            return response()->json(['response' => $thongTin], 200);
        }
        $relativePath = parse_url($inputs['url'], PHP_URL_PATH);
        $relativePath = str_replace('/FileData/', '', $relativePath);
        $path = config('file-path.base_path') . $relativePath;
       
        if (!file_exists($path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File không tồn tại trên hệ thống, vui lòng kiểm tra lại'
            ], 200);
        }
        // kiểm tra xem loại tài khoản chạy đang là page hay profile
        $AccountType = 'profile';
       
        if (!empty($account)) {
            if (!empty($account->MultiAccount)) {
                foreach ($account->MultiAccount as $_account) {
                    if (trim($_account->profile->id) == trim($inputs['uid'])) {
                        $cookies = '';
                        $AccountType = 'page';
                        // Xây dựng chuỗi cookie
                        foreach ($_account->session_info->session_cookies as $cookieJson) {
                            $cookie = json_decode($cookieJson);
                            $cookieString = "{$cookie->name}={$cookie->value}; path={$cookie->path}; domain={$cookie->domain}; ";
                            $cookieString .= ($cookie->secure ? 'Secure; ' : '') . ($cookie->httponly ? 'HttpOnly; ' : '') . ($cookie->samesite ? "SameSite={$cookie->samesite}; " : '');
                            $cookies .= $cookieString . "\n";
                        }
                        $account->uid = trim($inputs['uid']);
                        $account->android_device->cookies = $cookies;
                        $account->android_device->access_token = $_account->session_info->access_token;
                        // nếu vào đây xác định nó là dạng tài khoản page cần cập nhật thêm cấu hình auto từ PageConfig vào để dùng
                        $resultFindPage = $this->fanpageManagerRepository->findOneFanpage(['page_id' => $_account->profile->id]);
                        $account->config_auto = $resultFindPage->config_auto;
                    }
                }
            }
        }

        if ($account->config_auto->session == 'android') {
            // dành cho trường hop account đang cấu hình là phien android APK
           
            if ($AccountType == 'profile') {
                //profile
                $AccountInfo = new AccountInfoController($request, $this->accountRepository, $this->fanpageManagerRepository);
              
                $ResultUpload = $AccountInfo->SetAvatar($path, trim($inputs['uid']));

            } else {
                //page
                $PageInfo = new PageInfoController($account, $this->fanpageManagerRepository);
                $ResultUpload = $PageInfo->SetAvatar($path, trim($inputs['uid']));
            }
            exit;
        } else if ($account->config_auto->session == 'mobile') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else if ($account->config_auto->session == 'windows') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else {

            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản này chưa thiết lập phiên làm việc'
            ], 200);
        }
    }


    public function RenewSession(Request $request)
    {
        $body = $request->all();
        if (empty($body['uid'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu uid là không thể thiếu'
            ], 200);
        }

        $uid = $body['uid'];
        $account = $this->accountRepository->findByUid($body['uid']);
        if (!$account) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy thông tin tài khoản Facebook'
            ], 200);
        }
        if ($account->config_auto->session == 'android') {

           
            // dành cho trường hop account đang cấu hình là phien android APK
            $AccountInfo = new AccountInfoController($request,  $this->accountRepository, $this->fanpageManagerRepository );
            
            $AccountInfo->ReloadLoginAccount();
            
        } else if ($account->config_auto->session == 'mobile') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else if ($account->config_auto->session == 'windows') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản này chưa thiết lập phiên làm việc'
            ], 200);
        }
    }

    public function CheckPageNameAvailability(Request $request)
    {
        $inputs = $request->all();
        if (empty($inputs['pageName'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu pageName là không thể thiếu'
            ], 200);
        }
        if (empty($inputs['uid'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu uid là không thể thiếu'
            ], 200);
        }
        $account = $this->accountRepository->findByUid(trim($inputs['uid']));
        if (empty($account)) {
            $thongTin['message'] = 'Không tìm thấy dữ liệu phù hợp.';
            return response()->json(['response' => $thongTin], 200);
        }
        // kiểm tra xem loại tài khoản chạy đang là page hay profile
        $AccountType = 'profile';
        if (!empty($account)) {
            foreach ($account->MultiAccount as $_account) {
                if (trim($_account->profile->id) == trim($inputs['uid'])) {
                    $cookies = '';
                    $AccountType = 'page';
                    // Xây dựng chuỗi cookie
                    foreach ($_account->session_info->session_cookies as $cookieJson) {
                        $cookie = json_decode($cookieJson);
                        $cookieString = "{$cookie->name}={$cookie->value}; path={$cookie->path}; domain={$cookie->domain}; ";
                        $cookieString .= ($cookie->secure ? 'Secure; ' : '') . ($cookie->httponly ? 'HttpOnly; ' : '') . ($cookie->samesite ? "SameSite={$cookie->samesite}; " : '');
                        $cookies .= $cookieString . "\n";
                    }
                    $account->uid = trim($inputs['uid']);
                    $account->android_device->cookies = $cookies;
                    $account->android_device->access_token = $_account->session_info->access_token;
                    // nếu vào đây xác định nó là dạng tài khoản page cần cập nhật thêm cấu hình auto từ PageConfig vào để dùng
                    $resultFindPage = $this->fanpageManagerRepository->findOneFanpage(['page_id' => $_account->profile->id]);
                    $account->config_auto = $resultFindPage->config_auto;
                }
            }
        }
        if ($AccountType == 'page') {
            $thongTin['message'] = 'Tính năng không cho phép dùng tài khoản Page bắt buộc phải là Profile.';
            return response()->json(['response' => $thongTin], 200);
        }
        if ($account->config_auto->session == 'android') {
            // dành cho trường hop account đang cấu hình là phien android APK
            $homepage = new HomePageController($account, $this->accountRepository, $this->fanpageManagerRepository);

            $resultFind = $homepage->CheckPageNameAvailability(trim($inputs['pageName']));
            return response()->json($resultFind, 200);
        } else if ($account->config_auto->session == 'mobile') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else if ($account->config_auto->session == 'windows') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else {

            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản này chưa thiết lập phiên làm việc'
            ], 200);
        }
    }

    public function CheckCategoryNameAvailability(Request $request)
    {
        $inputs = $request->all();
        if (empty($inputs['categoryName'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu categoryName là không thể thiếu'
            ], 200);
        }

        if (empty($inputs['uid'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu uid là không thể thiếu'
            ], 200);
        }
        $account = $this->accountRepository->findByUid(trim($inputs['uid']));
        if (empty($account)) {
            $thongTin['message'] = 'Không tìm thấy dữ liệu phù hợp.';
            return response()->json(['response' => $thongTin], 200);
        }
        // kiểm tra xem loại tài khoản chạy đang là page hay profile
        $AccountType = 'profile';
        if (!empty($account)) {
            foreach ($account->MultiAccount as $_account) {
                if (trim($_account->profile->id) == trim($inputs['uid'])) {
                    $cookies = '';
                    $AccountType = 'page';
                    // Xây dựng chuỗi cookie
                    foreach ($_account->session_info->session_cookies as $cookieJson) {
                        $cookie = json_decode($cookieJson);
                        $cookieString = "{$cookie->name}={$cookie->value}; path={$cookie->path}; domain={$cookie->domain}; ";
                        $cookieString .= ($cookie->secure ? 'Secure; ' : '') . ($cookie->httponly ? 'HttpOnly; ' : '') . ($cookie->samesite ? "SameSite={$cookie->samesite}; " : '');
                        $cookies .= $cookieString . "\n";
                    }
                    $account->uid = trim($inputs['uid']);
                    $account->android_device->cookies = $cookies;
                    $account->android_device->access_token = $_account->session_info->access_token;
                    // nếu vào đây xác định nó là dạng tài khoản page cần cập nhật thêm cấu hình auto từ PageConfig vào để dùng
                    $resultFindPage = $this->fanpageManagerRepository->findOneFanpage(['page_id' => $_account->profile->id]);
                    $account->config_auto = $resultFindPage->config_auto;
                }
            }
        }
        if ($AccountType == 'page') {
            $thongTin['message'] = 'Tính năng không cho phép dùng tài khoản Page bắt buộc phải là Profile.';
            return response()->json(['response' => $thongTin], 200);
        }
        if ($account->config_auto->session == 'android') {
            // dành cho trường hop account đang cấu hình là phien android APK
            $pageControler = new HomePageController($account, $this->accountRepository, $this->fanpageManagerRepository);
            $resultFind = $pageControler->CheckPageCategoryAvailability(trim($inputs['categoryName']));
            return response()->json($resultFind, 200);
        } else if ($account->config_auto->session == 'mobile') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else if ($account->config_auto->session == 'windows') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else {

            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản này chưa thiết lập phiên làm việc'
            ], 200);
        }
    }

    public function CreateNewFanpage(Request $request)
    {
        $inputs = $request->all();
        if (empty($inputs['category'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu category là không thể thiếu'
            ], 200);
        }
        $category = trim($inputs['category']);
        if (empty($inputs['pageName'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu pageName là không thể thiếu'
            ], 200);
        }
        $pageName = trim($inputs['pageName']);
        if (empty($inputs['username'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu username là không thể thiếu'
            ], 200);
        }
        $username = trim($inputs['username']);
        if (empty($inputs['uid'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu uid là không thể thiếu'
            ], 200);
        }
        $account = $this->accountRepository->findByUid(trim($inputs['uid']));
        if (empty($account)) {
            $thongTin['message'] = 'Không tìm thấy dữ liệu phù hợp.';
            return response()->json(['response' => $thongTin], 200);
        }
        // kiểm tra xem loại tài khoản chạy đang là page hay profile
        $AccountType = 'profile';
        if (!empty($account)) {
            foreach ($account->MultiAccount as $_account) {
                if (trim($_account->profile->id) == trim($inputs['uid'])) {
                    $cookies = '';
                    $AccountType = 'page';
                    // Xây dựng chuỗi cookie
                    foreach ($_account->session_info->session_cookies as $cookieJson) {
                        $cookie = json_decode($cookieJson);
                        $cookieString = "{$cookie->name}={$cookie->value}; path={$cookie->path}; domain={$cookie->domain}; ";
                        $cookieString .= ($cookie->secure ? 'Secure; ' : '') . ($cookie->httponly ? 'HttpOnly; ' : '') . ($cookie->samesite ? "SameSite={$cookie->samesite}; " : '');
                        $cookies .= $cookieString . "\n";
                    }
                    $account->uid = trim($inputs['uid']);
                    $account->android_device->cookies = $cookies;
                    $account->android_device->access_token = $_account->session_info->access_token;
                    // nếu vào đây xác định nó là dạng tài khoản page cần cập nhật thêm cấu hình auto từ PageConfig vào để dùng
                    $resultFindPage = $this->fanpageManagerRepository->findOneFanpage(['page_id' => $_account->profile->id]);
                    $account->config_auto = $resultFindPage->config_auto;
                }
            }
        }
        if ($AccountType == 'page') {
            $thongTin['message'] = 'Tính năng không cho phép dùng tài khoản Page bắt buộc phải là Profile.';
            return response()->json(['response' => $thongTin], 200);
        }
        if ($account->config_auto->session == 'android') {
            // dành cho trường hop account đang cấu hình là phien android APK
            //CreateNewPage($CreateName, $CreateUsername, $category_ids = [])
            $pageControler = new HomePageController($account, $this->accountRepository, $this->fanpageManagerRepository);
            $resultFind = $pageControler->CreateNewPage($pageName, $username, [$category]);
            if (!empty($resultFind)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tạo trang thành công !'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tạo trang không thành công !'
                ], 200);
            }
        } else if ($account->config_auto->session == 'mobile') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else if ($account->config_auto->session == 'windows') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phiên làm việc chưa được hỗ trợ'
            ], 200);
        } else {

            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản này chưa thiết lập phiên làm việc'
            ], 200);
        }
    }
}
