<?php

namespace App\Modules\ImageVideo\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use HoangquyIT\ModelFacebook\FacebookApi;
use HoangquyIT\ModelFacebook\FbMediaDownloader;
use HoangquyIT\VideoFrameExtractor;
use App\Modules\Facebook\Repositories\Account\AccountRepository;
use App\Modules\Fanpage\Repositories\FanpageManagerRepositoryInterface;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\Profile\HomeAccountController;
use HoangquyIT\ModelFacebook\FacebookFind;
use HoangquyIT\FacebookAccount;
use HoangquyIT\ModelFacebook\Android\CheckConnect;
use HoangquyIT\ModelFacebook\Android\Profile\ProfileManager;




class ImageVideoManagerController extends Controller
{
    protected $accountRepository;
    protected $fanpageManagerRepository;

    public function __construct(AccountRepository $accountRepository, FanpageManagerRepositoryInterface $fanpageManagerRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->fanpageManagerRepository = $fanpageManagerRepository;
    }


    // Hàm kiểm tra IP local, tương tự LoginController
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

        $basePath = config('file-path.base_path');
        $outputDirImg = $basePath . 'Images/' . $uid;
        $outputDirVideo = $basePath . 'Video/' . $uid;

        if (!file_exists($outputDirImg)) {
            mkdir($outputDirImg, 0777, true);
        }
        if (!file_exists($outputDirVideo)) {
            mkdir($outputDirVideo, 0777, true);
        }

        //if (!extension_loaded('spaceviet')) {
        //    return response()->json(['error' => 'Extension spaceviet chưa được tải!'], 200);
        //}
        $url = $request->input('url');
        $extractFrames = $request->boolean('extract_frames');
        //
        $downloader = new FbMediaDownloader();
        
        $downloader->set_url($url);
        $response = $downloader->generate_data();
        //var_dump($datas);
        //die();
        //if (!fb_set_url($url)) {
        //    return response()->json(['error' => 'URL không hợp lệ hoặc không phải URL của Facebook.'], 200);
        //}

        //if (!fb_request()) {
        //    return response()->json(['error' => 'Không thể thực hiện yêu cầu HTTP.'], 200);
        // }

        //$response = fb_get_result();

        //if ($response === null) {
        //    return response()->json(['error' => 'Không nhận được phản hồi. Có thể URL không hợp lệ hoặc xảy ra lỗi trong quá trình xử lý.'], 200);
        //}

        // Chọn URL video để tải xuống (lấy URL đầu tiên)
        $videoUrl = isset($response->dl_urls->high) ? $response->dl_urls->high : null;
        if (!$videoUrl) {
            return response()->json(['error' => 'Không tìm thấy URL video hợp lệ.'], 200);
        }
        $videoUrl = str_replace('\/', '/', $videoUrl);

        // Đường dẫn đến ffmpeg (có thể đặt null để hệ thống tự lấy)
        $ffmpegPath = null;

        // Tên tệp tạo từ url hash sang md5 rồi cắt chuỗi lấy 10 ký tự
        $fileName = substr(md5($request->input('url')), 0, 10);

        //var_dump(download_video($ffmpegPath, $outputDirVideo, $fileName, $videoUrl));
        //die();
        /*
        // Tải video từ URL bang spaceviet
        if (!download_video($ffmpegPath, $outputDirVideo, $fileName, $videoUrl)) {
            return response()->json(['error' => 'Không thể tải xuống video.'], 200);
        }

        if ($extractFrames) {
            // Trích xuất các frame từ video
            //$extractor = new VideoFrameExtractor($ffmpegPath, $datas->id);
            if (!extract_frames($ffmpegPath, $outputDirImg, $fileName, $videoUrl)) {
                return response()->json(['error' => 'Không thể trích xuất hình ảnh từ video.'], 200);
            }

            // Đổi tên các file trong thư mục đầu ra
            rename_files($outputDirImg);
        }
		*/

