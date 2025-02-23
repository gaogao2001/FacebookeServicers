<?php
// FILE: ContentManagerController.php

namespace App\Modules\ContentManager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\ContentManager\Repositories\ContentManagerRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContentManagerController extends Controller
{
    // HIỆN TẠI HÌNH MẢNG HÌNH ẢNH ĐANG LƯU DƯỚI DẠNG JSON_encode trong database ; 
    // sau này sẽ tahy đổi lại k lưu dưới dang json_encode nữa

    protected $contentManagerRepository;
    public function __construct(ContentManagerRepositoryInterface $contentManagerRepository)
    {
        $this->contentManagerRepository = $contentManagerRepository;
    }

    private function isLocalIp($ip)
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6
        ) && (
            $ip === '127.0.0.1' ||
            $ip === '::1' ||
            substr($ip, 0, 8) === '192.168.' ||
            substr($ip, 0, 7) === '10.' ||
            (substr($ip, 0, 3) === '172' &&
                intval(substr($ip, 4, 2)) >= 16 &&
                intval(substr($ip, 4, 2)) <= 31)
        );
    }


    public function contentManagerPage()
    {
        return view('ContentManager::content_manager');
    }


    public function index()
    {
        $contents = $this->contentManagerRepository->findAll();
        return response()->json($contents);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'post_platform'  => 'required|string',
                'img' => 'nullable',
                'img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'price' => 'nullable|numeric',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            return response()->json(['errors' => $e->errors()], 200);
        }

        $data = [
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'post_platform' => $validatedData['post_platform'],
            'created_time' => now()->format('d/m/Y H:i:s'),
            'update_time' => now()->format('d/m/Y H:i:s'),
            'price' => $request->input('price', '0'),
            'latitude' => $validatedData['latitude'] ?? null,
            'longitude' => $validatedData['longitude'] ?? null,
        ];

        $imgs = [];

        // Xử lý hình ảnh từ input file (tải từ máy)
        if ($request->hasFile('img')) {
            foreach ($request->file('img') as $image) {
                $extension = strtolower($image->getClientOriginalExtension());
                $fileName  = time() . '_' . uniqid() . '.' . $extension;
                $path = $image->storeAs('ContentImage', $fileName, 'public');
                $imgs[] = '/storage/ContentImage/' . $fileName;
            }
        }

        // Nếu người dùng đã chọn hình từ FileManager, nhận existing_imgs
        if ($request->filled('existing_imgs')) {
            $existingImgs = json_decode($request->input('existing_imgs'), true);
            if (is_array($existingImgs)) {
                // Hợp nhất các hình đã upload và hình chọn từ FileManager
                $imgs = array_merge($imgs, $existingImgs);
            }
        }

        $data['imgs'] = json_encode($imgs);

        // Debug dữ liệu (Sau khi kiểm tra, hãy xóa dd())


        $this->contentManagerRepository->create($data);

        return response()->json(['message' => 'Thêm mới nội dung thành công.']);
    }


    public function show($id)
    {
        $content = $this->contentManagerRepository->findById($id);

        if (!$content) {
            return response()->json(['message' => 'Nội dung không tồn tại.'], 200);
        }

        return response()->json($content);
    }

    public function update(Request $request, $id)
    {
        $content = $this->contentManagerRepository->findById($id);

        if (!$content) {
            return response()->json(['message' => 'Nội dung không tồn tại.'], 200);
        }

        // Giải mã existing_imgs nếu là chuỗi JSON, mặc định rỗng
        $existingImgsInput = $request->input('existing_imgs', '[]');
        $existingImgs = is_string($existingImgsInput) ? json_decode($existingImgsInput, true) : $existingImgsInput;

        // Gộp trường existing_imgs vào request để validate đúng kiểu mảng
        $request->merge(['existing_imgs' => $existingImgs]);

        try {
            $validatedData = $request->validate([
                'title'           => 'required|string|max:255',
                'content'         => 'required|string',
                'post_platform'   => 'required|string',
                'img'             => 'nullable|array',
                'img.*'           => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'existing_imgs'   => 'nullable|array',
                'price'           => 'nullable|numeric',
                'latitude'        => 'nullable|numeric',
                'longitude'       => 'nullable|numeric',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            return response()->json(['errors' => $e->errors()], 200);
        }

        // Lấy danh sách hình ảnh đang lưu trong database (nếu có)
        if (isset($content->imgs)) {
            $currentImgs = is_string($content->imgs)
                ? (json_decode($content->imgs, true) ?: [])
                : (is_array($content->imgs) ? $content->imgs : []);
        } else {
            $currentImgs = [];
        }

        // Xác định các hình ảnh cần xóa (nếu có) - những hình đã lưu nhưng không có trong danh sách mới
        $imagesToDelete = array_diff($currentImgs, $existingImgs);
        foreach ($imagesToDelete as $oldImage) {
            $oldImagePath = str_replace('/storage/', '', $oldImage);
            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        // Xử lý các file hình ảnh tải lên từ input (tải từ máy)
        if ($request->hasFile('img')) {
            foreach ($request->file('img') as $image) {
                $extension = strtolower($image->getClientOriginalExtension());
                $fileName  = time() . '_' . uniqid() . '.' . $extension;
                $path = $image->storeAs('ContentImage', $fileName, 'public');
                $existingImgs[] = '/storage/ContentImage/' . $fileName;
            }
        }

        // Chuẩn bị dữ liệu cập nhật
        $data = [
            'title'         => $validatedData['title'],
            'content'       => $validatedData['content'],
            'post_platform' => $validatedData['post_platform'],
            'update_time'   => now()->format('d/m/Y H:i:s'),
            'imgs'          => json_encode($existingImgs), // Lưu dưới dạng JSON
            'price'         => $validatedData['price'] ?? 0,
            'latitude'      => $validatedData['latitude'] ?? null,
            'longitude'     => $validatedData['longitude'] ?? null,
        ];

        // Cập nhật dữ liệu thông qua repository
        $this->contentManagerRepository->update($id, $data);

        return response()->json(['message' => 'Cập nhật nội dung thành công.']);
    }


    public function destroy($id)
    {
        $content = $this->contentManagerRepository->findById($id);

        if (!$content) {
            return response()->json(['message' => 'Nội dung không tồn tại.'], 200);
        }

        try {
            // Xóa các hình ảnh liên quan
            if (!empty($content->imgs)) {
                $images = json_decode($content->imgs, true);
                foreach ($images as $image) {
                    $imagePath = str_replace('/storage/', '', $image);
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $this->contentManagerRepository->delete($id);

            return response()->json(['message' => 'Xóa nội dung thành công.'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting content:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Đã xảy ra lỗi khi xóa nội dung.'], 200);
        }
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

        $this->contentManagerRepository->update($id, [
            'latitude'  => $data['latitude'],
            'longitude' => $data['longitude'],
        ]);

        return redirect()->route('content-manager.show', $id)->with('success', 'Cập nhật tọa độ thành công');
    }

    public function uploadImage(Request $request)
    {
        // Chỉ cho phép upload từ IP local
        $clientIp = $request->ip();
        if (!$this->isLocalIp($clientIp)) {
            return response()->json(['error' => 'Không được phép upload từ IP không hợp lệ.'], 403);
        }

        // Validate file upload: chỉ cho phép các định dạng image an toàn
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:jpeg,jpg,png,gif|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first('file')], 422);
        }

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = time() . '_' . uniqid() . '.' . $extension;
        // Lưu vào disk public trong thư mục ContentImage
        $path = $file->storeAs('ContentImage', $fileName, 'public');

        // Trả về URL đầy đủ để hiển thị
        $url = asset('storage/' . $path);
        return response()->json(['location' => $url]);
    }
}
