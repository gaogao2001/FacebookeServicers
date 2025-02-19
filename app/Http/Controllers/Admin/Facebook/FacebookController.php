<?php

namespace App\Http\Controllers\Admin\Facebook;

use App\Http\Controllers\Controller;
use App\Repositories\Facebook\Account\AccountRepositoryInterface;
use Illuminate\Http\Request;
use HoangquyIT\ModelFacebook\FacebookApi;
use Illuminate\Support\Facades\Validator;


class FacebookController extends Controller
{
    protected $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function facebookPage()
    {
        $totalAccounts = $this->accountRepository->countAccounts([]);
        $dieAccounts = $this->accountRepository->countAccounts(['status' => 'CHECKPOINT']);
        $liveAccounts = $this->accountRepository->countAccounts(['status' => 'LIVE']);
        $kxdAccounts = $this->accountRepository->countAccounts(['status' => 'KXD']);



        $page = 1;
        $perPage = 1000;
        $paginationData = $this->accountRepository->searchAccounts([], $perPage, $page);

        $accounts = $paginationData['data'];

        // Lấy danh sách UID
        $uids = array_map(function ($account) {
            return $account->uid;
        }, $accounts);

        // Lấy toàn bộ dữ liệu friends và groups
        $friendsData = $this->accountRepository->getMultipleFriendsData($uids);
        $groupsData = $this->accountRepository->getMultipleGroupsData($uids);
        $historyData = $this->accountRepository->getHistoryData($uids);

        // Ghép dữ liệu friends và groups vào từng tài khoản
        foreach ($accounts as $account) {
            $uid = $account->uid;

            // Kiểm tra và gán dữ liệu friends nếu có
            if (isset($friendsData[$uid]->friends) && is_object($friendsData[$uid]->friends)) {
                $friends = $friendsData[$uid]->friends;

                // Đảm bảo rằng 'count' và 'friends_list' tồn tại
                $count = isset($friends->count) ? $friends->count : 0;
                $friends_list = isset($friends->friends_list) ? $friends->friends_list : [];

                $account->friends = (object)[
                    'count' => $count,
                    'friends_list' => $friends_list
                ];
            } else {
                $account->friends = (object)[
                    'count' => 0,
                    'friends_list' => []
                ];
            }

            // Kiểm tra và gán dữ liệu groups nếu có
            if (isset($groupsData[$uid]->groups) && is_object($groupsData[$uid]->groups)) {
                $groups = $groupsData[$uid]->groups;

                // Đảm bảo rằng 'count' và 'groups_list' tồn tại
                $count = isset($groups->count) ? $groups->count : 0;
                $groups_list = isset($groups->groups_list) ? $groups->groups_list : [];

                $account->groups = (object)[
                    'count' => $count,
                    'groups_list' => $groups_list
                ];
            } else {
                $account->groups = (object)[
                    'count' => 0,
                    'groups_list' => []
                ];
            }
            // Kiểm tra và gán dữ liệu history nếu có
            if (isset($historyData[$uid]) && is_array($historyData[$uid])) {
                $account->history = $historyData[$uid];
            } else {
                $account->history = [];
            }
        }

        $accountGroups = $this->accountRepository->getUniqueGroupAccounts();

        // Kiểm tra dữ liệu sau khi gán
        return view('admin.pages.Facebook.facebook_page', [
            'accounts' => $accounts,
            'currentPage' => $paginationData['currentPage'],
            'lastPage' => $paginationData['lastPage'],
            'totalAccounts' => $totalAccounts,
            'dieAccounts' => $dieAccounts,
            'liveAccounts' => $liveAccounts,
            'kxdAccounts' => $kxdAccounts,
            'accountGroups' => $accountGroups
        ]);
    }

