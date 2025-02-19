<?php

namespace App\Modules\Link\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Account\AccountRepository;
use App\Repositories\Account\AccountRepositoryInterface;
use App\Repositories\Roles\RoleRepository;
use App\Repositories\Roles\RoleRepositoryInterface;
use Illuminate\Http\Request;
use App\Services\AccountService;
use Illuminate\Support\Facades\Session;
use MongoDB\BSON\ObjectId;
use App\Modules\Link\Repositories\LinkRepositoryInterface;
use HoangquyIT\ModelFacebook\FacebookFind;


class LinkController extends Controller
{
    protected $linkRepository;
    protected $accountRepository;
    protected $rolesRepository;

    public function __construct(LinkRepositoryInterface $linkRepository, AccountRepositoryInterface $accountRepository, RoleRepository $rolesRepository)
    {
        $this->linkRepository = $linkRepository;
        $this->accountRepository = $accountRepository;
        $this->rolesRepository = $rolesRepository;
    }


    // Hiển thị giao diện quản lý liên kết
    public function index()
    {
        // Lấy thông tin người dùng từ session
        $user = Session::get('account');

        return view('Link::link', compact('user'));
    }

    // Lấy danh sách liên kết
    public function getLinks(Request $request)
    {
        // Lấy thông tin người dùng từ session
        $user = Session::get('account');
        if (!$user) {
            return response()->json(['error' => 'Không xác thực người dùng'], 200);
        }

        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 100);


        //lấy bừa 1 id miễn nó là admin thì dc
        $adminIdRoles = $this->rolesRepository->findOneAdmin();
        if (!$adminIdRoles) {
            return response()->json(['error' => 'Không tìm thấy bất kỳ nhóm admin nào'], 200);
        }
        // Lấy user_id của admin
        $admin = $this->accountRepository->findAdmin($adminIdRoles->_id);

        if (!$admin) {
            return response()->json(['error' => 'Không tìm thấy admin'], 200);
        }

        // if (isset($user['role']) && $user['role'] === 'Admin') {
        //     // Người dùng là Admin, lấy tất cả liên kết
        //     $links = $this->linkRepository->findAll();
        // } else {
        //     // Người dùng không phải Admin, chỉ lấy các liên kết mà họ đã thêm
        //     $links = $this->linkRepository->findByUserId($user['id']);
        // }

        // foreach ($links as &$link) {
        //     if (!isset($link['user_id'])) {
        //         $link['user_id'] = $admin['_id']; // Gắn user_id của admin
        //         $this->linkRepository->update($link['_id'], ['user_id' => $admin['_id']]);
        //     }

        //     if (!isset($link['facebook_uid'])) {
        //         $link['facebook_uid'] = null;
        //     }

        //     // Thêm thông tin người thêm liên kết
        //     $userData = $this->accountRepository->findById($link['user_id']);
        //     $link['added_by'] = $userData['email'] ?? 'Không xác định';
        // }

        if (isset($user['role']) && $user['role'] === 'Admin') {
            // Người dùng là Admin, không áp dụng bộ lọc
            $filters = [];
        } else {
            // Người dùng không phải Admin, chỉ lấy các liên kết mà họ đã thêm
            $filters = ['user_id' => (string) $user['id']];
        }

        if (isset($user['role']) && $user['role'] === 'Admin') {
            $linksPaginated = $this->linkRepository->findAllPaginated($perPage, $page, $filters);
        } else {
            $linksPaginated = $this->linkRepository->findByUserIdPaginated((string) $user['id'], $perPage, $page);
        }

        foreach ($linksPaginated['data'] as &$link) {
            if (!isset($link['user_id'])) {
                $link['user_id'] = (string) $admin->_id; // Gắn user_id của admin
                $this->linkRepository->update($link['_id'], ['user_id' => (string) $admin->_id]);
            }

            if (!isset($link['facebook_uid'])) {
                $link['facebook_uid'] = null;
            }

            // Thêm thông tin người thêm liên kết
            $userData = $this->accountRepository->findById($link['user_id']);
            $link['added_by'] = $userData->email ?? 'Không xác định';
        }


