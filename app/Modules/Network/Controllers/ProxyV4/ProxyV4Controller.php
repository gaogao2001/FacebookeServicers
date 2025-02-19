<?php

namespace App\Modules\Network\Controllers\ProxyV4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Network\Repositories\NetworkRepositoryInterface;


class ProxyV4Controller extends Controller
{
	protected $settingRepository;
	protected $InterFaceControler;
	
	public function __construct(NetworkRepositoryInterface $settingRepository)
	{
		$this->settingRepository = $settingRepository;
		$this->InterFaceControler = new \HoangquyIT\NetworkControler\NetworkControler();
	}
	
    public function proxyV4SystemPage()
	{

		// Đường dẫn thư mục chứa các tệp cấu hình PPPoE
		$directory = "/etc/ppp/peers/";
		$configs = [];

		// Kiểm tra nếu thư mục tồn tại
		if (is_dir($directory)) {
			// Lấy danh sách tất cả các tệp có đuôi .pppoe trong thư mục
			$files = glob($directory . "*.pppoe");

			foreach ($files as $filePath) {
				// Đọc nội dung tệp
				$fileContent = file_get_contents($filePath);
				$fileLines = explode(PHP_EOL, $fileContent);
				// Lấy tên cổng mạng từ tên tệp
				$fileName = basename($filePath); // Chỉ lấy tên tệp
				$portName = explode('.', $fileName)[0]; // Tên cổng mạng (phần trước dấu .) đây chính là tên chính của card cổng mạng
				//
				$interFaces = new \HoangquyIT\NetworkControler\NetworkControler();
				$resultCheck = $interFaces->getNetworkInterfaceInfo(trim($portName));
				
				// Phân tích nội dung tệp để tách các thông tin chính
				$configDetails = [
					'ip_address' => "Không tìm thấy IPV4", // Địa chỉ IP của cổng
					'ipv6_address' => "Không tìm thấy IPV6", // Địa chỉ IP của cổng
					'connection_status' => false,
					'username' => null,
					'password' => null,
					'nic' => null,
					'ifname' => null,
					'mac_address' => $resultCheck->mac_address ?? 'Không tìm thấy MAC',
					'ipv6' => null,
					'time_connected' => null,
					'usepeerdns' => false
				];
				

				$interfaces = null;
				foreach ($fileLines as $line) {
					$line = trim($line);
					if (stripos($line, 'user "') === 0) {
						$configDetails['username'] = trim(str_replace(['user "', '"'], '', $line));
					} elseif (stripos($line, 'password "') === 0) {
						$configDetails['password'] = trim(str_replace(['password "', '"'], '', $line));
					} elseif (stripos($line, 'nic-') === 0) {
						$configDetails['nic'] = trim(str_replace('nic-', '', $line));
					} elseif (stripos($line, 'ifname ') === 0) {
						$configDetails['ifname'] = trim(str_replace('ifname ', '', $line));
						$interfaces = str_replace(".pppoe", ".pppo", trim(str_replace('ifname ', '', $line)));
					} elseif (stripos($line, '+ipv6') === 0 || stripos($line, 'ipv6') !== false) {
						$configDetails['ipv6'] = true;
					} elseif (stripos($line, 'usepeerdns') === 0) {
						$configDetails['usepeerdns'] = true;
					}
				}

				// Lấy địa chỉ IP từ hệ thống nếu có `ifname`
				if (!empty($configDetails['ifname'])) {
					$ifname = $configDetails['ifname'];

					// Lấy địa chỉ IP
					$resultCheck = $interFaces->getNetworkInterfaceInfo(trim($interfaces));
					$commandIP = "ip addr show " . escapeshellarg($interfaces) . " | grep 'inet ' | awk '{print $2}'";
					
					$ipOutput = shell_exec($commandIP);
					
					if (!$resultCheck->status) {
						$interfaces = str_replace(".pppo", ".pppoe", $interfaces);
						$resultCheck = $interFaces->getNetworkInterfaceInfo(trim($interfaces));
					}
					if ($resultCheck->status)
					{
						
						$configDetails['ip_address'] = $resultCheck->ipv4_address ?? "Không tìm thấy IPV4";
						$configDetails['ipv6_address'] = $resultCheck->ipv6_address ?? "Không tìm thấy IPV6";
						$configDetails['connection_status'] = $resultCheck->connection_status ?? false;
						$configDetails['time_connected'] = $resultCheck->time_connected ?? '00:00:00';
					}
					
				}
				// Thêm cấu hình vào danh sách
				$configs[] = $configDetails;
			}
		}
		$interface = $this->InterFaceControler->GetAllInterface();
		$LstInterFaces = array();
		foreach($interface as $key => $value)
		{
			$LstInterFaces[] = $key;
		}
		$existingSettings = $this->settingRepository->findByArray($LstInterFaces);
		// Truyền dữ liệu sang view
		return view('Network::ProxyV4.proxy_v4_system', ['interface' => $existingSettings, 'configs' => $configs]);
	}

