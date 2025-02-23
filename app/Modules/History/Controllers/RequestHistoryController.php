<?php

namespace App\Modules\History\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\History\Repositories\Request\RequestHistoryRepositoryInterface;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;

class RequestHistoryController extends Controller
{
    protected $requestHistoryRepository;

    public function __construct(RequestHistoryRepositoryInterface $requestHistoryRepository)
    {
        $this->requestHistoryRepository = $requestHistoryRepository;
    }

    public function requestHistoryPage()
    {
        return view('History::request_history');
    }

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $filters = [];
        if (!empty($search)) {
            $escapedSearch = preg_quote($search, '/');

            $filters['$or'] = [
                ['uid' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['requestHeaders.data.request-line' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['httpStatusCode' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['errorCode' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['errorMessage' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['created_at' => ['$regex' => $escapedSearch, '$options' => 'i']],
                // Thêm các trường khác nếu cần
            ];
        }

        $requests = $this->requestHistoryRepository->searchRequests($filters, $perPage, $page);
        return response()->json($requests);
    }

    public function delete($id)
    {
        $this->requestHistoryRepository->delete($id);
        return response()->json(['message' => 'Xóa thành công']);
    }

    public function deleteAll(Request $request)
    {
        try {
            $this->requestHistoryRepository->deleteAll();
            return response()->json(['message' => 'Tất cả lịch sử yêu cầu đã được xóa thành công.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể xóa lịch sử yêu cầu.'], 500);
        }
    }

    public function show($id)
    {
        $request = $this->requestHistoryRepository->findById($id);

        if ($request) {
            return response()->json($request);
        } else {
            return response()->json(['message' => 'Không tìm thấy yêu cầu'], 200);
        }
    }

    public function searchRequests(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');

        $filters = [];
        if (!empty($search)) {
            $escapedSearch = preg_quote($search, '/');

            $filters['$or'] = [
                ['uid' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['requestHeaders.data.request-line' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['httpStatusCode' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['errorCode' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['errorMessage' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['created_at' => ['$regex' => $escapedSearch, '$options' => 'i']],
                // Thêm các trường khác nếu cần
            ];
        }

        $requests = $this->requestHistoryRepository->searchRequests($filters, 100, $page);
        return response()->json($requests);
    }
}