        // Kiểm tra và cập nhật user_id nếu thiếu


        return response()->json($linksPaginated);
    }

    public function addNewUrlFacebook(Request $request)
    {
        // Lấy thông tin người dùng từ session
        $user = Session::get('account');
        if (!$user) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không xác thực người dùng'
                ]
            ], 200);
        }

        $body = $request->all();

        if (empty($body['url'])) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'URL không được để trống'
                ]
            ], 200);
        }

        // Lấy URL từ yêu cầu
        $url = trim($body['url']);
        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';

        // Loại bỏ phần sau dấu ? nếu domain không chứa "facebook.com", "tiktok.com" hoặc "instagram.com"
        if (strpos($domain, 'facebook.com') === false && strpos($domain, 'tiktok.com') === false && strpos($domain, 'instagram.com') === false) {
            $url = strtok($url, '?');
        }

        $urlMd5 = md5($url);

        if (strpos($domain, 'tiktok.com') !== false) {
            $result = [
                'domain' => $domain,
                'url' => $url,
                'md5' => $urlMd5,
                'tiktok_uid' => null, // Có thể tìm UID TikTok sau này
                'user_id' => new ObjectId($user['id']),
                'use_status' => false,
            ];

            if (empty($result['user_id'])) {
                // Lấy user ID admin nếu không có user ID
                $result['user_id'] = null;
            }

            if ($this->linkRepository->findByMd5($urlMd5)) {
                return response()->json([
                    'data' => [
                        'status' => false,
                        'message' => 'Link đã tồn tại'
                    ]
                ], 409);
            }

            // Lưu dữ liệu vào repository
            $this->linkRepository->create($result);

            return response()->json([
                'data' => [
                    'status' => true,
                    'message' => 'URL TikTok đã được thêm vào cơ sở dữ liệu.',
                    'tiktok_uid' => $result['tiktok_uid'],
                ]
            ]);
        }

        if (strpos($domain, 'instagram.com') !== false) {
            $result = [
                'domain' => $domain,
                'url' => $url,
                'md5' => $urlMd5,
                'instagram_uid' => null, // Có thể tìm UID Instagram sau này
                'user_id' => new ObjectId($user['id']),
                'use_status' => false,
            ];

            if (empty($result['user_id'])) {
                // Lấy user ID admin nếu không có user ID
                $result['user_id'] = null;
            }

            if ($this->linkRepository->findByMd5($urlMd5)) {
                return response()->json([
                    'data' => [
                        'status' => false,
                        'message' => 'Link đã tồn tại'
                    ]
                ], 409);
            }

            // Lưu dữ liệu vào repository
            $this->linkRepository->create($result);

            return response()->json([
                'data' => [
                    'status' => true,
                    'message' => 'URL Instagram đã được thêm vào cơ sở dữ liệu.',
                    'instagram_uid' => $result['instagram_uid'],
                ]
            ]);
        }

        // Lấy UID từ URL bằng FacebookFind
        $FindUid = new FacebookFind($url);
        $facebookUid = $FindUid->GetFacebookID();

        // Nếu không lấy được UID, vẫn tiếp tục thêm URL vào database
        if (!$facebookUid) {
            $facebookUid = null;
            // Chuẩn bị dữ liệu để lưu
            $result = [
                'domain' => $domain,
                'url' => $url,
                'md5' => $urlMd5,
                'facebook_uid' => $facebookUid,
                'user_id' => new ObjectId($user['id']),
                'use_status' => false,
            ];

            if (empty($result['user_id'])) {
                // Lấy user ID admin nếu không có user ID
                $result['user_id'] = null;
            }

            if ($this->linkRepository->findByMd5($urlMd5)) {
                return response()->json([
                    'data' => [
                        'status' => false,
                        'message' => 'Link đã tồn tại'
                    ]
                ], 409);
            }

            // Lưu dữ liệu vào repository
            $this->linkRepository->create($result);

            return response()->json([
                'data' => [
                    'status' => true,
                    'message' => 'Không thể lấy UID từ URL. URL đã được thêm vào cơ sở dữ liệu.',
                    'facebook_uid' => $facebookUid,
                ]
            ]);
        }

        // Chuẩn bị dữ liệu để lưu khi có UID
        $result = [
            'domain' => $domain,
            'url' => $url,
            'md5' => $urlMd5,
            'facebook_uid' => $facebookUid,
            'user_id' => new ObjectId($user['id']),
            'use_status' => false,
        ];

        if (empty($result['user_id'])) {
            // Lấy user ID admin nếu không có user ID
            $result['user_id'] = null;
        }

        // Kiểm tra xem URL đã tồn tại chưa
        if ($this->linkRepository->findByMd5($urlMd5)) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Link đã tồn tại'
                ]
            ], 409);
        }

        // Lưu dữ liệu vào repository
        $this->linkRepository->create($result);

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Thêm mới thành công!',
                'facebook_uid' => $facebookUid,
            ]
        ]);
    }


    // Hiển thị thông tin liên kết
    public function showLink($id)
    {
        $link = $this->linkRepository->findById($id);

        if (!$link) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không tìm thấy liên kết'
                ]
            ], 404);
        }

        // Lấy thông tin người dùng từ session
        $user = Session::get('account');

        if (!$user) {
            return response()->json(['error' => 'Không xác thực người dùng'], 200);
        }

        // Kiểm tra quyền xem liên kết
        if (
            !(isset($user['role']) && $user['role'] === 'Admin') &&
            (string)$link['user_id'] !== $user['id']
        ) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không có quyền xem liên kết này'
                ]
            ], 403);
        }

        return response()->json($link);
    }

    // Cập nhật liên kết
    public function updateLink(Request $request, $id)
    {
        // Lấy thông tin người dùng từ session
        $user = Session::get('account');

        if (!$user) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không xác thực người dùng'
                ]
            ], 200);
        }

        $link = $this->linkRepository->findById($id);

        if (!$link) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không tìm thấy liên kết'
                ]
            ], 404);
        }

        // Kiểm tra quyền sở hữu hoặc là Admin
        if (
            !(isset($user['role']) && $user['role'] === 'Admin') &&
            (string)$link['user_id'] !== $user['id']
        ) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không có quyền cập nhật'
                ]
            ], 403);
        }

        $body = $request->all();

        if (empty($body['url'])) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'URL không được để trống'
                ]
            ], 200);
        }

        // Lấy URL từ yêu cầu
        $url = trim($body['url']);
        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';

        // Chỉ loại bỏ phần sau dấu ? nếu domain không chứa "facebook.com"
        if (strpos($domain, 'facebook.com') === false) {
            $url = strtok($url, '?');
        }

        $urlMd5 = md5($url);

        // Phân loại URL và tạo mảng với domain và url
        $result = [
            'domain' => $domain,
            'url' => $url,
            'md5' => $urlMd5
        ];

        // Kiểm tra xem URL đã tồn tại chưa (ngoại trừ URL hiện tại)
        $existingLink = $this->linkRepository->findByMd5($urlMd5);
        if ($existingLink && (string)$existingLink['_id'] !== $id) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Liên kết đã tồn tại'
                ]
            ], 409);
        }

        // Cập nhật URL
        $this->linkRepository->update($id, $result);

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Cập nhật thành công!'
            ]
        ]);
    }

    // Xóa liên kết
    public function deleteLink($id)
    {
        // Lấy thông tin người dùng từ session
        $user = Session::get('account');

        if (!$user) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không xác thực người dùng'
                ]
            ], 200);
        }

        $link = $this->linkRepository->findById($id);

        if (!$link) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không tìm thấy liên kết'
                ]
            ], 404);
        }

        // Kiểm tra quyền sở hữu hoặc là Admin
        if (
            !(isset($user['role']) && $user['role'] === 'Admin') &&
            (string)$link['user_id'] !== $user['id']
        ) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không có quyền xoá'
                ]
            ], 403);
        }

        $this->linkRepository->delete($id);

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Xóa thành công!'
            ]
        ]);
    }
}
