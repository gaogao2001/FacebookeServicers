<?php

namespace App\Http\Controllers\Admin\Facebook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use HoangquyIT\ModelFacebook\FacebookApi;
use HoangquyIT\ModelFacebook\FbMediaDownloader;

class ImageVideoManagerController extends Controller
{
    public function postVideo(Request $request)
    {
        // Kiểm tra tính hợp lệ của dữ liệu đầu vào
        $request->validate([
            'url' => 'required|url',
            'extract_frames' => 'nullable|boolean',
            'uid' => 'required'
        ]);
        // Nơi lưu trữ output của hình ảnh và video
        $uid = $request->input('uid');
        $outputDirImg = '/var/www/html/FileData/Images/' . $uid;
        $outputDirVideo = '/var/www/html/FileData/Video/' . $uid;

        if (!file_exists($outputDirImg)) {
            mkdir($outputDirImg, 0777, true);
        }
        if (!file_exists($outputDirVideo)) {
            mkdir($outputDirVideo, 0777, true);
        }

        if (!extension_loaded('spaceviet')) {
            return response()->json(['error' => 'Extension spaceviet chưa được tải!'], 500);
        }
        $url = $request->input('url');
        $extractFrames = $request->boolean('extract_frames');
        //
        $downloader = new FbMediaDownloader();
        $downloader->set_url($url);
        $response = $downloader->generate_data();
        //var_dump($datas);
        //die();
        //if (!fb_set_url($url)) {
        //    return response()->json(['error' => 'URL không hợp lệ hoặc không phải URL của Facebook.'], 400);
        //}

        //if (!fb_request()) {
        //    return response()->json(['error' => 'Không thể thực hiện yêu cầu HTTP.'], 500);
        // }

        //$response = fb_get_result();

        //if ($response === null) {
        //    return response()->json(['error' => 'Không nhận được phản hồi. Có thể URL không hợp lệ hoặc xảy ra lỗi trong quá trình xử lý.'], 500);
        //}

        // Chọn URL video để tải xuống (lấy URL đầu tiên)
        $videoUrl = isset($response->dl_urls->high) ? $response->dl_urls->high : null;
        if (!$videoUrl) {
            return response()->json(['error' => 'Không tìm thấy URL video hợp lệ.'], 500);
        }
        $videoUrl = str_replace('\/', '/', $videoUrl);

        // Đường dẫn đến ffmpeg (có thể đặt null để hệ thống tự lấy)
        $ffmpegPath = null;

        // Tên tệp tạo từ url hash sang md5 rồi cắt chuỗi lấy 10 ký tự
        $fileName = substr(md5($request->input('url')), 0, 10);

        //var_dump(download_video($ffmpegPath, $outputDirVideo, $fileName, $videoUrl));
        //die();
        // Tải video từ URL
        if (!download_video($ffmpegPath, $outputDirVideo, $fileName, $videoUrl)) {
            return response()->json(['error' => 'Không thể tải xuống video.'], 500);
        }

        if ($extractFrames) {
            // Trích xuất các frame từ video
            //$extractor = new VideoFrameExtractor($ffmpegPath, $datas->id);
            if (!extract_frames($ffmpegPath, $outputDirImg, $fileName, $videoUrl)) {
                return response()->json(['error' => 'Không thể trích xuất hình ảnh từ video.'], 500);
            }

            // Đổi tên các file trong thư mục đầu ra
            rename_files($outputDirImg);
        }

        return response()->json([
            'message' => 'Video đã được tải xuống thành công.',
            'data' => $response
        ]);
    }

    public function getVideo(Request $request, $id)
    {
        // Validate ID
        if (empty($id)) {
            return response()->json(['error' => 'UID không hợp lệ.'], 400);
        }

        $uid = $id;
        $outputDirVideo = public_path('FileData/Video/' . $uid);

        // Check if directory exists
        if (!file_exists($outputDirVideo)) {
            return response()->json(['error' => 'Không tìm thấy thư mục video cho người dùng.'], 404);
        }

        // Get all video files
        $files = scandir($outputDirVideo);
        $videos = array_filter($files, function ($file) use ($outputDirVideo) {
            return !in_array($file, ['.', '..']) && is_file($outputDirVideo . '/' . $file);
        });

        // Paginate videos
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 6);
        $offset = ($page - 1) * $limit;

        $paginatedVideos = array_slice($videos, $offset, $limit);

        $videoList = array_map(function ($file) use ($uid) {
            return asset('FileData/Video/' . $uid . '/' . $file);
        }, $paginatedVideos);

