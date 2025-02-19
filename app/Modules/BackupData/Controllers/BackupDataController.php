<?php

namespace App\Modules\BackupData\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Response;

class BackupDataController extends Controller
{

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

    // Hiển thị trang sao lưu dữ liệu
    public function index()
    {
        $databases = [];
        $collections = [];
        $backupFiles = [];

        try {
            $mongoClient = new \MongoDB\Client("mongodb://localhost:27017");
            $dbList = $mongoClient->listDatabases();

            foreach ($dbList as $database) {
                $dbName = $database->getName();
                $databases[] = ['database_name' => $dbName];

                // Lấy danh sách collections của từng database
                $db = $mongoClient->selectDatabase($dbName);
                foreach ($db->listCollections() as $collection) {
                    $collections[$dbName][] = $collection->getName();
                }
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể kết nối tới MongoDB: ' . $e->getMessage());
        }

        // Lấy danh sách file backup
        $backupDir = storage_path('app/BackupDb');

        if (is_dir($backupDir)) {
            $files = array_diff(scandir($backupDir), ['.', '..']);
            foreach ($files as $file) {
                $filePath = $backupDir . '/' . $file;
                if (is_file($filePath)) {
                    $backupFiles[] = [
                        'file_name' => $file,
                        'file_time' => date("Y-m-d H:i:s", filemtime($filePath)),
                        'file_size' => $this->formatFileSize(filesize($filePath)), // Tính kích thước file
                        'full_file_path' => $filePath,
                    ];
                }
            }
        }

        return view('BackupData::backup_data', [
            'databaseList' => $databases,
            'collections' => $collections, // Thêm danh sách collections
            'backupFiles' => $backupFiles
        ]);
    }



    /**
     * Định dạng dung lượng file thành KB, MB hoặc GB
     */
    private function formatFileSize($size)
    {
        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        } else {
            return $size . ' bytes';
        }
    }

    public function getCollections($database)
    {
        try {
            $mongoClient = new \MongoDB\Client("mongodb://localhost:27017");
            $db = $mongoClient->selectDatabase($database);

            $collections = [];
            foreach ($db->listCollections() as $collection) {
                $collections[] = $collection->getName();
            }

            return response()->json([
                'status' => true,
                'collections' => $collections
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể lấy danh sách collections: ' . $e->getMessage()
            ], 200);
        }
    }



    // Sao lưu cơ sở dữ liệu
    public function backupDatabase(Request $request)
    {
        $dbName = $request->input('database_name', '');
        $collectionName = $request->input('collection_name', '');

        if (empty($dbName)) {
            return response()->json([
                "response" => ["message" => "Tên database không hợp lệ!", "status" => false]
            ], 200);
        }

        $backupDir = storage_path('app/BackupDb');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = $backupDir . '/' . $dbName . ($collectionName ? "_$collectionName" : "") . "_$timestamp.gz";

        if (!is_dir($backupDir) && !mkdir($backupDir, 0777, true)) {
            return response()->json([
                "response" => ["message" => "Không thể tạo thư mục backup!", "status" => false]
            ], 200);
        }

        $mongodumpPath = '/usr/bin/mongodump';
        if (!file_exists($mongodumpPath)) {
            return response()->json([
                "response" => ["message" => "Không tìm thấy mongodump tại $mongodumpPath", "status" => false]
            ], 200);
        }

        $command = [$mongodumpPath, '--uri=mongodb://localhost:27017', "--db=$dbName", "--archive=$backupFile", '--gzip'];
        if ($collectionName) {
            $command[] = "--collection=$collectionName"; // Thêm collection nếu có
        }

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Backup error: ' . $process->getErrorOutput());

            return response()->json([
                "response" => ["message" => "Sao lưu thất bại: " . $process->getErrorOutput(), "status" => false]
            ], 200);
        }

