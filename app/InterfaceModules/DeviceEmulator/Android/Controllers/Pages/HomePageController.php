<?php

namespace App\InterfaceModules\DeviceEmulator\Android\Controllers\Pages;

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
use HoangquyIT\ModelFacebook\Android\Fanpage\PageManager;
use App\Modules\Fanpage\Repositories\FanpageManagerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class HomePageController extends Controller
{

    protected $accountRepository;
    protected $fanpageManagerRepository;
    protected $account; // day la luu giu thong tin goc tu mongodb tra ve khi connect
    protected $uid; // tai day chua thong tin facebook id
    protected $_id; // tai day chua thong tin facebook id
    protected $FacebookUse; // tai day luu tru thong tin account khi da chuyen doi sang array
    public $Message = null; // chua thong bao loi
    public $ConnectData = false; // ghi nhan status


    public function __construct($_account, $accountRepository, FanpageManagerRepositoryInterface $fanpageManagerRepository)
    {
        $this->accountRepository = $accountRepository;
		$this->fanpageManagerRepository = $fanpageManagerRepository;
		//
		$this->account = $_account;
		$this->uid = $_account->uid;
		$this->_id = $_account->_id;
		$this->FacebookUse = iterator_to_array($_account);
		unset($this->FacebookUse["_id"]); 
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


    public function CheckPageCategoryAvailability($CategoryName)
    {
		if (!empty($this->FacebookUse)) {
			$Accountuse = new FacebookAccount($this->FacebookUse);
			if ($Accountuse->Connect) {
				$_page = new PageManager($Accountuse);
				$resultFind = $_page->FindCategory($CategoryName);
				if($resultFind->status)
				{
					$thongTin['status'] = true;
					$thongTin['message'] = 'Thành công !';
					$thongTin['category'] = $resultFind->category;
					return $thongTin;
				}else {
					$thongTin['status'] = false;
					$thongTin['message'] = 'Không tìm thấy dữ liệu danh mục';
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
	public function CheckPageNameAvailability($PageName)
    {
		if (!empty($this->FacebookUse)) {
			$Accountuse = new FacebookAccount($this->FacebookUse);
			if ($Accountuse->Connect) {
				
				$_page = new PageManager($Accountuse);
				$ResultCheck = $_page->CheckNameAllowCreate($PageName);
				if($ResultCheck->status)
				{
					$thongTin['status'] = true;
					$thongTin['message'] = $ResultCheck->message;
					$thongTin['name'] = $ResultCheck->name;
					$thongTin['username'] = $ResultCheck->username;
					return $thongTin;
				}else {
					$thongTin['status'] = false;
					$thongTin['message'] = 'Tên không được phép dùng';
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

	public function CreateNewPage($CreateName, $CreateUsername, $category_ids = [])
    {
		if (!empty($this->FacebookUse)) {
			$Accountuse = new FacebookAccount($this->FacebookUse);
			if ($Accountuse->Connect) {
				$_page = new PageManager($Accountuse);
				$resultCreate = $_page->CreateNewPage($CreateName, $CreateUsername, $category_ids);
				if(!empty($resultCreate))
				{
					$profile = new ProfileManager($Accountuse);
					$MultiAccount = $profile->LoadAccountControl();
					if (!empty($MultiAccount)) {
                        foreach ($MultiAccount as $_select) {
                            $page_id = $_select->profile->id ?? null;
                            $page_name = $_select->profile->name ?? null;
                            $access_token = $_select->session_info->access_token ?? null;
							if($_select->AccountType == 'PageAccount')
							{
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

					$thongTin['status'] = true;
					$thongTin['message'] = 'Tạo trang thành công !';
					return $thongTin;
				}
				else{
					$thongTin['status'] = false;
					$thongTin['message'] = 'Tạo trang không thành công !';
					return $thongTin;
				}
				var_dump($resultCreate);
				die();
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
