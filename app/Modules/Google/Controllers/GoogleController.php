<?php

namespace App\Modules\Google\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Google\Repositories\AccountRepositoryInterface;
use PragmaRX\Google2FA\Google2FA;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class GoogleController extends Controller
{
    protected $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function google_page()
    {
        $accounts = $this->accountRepository->findAll();
        $accountsArray = [];

        foreach ($accounts as $account) {
            $accountsArray[] = [
                '_id' => (string) $account['_id'],
                'username' => $account['username'],
                'siteDomain' => $account['siteDomain'],
                'keyCode' => $account['keyCode'],
                'password' => $account['password'],
                'notes' => $account['notes'],
                'created_at' => $account['created_at']->toDateTime()->format('c'),
                'updated_at' => $account['updated_at']->toDateTime()->format('c'),
            ];
        }

        // Truyền biến accountsArray vào view
        return view('Google::google', compact('accountsArray'));
    }


    public function index()
    {
        $accounts = $this->accountRepository->findAll();
        $accountsArray = [];

        foreach ($accounts as $account) {
            $accountsArray[] = [
                '_id' => (string) $account['_id'],
                'username' => $account['username'],
                'siteDomain' => $account['siteDomain'],
                'keyCode' => $account['keyCode'],
                'password' => $account['password'],
                'notes' => $account['notes'],
                'created_at' => $account['created_at']->toDateTime()->format('c'),
                'updated_at' => $account['updated_at']->toDateTime()->format('c'),
            ];
        }

        return response()->json($accountsArray);
    }

    public function show($id)
    {
        try {
            $account = $this->accountRepository->findById(new ObjectId($id));
        } catch (\Exception $e) {
            return response()->json(['error' => 'ID không hợp lệ'], 200);
        }

        if (!$account) {
            return response()->json(['error' => 'Tài khoản không tồn tại'], 200);
        }

        return response()->json([
            '_id' => (string) $account['_id'],
            'username' => $account['username'],
            'siteDomain' => $account['siteDomain'],
            'keyCode' => $account['keyCode'],
            'password' => $account['password'],
            'notes' => $account['notes'],
            'created_at' => $account['created_at']->toDateTime()->format('c'),
            'updated_at' => $account['updated_at']->toDateTime()->format('c'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'siteDomain' => 'required|string|max:255',
            'keyCode' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'notes' => 'nullable|string',
        ]);

        $currentTimestamp = new UTCDateTime(now()->timestamp * 1000);
        $validated['created_at'] = $currentTimestamp;
        $validated['updated_at'] = $currentTimestamp;

        $newAccount = $this->accountRepository->create($validated);

        if ($newAccount) {
            return response()->json([
                'message' => 'Tài khoản được thêm mới thành công',
                'account' => $newAccount,
            ], 201);
        }

        return response()->json(['error' => 'Không thể tạo tài khoản'], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $objectId = new ObjectId($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'ID không hợp lệ'], 200);
        }

        $account = $this->accountRepository->findById($objectId);
        if (!$account) {
            return response()->json(['error' => 'Tài khoản không tồn tại'], 200);
        }

        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'siteDomain' => 'required|string|max:255',
            'keyCode' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_at'] = new UTCDateTime(now()->timestamp * 1000);

        $updatedAccount = $this->accountRepository->update($id, $validated);

        if ($updatedAccount) {
            return response()->json([
                'message' => 'Tài khoản được cập nhật thành công',
                'account' => $updatedAccount,
            ], 200);
        }

        return response()->json(['error' => 'Không thể cập nhật tài khoản'], 200);
    }

    public function destroy($id)
    {
        try {
            $objectId = new ObjectId($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'ID không hợp lệ'], 200);
        }

        $account = $this->accountRepository->findById($objectId);
        if (!$account) {
            return response()->json(['error' => 'Tài khoản không tồn tại'], 200);
        }

        $this->accountRepository->delete($id);

        return response()->json(['message' => 'Tài khoản được xóa thành công'], 200);
    }

    public function deleteAll(Request $request)
    {
        try {
            $this->accountRepository->deleteAll();
            return response()->json(['message' => 'Tất cả tài khoản Google đã được xóa thành công.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể xóa tài khoản Google.'], 500);
        }
    }

    public function generateCode(Request $request)
    {
        $keyCode = strtoupper(trim($request->input('keyCode')));
        $keyCode = preg_replace('/[^A-Z2-7]/', '', $keyCode);

        if (empty($keyCode)) {
            return response()->json(['error' => 'Key code không hợp lệ hoặc rỗng'], 200);
        }

        try {
            $google2fa = new Google2FA();
            $code = $google2fa->getCurrentOtp($keyCode);
            return response()->json(['code' => $code]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi tạo mã: ' . $e->getMessage()], 200);
        }
    }

    public function deleteByEmails(Request $request)
    {
        $emails = $request->input('emails');

        if (is_string($emails)) {
            $emails = explode(',', $emails); // Nếu gửi từ frontend là chuỗi, chuyển thành mảng
        }

        if (!is_array($emails) || empty($emails)) {
            return response()->json(['message' => 'Thất bại: Emails không hợp lệ hoặc rỗng.'], 200);
        }

        try {
            // Gọi repository để xóa tài khoản
            $this->accountRepository->deleteMany($emails);

            // Trả về thông báo thành công
            return response()->json(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu xảy ra
            return response()->json(['message' => 'Thất bại: ' . $e->getMessage()], 200);
        }
    }
}