	public function CreateProxyV4(Request $request)
	{
		$thongtin = array('status' => false, 'message' => "Số lượng proxy cần lớn hơn 0");
		$body = $request->all();
		
		if (!empty($body["limit"]) && intval($body["limit"]) > 0 && !empty($body["selectInterface"])) {
			$limit = intval($body["limit"]);
			$_Interface = trim($body["selectInterface"]);
			$filter = [
				$_Interface => [
					'$exists' => true
				]
			];
			
			$settings = $this->settingRepository->findOne($filter);
			
			if (!$settings) {
				$thongtin = array('status' => false, 'message' => "Không tìm thấy cấu hình nhà mạng");
			} else {
				
				$pppoe_username = $settings->$_Interface->pppoe_username ?? "default_username";
				//var_dump($pppoe_username);
				//exit();
				$pppoe_password = $settings->$_Interface->pppoe_password ?? "default_password";
				$interfaces = $_Interface ?? "enp6s0";
				//var_dump($interfaces);
				// exit();
				$ipv4 = $settings->$_Interface->ipv4 ?? true;
				$ipv6 = $settings->$_Interface->ipv6 ?? true;

				// Lặp qua danh sách interfaces
				$interfacesList = explode(',', $interfaces);
				$totalCreated = 0; // Biến đếm số file đã tạo

				foreach ($interfacesList as $network) {
					$network = trim($network);

					// Kiểm tra số lượng file trùng tên trong thư mục
					$directory = "/etc/ppp/peers/";
					if (!is_dir($directory)) {
						mkdir($directory, 0755, true);
					}

					// Tạo file cấu hình PPPoE cho từng interface
					while ($totalCreated < $limit) {
						$existingFiles = glob($directory . $network . "*.pppoe");
						$fileCount = count($existingFiles) + 1; // Số thứ tự tăng dần cho file mới
						$fileName = $network . ".ip" . $fileCount . ".pppoe";
						$filePath = $directory . $fileName;

						$config = [];
						$config[] = "noipdefault";
						$config[] = 'password "' . $pppoe_password . '"';
						$config[] = "noauth";
						$config[] = "#";
						$config[] = "persist";
						$config[] = "plugin rp-pppoe.so";
						$config[] = "nic-" . $network;
						$config[] = 'user "' . $pppoe_username . '"';
						$config[] = "#";
						// Kiểm tra hệ điều hành Linux (Ubuntu hoặc Debian)
						$osReleaseFile = '/etc/os-release';
						$linuxDistro = "Thông tin hệ điều hành không xác định.";
						if (file_exists($osReleaseFile)) {
							$osData = parse_ini_file($osReleaseFile, false, INI_SCANNER_RAW);
							if (isset($osData['PRETTY_NAME'])) {
								$linuxDistro = $osData['PRETTY_NAME'];
							} elseif (isset($osData['ID'])) {
								$linuxDistro = $osData['ID'];
							}
						}

						if (stripos($linuxDistro, '22') !== false) {
							$config[] = "defaultroute";
							$config[] = "defaultroute-metric 10" . $fileCount;
							$config[] = "replacedefaultroute";
						} else {
							$config[] = "usepeerdns";
						}
						$config[] = "#";
						$config[] = "ifname " . $fileName;
						if ($ipv4) {
							//$config[] = "# open IPv4";
							//$config[] = "+ipv4";
						}
						if ($ipv6) {
							$config[] = "# open IPv6";
							$config[] = "+ipv6";
							$config[] = "ipv6cp-accept-local";
							$config[] = "ipv6cp-accept-remote";
						}

						// Lưu file cấu hình vào hệ thống
						try {
							file_put_contents($filePath, implode(PHP_EOL, $config));
							$totalCreated++; // Tăng số lượng file đã tạo

							$thongtin = array(
								'status' => true,
								'message' => "Cấu hình PPPoE đã được tạo thành công",
								'files_created' => $totalCreated
							);

							// Kiểm tra nếu đã đạt đủ số lượng yêu cầu
							if ($totalCreated >= $limit) {
								break;
							}
						} catch (\Exception $e) {
							$thongtin = array(
								'status' => false,
								'message' => "Không thể tạo file cấu hình PPPoE: " . $e->getMessage()
							);
							break;
						}
					}

					// Dừng nếu đã đạt giới hạn
					if ($totalCreated >= $limit) {
						break;
					}
				}
			}
		}

		return response()->json($thongtin);
	}

