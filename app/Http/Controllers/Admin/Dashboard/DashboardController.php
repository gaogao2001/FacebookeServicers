<?php
namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Repositories\Google\Account\AccountRepository;
use App\Repositories\Google\Account\AccountRepositoryInterface;
use Illuminate\Http\Request;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;
use HoangquyIT\VideoFrameExtractor;
use HoangquyIT\ModelFacebook\FbMediaDownloader;


class DashboardController extends Controller
{
    private $accountService;
    private $acccountGoogleService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function dashboard()
    {
        return view('admin.pages.dashboard');
    }

    public function index()
	{
		$user = $this->accountService->findOne(Auth::id());
		// Kiểm tra trạng thái của dịch vụ smbd 
		$status = shell_exec('systemctl is-active smbd');
		$serviceStatus = trim($status) === 'active' ? 'running' : 'stopped';
		
		$status = shell_exec('systemctl is-active facebook');
		$FbService = trim($status) === 'active' ? 'running' : 'stopped';
		
		$status = shell_exec('systemctl is-active 3proxy');
		$ProxyService = trim($status) === 'active' ? 'running' : 'stopped';
		
		$status = shell_exec('systemctl is-active mongodb-backup.timer');
		$BackupService = trim($status) === 'active' ? 'running' : 'stopped';
		
		$status = shell_exec('systemctl is-active zalo');
		$ZaloService = trim($status) === 'active' ? 'running' : 'stopped';
		
		$status = shell_exec('systemctl is-active network_monitor.service');
		$NetworkMonitor = trim($status) === 'active' ? 'running' : 'stopped';
		
		$status = shell_exec('systemctl is-active system_monitor');
		$SystemMonitor = trim($status) === 'active' ? 'running' : 'stopped';

		return view('admin.pages.dashboard', compact('user', 'serviceStatus', 'FbService', 'ZaloService', 'ProxyService', 'BackupService', 'NetworkMonitor', 'SystemMonitor'));
	}

    public function inforAccount()
    {
        $user = $this->accountService->findOne(Auth::id());
        return view('admin.pages.admin_info_page', compact('user'));
    }

    public function show_role_page()
    {
        $user = $this->accountService->findOne(Auth::id());
        $menus = config('menus');

        return view('admin.pages.role_page', compact('user', 'menus'));
    }

    public function show_system_info()
    {
        $user = $this->accountService->findOne(Auth::id());
        return view('admin.pages.system_info', compact('user'));
    }

    public function loadSystemInfo()
    {
        return response()->json(deviceInfo());
    }

    public function showBackupData()
    {
        return view('admin.pages.backup_data');
    }

    public function reboot(Request $request)
    {
        return response()->json(restart_system());
    }

    public function shutdown(Request $request)
    {
        return response()->json(shutdown_system());
    }
	
	public function SeviceControler(Request $request)
	{
		$data = $request->all();
		if (empty($data['option']) || empty($data['name'])) {
			return response()->json([
				'success' => false,
				'message' => 'Dữ liệu không đầy đủ. Vui lòng kiểm tra lại!'
			]);
		}

		$name = trim($data['name']);
		$option = $data['option'];

		try {
			if ($option == 'ON') {
				shell_exec("sudo /bin/systemctl start $name");
				return response()->json([
					'success' => true,
					'message' => "Dịch vụ '$name' đã được bật thành công!"
				]);
			} elseif ($option == 'OFF') {
				shell_exec("sudo /bin/systemctl stop $name");
				return response()->json([
					'success' => true,
					'message' => "Dịch vụ '$name' đã bị tắt thành công!"
				]);
			} elseif ($option == 'RESTART') {
				shell_exec("sudo /bin/systemctl restart $name");
				return response()->json([
					'success' => true,
					'message' => "Dịch vụ '$name' đã được khởi động lại thành công!"
				]);
			} else {
				return response()->json([
					'success' => false,
					'message' => "Tùy chọn '$option' không hợp lệ. Vui lòng thử lại!"
				]);
			}
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Có lỗi xảy ra khi thực hiện lệnh: ' . $e->getMessage()
			]);
		}
	}
}
