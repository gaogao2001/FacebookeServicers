<?php
namespace App\InterfaceModules\DeviceEmulator\Android\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;
use HoangquyIT\FacebookAccount;
use HoangquyIT\ModelFacebook\Android\MainControler;
use HoangquyIT\ModelFacebook\Android\Profile\ProfileManager;
use HoangquyIT\ModelFacebook\Android\Accounts\AccountManager;

class PageInfoController extends Controller
{
	protected $accountRepository;
	protected $FacebookUse;
	protected $account;
	protected $uid;
	protected $_id;
	

    public function __construct($_account, $accountRepository)
    {
        $this->accountRepository = $accountRepository;
		$this->account = $_account;
		$this->uid = $_account->uid;
		$this->_id = $_account->_id;
		$this->FacebookUse = iterator_to_array($_account);
		unset($this->FacebookUse["_id"]); 
    }
	
	// $Account_uid buộc phải truyền vì dành cho truong hop đặt avatar cho profile hay page thì truyền nó vào để check
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
		if($AccountType == 'profile')
		{
			$thongTin['status'] = false;
			$thongTin['message'] = 'Hàm đang chỉ triển khai cho profile hãy trỏ lại hàm mới về profile do đây là Page Account';
			return $thongTin;
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
				if($resultSetAvatar)
				{
					$profile = new ProfileManager($Accountuse);
					$ProfileInfo = $profile->MyProfile();
					if(!empty($ProfileInfo->avatar))
					{
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
				}
				else{
					$thongTin['status'] = false;
					$thongTin['message'] = 'Đặt Avatar thất bại!';
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
		if($AccountType == 'profile')
		{
			$thongTin['status'] = false;
			$thongTin['message'] = 'Hàm đang chỉ triển khai cho profile hãy trỏ lại hàm mới về profile do đây là Page Account';
			return $thongTin;
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