	public function DeleteProxyv4(Request $request)
	{
		$thongtin = array('status' => false, 'message' => "file_path không thể thiếu");
		$body = $request->all();
		
		if (!empty($body["name"])) 
		{
			$filePath = '/etc/ppp/peers/'.$body["name"];
			$pppoeName = trim($body["name"]); // Tên PPPoE từ tên tệp

			// Kiểm tra xem tệp có tồn tại không
			if (file_exists($filePath)) {
				try {
					// Dừng PPPoE nếu đang chạy
					exec("sudo /usr/bin/poff " . escapeshellarg($pppoeName), $output, $returnCode);

					if ($returnCode === 0 || $returnCode === 1) { // 0: dừng thành công, 1: PPPoE không chạy
						// Thử xóa tệp
						if (unlink($filePath)) {
							$thongtin = array(
								'status' => true,
								'message' => "Tệp đã được xóa thành công và PPPoE đã dừng"
							);
						} else {
							$thongtin = array(
								'status' => false,
								'message' => "Không thể xóa tệp. Vui lòng thử lại!"
							);
						}
					} else {
						$thongtin = array(
							'status' => false,
							'message' => "Không thể dừng PPPoE: " . implode(", ", $output)
						);
					}
				} catch (\Exception $e) {
					$thongtin = array(
						'status' => false,
						'message' => "Đã xảy ra lỗi khi dừng PPPoE hoặc xóa tệp: " . $e->getMessage()
					);
				}
			} else {
				$thongtin = array(
					'status' => false,
					'message' => "Tệp không tồn tại hoặc đã bị xóa"
				);
			}
		}

		return response()->json($thongtin);
	}
	
	public function ConnectProxyv4(Request $request)
	{
		$thongtin = array('status' => false, 'message' => "name không thể thiếu");
		$body = $request->all();

		if (!empty($body["name"])) 
		{
			$pppoeName = trim($body["name"]); // Tên PPPoE từ tên tệp

			// Kiểm tra và thực hiện kết nối PPPoE
			try {
				exec("sudo /usr/bin/pon " . escapeshellarg($pppoeName), $output, $returnCode);

				if ($returnCode === 0) { // 0: kết nối thành công
					$thongtin = array(
						'status' => true,
						'message' => "Kết nối PPPoE thành công",
						'details' => implode(", ", $output)
					);
				} else {
					$thongtin = array(
						'status' => false,
						'message' => "Không thể kết nối PPPoE: " . implode(", ", $output)
					);
				}
			} catch (\Exception $e) {
				$thongtin = array(
					'status' => false,
					'message' => "Đã xảy ra lỗi khi kết nối PPPoE: " . $e->getMessage()
				);
			}
		}

		return response()->json($thongtin);
	}


