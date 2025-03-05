<?php

namespace  App\Modules\Ads\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Ads\Repositories\AdsManager\AdsManagerRepositoryInterface;
use App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface;
use MongoDB\BSON\ObjectId;

class AdsManagerController extends Controller
{
    protected $adsManagerRepository;
    protected $accountRepository;

    public function __construct(AdsManagerRepositoryInterface $adsManagerRepository, AccountRepositoryInterface $accountRepository)
    {
        $this->adsManagerRepository = $adsManagerRepository;
        $this->accountRepository = $accountRepository;
    }


    public function adsManagerPage()
    {
        $ads = $this->adsManagerRepository->findAll();

        return view('Ads::ads_manager', compact('ads'));
    }
    public function getAdsManager(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');

        $filters = [];

        // Kiểm tra xem có bộ lọc trong session không
        if (session()->has('fanpage_filters')) {
            // Xử lý danh sách insights từ session
            if (!empty(session('fanpage_filters.insights_list'))) {
                $insightsList = preg_split("/\r\n|\n|\r/", session('fanpage_filters.insights_list'));
                $insightsList = array_filter(array_map('trim', $insightsList));
                if (!empty($insightsList)) {
                    $filters['insights'] = ['$in' => $insightsList];
                }
            }

            // Xử lý danh sách act_id từ session
            if (!empty(session('fanpage_filters.act_id_list'))) {
                $actIdList = preg_split("/\r\n|\n|\r/", session('fanpage_filters.act_id_list'));
                $actIdList = array_filter(array_map('trim', $actIdList));
                if (!empty($actIdList)) {
                    $filters['act_id'] = ['$in' => $actIdList];
                }
            }

            // Thêm các bộ lọc khác từ session
            $additionalFilters = [
                'admin_hidden' => session('fanpage_filters.admin_hidden'),
                'timezone' => session('fanpage_filters.timezone'),
                'currency' => session('fanpage_filters.currency'),
                'account_status' => session('fanpage_filters.account_status'),
                'nguong_tt' => session('fanpage_filters.nguong_tt'),
                'nguong_tt_hientai' => session('fanpage_filters.nguong_tt_hientai'),
            ];

            foreach ($additionalFilters as $key => $value) {
                if (!empty($value)) {
                    $filters[$key] = $value;
                }
            }
        }

        // Thêm điều kiện tìm kiếm nếu có
        if (!empty($search)) {
            $escapedSearch = preg_quote($search, '/');

            $filters['$or'] = [
                ['name' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['act_id' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['insights' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['account_type' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['timezone' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['timezone_name' => ['$regex' => $escapedSearch, '$options' => 'i']],
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

        return response()->json(['message' => 'Không có mục nào được chọn'], 200);
    }

    public function filterAds(Request $request)
    {
        $filters = [];

        // Xử lý danh sách insights
        if (!empty($request->input('insights_list'))) {
            $insightsList = preg_split("/\r\n|\n|\r/", $request->input('insights_list'));
            $insightsList = array_filter(array_map('trim', $insightsList)); // Loại bỏ khoảng trắng và dòng trống
            if (!empty($insightsList)) {
                $filters['insights'] = ['$in' => $insightsList];
            }
        }

        // Xử lý danh sách act_id
        if (!empty($request->input('act_id_list'))) {
            $actIdList = preg_split("/\r\n|\n|\r/", $request->input('act_id_list'));
            $actIdList = array_filter(array_map('trim', $actIdList)); // Loại bỏ khoảng trắng và dòng trống
            if (!empty($actIdList)) {
                $filters['act_id'] = ['$in' => $actIdList];
            }
        }


        // Các filter khác
        $additionalFilters = [
            'admin_hidden' => $request->input('admin_hidden'),
            'timezone' => $request->input('timezone'),
            'currency' => $request->input('currency'),
            'account_status' => $request->input('account_status'),
            'nguong_tt' => $request->input('nguong_tt'),
            'nguong_tt_hientai' => $request->input('nguong_tt_hientai'),
        ];



        // Thêm các filter không rỗng vào mảng filters
        foreach ($additionalFilters as $key => $value) {
            if (!empty($value)) {
                $filters[$key] = $value;
            }
        }

        // Lưu bộ lọc vào session
        session(['fanpage_filters' => array_merge(
            ['insights_list' => $request->input('insights_list')],
            ['act_id_list' => $request->input('act_id_list')],
            $additionalFilters
        )]);


        $result = $this->adsManagerRepository->searchAds($filters);


        return response()->json($result);
    }

    public function clearFilter(Request $request)
    {
        // Xóa bộ lọc khỏi session
        $request->session()->forget(['fanpage_filters']);

        // Trả về dữ liệu không có bộ lọc
        $result = $this->adsManagerRepository->searchAds();

        return response()->json($result);
    }


    public function exportAccount(Request $request)
    {
        // Lấy dữ liệu từ request
        $accountListInput = $request->input('account_list', '');
        $chosenStructure = $request->input('chosen_structure', '');

        if (empty($chosenStructure)) {
            return response()->json(['message' => 'Chưa chọn cấu trúc xuất file'], 400);
        }

        // Tách cấu trúc thành mảng các trường (dạng "uid|password|qrcode|email|password_email|cookies_mobile|cookies_pc|cookies_app")
        $columns = array_filter(array_map('trim', explode('|', $chosenStructure)));
        if (empty($columns)) {
            return response()->json(['message' => 'Cấu trúc không hợp lệ'], 400);
        }

        // Tách danh sách tài khoản (mỗi dòng chứa uid) và lọc bỏ những giá trị trùng lặp
        $insights = array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $accountListInput)));
        $insights = array_values(array_unique($insights));
        if (empty($insights)) {
            return response()->json(['message' => 'Chưa nhập danh sách tài khoản'], 400);
        }

        $exportData = [];

        foreach ($insights as $insight) {
            // Gọi qua repository để lấy account theo uid (hoặc insight)
            $account = $this->accountRepository->findByUid($insight);
            $row = [];
            foreach ($columns as $col) {
                switch ($col) {
                    case 'uid':
                        $row[$col] = $account->uid ?? null;
                        break;
                    case 'password':
                        $row[$col] = $account->password ?? null;
                        break;
                    case 'qrcode':
                        $row[$col] = $account->qrcode ?? null;
                        break;
                    case 'email':
                        if (isset($account->email)) {
                            $row[$col] = is_array($account->email) ? implode(',', $account->email) : $account->email;
                        } else {
                            $row[$col] = null;
                        }
                        break;
                    case 'password_email':
                        $row[$col] = $account->password_email ?? null;
                        break;
                    case 'cookies_mobile':
                        $row[$col] = (isset($account->android_mobile) && isset($account->android_mobile->cookies))
                            ? $account->android_mobile->cookies : null;
                        break;
                    case 'cookies_pc':
                        $row[$col] = (isset($account->windows_device) && isset($account->windows_device->cookies))
                            ? $account->windows_device->cookies : null;
                        break;
                    case 'cookies_app':
                        $row[$col] = (isset($account->android_device) && isset($account->android_device->cookies))
                            ? $account->android_device->cookies : null;
                        break;
                    default:
                        $row[$col] = $account->{$col} ?? null;
                        break;
                }
            }
            $exportData[] = $row;
        }

        // Xuất file TXT
        $filename = 'export_accounts_' . date('Ymd_His') . '.txt';
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $delimiter = '|';
        $lines = [];
        // Dòng tiêu đề
        $lines[] = implode($delimiter, $columns);
        // Các dòng dữ liệu
        foreach ($exportData as $row) {
            $line = [];
            foreach ($columns as $col) {
                $line[] = $row[$col] !== null ? $row[$col] : '';
            }
            $lines[] = implode($delimiter, $line);
        }
        echo implode("\n", $lines);
        exit;
    }
}
