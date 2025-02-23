<?php

namespace App\Modules\Role\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Roles\RoleRepositoryInterface;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        $roles = $this->roleRepository->findAll();
        return response()->json($roles);
    }

    public function addRole(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $data = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'menu' => ['required', 'array'],
        ]);

        // Chỉ lưu trữ các URL của menu
        $data['menu'] = $request->input('menu');

        $role = $this->roleRepository->create($data);

        return response()->json($role);
    }

    public function show($id)
    {
        $role = $this->roleRepository->findById($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 200);
        }

        return response()->json($role);
    }

    public function update(Request $request, $id)
    {
        // Kiểm tra dữ liệu đầu vào
        $data = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'menu' => ['required', 'array'],
        ]);

        // Chỉ lưu trữ các URL của menu
        $data['menu'] = $request->input('menu');

        $role = $this->roleRepository->update($id, $data);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 200);
        }

        return response()->json($role);
    }

    public function delete($id)
    {
        $role = $this->roleRepository->delete($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 200);
        }

        return response()->json(['message' => 'Role deleted successfully']);
    }
}