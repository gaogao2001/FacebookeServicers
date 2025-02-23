<?php

namespace App\Http\Controllers\Admin\Zalo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Zalo\ZaloRepositoryInterface;

class ZaloController extends Controller
{
    protected $zaloRepository;

    //BỔ SUNG SAU (Thêm , xóa , tìm kiếm)

    public function __construct(ZaloRepositoryInterface $zaloRepository)
    {
        $this->zaloRepository = $zaloRepository;
    }

    public function zaloPage()
    {
        $zalo = $this->zaloRepository->findAll();
        return view('admin.pages.Zalo.zalo', compact('zalo'));
    }

   

    public function show($id)
    {
        $zalo = $this->zaloRepository->findById($id);

        return view('admin.pages.Zalo.zalo_edit', compact('zalo'));
    }

    public function update(Request $request, $id)
    {
        // Validate input data
        $validatedData = $request->validate([
            'password' => 'nullable|string|max:255',
            'user_agent' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'zpw_enk' => 'nullable|string|max:255',
            'networkuse.type' => 'nullable|string|max:50',
            'networkuse.ip' => 'nullable|ip',
            'networkuse_port' => 'nullable|integer',
            'z_uuid' => 'nullable|string|max:255',
            'cookies' => 'nullable|string',
            'useAccount' => 'nullable',
            'note' => 'nullable|string',
        ]);

        // Prepare data for update
        $data = [
            'password' => $validatedData['password'],
            'userangent' => $validatedData['user_agent'], // Correct mapping
            'phone' => $validatedData['phone'],
            'zpw_enk' => $validatedData['zpw_enk'],
            'networkuse' => [
                'type' => $validatedData['networkuse']['type'] ?? null,
                'ip' => $validatedData['networkuse']['ip'] ?? null,
                'port' => $validatedData['networkuse_port'] ?? null,
            ],
            'z_uuid' => $validatedData['z_uuid'],
            'cookies' => $validatedData['cookies'],
            'useAccount' => $validatedData['useAccount'] ?? null,
            'note' => $validatedData['note'],
        ];

        // Perform update
        $updateResult = $this->zaloRepository->update($id, $data);

        if ($updateResult->getModifiedCount() > 0) {
            return redirect()->route('zalos.edit', $id)->with('success', 'Cập nhật dữ liệu thành công.');
        }

        return redirect()->route('zalos.edit', $id)->with('error', 'Không có thay đổi nào được thực hiện.');
    }
}