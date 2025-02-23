<?php

namespace App\Modules\History\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\History\Repositories\Facebook\FacebookHistoryRepositoryInterface;
use MongoDB\BSON\ObjectId;

class FacebookHistoryController extends Controller
{
    protected $historyRepository;

    public function __construct(FacebookHistoryRepositoryInterface $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    public function facebookHistoryPage()
    {
        return view('History::facebook_history');
    }

    public function index(Request $request)
    {
        $perPage = 15;
        $page = (int) $request->input('page', 1);

        // Đảm bảo $page là số hợp lệ
        if ($page < 1) {
            return response()->json(['message' => 'Invalid page number'], 200);
        }

        $historyData = $this->historyRepository->getAllHistoryData($perPage, $page);
        $total = $this->historyRepository->countAllHistoryData();
        $lastPage = max(1, ceil($total / $perPage)); // Đảm bảo lastPage ít nhất là 1

        // Kiểm tra nếu trang vượt quá lastPage
        if ($page > $lastPage) {
            return response()->json(['message' => 'Page not found'], 200);
        }

        return response()->json([
            'data' => $historyData,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'total' => $total,
        ]);
    }

    public function delete($id)
    {
        $this->historyRepository->delete($id);

        return response()->json(['message' => 'Delete success']);
    }

    public function allDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            $objectIds = array_map(function ($id) {
                return new ObjectId($id);
            }, $ids);
            $this->historyRepository->deleteMany(['_id' => ['$in' => $objectIds]]);
            return response()->json(['message' => 'Deleted successfully']);
        }

        return response()->json(['message' => 'No items selected'], 200);
    }

    public function deleteAllHistory(Request $request)
    {
        try {
            $this->historyRepository->deleteAll();
            return response()->json(['message' => 'Tất cả lịch sử đã được xóa thành công.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể xóa lịch sử.'], 500);
        }
    }


    public function getHistoryByUid($uid, Request $request)
    {
        $perPage = 10;
        $page = (int) $request->input('page', 1);

        // Đảm bảo $page là số hợp lệ
        if ($page < 1) {
            return response()->json(['message' => 'Invalid page number'], 200);
        }

        $uids = [$uid];

        $historyData = $this->historyRepository->getHistoryData($uids, $perPage, $page);
        $total = $this->historyRepository->countHistoryData($uids);
        $lastPage = max(1, ceil($total / $perPage)); // đảm bảo lastPage ít nhất là 1

        // Kiểm tra nếu trang vượt quá lastPage
        if ($page > $lastPage) {
            return response()->json(['message' => 'Page not found'], 200);
        }

        return response()->json([
            'data' => $historyData,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'total' => $total,
        ]);
    }
}
