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
        $success = $videoEditor->createBasicVideo($localImages, $outputFile, 30, 1280, 720, $totalDuration, $audioPath ,  $displayMode);

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
            'videos' => 'required|array|min:1',
            'videos.*' => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg',
            'outputFile' => 'required|string',
            'audioConcat' => 'nullable|file|mimes:mp3,wav,ogg',
        ]);

        $videos = $request->file('videos');
        // Cũng tạo file demo với tiền tố preview_
        $outputFile = 'preview_' . $request->outputFile;
        $audioPath = $request->file('audioConcat');
        $keepVideoAudio = $request->has('keepVideoAudio');

        $outputFolder = '/var/www/FacebookService/public/output/Preview';
        $videoEditor = new VideoEditor($outputFolder);

        $success = $videoEditor->concatVideos(
            $videos,
            $outputFile,
            $keepVideoAudio,
            $audioPath,
            []  // Giả sử không dùng chuyển cảnh ở đây
        );

        if ($success) {
            $previewUrl = asset("output/Preview/" . $outputFile);
            return response()->json(['message' => 'Tạo video demo thành công', 'previewUrl' => $previewUrl], 200);
        } else {
            return response()->json(['message' => 'Tạo video demo thất bại'], 500);
        }
    }

    public function extractAudio(Request $request)
    {
        $video = $request->file('video');
        $outputAudio = $request->outputAudio;


        $outputFolder = '/var/www/html/ouput/Audio';

        $audio = new VideoEditor($outputFolder);

        $success = $audio->extractAudio($video, $outputAudio);

        if ($success) {
            return response()->json(['message' => 'Tách audio thành công', 'outputFile' => $outputAudio], 200);
        } else {
            return response()->json(['message' => 'Tách audio thất bại'], 500);
        }
    }

    public function concatVideoSegmentsPreview(Request $request)
    {
        // validate input tương tự concatVideoSegments
        $request->validate([
            'videos.*'  => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg',
            'segments'  => 'required|array',
            'segments.*.start' => 'required|numeric',
            'segments.*.end'   => 'required|numeric',
            'outputFile' => 'required|string',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg'
        ]);

        // Xử lý như concatVideoSegments
        // Nhận file video, segments, và các thông tin khác
        $videoFiles = $request->file('videos');
        $segments = $request->input('segments');
        $outputFile = $request->input('outputFile');
        $keepVideoAudio = $request->has('keepVideoAudio');

        $videosPaths = [];
        foreach ($videoFiles as $file) {
            $videosPaths[] = $file->getPathname();
        }
        $audioPath = null;
        if (!$keepVideoAudio && $request->hasFile('audio')) {
            $audioPath = $request->file('audio')->getPathname();
        }

        // Sử dụng thư mục tạm cho file demo
        $outputFolder = '/var/www/FacebookService/public/output/Preview';
        $videoEditor = new VideoEditor($outputFolder);

        // Giả sử hàm concatVideoSegments tạo file và trả về bool.
        // Bạn có thể lưu tên file preview (ví dụ: prefix 'preview_' + $outputFile)
        $previewFile = 'preview_' . $outputFile;
        $success = $videoEditor->concatVideoSegments($videosPaths, $segments, $previewFile, $keepVideoAudio, $audioPath);

        if ($success) {
            // Trả về URL của file preview (cần đảm bảo file được truy cập từ web)
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
}
