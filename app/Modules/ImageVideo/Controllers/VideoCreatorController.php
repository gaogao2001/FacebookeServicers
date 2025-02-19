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
            'images.*' => 'image',
            'audio'  => 'required|file|mimes:mp3,wav,ogg',
            'totalDuration' => 'required|numeric'
        ]);

        $images = $request->file('images');
        $audio = $request->file('audio');
        $totalDuration = $request->totalDuration;

        // Tạo tên file video độc nhất
        $outputFile = uniqid('video_', true) . '.mp4';

        // Đường dẫn thư mục output cần đảm bảo tồn tại và có quyền ghi
        $outputFolder = '/var/www/html/ouput';
        $videoEditor = new VideoEditor($outputFolder);

        // Xử lý tạo video
        $success = $videoEditor->createBasicVideo($images, $outputFile, 30, 1280, 720, $totalDuration, $audio);

        if ($success) {
            // Sau khi xử lý xong và video được tạo thành công, lưu tên file vào file /tmp/video_create.txt
            file_put_contents("/tmp/video_create.txt", $outputFile);
            // Hiển thị danh sách file hình ảnh đã lưu trong thư mục output

            return response()->json(['message' => 'Tạo video thành công', 'outputFile' => $outputFile], 200);
        } else {
            return response()->json(['message' => 'Tạo video thất bại'], 500);
        }
    }

    public function createVideoWithAudio(Request $request)
    {
        $request->validate([
            'videos' => 'required|array|min:1',
            'videos.*' => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg',
            'outputFile' => 'required|string',
            'audioConcat' => 'nullable|file|mimes:mp3,wav,ogg',
            'transitionType' => 'required_if:applyTransition,1',
            'transitionDuration' => 'required_if:applyTransition,1|numeric',
            'transitionOffset' => 'required_if:applyTransition,1|numeric',
            'targetWidth' => 'required_if:applyTransition,1|numeric',
            'targetHeight' => 'required_if:applyTransition,1|numeric',
        ]);


        $videos = $request->file('videos');
        $outputFile = $request->outputFile;
        $audioPath = $request->file('audioConcat');
        $keepVideoAudio = $request->has('keepVideoAudio');


        $applyTransition = $request->has('applyTransition');

        if (!$request->has('applyTransition')) {
            $transitionOptions = [];
        } else {
            $transitionOptions = [
                'enable'              => true,
                'transition_type'     => $request->input('transitionType'),
                'transition_duration' => $request->input('transitionDuration'),
                'transition_offset'   => $request->input('transitionOffset'),
                'width'               => $request->input('targetWidth'),
                'height'              => $request->input('targetHeight'),
            ];
        }

        $outputFolder = '/var/www/html/ouput';

        $videoEditor = new VideoEditor($outputFolder);

        dd($videos, $outputFile, $keepVideoAudio, $audioPath, $transitionOptions);
        $success = $videoEditor->concatVideos(
            $videos,
            $outputFile,
            $keepVideoAudio,
            $audioPath,
            $transitionOptions
        );
        // dd($success);

        if ($success) {
            file_put_contents("/tmp/video_create.txt", $outputFile);
            return response()->json(['message' => 'Ghép video thành công', 'outputFile' => $outputFile], 200);
        } else {
            return response()->json(['message' => 'Ghép video thất bại'], 500);
        }
    }


    public function extractAudio()
    {
        $audio = new VideoEditor('/var/www/FacebookService/.vscode/FileVideoImage/audio');

        $inputVideo = '/var/www/FacebookService/.vscode/FileVideoImage/video/7f08c80462.mp4';
        $outputAudio = 'extracted_audio.mp3';

        $success = $audio->extractAudio($inputVideo, $outputAudio);
        var_dump($success);
        die('iiiiiiiiiiiiiii');
    }
}
