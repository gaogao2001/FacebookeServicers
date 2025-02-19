<?php

namespace App\Modules\Country\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Country\Repositories\CountryRepositoryInterface;
use MongoDB\BSON\ObjectId;

class CountryController extends Controller
{

    protected $countryRepository;

    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function countryPage()
    {
        return view('Country::country_page');
    }

    public function getCountries(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');

        $filters = [];
        if (!empty($search)) {
            // Bảo vệ đầu vào của người dùng bằng cách escape các ký tự đặc biệt trong regex
            $escapedSearch = preg_quote($search, '/');

            // Mở rộng tìm kiếm trên nhiều trường bằng cách sử dụng toán tử $or
            $filters['$or'] = [
                ['location_country' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['City' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['id_country' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['language' => ['$regex' => $escapedSearch, '$options' => 'i']],
                // Bạn có thể thêm các trường khác nếu cần
            ];
        }

        $countries = $this->countryRepository->searchCountries($filters, 300, $page);
        return response()->json($countries);
    }

    public function delete($id)
    {
        $this->countryRepository->delete($id);

        return response()->json(['message' => 'Delete success']);
    }

    public function allDelete(Request $request)
    {
        $deleteAll = $request->input('deleteAll', false);
        $ids = $request->input('ids', []);

        if ($deleteAll) {
            // Xóa tất cả các bản ghi trong collection
            $this->countryRepository->deleteMany([]);
            return response()->json(['message' => 'Đã xóa toàn bộ dữ liệu thành công']);
        } elseif (!empty($ids)) {
            $objectIds = array_map(function ($id) {
                return new ObjectId($id);
            }, $ids);

            if (count($objectIds) > 0) {
                $this->countryRepository->deleteMany(['_id' => ['$in' => $objectIds]]);
                return response()->json(['message' => 'Đã xóa các mục đã chọn thành công']);
            }
        }

        return response()->json(['message' => 'Không có mục nào được chọn'], 200);
    }
}