        return response()->json([
            "response" => ["message" => "Sao lưu thành công!", "status" => true]
        ], 200);
    }


    // Khôi phục cơ sở dữ liệu
    public function restoreDatabase(Request $request)
    {
        $backupFile = $request->input('backup_file', '');

        if (!file_exists($backupFile)) {
            return response()->json([
                "response" => ["message" => "File backup không tồn tại.", "status" => false]
            ], 200);
        }

        $fileName = basename($backupFile);

        // Validate tên file
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $fileName)) {
            return response()->json([
                "response" => ["message" => "Tên file chứa ký tự không hợp lệ.", "status" => false]
            ], 200);
        }

        $dbName = strtok($fileName, '_');
        $mongorestorePath = '/usr/bin/mongorestore';

        if (!file_exists($mongorestorePath)) {
            return response()->json([
                "response" => ["message" => "Không tìm thấy mongorestore tại $mongorestorePath", "status" => false]
            ], 200);
        }

        try {
            // Kết nối tới MongoDB
            $client = new \MongoDB\Client("mongodb://localhost:27017");

            // Xóa dữ liệu cũ
            $database = $client->selectDatabase($dbName);
            $database->drop();
        } catch (\Exception $e) {
            return response()->json([
                "response" => ["message" => "Lỗi khi xóa dữ liệu cũ: " . $e->getMessage(), "status" => false]
            ], 200);
        }

        // Thực hiện khôi phục
        $command = "$mongorestorePath --uri=\"mongodb://localhost:27017\" --db=$dbName --archive=$backupFile --gzip 2>&1";
        exec($command, $output, $returnVar);
        if ($returnVar !== 0) {
            return response()->json([
                "response" => ["message" => "Khôi phục thất bại: " . implode("\n", $output), "status" => false]
            ], 200);
        }

        return response()->json([
            "response" => ["message" => "Khôi phục thành công!", "status" => true]
        ], 200);
    }

    // Xóa file backup
    public function deleteBackup(Request $request)
    {

        $backupFile = $request->input('backup_file', '');

        if (file_exists($backupFile) && unlink($backupFile)) {
            return response()->json([
                "response" => ["message" => "Xóa file backup thành công.", "status" => true]
            ], 200);
        }

        return response()->json([
            "response" => ["message" => "Không thể xóa file backup hoặc file không tồn tại.", "status" => false]
        ], 200);
    }

    public function downloadBackup(Request $request)
    {
        $backupFile = $request->input('backup_file', '');

        $backupDir = storage_path('app/BackupDb');

        // Ensure the backup file is within the backup directory
        $realPath = realpath($backupFile);
        if (strpos($realPath, $backupDir) !== 0 || !file_exists($realPath)) {
            return response()->json([
                "response" => ["message" => "File backup không tồn tại hoặc không hợp lệ.", "status" => false]
            ], 200);
        }

        $fileName = basename($backupFile);

        return response()->download($realPath, $fileName);
    }


    public function uploadBackup(Request $request)
    {
        $clientIp = $request->ip();
        if (!$this->isLocalIp($clientIp)) {
            return response()->json([
                "response" => ["message" => "Không được phép upload file từ IP không hợp lệ.", "status" => false]
            ], 403);
        }

        if (!$request->hasFile('backup_file')) {
            return response()->json([
                "response" => ["message" => "Không có file nào được tải lên.", "status" => false]
            ], 200);
        }

        $file = $request->file('backup_file');

        if ($file->getClientOriginalExtension() !== 'gz') {
            return response()->json([
                "response" => ["message" => "Loại file không hợp lệ. Vui lòng tải lên file .gz.", "status" => false]
            ], 200);
        }

        $backupDir = storage_path('app/BackupDb');

        if (!is_dir($backupDir) && !mkdir($backupDir, 0777, true)) {
            return response()->json([
                "response" => ["message" => "Không thể tạo thư mục backup.", "status" => false]
            ], 200);
        }

        $fileName = $file->getClientOriginalName();

        // Validate tên file
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $fileName)) {
            return response()->json([
                "response" => ["message" => "Tên file chứa ký tự không hợp lệ.", "status" => false]
            ], 200);
        }

        try {
            $file->move($backupDir, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                "response" => ["message" => "Lỗi khi tải lên file: " . $e->getMessage(), "status" => false]
            ], 200);
        }

        return response()->json([
            "response" => ["message" => "Tải lên file thành công.", "status" => true]
        ], 200);
    }

    public function deleteMultipleBackups(Request $request)
    {
        $files = $request->input('files', []);
        if (empty($files) || !is_array($files)) {
            return response()->json([
                "response" => ["message" => "Dữ liệu không hợp lệ.", "status" => false]
            ], 200);
        }

        $failures = [];
        foreach ($files as $backupFile) {
            if (file_exists($backupFile)) {
                if (!unlink($backupFile)) {
                    $failures[] = $backupFile;
                }
            } else {
                $failures[] = $backupFile;
            }
        }

        if (!empty($failures)) {
            return response()->json([
                "response" => ["message" => "Xóa không thành công các file: " . implode(', ', $failures), "status" => false]
            ], 200);
        }

        return response()->json([
            "response" => ["message" => "Xóa các file backup thành công.", "status" => true]
        ], 200);
    }
}
