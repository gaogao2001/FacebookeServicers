<?php

namespace App\Modules\ImageVideo\Controllers;

use App\Http\Controllers\Controller;
use HoangquyIT\VideoEditor;
use Illuminate\Http\Request;

if (!class_exists('HoangquyIT\InvalidArgumentException')) {
    class_alias('InvalidArgumentException', 'HoangquyIT\InvalidArgumentException');
}


class VideoCreatorController extends Controller
{

    public function videoCreatorPage()
    {
        return view('VideoImage::video_image_editor');
    }

    public function createBasicVideo(Request $request)
    {
        $request->validate([
            'audio'  => 'required|file|mimes:mp3,wav,ogg',
            'totalDuration' => 'required|numeric',
            'displayMode' => 'required|in:distributed,loop' // Thêm validation cho displayMode
        ]);

        // Mảng cuối cùng chứa tất cả hình ảnh theo đúng thứ tự
        $finalImages = [];

        // Lấy thứ tự hình ảnh đã được truyền từ client
        $imageOrder = $request->input('image_order');
        $orderArray = [];
        if ($imageOrder) {
            $orderArray = json_decode($imageOrder, true) ?: [];
        }

        // Lấy danh sách file upload từ máy
        $uploadedImages = $request->file('images');
        if (!is_array($uploadedImages)) {
            $uploadedImages = [];
        }

        // Chuẩn bị mảng đường dẫn tạm cho hình upload
        $uploadedPaths = [];
        foreach ($uploadedImages as $img) {
            $uploadedPaths[] = $img->getRealPath();
        }

        // Lấy hình từ FileManager dưới dạng JSON
        $existingImagesInput = $request->input('existing_images');
        $existingPaths = [];
        if ($existingImagesInput) {
            $existingPaths = json_decode($existingImagesInput, true) ?: [];
        }

        // Chỉ số hiện tại cho mỗi loại ảnh
        $localIndex = 0;
        $fileManagerIndex = 0;

        // Xử lý theo thứ tự từ mảng order
        foreach ($orderArray as $type) {
            if ($type === 'local' && $localIndex < count($uploadedPaths)) {
                $finalImages[] = $uploadedPaths[$localIndex];
                $localIndex++;
            } elseif ($type === 'filemanager' && $fileManagerIndex < count($existingPaths)) {
                $finalImages[] = $existingPaths[$fileManagerIndex];
                $fileManagerIndex++;
            }
        }

        // Kiểm tra nếu finalImages rỗng
        if (empty($finalImages)) {
            return response()->json(['message' => 'Danh sách hình ảnh không được để trống.'], 422);
        }

        // Chuyển đổi các URL thành file local tạm thời
        $localImages = [];
        $downloadedImages = []; // Để cleanup sau

        foreach ($finalImages as $img) {
            if (filter_var($img, FILTER_VALIDATE_URL)) {
                // Xử lý URL
                $contents = @file_get_contents($img);
                if ($contents === false) {
                    continue;
                }
                $tempImage = tempnam(sys_get_temp_dir(), 'img_') . '.jpg';
                file_put_contents($tempImage, $contents);
                $localImages[] = $tempImage;
                $downloadedImages[] = $tempImage;
            } else {
                // Đã là file local
                $localImages[] = $img;
            }
        }

        // Phần còn lại của hàm giữ nguyên
        if (empty($localImages)) {
            return response()->json(['message' => 'Không thể tải được hình ảnh.'], 422);
        }

        // Audio upload
        $audio = $request->file('audio');
        $audioPath = $audio->getRealPath();
        $totalDuration = $request->totalDuration;
        $displayMode = $request->input('displayMode', 'distributed');

        $outputFile = 'preview_' . uniqid('video_', true) . '.mp4';
        $outputFolder = '/var/www/FacebookService/public/output/Preview';
        $videoEditor = new VideoEditor($outputFolder);

        // Gọi hàm tạo video với mảng đường dẫn đã được sắp xếp đúng thứ tự
        $success = $videoEditor->createBasicVideo($localImages, $outputFile, 30, 1280, 720, $totalDuration, $audioPath,  $displayMode);

        // Cleanup các file ảnh tạm
        foreach ($downloadedImages as $tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        if ($success) {
            $previewUrl = asset("output/Preview/" . $outputFile);
            return response()->json(['message' => 'Tạo video demo thành công', 'previewUrl' => $previewUrl], 200);
        } else {
            return response()->json(['message' => 'Tạo video demo thất bại'], 500);
        }
    }

    public function createVideoWithAudio(Request $request)
    {
        $request->validate([
            'outputFile' => 'required|string',
            'audioConcat' => 'nullable|file|mimes:mp3,wav,ogg',
        ]);

        // Mảng cuối cùng chứa tất cả video theo đúng thứ tự
        $finalVideos = [];
        $downloadedVideos = []; // Để cleanup sau

        // Lấy thứ tự video đã được truyền từ client
        $videoOrder = $request->input('video_order');
        $orderArray = [];
        if ($videoOrder) {
            $orderArray = json_decode($videoOrder, true) ?: [];
        }

        // Lấy danh sách file upload từ máy
        $uploadedVideos = $request->file('videos');
        if (!is_array($uploadedVideos)) {
            $uploadedVideos = [];
        }

        // Chuẩn bị mảng đường dẫn tạm cho video upload
        $uploadedPaths = [];
        foreach ($uploadedVideos as $video) {
            $uploadedPaths[] = $video->getRealPath();
        }

        // Lấy video từ FileManager dưới dạng JSON
        $existingVideosInput = $request->input('existing_videos');
        $existingPaths = [];
        if ($existingVideosInput) {
            if (is_array($existingVideosInput)) {
                $existingPaths = $existingVideosInput;
            } else {
                $existingPaths = json_decode($existingVideosInput, true) ?: [];
            }
        }

        // Chỉ số hiện tại cho mỗi loại video
        $localIndex = 0;
        $fileManagerIndex = 0;

        // Xử lý theo thứ tự từ mảng order
        if (!empty($orderArray)) {
            foreach ($orderArray as $type) {
                if ($type === 'local' && $localIndex < count($uploadedPaths)) {
                    $finalVideos[] = $uploadedPaths[$localIndex];
                    $localIndex++;
                } elseif ($type === 'filemanager' && $fileManagerIndex < count($existingPaths)) {
                    $url = $existingPaths[$fileManagerIndex];
                    // Tải video từ URL về file tạm
                    $contents = @file_get_contents($url);
                    if ($contents === false) {
                        continue;
                    }
                    $tempVideo = tempnam(sys_get_temp_dir(), 'video_') . '.mp4';
                    file_put_contents($tempVideo, $contents);
                    $finalVideos[] = $tempVideo;
                    $downloadedVideos[] = $tempVideo; // Để cleanup sau
                    $fileManagerIndex++;
                }
            }
        } else {
            // Fallback nếu không có order array: local videos trước, sau đó là FileManager videos
            $finalVideos = array_merge($uploadedPaths, array_map(function ($url) use (&$downloadedVideos) {
                $contents = @file_get_contents($url);
                if ($contents === false) {
                    return null;
                }
                $tempVideo = tempnam(sys_get_temp_dir(), 'video_') . '.mp4';
                file_put_contents($tempVideo, $contents);
                $downloadedVideos[] = $tempVideo;
                return $tempVideo;
            }, $existingPaths));
            $finalVideos = array_filter($finalVideos); // Remove nulls
        }

        // Kiểm tra nếu finalVideos rỗng
        if (empty($finalVideos)) {
            return response()->json(['message' => 'Danh sách video không được để trống.'], 422);
        }

        // Cũng tạo file demo với tiền tố preview_
        $outputFile = 'preview_' . $request->outputFile;
        $audioPath = $request->hasFile('audioConcat') ? $request->file('audioConcat')->getRealPath() : null;
        $keepVideoAudio = $request->has('keepVideoAudio');

        $outputFolder = '/var/www/FacebookService/public/output/Preview';
        $videoEditor = new VideoEditor($outputFolder);

        $success = $videoEditor->concatVideos(
            $finalVideos,
            $outputFile,
            $keepVideoAudio,
            $audioPath
        );

        // Cleanup các file tạm
        foreach ($downloadedVideos as $tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        if ($success) {
            $previewUrl = asset("output/Preview/" . $outputFile);
            return response()->json(['message' => 'Tạo video demo thành công', 'previewUrl' => $previewUrl], 200);
        } else {
            return response()->json(['message' => 'Tạo video demo thất bại'], 500);
        }
    }

    public function extractAudio(Request $request)
    {
        $request->validate([
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg',
            'video_url' => 'nullable|string',
            'video_type' => 'required|in:local,filemanager',
            'outputAudio' => 'required|string'
        ]);

        $videoPath = null;
        $tempFile = null;

        // Xử lý video từ nguồn được chọn
        if ($request->input('video_type') === 'local') {
            if (!$request->hasFile('video')) {
                return response()->json(['message' => 'Vui lòng chọn file video'], 422);
            }
            $videoPath = $request->file('video')->getRealPath();
        } else if ($request->input('video_type') === 'filemanager') {
            $videoUrl = $request->input('video_url');
            if (empty($videoUrl)) {
                return response()->json(['message' => 'URL video không hợp lệ'], 422);
            }

            // Tải video từ FileManager
            $contents = @file_get_contents($videoUrl);
            if ($contents === false) {
                return response()->json(['message' => 'Không thể tải video từ URL'], 422);
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'video_') . '.mp4';
            file_put_contents($tempFile, $contents);
            $videoPath = $tempFile;
        }

        if (!$videoPath) {
            return response()->json(['message' => 'Không tìm thấy video để xử lý'], 422);
        }

        $outputAudio = $request->outputAudio;
        $outputFolder = '/var/www/html/output/Audio';

        $audioEditor = new VideoEditor($outputFolder);
        $success = $audioEditor->extractAudio($videoPath, $outputAudio);

        // Dọn dẹp file tạm nếu có
        if ($tempFile && file_exists($tempFile)) {
            @unlink($tempFile);
        }

        if ($success) {
            return response()->json([
                'message' => 'Tách audio thành công',
                'outputFile' => $outputAudio
            ], 200);
        } else {
            return response()->json([
                'message' => 'Tách audio thất bại'
            ], 500);
        }
    }

    public function concatVideoSegmentsPreview(Request $request)
    {
        // validate input
        $request->validate([
            'videos.*'  => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg',
            'segments'  => 'required|array',
            'segments.*.start' => 'required|numeric',
            'segments.*.end'   => 'required|numeric',
            'outputFile' => 'required|string',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg',
            'video_types' => 'nullable|string',  // JSON array của video types
            'video_urls' => 'nullable|string'    // JSON array của video URLs
        ]);

        // Nhận file video, segments, và các thông tin khác
        $videoFiles = $request->file('videos') ?? [];
        $segments = $request->input('segments');
        $outputFile = $request->input('outputFile');
        $keepVideoAudio = $request->has('keepVideoAudio');

        // Parse thông tin về loại video và URLs
        $videoTypes = json_decode($request->input('video_types', '[]'), true) ?? [];
        $videoUrls = json_decode($request->input('video_urls', '[]'), true) ?? [];

        // Mảng chứa tất cả đường dẫn video
        $videosPaths = [];
        $downloadedVideos = []; // Để cleanup sau

        // Chỉ số hiện tại cho các video được upload
        $uploadIndex = 0;

        // Xử lý từng segment theo thứ tự
        for ($i = 0; $i < count($videoTypes); $i++) {
            if ($videoTypes[$i] === 'local') {
                // Nếu là video local, lấy từ file upload
                if (isset($videoFiles[$uploadIndex])) {
                    $videosPaths[] = $videoFiles[$uploadIndex]->getPathname();
                    $uploadIndex++;
                }
            } else if ($videoTypes[$i] === 'filemanager' && !empty($videoUrls[$i])) {
                // Nếu là video từ FileManager, tải về file tạm
                $contents = @file_get_contents($videoUrls[$i]);
                if ($contents !== false) {
                    $tempVideo = tempnam(sys_get_temp_dir(), 'video_') . '.mp4';
                    file_put_contents($tempVideo, $contents);
                    $videosPaths[] = $tempVideo;
                    $downloadedVideos[] = $tempVideo;
                }
            }
        }
        // Xử lý audio path
        $audioPath = null;
        if (!$keepVideoAudio && $request->hasFile('audio')) {
            $audioPath = $request->file('audio')->getPathname();
        }
        // Sử dụng thư mục tạm cho file demo
        $outputFolder = '/var/www/FacebookService/public/output/Preview';
        $videoEditor = new VideoEditor($outputFolder);

        // Tạo preview file
        $previewFile = 'preview_' . $outputFile;
        $success = $videoEditor->concatVideoSegments($videosPaths, $segments, $previewFile, $keepVideoAudio, $audioPath);

        // Cleanup các file tạm
        foreach ($downloadedVideos as $tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        if ($success) {
            $previewUrl = asset("output/Preview/" . $previewFile);
            return response()->json([
                'message' => 'Ghép video demo thành công',
                'previewUrl' => $previewUrl
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ghép video demo thất bại'
            ], 500);
        }
    }

    public function confirmExport(Request $request)
    {
        // Xác nhận xuất file cuối cùng
        // Nhận thông tin cần thiết (có thể lưu lại thông tin preview trong session hoặc truyền lại từ form)
        // Sau đó, chuyển đổi file preview thành file cuối cùng ở thư mục export (ví dụ: '/var/www/html/ouput/Video')
        $outputFile = $request->input('outputFile');
        $previewFile =  $outputFile;
        $finalFile = $outputFile;
        $previewFolder = '/var/www/FacebookService/public/output/Preview/';
        $finalFolder = '/var/www/html/output/Video/';

        if (file_exists($previewFolder . $previewFile)) {
            // Di chuyển file demo sang thư mục final (hoặc copy)
            if (rename($previewFolder . $previewFile, $finalFolder . $finalFile)) {
                return response()->json([
                    'message' => 'Xuất file thành công',
                    'outputFile' => $finalFile,
                    'fileUrl' => asset("output/Video/" . $finalFile)
                ], 200);
            }
        }
        return response()->json([
            'message' => 'Xuất file thất bại'
        ], 500);
    }

    public function cutVideoPreview(Request $request)
    {
        $request->validate([
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg',
            'video_url' => 'nullable|string',
            'video_type' => 'required|in:local,filemanager',
            'start_time' => 'required|numeric|min:0',
            'end_time' => 'required|numeric|gt:start_time',
            'outputFile' => 'required|string',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg',
            'keepAudio' => 'nullable'
        ]);

        $videoPath = null;
        $tempFile = null;

        // Xử lý video từ nguồn được chọn
        if ($request->input('video_type') === 'local') {
            if (!$request->hasFile('video')) {
                return response()->json(['message' => 'Vui lòng chọn file video'], 422);
            }
            $videoPath = $request->file('video')->getRealPath();
        } else if ($request->input('video_type') === 'filemanager') {
            $videoUrl = $request->input('video_url');
            if (empty($videoUrl)) {
                return response()->json(['message' => 'URL video không hợp lệ'], 422);
            }

            // Tải video từ FileManager
            $contents = @file_get_contents($videoUrl);
            if ($contents === false) {
                return response()->json(['message' => 'Không thể tải video từ URL'], 422);
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'video_') . '.mp4';
            file_put_contents($tempFile, $contents);
            $videoPath = $tempFile;
        }

        if (!$videoPath) {
            return response()->json(['message' => 'Không tìm thấy video để xử lý'], 422);
        }

        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $keepAudio = $request->has('keepAudio');
        $outputFile = 'preview_' . $request->input('outputFile');

        // Xử lý audio path
        $audioPath = null;
        if (!$keepAudio && $request->hasFile('audio')) {
            $audioPath = $request->file('audio')->getRealPath();
        }

        $outputFolder = '/var/www/FacebookService/public/output/Preview';
        $videoEditor = new VideoEditor($outputFolder);

        // Gọi hàm cắt video
        $success = $videoEditor->cutVideo($videoPath, $outputFile, $startTime, $endTime, $keepAudio, $audioPath);

        // Dọn dẹp file tạm nếu có
        if ($tempFile && file_exists($tempFile)) {
            @unlink($tempFile);
        }

        if ($success) {
            $previewUrl = asset("output/Preview/" . $outputFile);
            return response()->json([
                'message' => 'Cắt video thành công',
                'previewUrl' => $previewUrl
            ], 200);
        } else {
            return response()->json([
                'message' => 'Cắt video thất bại'
            ], 500);
        }
    }
}
