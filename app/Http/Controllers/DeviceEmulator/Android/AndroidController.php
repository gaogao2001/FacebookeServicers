<?php
namespace App\Http\Controllers\DeviceEmulator\Android;

use App\Http\Controllers\Controller;
use App\Repositories\Facebook\Account\AccountRepositoryInterface;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;
use HoangquyIT\FacebookAccount;// ok
use HoangquyIT\ModelFacebook\FacebookUnlock;// ok

class AndroidController extends Controller
{
	protected $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }
	
    public function unlock(Request $request, $uid = null)
	{
		if (is_null($uid) || empty($uid) || !is_string($uid)) {
			return redirect()->route('facebook.pages')->with('error', 'UID không hợp lệ.');
		}

		$accounts = $this->accountRepository->searchAccounts(['uid' => $uid]);
		if (empty($accounts) || empty($accounts['data'])) {
			return redirect()->route('facebook.pages')->with('error', 'Không tìm thấy tài khoản.');
		}

		$FacebookUse = iterator_to_array($accounts["data"][0]); // Đổi dữ liệu MongoDB sang mảng
		unset($FacebookUse["_id"]); // Xóa bỏ dữ liệu ID của MongoDB trong mảng
		$Accountuse = new FacebookAccount($FacebookUse); // Kết nối CURL
		$baseUrl = $request->fullUrl();
		if ($Accountuse->Connect) {
			$Unlock = new FacebookUnlock($Accountuse, 'android');
			if ($request->isMethod('get')) {
				// Logic cho GET request
				print_r($Unlock->UnlockAndroid($baseUrl));
			} elseif ($request->isMethod('post')) {
				// Logic cho POST request
				$postData = $request->all(); // Lấy tất cả dữ liệu POST
				var_dump($postData); // Hiển thị dữ liệu POST
				die(); // Ngừng xử lý sau khi hiển thị dữ liệu
			}
		}
	}

}
