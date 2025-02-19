<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Account\AccountRepositoryInterface;
use App\Repositories\Roles\RoleRepositoryInterface;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $accountRepository;
    protected $roleRepository;

    public function __construct(AccountRepositoryInterface $accountRepository, RoleRepositoryInterface $roleRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        // Tải danh sách User và Role Name
        $users = $this->accountRepository->findAll();

        foreach ($users as &$user) {
            // Tìm tên role từ collection roles
            $role = $this->roleRepository->findById(new ObjectId($user['role'] ?? ''));
            $user['role_name'] = $role['name'] ?? 'Unknown';
        }

        return response()->json($users);
    }

    public function show($id)
    {
        $user = $this->accountRepository->findById($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Thêm tên Role vào User
        $role = $this->roleRepository->findById(new ObjectId($user['role'] ?? ''));
        $user['role_name'] = $role['name'] ?? 'Unknown';

        return response()->json($user);
    }

    public function addUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'role' => ['required', 'string'],
        ]);

        // Kiểm tra Role tồn tại
        if (!$this->roleRepository->findById(new ObjectId($data['role']))) {
            return response()->json(['errors' => ['role' => 'Role không hợp lệ']], 422);
        }

        // Kiểm tra Email đã tồn tại
        if ($this->accountRepository->findByEmail($data['email'])) {
            return response()->json(['errors' => ['email' => 'Email này đã tồn tại']], 422);
        }

        $data = $this->prepareUserData($data);

        $user = $this->accountRepository->create($data);

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'role' => ['required', 'string'],
        ]);

        // Kiểm tra Role tồn tại
        if (!$this->roleRepository->findById(new ObjectId($data['role']))) {
            return response()->json(['errors' => ['role' => 'Role không hợp lệ']], 422);
        }

        $data = $this->prepareUserData($data, false); // Không cập nhật create_time

        $user = $this->accountRepository->update($id, $data);

        return response()->json($user);
    }

    public function delete($id)
    {
        $this->accountRepository->delete($id);

        return response()->json(['message' => 'Xóa người dùng thành công.']);
    }

    private function prepareUserData(array $data, $isNew = true)
    {
        // Mã hóa mật khẩu
        $data['password'] = md5(trim($data['password']));

        // Chuyển role thành ObjectId
        $data['role'] = new ObjectId($data['role']);

        // Thời gian tạo/cập nhật
        $now = new UTCDateTime();
        $data['update_time'] = $now;
        if ($isNew) {
            $data['create_time'] = $now;
        }

        // Tạo token duy nhất
        $secretKey = env('HMAC_SECRET_KEY', 'default_secret');
        $data['token'] = hash_hmac('sha256', $data['email'] . $now, $secretKey);

        return $data;
    }


    public function showProfile()
    {
        $userSession = session('account'); // Lấy thông tin người dùng từ session
        if (!$userSession) {
            return redirect('/login')->withErrors(['error' => 'Vui lòng đăng nhập để tiếp tục.']);
        }
        // Lấy thông tin tài khoản từ database
        $user = app(AccountRepositoryInterface::class)->findById($userSession['id']);
        if (!$user) {
            return redirect()->route('profile')->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }
        // Lấy tên quyền hạn từ RoleRepository
        $role = app(RoleRepositoryInterface::class)->findById($user['role']);
        $user['role_name'] = $role['name'] ?? 'Unknown'; // Gắn tên quyền hạn vào $user

        return view('admin.pages.user_profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = session('account'); // Lấy thông tin người dùng từ session

        if (!$user) {
            return redirect('/login')->withErrors(['error' => 'Vui lòng đăng nhập để tiếp tục.']);
        }

        // Lấy thông tin từ repository bằng id từ session
        $account = app(AccountRepositoryInterface::class)->findById($user['id']);

        if (!$account) {
            return redirect()->route('profile')->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'token' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Chuẩn bị dữ liệu cập nhật
        $updateData = [
            'name' => $request->name,
            'token' => $request->token,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = md5(trim($request->password)); // Hoặc sử dụng bcrypt
        }

        // Cập nhật dữ liệu vào database
        app(AccountRepositoryInterface::class)->update($account['_id'], $updateData);

        // Cập nhật session
        session([
            'account.name' => $updateData['name'],
            'account.token' => $updateData['token'],
        ]);

        return redirect()->route('profile')->with('success', 'Cập nhật thông tin thành công.');
    }
}
