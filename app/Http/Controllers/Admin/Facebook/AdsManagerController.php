<?php

namespace App\Http\Controllers\Admin\Facebook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Facebook\AdsManager\AdsManagerRepositoryInterface;
use MongoDB\BSON\ObjectId;

class AdsManagerController extends Controller
{
    protected $adsManagerRepository;

    public function __construct(AdsManagerRepositoryInterface $adsManagerRepository)
    {
        $this->adsManagerRepository = $adsManagerRepository;
    }

    public function adsManagerPage()
    {
        return view('admin.pages.Facebook.ads_manager');
    }

    public function getAdsManager(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');

        $filters = [];
        if (!empty($search)) {
            $escapedSearch = preg_quote($search, '/');

            $filters['$or'] = [
                ['name' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['act_id' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['insights' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['account_type' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['timezone' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['timezone_name' => ['$regex' => $escapedSearch, '$options' => 'i']],
                // Thêm các trường khác nếu cần
            ];
        }

        $perPage = $request->input('per_page', 100);

        $result = $this->adsManagerRepository->searchAds($filters, $perPage, $page);

        return response()->json($result);
    }

    public function delete($id)
    {
        $this->adsManagerRepository->delete($id);

        return response()->json(['message' => 'Xóa thành công']);
    }

    public function allDelete(Request $request)
    {
        $deleteAll = $request->input('deleteAll', false);
        $ids = $request->input('ids', []);

        if ($deleteAll) {
            // Xóa tất cả các bản ghi trong collection
            $this->adsManagerRepository->deleteMany([]);
            return response()->json(['message' => 'Đã xóa toàn bộ dữ liệu thành công']);
        } elseif (!empty($ids)) {
            $objectIds = array_map(function ($id) {
                return new ObjectId($id);
            }, $ids);

            if (count($objectIds) > 0) {
                $this->adsManagerRepository->deleteMany(['_id' => ['$in' => $objectIds]]);
                return response()->json(['message' => 'Đã xóa các mục đã chọn thành công']);
            }
        }

        return response()->json(['message' => 'Không có mục nào được chọn'], 400);
    }
}