        return response()->json([
            'uid' => $uid,
            'videos' => array_values($videoList),
            'hasMore' => $offset + $limit < count($videos),
        ]);
    }

    public function uploadVideo(Request $request, $id)
    {
        // Validate ID
        if (empty($id)) {
            return response()->json(['error' => 'UID không hợp lệ.'], 400);
        }

        $uid = $id;
        $outputDirVideo = public_path('FileData/Video/' . $uid);

        if (!file_exists($outputDirVideo)) {
            mkdir($outputDirVideo, 0755, true);
        }

        $request->validate([
            'file' => 'required|mimes:mp4,avi,mov,wmv|max:5120000',
        ]);

        $file = $request->file('file');

        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move($outputDirVideo, $fileName);

        $filePath = asset('FileData/Video/' . $uid . '/' . $fileName);

        return response()->json([
            'message' => 'Video đã được tải lên thành công.',
            'filePath' => $filePath,
        ]);
    }


    public function deleteVideo(Request $request, $id)
    {
        $videoPath = $request->input('video');

        if (empty($videoPath)) {
            return response()->json(['error' => 'Đường dẫn video không hợp lệ.'], 400);
        }

        $uid = $id;
        $outputDirVideo = public_path('FileData/Video/' . $uid);

        if (!file_exists($outputDirVideo)) {
            return response()->json(['error' => 'Không tìm thấy thư mục video cho người dùng.'], 404);
        }

        $absolutePath = $outputDirVideo . '/' . basename($videoPath);

        if (file_exists($absolutePath)) {
            unlink($absolutePath);
            return response()->json(['message' => 'Xóa video thành công.']);
        } else {
            return response()->json(['error' => 'Video không tồn tại.'], 404);
        }
    }

    public function getImage(Request $request, $id)
    {
        // Validate ID
        if (empty($id)) {
            return response()->json(['error' => 'UID không hợp lệ.'], 400);
        }

        $uid = $id;
        $outputDirImage = public_path('FileData/Images/' . $uid);

        // Check if directory exists
        if (!file_exists($outputDirImage)) {
            return response()->json(['error' => 'Không tìm thấy thư mục ảnh cho người dùng.'], 404);
        }

        // Get all image files
        $files = scandir($outputDirImage);
        $images = array_filter($files, function ($file) use ($outputDirImage) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return !in_array($file, ['.', '..']) && is_file($outputDirImage . '/' . $file) && in_array($extension, $allowedExtensions);
        });

        // Sort images alphabetically (you can sort by other criteria if needed)
        sort($images);

        // Pagination
        $page = max(1, (int)$request->query('page', 1)); // Ensure page >= 1
        $limit = min(300, (int)$request->query('limit', 50)); // Limit to a max of 300 images per request
        $offset = ($page - 1) * $limit;

        $paginatedImages = array_slice($images, $offset, $limit);

        $imageList = array_map(function ($file) use ($uid) {
            return asset('FileData/Images/' . $uid . '/' . $file);
        }, $paginatedImages);

        // Build response
        return response()->json([
            'uid' => $uid,
            'totalImages' => count($images), // Total number of images
            'images' => array_values($imageList),
            'currentPage' => $page, // Current page number
            'hasMore' => $offset + $limit < count($images), // Check if there are more images
        ]);
    }

    public function deleteImage(Request $request, $id)
    {
        $imagePath = $request->input('image');

        if (empty($imagePath)) {
            return response()->json(['error' => 'Đường dẫn ảnh không hợp lệ.'], 400);
        }

        $uid = $id;
        $outputDirImage = public_path('FileData/Images/' . $uid);

        if (!file_exists($outputDirImage)) {
            return response()->json(['error' => 'Không tìm thấy thư mục ảnh cho người dùng.'], 404);
        }
        $absolutePath = $outputDirImage . '/' . basename($imagePath); // Đảm bảo lấy đúng file

        if (file_exists($absolutePath)) {
            unlink($absolutePath);
            return response()->json(['message' => 'Xóa ảnh thành công.']);
        } else {
            return response()->json(['error' => 'Ảnh không tồn tại.'], 404);
        }
    }

    public function updateImage(Request $request, $id)
    {
        $uid = $id;
        $outputDirImage = public_path('FileData/Images/' . $uid);

        if (!file_exists($outputDirImage)) {
            return response()->json(['error' => 'Thư mục ảnh không tồn tại.'], 404);
        }

        // Kiểm tra oldFileName
        $oldFileName = $request->input('oldFileName');
        if (empty($oldFileName)) {
            return response()->json(['error' => 'Tên file cũ không được gửi hoặc không hợp lệ.'], 400);
        }

        // Kiểm tra file ảnh
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'Không nhận được file ảnh.'], 400);
        }

        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();

        // Xác định file cũ và kiểm tra
        $targetFile = $outputDirImage . '/' . $oldFileName;
        if (!file_exists($targetFile)) {
            return response()->json(['error' => 'File cũ không tồn tại.'], 404);
        }

        // Xóa file cũ và lưu file mới
        unlink($targetFile);
        $image->move($outputDirImage, $oldFileName);

        return response()->json([
            'message' => 'Hình ảnh đã được cập nhật.',
            'imageUrl' => asset('FileData/Images/' . $uid . '/' . $oldFileName),
        ]);
    }
}
