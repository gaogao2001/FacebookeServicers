<?php

namespace App\Http\Controllers\DeviceEmulator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Facebook\Account\AccountRepositoryInterface;
use App\Repositories\Facebook\FanpageManager\FanpageManagerRepositoryInterface;

class DeviceEmulatorController extends Controller
{
	protected $FacebookAccount;
	protected $PageAccount;
	
	public function __construct(AccountRepositoryInterface $accountRepository, FanpageManagerRepositoryInterface $fanpageManagerRepository)
    {
        $this->FacebookAccount = $accountRepository;
		$this->PageAccount = $fanpageManagerRepository;
    }
	
    public function validateInputs(Request $request, array $rules)
    {
        $request->validate($rules);

        $url = $request->input('url');
        $uid = $request->input('uid');

        if (empty($url) || empty($uid)) {
            return response()->json([
                'status' => 'error',
                'message' => empty($url) ? 'Thiếu dữ liệu cần thiết: url' : 'Thiếu dữ liệu cần thiết: uid'
            ], 400);
        }

        return compact('url', 'uid');
    }

    public function validateFilePath($url)
    {
        $parsedUrl = parse_url($url, PHP_URL_PATH);
        $fileDataIndex = strpos($parsedUrl, '/FileData');

        if ($fileDataIndex === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'URL không đúng định dạng hoặc không chứa FileData'
            ], 400);
        }

        $fileDataPath = '/var/www/html' . substr($parsedUrl, $fileDataIndex);

        if (!file_exists($fileDataPath) || !is_file($fileDataPath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File không tồn tại hoặc đường dẫn không hợp lệ',
                'fileDataPath' => $fileDataPath
            ], 404);
        }

        return $fileDataPath;
    }

    public function ExportVideo(Request $request)
    {
        $inputs = $this->validateInputs($request, [
            'url' => 'required|url',
            'uid' => 'required'
        ]);
        if ($inputs instanceof \Illuminate\Http\JsonResponse) {
            return $inputs;
        }

        $fileDataPath = $this->validateFilePath($inputs['url']);
        if ($fileDataPath instanceof \Illuminate\Http\JsonResponse) {
            return $fileDataPath;
        }

        $outputDirImg = '/var/www/html/FileData/Images/' . $inputs['uid'];
        $fileName = substr(md5($inputs['url']), 0, 10);

        if (!extract_frames(null, $outputDirImg, $fileName, $fileDataPath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi trong quá trình Export'
            ], 500);
        }

        rename_files($outputDirImg);

        return response()->json([
            'status' => 'success',
            'message' => 'Export Video ra Image hoàn tất'
        ], 200);
    }

    public function postVideo(Request $request)
    {
        $this->processRequest($request, 'postVideo');
    }

    public function postReels(Request $request)
    {
        $this->processRequest($request, 'postReels');
    }

    public function liveVideo(Request $request)
    {
        $this->processRequest($request, 'liveVideo');
    }

	private function processRequest(Request $request, $action)
	{
		$inputs = $this->validateInputs($request, [
			'url' => 'required|url',
			'type' => 'required',
			'uid' => 'required'
		]);
		if ($inputs instanceof \Illuminate\Http\JsonResponse) {
			return $inputs;
		}

		$fileDataPath = $this->validateFilePath($inputs['url']);
		if ($fileDataPath instanceof \Illuminate\Http\JsonResponse) {
			return $fileDataPath;
		}
		
		// Lấy giá trị type từ request
		$type = $request->input('type');
		$FacebookUse = [];
		// Hiển thị giá trị của url và uid
		if($type == 'profile')
		{
			// day là profile facebook
			$account = $this->FacebookAccount->findByUid(trim($inputs['uid']));
			if(!$account)
			{
				return response()->json([
					'status' => 'error',
					'message' => 'Không tìm thấy thông tin tài khoản Facebook'
				], 500);
			}
			$FacebookUse = iterator_to_array($account);
		}
		else if($type == 'page')
		{
			// day là page facebook
		}
		else{
			// chưa xác dinh
		}
		var_dump('fileDataPath: ' . $fileDataPath);
		var_dump('Action: ' . $action);
		var_dump('URL: ' . $inputs['url']);
		var_dump('UID: ' . $inputs['uid']);
		var_dump($FacebookUse);
		die();
	}

}
