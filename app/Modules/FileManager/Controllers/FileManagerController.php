<?php

namespace App\Modules\FileManager\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Facebook\Repositories\Account\AccountRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class FileManagerController extends Controller
{
    protected $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function fileManagerPage(Request $request)
    {
        // Lấy danh sách các nhóm tài khoản
        $groupAccounts = $this->accountRepository->getUniqueGroupAccounts();

        // Lấy nhóm được chọn từ request
        $selectedGroup = $request->input('group');

        $filePaths = [
            'videos' => [],
            'images' => []
        ];

        if ($selectedGroup) {
            // Lấy tất cả uid thuộc nhóm này
            $uids = $this->accountRepository->findByGroup($selectedGroup);

            // Lấy đường dẫn file dựa trên uids
            $filePaths = $this->accountRepository->getFilesByUids($uids);
        }

        return view('FileManager::file_manager', [
            'groupAccounts' => $groupAccounts,
            'filePaths' => $filePaths,
            'selectedGroup' => $selectedGroup,
        ]);
    }

    public function getFilesByGroup(Request $request)
    {

        $group = $request->input('group');
        $storagePath = $request->input('storage_path');

        Log::info('Group selected:', ['group' => $group]);

        if (!$group) {
            return response()->json(['error' => 'Group không được chọn.'], 200);
        }

        // if (!$storagePath || strpos($storagePath, '/var/www/') !== 0) {
        //     return response()->json(['error' => 'Đường dẫn lưu trữ không hợp lệ.'], 200);
        // }

        $uids = $this->accountRepository->findByGroup($group);

        Log::info('UIDs found:', ['uids' => $uids]);

        return response()->json([
            'uids' => $uids
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    // Phương thức mới để lấy cấu trúc thư mục
    public function getDirectoryTree()
    {
        $basePath = '/var/www/';
        $tree = $this->generateDirectoryTree($basePath);

        return response()->json($tree);
    }

    private function generateDirectoryTree($dir, $basePath = '/var/www/')
    {
        $tree = [];

        if (!is_dir($dir)) {
            return $tree;
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $fullPath = realpath($dir . DIRECTORY_SEPARATOR . $file);
            if ($fullPath && strpos($fullPath, $basePath) === 0) {
                if (is_dir($fullPath)) {
                    $tree[] = [
                        'text' => $file,
                        'children' => $this->generateDirectoryTree($fullPath, $basePath),
                        'path' => $fullPath
                    ];
                } else {
                    // Nếu cần hiển thị file, có thể thêm vào đây
                }
            }
        }

        return $tree;
    }

    public function folder()
    {
        return view('FileManager::folder');
    }

    public function getDirectories(Request $request)
    {
        $path = realpath($request->input('path'));

        if (!$path || strpos($path, '/var/www/html/FileData') !== 0) {
            return response()->json(['error' => 'Thư mục không hợp lệ.'], 200);
        }

        $directories = array_filter(glob($path . '/*'), 'is_dir');
        $response = array_map('basename', $directories);

        return response()->json(['success' => $response]);
    }


    // New method to create a folder
    public function createFolder(Request $request)
    {
        $path = realpath($request->input('path'));
        $newFolderName = trim(basename($request->input('newFolderName')));

        if (!$path || strpos($path, '/var/www/html/FileData') !== 0 || empty($newFolderName)) {
            return response()->json(['error' => 'Thư mục không hợp lệ hoặc tên thư mục không hợp lệ.'], 200);
        }

        $newFolderPath = $path . DIRECTORY_SEPARATOR . $newFolderName;
        if (is_dir($newFolderPath)) {
            return response()->json(['error' => 'Thư mục đã tồn tại.'], 200);
        }

        if (mkdir($newFolderPath, 0777, true)) {
            return response()->json(['success' => 'Thư mục đã được tạo thành công.']);
        } else {
            return response()->json(['error' => 'Không thể tạo thư mục.'], 200);
        }
    }

    //function đổi path config/file-path.php

    public function updateFilePath(Request $request)
    {

        // Validate đầu vào
        $request->validate([
            'selectedPath' => 'required|string',
        ]);

        $newBasePath = rtrim($request->input('selectedPath'), '/') . '/';

        // Đường dẫn tới file cấu hình
        $configPath = config_path('file-path.php');


        // Kiểm tra file cấu hình tồn tại
        if (!File::exists($configPath)) {
            return response()->json(['error' => 'File cấu hình không tồn tại.'], 404);
        }

        // Nội dung mới cho file cấu hình
        $newConfigContent = "<?php

        return [
            'base_path' => '{$newBasePath}',
        ];
        ";


        // Ghi nội dung mới vào file cấu hình
        try {
            File::put($configPath, $newConfigContent);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể ghi vào file cấu hình.'], 200);
        }

        // Xóa cache cấu hình để áp dụng thay đổi
        Artisan::call('config:clear');

        return response()->json(['message' => 'Cấu hình đã được cập nhật thành công.']);
    }

    public function deleteFolder(Request $request)
    {
        $path = realpath($request->input('path'));

        // Kiểm tra tính hợp lệ của đường dẫn
        if (!$path || strpos($path, '/var/www/html/FileData') !== 0 || !is_dir($path)) {
            return response()->json(['error' => 'Đường dẫn folder không hợp lệ.'], 200);
        }

        // Sử dụng hàm của Laravel để xóa thư mục cùng toàn bộ nội dung
        try {
            \Illuminate\Support\Facades\File::deleteDirectory($path);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể xóa thư mục: ' . $e->getMessage()], 200);
        }

        return response()->json(['success' => 'Thư mục đã được xóa thành công.']);
    }

    public function getAllImages(Request $request)
    {
        // Sử dụng thư mục public chứa hình ảnh làm base
        $basePath = public_path('FileData/Images');

        $tree = [];

        if (is_dir($basePath)) {
            // Lấy tất cả các file trong thư mục base (đệ quy)
            $files = File::allFiles($basePath);
            foreach ($files as $file) {
                $extension = strtolower($file->getExtension());
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    // Lấy đường dẫn tương đối so với thư mục gốc
                    $relativePath = str_replace($basePath, '', $file->getRealPath());
                    // Nếu hình ảnh nằm trong thư mục con dạng chỉ chứa số (ví dụ: /1046151503/filename)
                    if (preg_match("#^/([\d]+)/#", $relativePath, $matches)) {
                        $folder = $matches[1];
                        $imageInfo = [
                            'name' => $file->getFilename(),
                            'path' => $file->getRealPath(),
                            'url'  => asset('FileData/Images' . $relativePath),
                        ];
                        // Gom nhóm theo thư mục
                        $tree[$folder][] = $imageInfo;
                    }
                }
            }
        } else {
            Log::error("Base path không hợp lệ: " . $basePath);
        }


        return response()->json($tree);
    }

    public function getAllVideos(Request $request)
    {
        // Sử dụng thư mục public chứa video làm base
        $basePath = public_path('FileData/Video');

        $tree = [];

        if (is_dir($basePath)) {
            // Lấy tất cả các file trong thư mục base (đệ quy)
            $files = File::allFiles($basePath);
            foreach ($files as $file) {
                $extension = strtolower($file->getExtension());
                if (in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'])) {
                    // Lấy đường dẫn tương đối so với thư mục gốc
                    $relativePath = str_replace($basePath, '', $file->getRealPath());
                    // Nếu video nằm trong thư mục con dạng chỉ chứa số (ví dụ: /1046151503/filename)
                    if (preg_match("#^/([\d]+)/#", $relativePath, $matches)) {
                        $folder = $matches[1];
                        $videoInfo = [
                            'name' => $file->getFilename(),
                            'path' => $file->getRealPath(),
                            'url'  => asset('FileData/Video' . $relativePath),
                        ];
                        // Gom nhóm theo thư mục
                        $tree[$folder][] = $videoInfo;
                    }
                }
            }
        } else {
            Log::error("Base path không hợp lệ: " . $basePath);
        }

   

        return response()->json($tree);
    }
}
