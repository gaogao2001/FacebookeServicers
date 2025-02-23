<?php

namespace App\Modules\EmailScan\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\EmailScan\Repositories\EmailScanRepositoryInterface;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;

class EmailScanController extends Controller
{
    protected $emailScanRepository;
    protected $accountService;


    public function __construct(EmailScanRepositoryInterface $emailScanRepository, AccountService $accountService)
    {
        $this->emailScanRepository = $emailScanRepository;
        $this->accountService = $accountService;
    }

    public function index()
    {
        $user = $this->accountService->findOne(Auth::id());

        return view('EmailScan::email_scan', compact('user'));
    }

    public function emailScan(Request $request)
    {
        $search = $request->input('search');
        $perPage = 200;
        $page = $request->input('page', 1);

        $filters = [];

        if ($search) {
            $filters = [
                '$or' => [
                    ['uid' => new \MongoDB\BSON\Regex($search, 'i')],
                    ['email' => new \MongoDB\BSON\Regex($search, 'i')],
                    ['domain' => new \MongoDB\BSON\Regex($search, 'i')],
                    ['fullname' => new \MongoDB\BSON\Regex($search, 'i')],
                    ['quocgia' => new \MongoDB\BSON\Regex($search, 'i')],
                    ['quequan' => new \MongoDB\BSON\Regex($search, 'i')],
                    // Thêm các trường khác nếu cần
                ]
            ];
        }

        $emailScans = $this->emailScanRepository->searchEmailScans($filters, $perPage, $page);

        return response()->json($emailScans);
    }

    public function addEmailScan(Request $request)
    {
        $request->validate([
            'uid' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'domain' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'fullname' => 'nullable|string|max:255',
            'follow' => 'nullable|integer',
            'friend' => 'nullable|integer',
            'sinhnhat' => 'nullable|date_format:Y-m-d',
            'quequan' => 'nullable|string|max:255',
            'quocgia' => 'nullable|string|max:255',
        ]);
        try {
            // Kiểm tra trùng UID
            $uid = $request->input('uid');
            if ($uid) {
                $existingEmailScan = $this->emailScanRepository->findByUid($uid);
                if ($existingEmailScan) {
                    // UID đã tồn tại
                    return response()->json([
                        'status' => false,
                        'message' => 'Email đã tồn tại'
                    ]);
                }
            }
            // Tạo mới EmailScan
            $emailScan = $this->emailScanRepository->create($request->only([
                'uid',
                'email',
                'domain',
                'phone',
                'fullname',
                'follow',
                'friend',
                'sinhnhat',
                'quequan',
                'quocgia'
            ]));

            return response()->json([
                'status' => true,
                'data' => $emailScan,
                'message' => 'Thêm mới thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Thêm mới thất bại'
            ]);
        }
    }

    public function showEmailScan($id)
    {
        $emailScan = $this->emailScanRepository->findById($id);

        if (!$emailScan) {
            return response()->json(['error' => 'Email Scan not found'], 200);
        }

        return response()->json($emailScan);
    }

    public function updateEmailScan(Request $request, $id)
    {
        $request->validate([
            'uid' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'domain' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'fullname' => 'nullable|string|max:255',
            'follow' => 'nullable|integer',
            'friend' => 'nullable|integer',
            'sinhnhat' => 'nullable|date_format:Y-m-d',
            'quequan' => 'nullable|string|max:255',
            'quocgia' => 'nullable|string|max:255',
        ]);

        try {
            $uid = $request->input('uid');
            if ($uid) {
                $existingEmailScan = $this->emailScanRepository->findByUid($uid);
                if ($existingEmailScan && $existingEmailScan->_id != $id) { 
                    // UID đã tồn tại
                    return response()->json([
                        'status' => false,
                        'message' => 'UID đã tồn tại'
                    ], 200);
                }
            }

            $emailScan = $this->emailScanRepository->update($id, $request->only([
                'uid',
                'email',
                'domain',
                'phone',
                'fullname',
                'follow',
                'friend',
                'sinhnhat',
                'quequan',
                'quocgia'
            ]));

            return response()->json([
                'status' => true,
                'data' => $emailScan,
                'message' => 'Cập nhật thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật thất bại'
            ], 200);
        }
    }

    public function delete($id)
    {
        try {
            $deleted = $this->emailScanRepository->delete($id);

            if (!$deleted) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email Scan không tồn tại'
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Xóa thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Xóa thất bại'
            ], 200);
        }
    }
}