    public function loadMoreAccounts(Request $request)
    {
        $page = (int) $request->get('page', 1); // Trang chính
        $perPage = 1000; // Số lượng bản ghi trên mỗi trang chính

        if ($page < 1) {
            return response()->json(['data' => [], 'message' => 'Invalid page number'], 400);
        }

        $paginationData = $this->accountRepository->searchAccounts([], $perPage, $page);

        if (empty($paginationData['data'])) {
            return response()->json(['data' => [], 'message' => 'No more data'], 200);
        }

        return response()->json([
            'data' => $paginationData['data'],
            'currentPage' => $page,
            'lastPage' => $paginationData['lastPage'],
            'totalRecords' => $paginationData['total'], // Tổng số bản ghi
        ], 200);
    }

    public function show($id)
    {
        $accounts = $this->accountRepository->findById($id);

        if (!$accounts) {
            return redirect()->back()->with('error', 'Không tìm thấy tài khoản.');
        }

        $friendsData = $this->accountRepository->getMultipleFriendsData([$accounts->uid]);
        $groupsData = $this->accountRepository->getMultipleGroupsData([$accounts->uid]);
        $historyData = $this->accountRepository->getHistoryData([$accounts->uid]);


        // Gắn dữ liệu friends
        if (isset($friendsData[$accounts->uid]->friends) && is_object($friendsData[$accounts->uid]->friends)) {
            $friends = $friendsData[$accounts->uid]->friends;

            // Đảm bảo rằng 'count' và 'friends_list' tồn tại
            $count = isset($friends->count) ? $friends->count : 0;
            $friends_list = isset($friends->friends_list) ? $friends->friends_list : [];

            $accounts->friends = (object)[
                'count' => $count,
                'friends_list' => $friends_list
            ];
        } else {
            $accounts->friends = (object)[
                'count' => 0,
                'friends_list' => []
            ];
        }

        if (isset($groupsData[$accounts->uid]->groups_list) && is_array($groupsData[$accounts->uid]->groups_list)) {
            $groups_list = $groupsData[$accounts->uid]->groups_list;
            $count = count($groups_list);

            $accounts->groups = (object)[
                'count' => $count,
                'groups_list' => $groups_list
            ];
        } else {
            $accounts->groups = (object)[
                'count' => 0,
                'groups_list' => []
            ];
        }

        // Gắn dữ liệu history
        if (isset($historyData[$accounts->uid]) && is_array($historyData[$accounts->uid])) {
            $accounts->history = $historyData[$accounts->uid];
        } else {
            $accounts->history = [];
        }

        return view('admin.pages.Facebook.facebook_edit', compact('accounts'));
    }

    public function showJson($id)
    {
        $accounts = $this->accountRepository->findById($id);

        return response()->json($accounts);
    }

    public function update(Request $request, $id)
    {
        $data = $request->except('_token');

        $account = $this->accountRepository->findById($id);
        if (!$account) {
            return redirect()->route('facebook.edit', $id)->with('error', 'Không tìm thấy tài khoản.');
        }

        // Cập nhật dữ liệu, tự động thêm trường mới nếu không tồn tại
        foreach ($data as $key => $value) {
            // Nếu giá trị không rỗng, cập nhật
            if (!is_null($value)) {
                $account->$key = $value;
            }
        }

        $updateResult = $this->accountRepository->update($id, $data);

        if ($updateResult) {
            return redirect()->route('facebook.edit', $id)->with('success', 'Cập nhật tài khoản thành công.');
        }

        return redirect()->route('facebook.edit', $id)->with('error', 'Cập nhật tài khoản thất bại.');
    }

    public function destroy($id)
    {
        $deleteResult = $this->accountRepository->delete($id);

        if ($deleteResult) {
            return redirect()->route('facebook.pages')->with('success', 'Xóa tài khoản thành công.');
        }

        return redirect()->route('facebook.pages')->with('error', 'Xóa tài khoản thất bại.');
    }

    public function searchAccounts(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 50);
        $page = $request->input('page', 1);

        $filters = [];

