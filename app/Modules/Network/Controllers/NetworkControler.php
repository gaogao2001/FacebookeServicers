<?php

namespace App\Modules\Network\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Network\Repositories\NetworkRepositoryInterface;
use MongoDB\BSON\Regex;
use HoangquyIT\ProxyV6;

class NetworkControler extends Controller
{
	protected $settingRepository;
	protected $InterFaceControler;
	public function __construct(NetworkRepositoryInterface $settingRepository)
	{
		$this->settingRepository = $settingRepository;
		$this->InterFaceControler = new \HoangquyIT\NetworkControler\NetworkControler();
	}

	public function index()
	{
		
		$interface = $this->InterFaceControler->GetAllInterface();
		$NetworkInfo = $this->InterFaceControler->getNetworkStatistics();

		$LstInterFaces = array();
		foreach($interface as $key => $value)
		{
			$LstInterFaces[] = $key;
		}
		$existingSettings = $this->settingRepository->findByArray($LstInterFaces);
		return view('Network::NetwrorkControler', ['NetworkInfo' => $NetworkInfo, 'interface' => $interface, 'settings' => $existingSettings]);
	}
	

	public function updateSettings(Request $request)
	{
		$AllBody = $request->all();
		foreach($AllBody as $key => $value)
		{
			if(is_array(($value)))
			{
				// Điều kiện filter
				$filter = [$key  => ['$exists' => true]];

				// Kiểm tra xem dữ liệu đã tồn tại chưa
				$existingSettings = $this->settingRepository->findOne($filter);

				// Nếu dữ liệu chưa tồn tại thì insert
				if (!$existingSettings) {
					$this->settingRepository->insertOne([$key  => $value]);
				}
				else{
					// Nếu dữ liệu đã tồn tại thì update
					$this->settingRepository->updateOne($filter, ['$set' => [$key => $value]]);
				}
			}
		}

		return response()->json([
			'title' => 'Thành Công',
			'msg' => 'Cập nhật thành công!',
			'label' => 'success'
		]);
	}
	
	public function InterfaceControler(Request $request)
	{
		$data = [
			'port_name' => $request->input('port_name', null),
			'status' => $request->input('status', 0)
		];

		$return_var = null;
		$output = [];
		$command = '';

		// Kiểm tra trạng thái và thực hiện lệnh tương ứng
		if (intval($data['status']) > 0) {
			$command = 'sudo ip link set ' . escapeshellarg(trim($data['port_name'])) . ' up';
		} else {
			$command = 'sudo ip link set ' . escapeshellarg(trim($data['port_name'])) . ' down';
		}

		// Thực thi lệnh
		exec($command, $output, $return_var);

		// Trả kết quả về cho client
		return response()->json([
			'title' => $return_var === 0 ? 'Thành Công' : 'Thất Bại',
			'msg' => $return_var === 0 ? 'Cập nhật thành công!' : 'Có lỗi xảy ra khi thực thi lệnh.',
			'label' => $return_var === 0 ? 'success' : 'error',
			'command' => $command,
			'output' => $output,
			'return_var' => $return_var
		]);
	}

}
