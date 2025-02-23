<?php

namespace App\Modules\Network\Controllers\ProxyV6;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Network\Repositories\ProxyV6\ProxyV6RepositoryInterface;

use App\Modules\Network\Repositories\NetworkRepositoryInterface;
use MongoDB\BSON\Regex;
use HoangquyIT\ProxyV6;

class ProxyV6Controller extends Controller
{
    protected $proxyV6Repository;
    protected $settingRepository;
	protected $GenProxy;

    public function __construct(ProxyV6RepositoryInterface $proxyV6Repository, NetworkRepositoryInterface $settingRepository)
    {
        $this->GenProxy = new ProxyV6();
        $this->settingRepository = $settingRepository;
		$this->proxyV6Repository = $proxyV6Repository;
    }

    public function proxySystemPage()
	{
		$_Ipv6List = [];
		$ListInterface = $this->GenProxy->LoadListInterface();
		foreach ($ListInterface as $keys) {
			$item = (object)$keys;
			if (!empty($item->ipv6)) {
				$SplitIp = explode(":", $item->ipv6);
				if (count($SplitIp) > 4) {
					$firstFourBlocks = array_slice($SplitIp, 0, 4);
					$_Ipv6List[] = implode(":", $firstFourBlocks);
				}
			}
		}
		$this->proxyV6Repository->DeleteAllProxyExpired($_Ipv6List);
		return view('Network::ProxyV6.proxy_system', ['ListInterface' => $ListInterface]);
	}


    public function index()
    {
        $proxyV6System = $this->proxyV6Repository->findAll();
	

        return response()->json($proxyV6System);
    }

    public function create(Request $request)
    {
        $thongtin = array('status' => false, 'message' => "Số lượng proxy cần lớn hơn 0");
		$body = $request->all();
		if(!empty($body["limit"]) && intval($body["limit"]) > 0)
		{
			$RandomPassword = true;
			$networkInterface = $body["interface"];
			$GenProxy = new ProxyV6();
			$result = $GenProxy->CreateInterface($networkInterface, intval($body["limit"]));
			
			if ($result->status) {
				// Khởi tạo mảng ProxyConfig để lưu cấu hình proxy
				$existingPorts = $this->proxyV6Repository->list([], ['projection' => ['port' => 1]]);
				foreach ($existingPorts as $document) {
					$usedPorts[] = $document['port'];
				}
				$usedPorts = array(); // Mảng để lưu trữ các cổng đã sử dụng
				foreach ($result->ip as $Keys) {
					$port = $GenProxy->randomFreePort(10000, 65535, $usedPorts);
					// Lưu cổng vừa được sử dụng vào mảng $usedPorts
					$usedPorts[] = $port;
					// Thêm cấu hình vào mảng ProxyConfig
					 // Tạo tên đăng nhập và mật khẩu cho từng proxy nếu cần
					$proxyUsername = empty($username) ? 'SeviceProxy'.$port : $username;
					$proxyPassword = empty($password) ? $GenProxy->random_passwd() : $password;
					//
					$ProxyConfig = array(
						'config_name' => $result->eth.$port.'v6',
						'eth' => $result->eth,
						'interface' => $Keys,
						//'username' => $proxyUsername,
						//'password' => $proxyPassword,
						'port' => $port,
						'last_time' => date("d/m/Y H:i:s"),
						'status' => 'pending',
						'port_status' => 'pending',
						'access_token' => $GenProxy->generateToken($result->eth.$port.'v6')
					);
					// Lệnh shell cần thực hiện
					$command = 'sudo /usr/sbin/ip -6 addr add '.$Keys.'/64 dev '.$result->eth;
					$output = [];
					$return_var = 0;
					exec($command, $output, $return_var);
					if ($return_var === 0) {
						$ProxyConfig['status'] = 'success';
					}
					$thongtin['status'] = true;
					$thongtin['message'] = 'thành công !';

					$resultInsert = $this->proxyV6Repository->insertOne($ProxyConfig);
				}

				// Update setting
				$filter = ['is_reload' => ['$exists' => true]];
				$update = [
					'$set' => ['is_reload' => true]
				];
				$options = ['upsert' => true];
				$result = $this->settingRepository->updateOne($filter, $update, $options);
			}
		}
		return response()->json($thongtin);
    }

    public function ReloadIpv6(string $id)
    {
		$result = $this->proxyV6Repository->findById($id);
		$GenProxy = new ProxyV6();
		$responseData = ['status' => false, 'msg' => 'Có lỗi trong quá trình cập nhật'];
		if ($result) {
			// Trả về document được tìm thấy
			
			$command = 'sudo /usr/sbin/ip -6 addr del '.$result->interface.'/64 dev '.$result->eth;
			$output = [];
			$return_var = 0;
			exec($command, $output, $return_var);
			$username = !empty($result->username) ? $result->username : null;
			$password = !empty($result->password) ? $result->password : null;

			$resultCreate = $GenProxy->CreateInterface($result->eth, 1);
			if($resultCreate->status)
			{
				// Cập nhật thông tin
				$interface = trim($resultCreate->ip[0]);
				$test = $this->proxyV6Repository->updateOne($id,
                    [
                        'interface' => trim($resultCreate->ip[0]),
                        'port_status' => 'pending',
                        'last_time' => date("d/m/Y H:i:s"),
                        'is_updated' => true,
                        'is_file_updated' => true,
                    ]
				);
				$command = 'sudo /usr/sbin/ip -6 addr add '.trim($resultCreate->ip[0]).'/64 dev '.$resultCreate->eth;
				exec($command);

				// update config file
				// $filename = '/usr/local/etc/3proxy/3proxy.cfg';
				// $GenProxy->updateProxyConfig($filename, $result->access_token, $result->username, $result->password, $result->port, $interface);
				// exec('sudo systemctl restart 3proxy.service');

				$DefaultPath = '/etc/ServiceProxy/proxy_cfg';
				$configName = $result->config_name;
				$testFile = $GenProxy->makeFileConfigProxy($DefaultPath."/ProxyV6_{$configName}.cfg", $interface, '0.0.0.0', $result->port, $username, $password, $result->access_token);
				$responseData = ['status' => true, 'msg' => 'Cập nhật thành công'];

				return response()->json($responseData);
			}
		}
		return response()->json($responseData);
    }