        if ($search) {
            $regex = new \MongoDB\BSON\Regex($search, 'i');
            $filters = [
                '$or' => [
                    ['uid' => $regex],
                    ['fullname' => $regex],
                    ['email' => $regex],
                    ['status' => $regex],
                    // Thêm các trường khác bạn muốn tìm kiếm
                ]
            ];
        }

        $accounts = $this->accountRepository->searchAccounts($filters, $perPage, $page);

        return response()->json($accounts);
    }

    ///check live
    public function checkLiveUid($uid)
    {
        if (!empty($uid)) {
            $FbSdk = new FacebookApi();
            $resultCheck = $FbSdk->checkFacebookUID($uid);

            // Kiểm tra kết quả trả về
            if (in_array($resultCheck, ['LIVE', 'CHECKPOINT'])) {
                // Tìm tài khoản theo UID thông qua Repository
                $existingAccount = $this->accountRepository->findByUid($uid);

                if ($existingAccount) {
                    // Cập nhật trạng thái tài khoản thông qua Repository
                    $this->accountRepository->updateStatus($uid, $resultCheck);
                }

                return response()->json([
                    'response' => [
                        'status' => true,
                        'message' => $resultCheck,
                        'uid' => $uid,
                    ]
                ], 200);
            }

            return response()->json([
                'response' => [
                    'status' => false,
                    'message' => 'UID không hợp lệ hoặc không kiểm tra được.',
                    'uid' => $uid,
                ]
            ], 200);
        }

        return response()->json([
            'response' => [
                'status' => false,
                'message' => 'UID không được cung cấp.'
            ]
        ], 400);
    }

    public function loadAllFacebook()
    {
        $uids = $this->accountRepository->getAllUids();

        if (!empty($uids)) {
            return response()->json([
                'response' => [
                    'status' => true,
                    'uids' => $uids,
                    'message' => 'Tải UID thành công.'
                ]
            ], 200);
        }

        return response()->json([
            'response' => [
                'status' => false,
                'message' => 'Không tìm thấy UID nào.'
            ]
        ], 404);
    }

    public function sendAccounts(Request $request)
    {
        // Bước 1: Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'target_url' => 'required|url',
            'selected_accounts' => 'array',
            'selected_accounts.*' => 'string', // UID là chuỗi
            'group_account' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ]);
        }

        $targetUrl = $request->input('target_url');
        $selectedAccounts = $request->input('selected_accounts', []);
        $groupAccount = $request->input('group_account');

        if (empty($selectedAccounts) && empty($groupAccount)) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng chọn tài khoản hoặc chọn nhóm tài khoản.'
            ]);
        }

        if (!empty($groupAccount)) {
            $selectedAccounts = $this->accountRepository->findByGroup($groupAccount);

            if (empty($selectedAccounts)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy tài khoản nào thuộc nhóm đã chọn.'
                ]);
            }
        }

        $accountsData = $this->accountRepository->findByUids($selectedAccounts);

        if (empty($accountsData)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tài khoản nào phù hợp.'
            ]);
        }

        // Bước 3: Tạo phản hồi JSON
        return response()->json([
            'status' => true,
            'data' => $accountsData,
        ], 200);
    }


    public function changeAccountGroup(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'account_group' => 'required|string',
            'selected_accounts' => 'required|array',
            'selected_accounts.*' => 'string' // Giả sử UID là chuỗi
        ]);

        $newGroup = $request->input('account_group');
        $selectedAccounts = $request->input('selected_accounts');

        // Cập nhật trường groups_account cho các tài khoản đã chọn
        $updateResult = $this->accountRepository->updateManyByUids($selectedAccounts, ['groups_account' => $newGroup]);

        if ($updateResult) {
            return response()->json([
                'status' => 'success',
                'message' => 'Đã cập nhật nhóm tài khoản thành công.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Cập nhật nhóm tài khoản thất bại.'
            ]);
        }
    }
}
