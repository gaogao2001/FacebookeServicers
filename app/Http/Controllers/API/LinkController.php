<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ContentManager\Link\LinkRepositoryInterface;
use App\Repositories\Account\AccountRepositoryInterface;
use MongoDB\BSON\ObjectId;

class LinkController extends Controller
{
    protected $linkRepository;
    protected $accountRepository;

    public function __construct(LinkRepositoryInterface $linkRepository, AccountRepositoryInterface $accountRepository)
    {
        $this->linkRepository = $linkRepository;
        $this->accountRepository = $accountRepository;
    }

    // Thêm mới URL Facebook
    public function addNewUrlFacebook(Request $request)
    {
        // Lấy token từ header Authorization
        $token = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $token); // Loại bỏ "Bearer " nếu có

        // Xác thực người dùng bằng token
        $user = $this->accountRepository->findByToken($token);
        if (!$user) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Không xác thực được người dùng'
                ]
            ], 401);
        }

        // Lấy dữ liệu từ body
        $body = $request->all();

        // Kiểm tra URL có hợp lệ không
        if (empty($body['url'])) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'URL không được để trống'
                ]
            ], 400);
        }

        // Xử lý URL
        $url = trim($body['url']);
        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';

        // Chỉ loại bỏ phần sau dấu ? nếu domain không chứa "facebook.com"
        if (strpos($domain, 'facebook.com') === false) {
            $url = strtok($url, '?');
        }

        $urlMd5 = md5($url);

        // Chuẩn bị dữ liệu để lưu
        $result = [
            'domain' => $domain,
            'url' => $url,
            'md5' => $urlMd5,
            'user_id' => new ObjectId($user['_id']),
        ];

        // Kiểm tra URL đã tồn tại chưa
        if ($this->linkRepository->findByMd5($urlMd5)) {
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Link đã tồn tại'
                ]
            ], 409);
        }

        // Tạo mới URL
        $this->linkRepository->create($result);

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Thêm mới thành công!'
            ]
        ]);
    }
}