        $_Download = new VideoFrameExtractor($ffmpegPath, $fileName);
        $_Download->setOutputDir($outputDirVideo);
        //var_dump($videoUrl);
        if ($_Download->downloadVideo($videoUrl)) {
            if ($extractFrames) {
                // Trích xuất các frame từ video
                $_Download->setOutputDir($outputDirImg);
                if (!$_Download->extractFrames($videoUrl)) {
                    return response()->json(['error' => 'Không thể trích xuất hình ảnh từ video.'], 200);
                }
                $_Download->renameFilesInDirectory($outputDirImg);
            }
            return response()->json([
                'message' => 'Video đã được tải xuống thành công.',
                'data' => $response
            ]);
        } else {
            return response()->json([
                'message' => 'Quá trình download video thất bại chưa xác định được lý do',
                'data' => $response
            ]);
        }
    }

    public function getVideo(Request $request, $id)
    {
        // Validate ID
        if (empty($id)) {
            return response()->json(['error' => 'UID không hợp lệ.'], 200);
        }


        $uid = $id;
        $basePath = config('file-path.base_path');
        $outputDirVideo = $basePath . 'Video/' . $uid;

        // Check if directory exists
        if (!file_exists($outputDirVideo)) {
            return response()->json(['error' => 'Không tìm thấy thư mục video cho người dùng.'], 200);
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
        // Kiểm tra IP, chỉ cho phép local upload file
        $clientIp = $request->ip();
        if (!$this->isLocalIp($clientIp)) {
            return response()->json(['error' => 'Không được phép upload từ IP không hợp lệ.'], 403);
        }


        // Validate ID
        if (empty($id)) {
            return response()->json(['error' => 'UID không hợp lệ.'], 200);
        }

        $uid = $id;
        $basePath = config('file-path.base_path');
        $outputDirVideo = $basePath . 'Video/' . $uid;

        if (!file_exists($outputDirVideo)) {
            mkdir($outputDirVideo, 0755, true);
        }

        $request->validate([
            'file' => 'required|mimes:mp4,avi,mov,wmv|max:5120000',
        ]);


        $file = $request->file('file');

        // Kiểm tra phần mở rộng file để tránh trường hợp upload file nguy hiểm (php, js,…)
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['mp4', 'avi', 'mov', 'wmv'];
        if (!in_array($extension, $allowedExtensions)) {
            return response()->json(['error' => 'Định dạng tệp không hợp lệ.'], 422);
        }

        // Tạo tên file duy nhất và di chuyển file đến thư mục đích
        $fileName = time() . '_' . uniqid() . '.' . $extension;
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
            return response()->json(['error' => 'Đường dẫn video không hợp lệ.'], 200);
        }

        $uid = $id;
        $basePath = config('file-path.base_path');
        $outputDirVideo = $basePath . 'Video/' . $uid;

        if (!file_exists($outputDirVideo)) {
            return response()->json(['error' => 'Không tìm thấy thư mục video cho người dùng.'], 200);
        }

        $absolutePath = $outputDirVideo . '/' . basename($videoPath);

        if (file_exists($absolutePath)) {
            unlink($absolutePath);
            return response()->json(['message' => 'Xóa video thành công.']);
        } else {
            return response()->json(['error' => 'Video không tồn tại.'], 200);
        }
    }

    public function getImage(Request $request, $id)
    {
        // Validate ID
        if (empty($id)) {
            return response()->json(['error' => 'UID không hợp lệ.'], 200);
        }

        $uid = $id;
        $basePath = config('file-path.base_path');
        $outputDirImage = $basePath . 'Images/' . $uid;


        // Check if directory exists
        if (!file_exists($outputDirImage)) {
            return response()->json(['error' => 'Không tìm thấy thư mục ảnh cho người dùng.'], 200);
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
            return response()->json(['error' => 'Đường dẫn ảnh không hợp lệ.'], 200);
        }
        $uid = $id;
        $basePath = config('file-path.base_path');
        $outputDirImage = $basePath . 'Images/' . $uid;

        if (!file_exists($outputDirImage)) {
            return response()->json(['error' => 'Không tìm thấy thư mục ảnh cho người dùng.'], 200);
        }
        $absolutePath = $outputDirImage . '/' . basename($imagePath); // Đảm bảo lấy đúng file

        if (file_exists($absolutePath)) {
            unlink($absolutePath);
            return response()->json(['message' => 'Xóa ảnh thành công.']);
        } else {
            return response()->json(['error' => 'Ảnh không tồn tại.'], 200);
        }
    }

    public function uploadImage(Request $request, $id)
    {
        // Validate ID
        if (empty($id)) {
            return response()->json(['error' => 'UID không hợp lệ.'], 200);
        }

        $uid = $id;
        $basePath = config('file-path.base_path');
        $outputDirImage = $basePath . 'Images/' . $uid;

        if (!file_exists($outputDirImage)) {
            mkdir($outputDirImage, 0755, true);
        }

        $request->validate([
            'file' => 'required|mimes:jpeg,png,jpg,gif,svg|max:20480', // 20MB max
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($outputDirImage, $fileName);

        $filePath = asset('FileData/Images/' . $uid . '/' . $fileName);

        return response()->json([
            'message' => 'Hình ảnh đã được tải lên thành công.',
            'filePath' => $filePath,
        ]);
    }

    public function updateImage(Request $request, $id)
    {
        $uid = $id;
        $basePath = config('file-path.base_path');
        $outputDirImage = $basePath . 'Images/' . $uid;

        if (!file_exists($outputDirImage)) {
            return response()->json(['error' => 'Thư mục ảnh không tồn tại.'], 200);
        }

        // Kiểm tra oldFileName
        $oldFileName = $request->input('oldFileName');
        if (empty($oldFileName)) {
            return response()->json(['error' => 'Tên file cũ không được gửi hoặc không hợp lệ.'], 200);
        }

        // Kiểm tra file ảnh
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'Không nhận được file ảnh.'], 200);
        }

        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();

        // Xác định file cũ và kiểm tra
        $targetFile = $outputDirImage . '/' . $oldFileName;
        if (!file_exists($targetFile)) {
            return response()->json(['error' => 'File cũ không tồn tại.'], 200);
        }

        // Xóa file cũ và lưu file mới
        unlink($targetFile);
        $image->move($outputDirImage, $oldFileName);



        return response()->json([
            'message' => 'Hình ảnh đã được cập nhật.',
            'imageUrl' => asset('FileData/Images/' . $uid . '/' . $oldFileName),
        ]);
    }

    public function cutVideo(Request $request)
    {
        $request->validate([
            'uid' => 'required',
            'video_path' => 'required|string',
            'start' => 'required|string',
            'end' => 'required|string',
        ]);

        $uid = $request->input('uid');
        $videoPath = $request->input('video_path');
        $start = $request->input('start');
        $end = $request->input('end');

        $ffmpegPath = '/usr/bin/ffmpeg';
        $newFileName = 'cut_' . uniqid();
        $basePath = config('file-path.base_path');
        $outputDirVideo = $basePath . 'Video/' . $uid;

        $extractor = new VideoFrameExtractor($ffmpegPath, $newFileName);
        $extractor->setOutputDir($outputDirVideo);

        $result = $extractor->cutVideo($videoPath, $start, $end);

        if ($result) {
            $newVideoPath = asset('FileData/Video/' . $uid . '/' . $newFileName . '.mp4');
            return response()->json([
                'status' => 'success',
                'new_video_path' => $newVideoPath
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Cắt video thất bại.'
            ]);
        }
    }

    public function getVideoByUrl(Request $request)
    {
        $uid = $request->input('uid');
        $url = $request->input('url');

        // Gọi private function để lấy Facebook UID từ url
        $facebookUid = $this->extractFacebookUidFromUrl($url);

        $end_cursor = $request->input('end_cursor', null);
        $extractFrames = $request->boolean('extract_frames');

        $homeAccount = new HomeAccountController(
            $request,
            $this->accountRepository,
            $this->fanpageManagerRepository
        );

        $homeAccount->vailidateUids($uid);

        if (!empty($facebookUid)) {

            if ($homeAccount->ConnectData) {

                $Accountuse = new FacebookAccount($homeAccount->getFacebookUse());

                if ($Accountuse->Connect) {

                    $_profile = new ProfileManager($Accountuse);

                    if ($end_cursor) {
                        $moreData  = $_profile->ViewMoreVideoProfile($uid, $end_cursor);

                        return response()->json($moreData, 200);
                    } else {
                        $ResultVideoData = $_profile->GetAllVideoProfile($facebookUid);
                    }

                    return response()->json($ResultVideoData, 200);
                } else {
                    $thongTin['status'] = false;
                    $thongTin['message'] = 'Không thể thiết lập kết nối Device Android';
                    return response()->json(['response' => $thongTin], 200);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $homeAccount->Message
                ], 200);
            }
        } else {
            return response()->json(['error' => 'Không tìm thấy UID Facebook.'], 200);
        }
    }

    private function extractFacebookUidFromUrl($url)
    {
        $findUid = new FacebookFind($url);
        return $findUid->GetFacebookID();
    }
}
