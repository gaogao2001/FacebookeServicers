<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Repositories\Account\AccountRepositoryInterface;
use App\Repositories\Roles\RoleRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class LoginController extends Controller
{
    protected $accountRepository;
    protected $roleRepository;

    public function __construct(AccountRepositoryInterface $accountRepository, RoleRepositoryInterface $roleRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->roleRepository = $roleRepository;
    }

    public function showFormLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $account = $this->accountRepository->findByEmail($credentials['email']);
        $hashedPassword = md5(trim($credentials['password']));

        if ($account && $hashedPassword === $account['password']) {
            $roleId = (string) ($account['role'] ?? '');
            $role = $this->roleRepository->findById($roleId);

            if (!$role) {
                return back()->withErrors(['role' => 'Role không tồn tại.']);
            }

            // Lấy danh sách menu từ role
            $allowedRoutes = $role['menu'] ?? [];
            if ($allowedRoutes instanceof \HoangquyIT\MongoDB\Model\BSONArray) {
                $allowedRoutes = $allowedRoutes->getArrayCopy();
            }

            // Lấy cấu hình menu từ menus.php
            $menusConfig = config('menus');

            // Tạo danh sách menu được phép truy cập
            $menus = [];
            foreach ($menusConfig as $menuName => $menuInfo) {
                if (isset($menuInfo['children'])) {
                    // Kiểm tra các menu con
                    $children = [];
                    foreach ($menuInfo['children'] as $childName => $childInfo) {
                        if (in_array($childInfo['url'], $allowedRoutes)) {
                            $children[$childName] = $childInfo;
                        }
                    }
                    if (!empty($children)) {
                        $menuInfo['children'] = $children;
                        $menus[$menuName] = $menuInfo;
                    }
                } else {
                    if (in_array($menuInfo['url'], $allowedRoutes)) {
                        $menus[$menuName] = $menuInfo;
                    }
                }
            }

            // Lưu thông tin tài khoản và quyền vào session
            session(['account' => [
                'id' => (string) $account['_id'],
                'name' => $account['name'],
                'email' => $account['email'],
                'role' => $role['name'],
                'menu' => $menus, // Lưu các menu được phép truy cập
            ]]);

            $defaultRedirect = '/login';

            if (!empty($menus)) {
                foreach ($menus as $menu) {
                    if (isset($menu['url'])) {
                        $defaultRedirect = $menu['url'];
                        break;
                    } elseif (isset($menu['children'])) {
                        // Tìm URL đầu tiên trong các children
                        foreach ($menu['children'] as $child) {
                            if (isset($child['url'])) {
                                $defaultRedirect = $child['url'];
                                break 2; // Thoát cả vòng lặp ngoài
                            }
                        }
                    }
                }
            }

            return redirect()->intended($defaultRedirect);
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng']);
    }



    public function showFormRegister()
    {
        return view('admin.auth.register');
    }

    private function getAllMenuUrls($menus)
    {
        $urls = [];
        foreach ($menus as $menu) {
            if (isset($menu['url'])) {
                $urls[] = $menu['url'];
            }
            if (isset($menu['children'])) {
                $urls = array_merge($urls, $this->getAllMenuUrls($menu['children']));
            }
        }
        
        return $urls;
    }


    public function register(Request $request)
    {
        // Kiểm tra xem có tài khoản nào tồn tại không
        $existingAccounts = $this->accountRepository->findAll();
        if (count($existingAccounts) > 0) { // Sử dụng count() để kiểm tra
            return redirect('/login')->withErrors(['error' => 'Đăng ký đã bị đóng. Đã tồn tại tài khoản admin.']);
        }

        // Xác thực đầu vào
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:accounts,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);
        // kiểm tra coi có quyền admin nào chưa
        $createdRoleId = $this->roleRepository->findOneAdmin();
        if (!$createdRoleId) {
            // chưa có thì tạo mới
            // Tạo vai trò Admin với tất cả các menu
            $menus = Config::get('menus');

            $menuUrls = $this->getAllMenuUrls($menus);


            $roleData = [
                'name' => 'Admin',
                'description' => 'Quản trị viên với quyền truy cập đầy đủ',
                'menu' => $menuUrls,
            ];

            $createdRole = $this->roleRepository->create($roleData); // Lưu trữ vai trò vừa tạo
            $createdRoleId = $createdRole->getInsertedId();
        } else {
            $createdRoleId = $createdRoleId->_id;
        }

        // Thời gian tạo/cập nhật
        $now = time(); // Lấy thời gian hiện tại
        $secretKey = env('HMAC_SECRET_KEY', 'default_secret'); // Lấy khóa bí mật từ .env
        $token = hash_hmac('sha256', $data['email'] . $now, $secretKey); // Tạo token


        // Tạo tài khoản Admin
        $accountData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => md5(trim($data['password'])), // Giữ nguyên MD5 như yêu cầu
            'role' => $createdRoleId,
            'token' => $token,
        ];
        $this->accountRepository->create($accountData);

        return redirect('/login')->with('success', 'Đăng ký Admin thành công');
    }


    public function logout()
    {
        session()->forget('account');
        return redirect('/login');
    }
}