    public function CheckProxy(string $id)
    {
		$result = $this->proxyV6Repository->findById($id);

		if (!$result) {
			return response()->json(['status' => false, 'msg' => 'Proxy không tồn tại'], 404);
		}

		// Lấy IP và port từ proxy trong database
		$proxyIp = $result->ip ?? '127.0.0.1';
		$proxyPort = $result->port ?? '8080';
		//var_dump($proxyPort);
		//die();
		// Tạo một cURL để lấy địa chỉ IP thông qua proxy
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api64.ipify.org/?format=json");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		// Thiết lập proxy cho cURL
		curl_setopt($ch, CURLOPT_PROXY, $proxyIp);
		curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);

		// Thực hiện yêu cầu và lấy phản hồi
		$apiResponse = curl_exec($ch);
		curl_close($ch);

		if ($apiResponse === false) {
            return response()->json(['status' => false, 'msg' => 'Không thể kết nối tới API thông qua proxy'], 200);
		}

		$ipData = json_decode($apiResponse, true);
		$ip = $ipData['ip'] ?? 'Không xác định';

		// Trả về phản hồi
		return response()->json(['status' => true, 'msg' => 'Hoàn tất', 'proxy_ip' => $proxyIp, 'proxy_port' => $proxyPort, 'ip' => $ip]);
    }

    public function DeleteProxyv6(string $id)
    {
		// Tìm một document dựa trên _id
		$result = $this->proxyV6Repository->findById($id);
		if ($result) {
			// Trả về document được tìm thấy
			//print_r(json_encode($result));
			//exit();
			//var_dump($result->interface);
			$command = 'sudo /usr/sbin/ip -6 addr del '.$result->interface.'/64 dev '.$result->eth;//sudo ip -6 addr del 2402:800:63ae:cfa3:ee0a:aea8:5364:6b70/64 dev eno1
			$output = [];
			$return_var = 0;
			exec($command, $output, $return_var);

			$listPidsCommand = "sudo lsof -t -i tcp:{$result->port}";
			exec($listPidsCommand, $outputPids, $status);
			if ($status === 0 && !empty($outputPids)) {
				foreach ($outputPids as $pid) {
					exec("sudo kill -9 {$pid}", $killOutput, $killStatus);
				}
			}
		

			$filePath = "/etc/ServiceProxy/proxy_cfg/ProxyV6_{$result->config_name}.cfg";
			if (file_exists($filePath)) {
				unlink($filePath);
			}
			
            $this->proxyV6Repository->deleteOne($id);
		}
		return redirect()->route('proxyv6.index');
    }

    public function deleteAllProxies()
    {
        $ports = $this->proxyV6Repository->deleteAllProxies();

        $batchSize = 90;
		for ($i = 0; $i < count($ports); $i += $batchSize) {
			// Get the current batch
			$batch = array_slice($ports, $i, $batchSize);
			
			$portList = implode(',', $batch);
			
			$command = "sudo /usr/bin/lsof -ti:{$portList} | xargs -r sudo kill -9";
			
			exec($command . ' 2>&1', $output, $return_var);
		}

		$defaultPath = '/etc/ServiceProxy/proxy_cfg';
		$files = scandir($defaultPath);
		foreach ($files as $file) {
			if ($file !== '.' && $file !== '..') {
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if (strpos($file, 'ProxyV6') !== false && $extension == 'cfg') {
					$filePath = $defaultPath . '/' . $file;
					// Xoá file
					if (file_exists($filePath)) {
						unlink($filePath);
					}
				}
			}
		}

		shell_exec('sudo systemctl restart config_proxyv6.service');

        return response()->json(['status' => true, 'msg' => 'hoàn tất']);
    }


    public function searchProxies(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 200);
        $page = $request->input('page', 1);

        $filters = [];

        if ($search) {
            $regex = new Regex($search, 'i');
            $filters = [
                '$or' => [
                    ['config_name' => $regex],
                    ['eth' => $regex],
                    ['interface' => $regex],
                    ['port' => $regex],
                    ['phone' => $regex],
                    ['status' => $regex],
                    ['port_status' => $regex],
                ]
            ];
        }
        
        $proxies = $this->proxyV6Repository->searchProxies($filters, $perPage, $page);

        return response()->json($proxies);
    }

	public function checkProxyV6Status(Request $request)
    {
        $ids = $request->get('ids');

		$totalProxy = $this->proxyV6Repository->countProxies();
		$totalSuccess = $this->proxyV6Repository->countProxies(['port_status' => 'success']);

		$listProxies = [];
		if (!empty($ids)) {

			$listProxiesV6 = $this->proxyV6Repository->getProxiesV6ByIds($ids, ['config_name', 'port_status']);

			foreach ($listProxiesV6 as $document) {
				$listProxies[] = (object)[
					'config_name' => $document['config_name'],
					'port_status' => $document['port_status'],
				];
			}
		}

		$responseData = [
			'total_proxy' => $totalProxy,
			'total_success' => $totalSuccess,
			'list_proxies' => $listProxies,
		];

		return response()->json($responseData);
    }
}
