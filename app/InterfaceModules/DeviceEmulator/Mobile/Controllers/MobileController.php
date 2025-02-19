<?php

namespace App\InterfaceModules\DeviceEmulator\Mobile\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;

class MobileController extends Controller
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

		if ($request->isMethod('get')) {
			// Logic cho GET request
			die('sssssssss');
		} elseif ($request->isMethod('post')) {
			// Logic cho POST request
			die('vvvvvvvvv');
		}
	}
}