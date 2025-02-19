<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface;

class AccountController extends Controller
{
	protected $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }
	
    public function addAccountFacebook(Request $request)
    {
		if(count($request->all()) < 2)
		{
			return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Body bắt buộc phải có ít nhất 2 trường dữ liệu để thao tác'
                ]
            ], 200);
		}
		$RequestBody = (object)$request->all();
        if(!empty($RequestBody->uid))
		{
			$existingAccount = $this->accountRepository->findByUid($RequestBody->uid);
			if ($existingAccount) {
				$this->accountRepository->update($existingAccount->_id, $request->all());
				return response()->json([
					'data' => [
						'status' => true,
						'message' => 'Cập nhật dữ liệu thành công !'
					]
				], 200);
			} else {
				$this->accountRepository->create($request->all());
				return response()->json([
					'data' => [
						'status' => true,
						'message' => 'Thêm mới dữ liệu thành công !'
					]
				], 200);
			}
		}
		else
		{
			return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không có dữ liệu uid, uid là trường dữ liệu bắt buộc phải có'
                ]
            ], 200);
		}
    }
	
	public function CountAccountFacebook(Request $request)
    {
		$count = $this->accountRepository->countAll();
		return response()->json([
			'data' => [
				'status' => true,
				'count' => $count
			]
		], 200);
    }
}
