<?php
// FILE: ContentManagerController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ContentManager\ContentManagerRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ContentManagerController extends Controller
{
    // HIỆN TẠI HÌNH MẢNG HÌNH ẢNH ĐANG LƯU DƯỚI DẠNG JSON_encode trong database ; 
    // sau này sẽ tahy đổi lại k lưu dưới dang json_encode nữa

    protected $contentManagerRepository;
    public function __construct(ContentManagerRepositoryInterface $contentManagerRepository)
    {
        $this->contentManagerRepository = $contentManagerRepository;
    }


    public function contentManagerPage()
    {
        return view('admin.pages.content_manager');
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
                'img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        }

        $data = [
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'post_platform' => $validatedData['post_platform'],
            'created_time' => now()->format('d/m/Y H:i:s'),
            'update_time' => now()->format('d/m/Y H:i:s'),
            'price' => $request->input('price', '0'),
        ];

        $data['imgs'] = [];

        if ($request->hasFile('img')) {
            foreach ($request->file('img') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('ContentImage', $imageName, 'public');
                $data['imgs'][] = Storage::url($path);
            }
        }

        // Lưu mảng hình ảnh dưới dạng JSON
        $data['imgs'] = json_encode($data['imgs']);

        $this->contentManagerRepository->create($data);

        return response()->json(['message' => 'Thêm mới nội dung thành công.']);
    }


    public function show($id)
    {
        $content = $this->contentManagerRepository->findById($id);

        if (!$content) {
            return response()->json(['message' => 'Nội dung không tồn tại.'], 404);
        }

        return response()->json($content);
    }


    public function update(Request $request, $id)
    {
        $content = $this->contentManagerRepository->findById($id);

        if (!$content) {
            return response()->json(['message' => 'Nội dung không tồn tại.'], 404);
        }

        // Decode existing_imgs nếu là chuỗi JSON
        $existingImgsInput = $request->input('existing_imgs', '[]');
        $existingImgs = is_string($existingImgsInput) ? json_decode($existingImgsInput, true) : $existingImgsInput;

        // Thêm existing_imgs vào request để validation nhận dạng đúng kiểu dữ liệu
        $request->merge(['existing_imgs' => $existingImgs]);

        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'post_platform'  => 'required|string',
                'img' => 'nullable|array',
                'img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'existing_imgs' => 'nullable|array',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        }

        // Kiểm tra và giải mã trường imgs từ database nếu cần
        if (isset($content->imgs)) {
            if (is_string($content->imgs)) {
                $currentImgs = json_decode($content->imgs, true) ?: [];
            } elseif (is_array($content->imgs)) {
                $currentImgs = $content->imgs;
            } else {
                $currentImgs = [];
            }
        } else {
            $currentImgs = [];
        }

        // Xác định các hình ảnh cần xóa (nếu có)
        $imagesToDelete = array_diff($currentImgs, $existingImgs);
        foreach ($imagesToDelete as $oldImage) {
            $oldImagePath = str_replace('/storage/', '', $oldImage);
            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        // Xử lý hình ảnh mới được tải lên
        if ($request->hasFile('img')) {
            foreach ($request->file('img') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('ContentImage', $imageName, 'public');
                $existingImgs[] = Storage::url($path);
            }
        }

        // Chuẩn bị dữ liệu để cập nhật
        $data = [
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'post_platform' => $validatedData['post_platform'],
            'update_time' => now()->format('d/m/Y H:i:s'),
            'imgs' => json_encode($existingImgs), // Mã hóa imgs thành chuỗi JSON
        ];

        // Cập nhật vào database thông qua repository
        $this->contentManagerRepository->update($id, $data);

        return response()->json(['message' => 'Cập nhật nội dung thành công.']);
    }



    public function destroy($id)
    {
        $content = $this->contentManagerRepository->findById($id);

        if (!$content) {
            return response()->json(['message' => 'Nội dung không tồn tại.'], 404);
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
            return response()->json(['message' => 'Đã xảy ra lỗi khi xóa nội dung.'], 500);
        }
    }
}
