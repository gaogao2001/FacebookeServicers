<?php
namespace App\Modules\ServiceController\Controller;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ServiceController extends Controller
{
    public function index()
	{
		$status = shell_exec('systemctl is-active FacebookSeedings.timer');
		$FacebookSeedingsStatus = trim($status) === 'active' ? 'running' : 'stopped';
		$startTime = null;
		$startTime = shell_exec('systemctl show -p ActiveEnterTimestamp FacebookSeedings.timer');
		
		if (!empty($startTime)) {
			$startTime = trim(str_replace('ActiveEnterTimestamp=', '', $startTime));
			// Kiểm tra nếu $startTime là chuỗi thời gian hợp lệ
			if (strtotime($startTime) !== false) {
				$startTime = Carbon::parse($startTime, 'UTC')
								   ->setTimezone('Asia/Ho_Chi_Minh')
								   ->format('H:i:s d-m-Y');
			} else {
				$startTime = null; // Đặt null nếu không phải thời gian hợp lệ
			}
		}

		$FacebookSeedings = array('status' => $FacebookSeedingsStatus, 'startTime' => $startTime);

		return view('ServiceController::service_manager_page', compact('FacebookSeedings'));
	}


    public function getHistory()
    {
        $command = 'sudo journalctl -u FacebookSeedings.timer --since "1 week ago" --no-pager';
        $history = shell_exec($command);
        
        if ($history === null) {
            return response()->json(['error' => 'Failed to retrieve logs'], 500);
        }

        return response()->json(['history' => nl2br($history)]);
    }

}