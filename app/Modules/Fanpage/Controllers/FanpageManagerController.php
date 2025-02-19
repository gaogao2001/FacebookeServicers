<?php

namespace App\Modules\Fanpage\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface;
use App\Modules\Fanpage\Repositories\FanpageManagerRepositoryInterface;
use App\Modules\ConfigAuto\Repositories\ConfigAutoRepositoryInterface;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FanpageManagerController extends Controller
{
    protected $fanpageManagerRepository;
    protected $accountRepository;
    protected $configAutoRepository;

    public function __construct(FanpageManagerRepositoryInterface $fanpageManagerRepository, AccountRepositoryInterface $accountRepository, ConfigAutoRepositoryInterface $configAutoRepository)
    {
        $this->fanpageManagerRepository = $fanpageManagerRepository;
        $this->accountRepository = $accountRepository;
        $this->configAutoRepository = $configAutoRepository;

    }

    public function fanpageManagerPage()
    {
        $countsFanpages = $this->fanpageManagerRepository->countsFanpage();

      
        return view('Fanpage::fanpage_manager' , compact('countsFanpages'));
    }


    public function getFanpageManager(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');

        // Lấy bộ lọc từ session nếu có
        $filterData = session('fanpage_filters', []);

        $filters = [];

        // Áp dụng bộ lọc từ session
        if (!empty($filterData)) {
            // Bộ lọc Like
            if (!empty($filterData['likes_min']) || !empty($filterData['likes_max'])) {
                $filters['likes'] = [];
                if (!empty($filterData['likes_min'])) {
                    $filters['likes']['$gte'] = (int)$filterData['likes_min'];
                }
                if (!empty($filterData['likes_max'])) {
                    $filters['likes']['$lte'] = (int)$filterData['likes_max'];
                }
            }

            // Bộ lọc Followers
            if (!empty($filterData['followers_min']) || !empty($filterData['followers_max'])) {
                $filters['followers'] = [];
                if (!empty($filterData['followers_min'])) {
                    $filters['followers']['$gte'] = (int)$filterData['followers_min'];
                }
                if (!empty($filterData['followers_max'])) {
                    $filters['followers']['$lte'] = (int)$filterData['followers_max'];
                }
            }

            // Bộ lọc Posts
            if (!empty($filterData['posts_min']) || !empty($filterData['posts_max'])) {
                $filters['posts'] = [];
                if (!empty($filterData['posts_min'])) {
                    $filters['posts']['$gte'] = (int)$filterData['posts_min'];
                }
                if (!empty($filterData['posts_max'])) {
                    $filters['posts']['$lte'] = (int)$filterData['posts_max'];
                }
            }
        }

        // Áp dụng bộ lọc tìm kiếm nếu có
        if (!empty($search)) {
            $escapedSearch = preg_quote($search, '/');

            $filters['$or'] = [
                ['page_name' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['uid_controler' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['page_id' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['SourceControl' => ['$regex' => $escapedSearch, '$options' => 'i']],
            ];
        }

        $perPage = $request->input('per_page', 15);

        $result = $this->fanpageManagerRepository->searchFanpages($filters, $perPage, $page);

        // Kiểm tra nếu không có dữ liệu trả về
        if (empty($result['data'])) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy fanpage nào thỏa điều kiện.',
                'data' => [],
            ]);
        }

        // Trả về dữ liệu nếu có kết quả
        return response()->json([
            'status' => true,
            'data' => $result['data'],
            'currentPage' => $result['currentPage'],
            'lastPage' => $result['lastPage'],
            'perPage' => $result['perPage'],
            'total' => $result['total'],
        ]);
    }

    public function editPage($id)
    {
        $fanpage = $this->fanpageManagerRepository->findById($id);

        $uids = is_array($fanpage->uid_controler) ? $fanpage->uid_controler : [$fanpage->uid_controler];

        $friendsData = $this->accountRepository->getMultipleFriendsData($uids);
        $groupsData = $this->accountRepository->getMultipleGroupsData($uids);


        // Chuyển về phần tử đầu tiên nếu có dữ liệu
        $friendsData = reset($friendsData)->friends ?? null;

        return view('Fanpage::Form.fanpage_edit', compact('fanpage', 'friendsData', 'groupsData'));
    }

    public function updateFanpage(Request $request, $id)
    {
        $request->validate([
            'page_name' => 'required|string|max:255',
            'access_token' => 'required|string',
        ]);

        $this->fanpageManagerRepository->update($id, [
            'page_name' => $request->input('page_name'),
            'access_token' => $request->input('access_token'),
        ]);

        return redirect()->route('fanpage-manager.edit', $id)->with('success', 'Cập nhật thành công');
    }


    public function delete($id)
    {
        $this->fanpageManagerRepository->delete($id);

        return response()->json(['message' => 'Xóa thành công']);
    }

    public function updateCoordinates(Request $request, $id)
    {
        $data = $request->only(['latitude', 'longitude']);
        $validator = Validator::make($data, [
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 200);
        }

        // Truyền dữ liệu trực tiếp thay vì lồng '$set'
        $this->fanpageManagerRepository->update($id, [
            'latitude'  => $data['latitude'],
            'longitude' => $data['longitude'],
        ]);

        return redirect()->route('fanpage-manager.edit', $id)->with('success', 'Cập nhật tọa độ thành công');
    }

    public function selectDelete(Request $request)
    {

        // Tiếp tục xử lý như trước
        $pageId = $request->input('selected_fanpages', []);

        $this->fanpageManagerRepository->deleteMany(['page_id' => ['$in' =>  $pageId]]);

        return response()->json(['message' => 'Xóa thành công']);
    }


    public function deleteAllFanpages(Request $request)
    {
        try {

            $this->fanpageManagerRepository->deleteMany([]);

            return response()->json(['message' => 'Đã xóa toàn bộ Fanpages và dữ liệu liên quan thành công.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting all fanpages: ' . $e->getMessage());

            return response()->json(['message' => 'Đã xảy ra lỗi khi xóa dữ liệu.'], 200);
        }
    }

    public function filterFanpages(Request $request)
    {
        $filterData = $request->only(['likes_min', 'likes_max', 'followers_min', 'followers_max', 'posts_min', 'posts_max']);
        session(['fanpage_filters' => $filterData]);

        $filters = [];

        // Bộ lọc Like
        if (!empty($filterData['likes_min']) || !empty($filterData['likes_max'])) {
            $filters['likes'] = [];
            if (!empty($filterData['likes_min'])) {
                $filters['likes']['$gte'] = (int)$filterData['likes_min'];
            }
            if (!empty($filterData['likes_max'])) {
                $filters['likes']['$lte'] = (int)$filterData['likes_max'];
            }
        }

        // Bộ lọc Followers
        if (!empty($filterData['followers_min']) || !empty($filterData['followers_max'])) {
            $filters['followers'] = [];
            if (!empty($filterData['followers_min'])) {
                $filters['followers']['$gte'] = (int)$filterData['followers_min'];
            }
            if (!empty($filterData['followers_max'])) {
                $filters['followers']['$lte'] = (int)$filterData['followers_max'];
            }
        }

        // Bộ lọc Posts
        if (!empty($filterData['posts_min']) || !empty($filterData['posts_max'])) {
            $filters['posts'] = [];
            if (!empty($filterData['posts_min'])) {
                $filters['posts']['$gte'] = (int)$filterData['posts_min'];
            }
            if (!empty($filterData['posts_max'])) {
                $filters['posts']['$lte'] = (int)$filterData['posts_max'];
            }
        }

        $perPage = 15;
        $page = 1;

        $result = $this->fanpageManagerRepository->searchFanpages($filters, $perPage, $page);

        if (empty($result['data'])) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy fanpage nào thỏa điều kiện.'], 200);
        }

        return response()->json(['status' => true, 'data' => $result['data']]);
    }

    public function clearFilter(Request $request)
    {
        session()->forget('fanpage_filters');

        $filters = [];
        $perPage = 15;
        $page = 1;

        $result = $this->fanpageManagerRepository->searchFanpages($filters, $perPage, $page);

        return response()->json(['status' => true, 'data' => $result['data']]);
    }
}