	public function DisConnectProxyv4(Request $request)
	{
		$thongtin = array('status' => false, 'message' => "name không thể thiếu");
		$body = $request->all();

		if (!empty($body["name"])) 
		{
			$pppoeName = trim($body["name"]); // Tên PPPoE từ tên tệp

			// Kiểm tra và thực hiện ngắt kết nối PPPoE
			try {
				exec("sudo /usr/bin/poff " . escapeshellarg($pppoeName), $output, $returnCode);

				if ($returnCode === 0 || $returnCode === 1) { // 0: ngắt kết nối thành công, 1: PPPoE không chạy
					$message = ($returnCode === 0) ? "Ngắt kết nối PPPoE thành công" : "PPPoE không chạy nhưng ngắt kết nối vẫn thành công";
					$thongtin = array(
						'status' => true,
						'message' => $message,
						'details' => implode(", ", $output)
					);
				} else {
					$thongtin = array(
						'status' => false,
						'message' => "Không thể ngắt kết nối PPPoE: " . implode(", ", $output)
					);
				}
			} catch (\Exception $e) {
				$thongtin = array(
					'status' => false,
					'message' => "Đã xảy ra lỗi khi ngắt kết nối PPPoE: " . $e->getMessage()
				);
			}
		}

		return response()->json($thongtin);
	}

	public function BtnReloadIp(Request $request)
	{
		$thongtin = array('status' => false, 'time_connected' => null);
		$body = $request->all();

		if (!empty($body["name"])) 
		{
			$pppoeName = trim($body["name"]);
			// Kiểm tra và thực hiện ngắt kết nối PPPoE
			try {
				exec("sudo /usr/bin/poff " . escapeshellarg($pppoeName), $output, $returnCode);

				if ($returnCode === 0 || $returnCode === 1) { // 0: ngắt kết nối thành công, 1: PPPoE không chạy
					try {
						exec("sudo /usr/bin/pon " . escapeshellarg($pppoeName), $output, $returnCode);
		
						if ($returnCode === 0) { // 0: kết nối thành công
							$thongtin = array(
								'status' => true,
								'message' => "Kết nối PPPoE thành công",
								'details' => implode(", ", $output)
							);
						} else {
							$thongtin = array(
								'status' => false,
								'message' => "Không thể kết nối PPPoE: " . implode(", ", $output)
							);
						}
					} catch (\Exception $e) {
						$thongtin = array(
							'status' => false,
							'message' => "Đã xảy ra lỗi khi kết nối PPPoE: " . $e->getMessage()
						);
					}
				} else {
					$thongtin = array(
						'status' => false,
						'message' => "Không thể ngắt kết nối PPPoE: " . implode(", ", $output)
					);
				}
			} catch (\Exception $e) {
				$thongtin = array(
					'status' => false,
					'message' => "Đã xảy ra lỗi khi ngắt kết nối PPPoE: " . $e->getMessage()
				);
			}
		}

		return response()->json($thongtin);
	}

	public function CheckTimeConnect(Request $request)
	{
		$thongtin = array('status' => false, 'time_connected' => null);
		$body = $request->all();

		if (!empty($body["ifname"])) 
		{
			$ifname = trim($body["ifname"]);
			$interFaces = new \HoangquyIT\NetworkControler\NetworkControler();
			$thongtin = $interFaces->getNetworkInterfaceInfo(trim($ifname));
		}

		return response()->json($thongtin);
	}
}
