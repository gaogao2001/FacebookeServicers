<?php

namespace App\Http\Controllers\Admin\Facebook;

use App\Http\Controllers\Controller;
use App\Repositories\Facebook\Account\AccountRepositoryInterface;
use App\Repositories\Facebook\FanpageManager\FanpageManagerRepositoryInterface;
use App\Repositories\ConfigAuto\ConfigAutoRepositoryInterface;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;

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
        return view('admin.pages.Facebook.Fanpage.fanpage_manager');
    }

    public function getFanpageManager(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');

        $filters = [];
        if (!empty($search)) {
            $escapedSearch = preg_quote($search, '/');

            $filters['$or'] = [
                ['page_name' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['uid_controler' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['page_id' => ['$regex' => $escapedSearch, '$options' => 'i']],
                ['SourceControl' => ['$regex' => $escapedSearch, '$options' => 'i']],

            ];
        }

        $perPage = $request->input('per_page', 100);

        $result = $this->fanpageManagerRepository->searchFanpages($filters, $perPage, $page);

        return response()->json($result);
    }

    public function editPage($id)
    {
        $fanpage = $this->fanpageManagerRepository->findById($id);

        $uids = is_array($fanpage->uid_controler) ? $fanpage->uid_controler : [$fanpage->uid_controler];

        $friendsData = $this->accountRepository->getMultipleFriendsData($uids);
        $groupsData = $this->accountRepository->getMultipleGroupsData($uids);


        // Chuyển về phần tử đầu tiên nếu có dữ liệu
        $friendsData = reset($friendsData)->friends ?? null;




        return view('admin.pages.Facebook.Fanpage.fanpage_edit', compact('fanpage', 'friendsData', 'groupsData'));
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

    public function allDelete(Request $request)
    {
        $deleteAll = $request->input('deleteAll', false);
        $ids = $request->input('ids', []);

        if ($deleteAll) {
            // Xóa tất cả các bản ghi trong collection
            $this->fanpageManagerRepository->deleteMany([]);
            return response()->json(['message' => 'Đã xóa toàn bộ dữ liệu thành công']);
        } elseif (!empty($ids)) {
            $objectIds = array_map(function ($id) {
                return new ObjectId($id);
            }, $ids);

            if (count($objectIds) > 0) {
                $this->fanpageManagerRepository->deleteMany(['_id' => ['$in' => $objectIds]]);
                return response()->json(['message' => 'Đã xóa các mục đã chọn thành công']);
            }
        }

        return response()->json(['message' => 'Không có mục nào được chọn'], 400);
    }
}
