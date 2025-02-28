<?php

namespace App\Modules\Document\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function create(Request $request)
    {
        

        // Validate dữ liệu đầu vào từ modal mới (không validate file upload ở đây)
        $validatedData = $request->validate([
            'id'      => 'required|string',
            'title'   => 'required|string',
            'content' => 'required|string'
        ]);

        $pageId   = $validatedData['id'];
        $pageDir  = public_path("assets/documentation/pages/{$pageId}");
        $dirImages = $pageDir . '/images';
        $dirVideos = $pageDir . '/videos';

        // Tạo thư mục trang nếu chưa có
        if (!is_dir($pageDir)) {
            if (!mkdir($pageDir, 0755, true)) {
                return response()->json(['message' => 'Không thể tạo thư mục trang.'], 500);
            }
        }
        // Tạo thư mục images nếu chưa có
        if (!is_dir($dirImages)) {
            if (!mkdir($dirImages, 0755, true)) {
                return response()->json(['message' => 'Không thể tạo thư mục hình ảnh.'], 500);
            }
        }
        // Tạo thư mục videos nếu chưa có
        if (!is_dir($dirVideos)) {
            if (!mkdir($dirVideos, 0755, true)) {
                return response()->json(['message' => 'Không thể tạo thư mục video.'], 500);
            }
        }

        // Xử lý file upload
        $images = [];
        $videos = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $mime = $file->getMimeType();
                    $extension = $file->getClientOriginalExtension();
                    $filename = uniqid() . '.' . $extension;
                    if (strpos($mime, 'image/') === 0) {
                        // Lưu file hình ảnh
                        $file->move($dirImages, $filename);
                        $images[] = $filename;
                    } elseif (strpos($mime, 'video/') === 0) {
                        // Lưu file video
                        $file->move($dirVideos, $filename);
                        $videos[] = $filename;
                    }
                }
            }
        }

        // Tạo nội dung JSON với cấu trúc đơn giản gồm: tiêu đề, nội dung, hình ảnh & video
        $contentData = [
            'id'      => $validatedData['id'],
            'title'   => $validatedData['title'],
            'content' => $validatedData['content'],
            'images'  => $images,
            'videos'  => $videos
        ];

        // Lưu file content.json cho trang vừa tạo
        $filePath = $pageDir . "/content.json";
        $jsonContent = json_encode($contentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($filePath, $jsonContent) !== false) {
            // Cập nhật index.json sau khi tạo document thành công
            $indexPath = public_path("assets/documentation/index.json");
            if (file_exists($indexPath)) {
                $indexData = json_decode(file_get_contents($indexPath), true);
            } else {
                $indexData = [
                    'version'     => "1.0",
                    'appName'     => "Facebook Service",
                    'lastUpdated' => date('Y-m-d'),
                    'pages'       => []
                ];
            }

            // Kiểm tra xem trang này đã có trong index chưa
            $found = false;
            if (!empty($indexData['pages']) && is_array($indexData['pages'])) {
                foreach ($indexData['pages'] as &$page) {
                    if ($page['id'] === $validatedData['id']) {
                        // Cập nhật title nếu cần
                        $page['title'] = $validatedData['title'];
                        $found = true;
                        break;
                    }
                }
            } else {
                $indexData['pages'] = [];
            }

            // Nếu chưa có thì thêm mới
            if (!$found) {
                $newPage = [
                    'id'          => $validatedData['id'],
                    'title'       => $validatedData['title'],
                    'description' => '',
                    'icon'        => ''
                ];
                $indexData['pages'][] = $newPage;
            }

            // Cập nhật ngày cập nhật của index
            $indexData['lastUpdated'] = date('Y-m-d');

            // Lưu lại file index.json
            $indexJson = json_encode($indexData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($indexPath, $indexJson);

            return response()->json([
                'message' => 'Document đã được tạo thành công và index.json đã được cập nhật.',
                'data'    => $contentData
            ], 201);
        } else {
            return response()->json(['message' => 'Tạo document thất bại.'], 500);
        }
    }
}
