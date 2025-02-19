<?php

namespace App\Http\Controllers\Admin\Network;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Network\NetworkRepositoryInterface;
use MongoDB\BSON\Regex;
use HoangquyIT\ProxyV6;

class NetworkControler extends Controller
{

	protected $settingRepository;
	public function __construct(NetworkRepositoryInterface $settingRepository)
	{
		$this->settingRepository = $settingRepository;
	}

	public function index()
	{
		$defaultConfig = array(
			'pppoe_username' => 'default',
			'pppoe_password' => 'default',
			'port_min' => 1000,
			'port_max' => 10000,
			'network' => 'FPT',
			'ipv4' => true,
			'ipv6' => true
		);

		// Điều kiện filter
		$filter = ['pppoe' => ['$exists' => true]];

		// Kiểm tra xem dữ liệu đã tồn tại chưa
		$existingSettings = $this->settingRepository->findOne($filter);

		// Nếu dữ liệu chưa tồn tại thì insert
		if (!$existingSettings) {
			$this->settingRepository->insertOne(['pppoe' => $defaultConfig]);
		}

		// Truy vấn lại tài liệu mới
		$settings = $this->settingRepository->findOne($filter);

		return view('admin.pages.Netwrork.NetwrorkControler', ['settings' => $settings]);
	}
	

	public function updateSettings(Request $request)
	{
		
		$data = [
			'pppoe_username' => $request->input('pppoe_username', null),
			'pppoe_password' => $request->input('pppoe_password', null),
			'port_min' => (int) $request->input('port_min', 1000),
			'port_max' => (int) $request->input('port_max', 10000),
			'network' => $request->input('network', 'FPT'),
			'ipv4' => $request->has('ipv4'),
			'ipv6' => $request->has('ipv6')
		];
		
		// Define the filter
		$filter = ['pppoe' => ['$exists' => true]];

		$update = ['$set' => ['pppoe' => $data]];
		$options = ['upsert' => true];

		$this->settingRepository->updateOne($filter, $update, $options);

		return response()->json([
			'title' => 'Thành Công',
			'msg' => 'Cập nhật thành công!',
			'label' => 'success'
		]);
	}
}
