<?php

namespace App\Modules\CronTab\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\CronTab\Repositories\CrontabRepositoryInterface;
use HoangquyIT\CrontabManager\Scheduler;

class CrontabController extends Controller
{
    protected $crontabRepository;
    protected $CrontabWorker;

    public function __construct(CrontabRepositoryInterface $crontabRepository)
    {
        $this->crontabRepository = $crontabRepository;
        $this->CrontabWorker = new Scheduler();
    }

    public function crontabPage()
    {
        // Build the tree structure for the directory.
        $directory = base_path('CrontabService');
        $phpFilesTree = $this->getPhpFilesTree($directory);
        $CronList = $this->getCrontabList();
        return view('CronTab::crontab', compact('phpFilesTree', 'CronList'));
    }

    /**
     * Recursively builds a tree of PHP files and folders.
     * Every folder is included, even if it has no PHP files.
     */
    private function getPhpFilesTree($directory)
    {
        $folder = [
            'name'     => basename($directory) ?: $directory, // Use basename as display name
            'fullPath' => $directory,
            'type'     => 'folder',
            'children' => [],
        ];

        $entries = scandir($directory);
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $fullPath = $directory . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($fullPath)) {
                // Recursively get the subtree for folders.
                $folder['children'][] = $this->getPhpFilesTree($fullPath);
            } else {
                if (strtolower(pathinfo($entry, PATHINFO_EXTENSION)) === 'php') {
                    $folder['children'][] = [
                        'name' => $entry,
                        'type' => 'file',
                        'path' => $fullPath, // Full path used when file is selected.
                    ];
                }
            }
        }
        return $folder;
    }

    /**
     * Lấy danh sách cron jobs và trả về dưới dạng mảng
     */
    private function getCrontabList()
    {
        $crontabList = shell_exec("crontab -l 2>/dev/null");

        // Kiểm tra nếu crontab rỗng hoặc có lỗi
        if (empty($crontabList)) {
            return [];
        }

        // Chuyển crontab thành mảng, loại bỏ dòng trống
        return array_filter(array_map('trim', explode("\n", $crontabList)));
    }

    /**
     * Thêm cron job mới vào crontab
     */
    public function submitCronTab(Request $request)
    {
        $command = $request->input('command');
        $quantity = (int) $request->input('quantity', 1);

        
        // Kiểm tra lệnh nhập vào
        if (empty($command)) {
            return response()->json([
                'success' => false,
                'message' => 'Command is required.'
            ]);
        }
        // Thoát ký tự đặc biệt để tránh lỗi shell injection
        $escapedCommand = escapeshellarg($command);
        // Lệnh thêm cron job
        $fullCommand = "(crontab -l 2>/dev/null; echo $escapedCommand) | crontab -";
        // Thực thi lệnh trên shell
        for ($i = 0; $i < $quantity; $i++) {
            shell_exec($fullCommand);
        }
        // Gọi hàm lấy danh sách cron jobs
        $crontabArray = $this->getCrontabList();

        // Kiểm tra xem lệnh vừa thêm có tồn tại không
        if (in_array($command, $crontabArray)) {
            return response()->json([
                'success' => true,
                'command' => $command,
                'message' => 'Thêm mới thành công !!',
                'crontab' => $crontabArray
            ]);
        } else {
            return response()->json([
                'success' => false,
                'command' => $command,
                'message' => 'Thêm thất bại. Vui lòng kiểm tra lại quyền hoặc lệnh !!',
                'crontab' => $crontabArray
            ]);
        }
    }

    public function show($id)
    {
        $pathData = $this->crontabRepository->findByIds($id);
        if (!$pathData) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $pathData], 200);
    }

    // 2. Update path by ID
    /**
     * Cập nhật một dòng cron job duy nhất trong crontab
     */
    public function updateCronTab(Request $request, $index)
    {
        $newCommand = $request->input('command');
        // Remove debugging printed output
        if ($index === null || empty($newCommand)) {
            return response()->json([
                'success' => false,
                'message' => 'Both index and command are required.'
            ]);
        }
        $crontabList = shell_exec("crontab -l 2>/dev/null");
        $crontabArray = explode("\n", trim($crontabList));
        if (!isset($crontabArray[intval($index)])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid index.'
            ]);
        }
        $crontabArray[intval($index)] = trim($newCommand);
        $newCrontab = implode("\n", $crontabArray) . "\n";
        shell_exec("echo " . escapeshellarg($newCrontab) . " | crontab -");
        $updatedCrontabArray = explode("\n", trim(shell_exec("crontab -l 2>/dev/null")));
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công !!',
            'crontab' => $updatedCrontabArray
        ]);
    }

    // 3. Delete path by ID
    /**
     * Xóa một dòng cron job khỏi crontab (nếu có nhiều dòng giống nhau, chỉ xóa 1 dòng)
     */
    public function deleteCronTab(Request $request)
    {
        $index = $request->input('index');

        // Kiểm tra nếu không có index được nhập vào
        if ($index === null) {
            return response()->json([
                'success' => false,
                'message' => 'Index is required for deletion.'
            ]);
        }

        // Lấy danh sách crontab hiện tại
        $crontabArray = $this->getCrontabList();

        // Kiểm tra xem index có hợp lệ không
        if (!isset($crontabArray[$index])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid index.'
            ]);
        }

        // Xóa duy nhất 1 dòng lệnh tại index
        unset($crontabArray[$index]);

        // Ghi danh sách mới vào crontab
        $newCrontab = implode("\n", $crontabArray) . "\n";
        file_put_contents("/tmp/crontab.txt", $newCrontab);
        shell_exec("crontab /tmp/crontab.txt && rm /tmp/crontab.txt");

        // Kiểm tra lại sau khi xóa
        $newCrontabArray = $this->getCrontabList();

        return response()->json([
            'success' => true,
            'message' => 'Xóa thành công !!',
            'crontab' => $newCrontabArray
        ]);
    }

    /**
     * Xóa tất cả cron jobs khỏi crontab
     */
    public function deleteAllCronTab(Request $request)
    {
        // Ghi nội dung rỗng vào file tạm và cập nhật crontab
        file_put_contents("/tmp/crontab.txt", "");
        shell_exec("crontab /tmp/crontab.txt && rm /tmp/crontab.txt");

        return response()->json([
            'success' => true,
            'message' => 'Xóa hết thành công !!',
            'crontab' => []
        ]);
    }
}
