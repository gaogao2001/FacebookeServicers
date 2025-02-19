<?php

namespace App\Http\Controllers\ConfigAuto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ConfigAuto\ConfigAutoRepositoryInterface;
use App\Repositories\Facebook\Account\AccountRepositoryInterface;
use App\Repositories\Facebook\FanpageManager\FanpageManagerRepositoryInterface;

class AutoConfigController extends Controller
{
    protected $configAutoRepository;
    protected $accountRepository;
    protected $fanpageManagerRepository;

    public function __construct(ConfigAutoRepositoryInterface $configAutoRepository, AccountRepositoryInterface $accountRepository, FanpageManagerRepositoryInterface $fanpageManagerRepository)
    {
        $this->configAutoRepository = $configAutoRepository;
        $this->accountRepository = $accountRepository;
        $this->fanpageManagerRepository = $fanpageManagerRepository;
    }

    public function autoConfigPage()
    {
        $dbConfigFacebook = $this->configAutoRepository->findByConfigAuto('facebook');
        $dbConfigFanpage = $this->configAutoRepository->findByConfigAuto('fanpage');
        $dbConfigZalo = $this->configAutoRepository->findByConfigAuto('zalo');

        $defaultConfigFacebook = config('defaultconfigs.defaultConfigFacebook');
        $defaultConfigFanpage = config('defaultconfigs.defaultConfigFanpage');
        $defaultConfigZalo = config('defaultconfigs.defaultConfigZalo');

        $configFacebook = $dbConfigFacebook ?? $defaultConfigFacebook;
        $configFanpage = $dbConfigFanpage ?? $defaultConfigFanpage;
        $configZalo = $dbConfigZalo ?? $defaultConfigZalo;


        return view('config_auto.auto_config_page', compact('configFacebook', 'configZalo', 'configFanpage'));
    }

    public function saveFacebookConfig(Request $request)
    {
        // Lấy dữ liệu từ form
        $data = $request->all();

        // Lấy cấu hình hiện tại từ database hoặc mặc định
        $existingConfig = $this->configAutoRepository->findByConfigAuto('facebook');
        $defaultConfig = config('defaultconfigs.defaultConfigFacebook');

        // Nếu tồn tại trong database, chỉ cập nhật các trường thay đổi
        if ($existingConfig) {
            $updatedConfig = array_replace_recursive($defaultConfig, $existingConfig, $data);
            $this->configAutoRepository->saveConfig('facebook', $updatedConfig);
        } else {
            // Nếu chưa tồn tại, tạo mới với dữ liệu form kết hợp default
            $newConfig = array_replace_recursive($defaultConfig, $data);
            $this->configAutoRepository->saveConfig('facebook', $newConfig);
        }

        // Lấy cấu hình mới sau khi lưu
        $configAutoData = $this->configAutoRepository->findByConfigAuto('facebook');

        // Loại bỏ `_id` nếu tồn tại
        if (isset($configAutoData['_id'])) {
            unset($configAutoData['_id']);
        }

        // Cập nhật tất cả các bản ghi trong Account Facebook với cấu hình mới
        $updateResult = $this->accountRepository->updateConfigAuto($configAutoData);


        if ($updateResult->getModifiedCount() > 0) {
            return redirect()->route('config.page')->with('success', 'Cấu hình Facebook đã được cập nhật và áp dụng cho tất cả tài khoản!');
        } else {
            return redirect()->route('config.page')->with('warning', 'Cấu hình được lưu nhưng không có tài khoản nào được cập nhật.');
        }
    }

    public function saveFanpageConfig(Request $request)
    {

        // Lấy dữ liệu từ form
        $data = $request->all();

        // Lấy cấu hình hiện tại từ database hoặc mặc định
        $existingConfig = $this->configAutoRepository->findByConfigAuto('fanpage');
        $defaultConfig = config('defaultconfigs.defaultConfigFanpage');

        // Nếu tồn tại trong database, chỉ cập nhật các trường thay đổi
        if ($existingConfig) {
            $updatedConfig = array_replace_recursive($defaultConfig, $existingConfig, $data);
            $this->configAutoRepository->saveConfig('fanpage', $updatedConfig);
        } else {
            // Nếu chưa tồn tại, tạo mới với dữ liệu form kết hợp default
            $newConfig = array_replace_recursive($defaultConfig, $data);
            $this->configAutoRepository->saveConfig('fanpage', $newConfig);
        }
        // Lấy cấu hình mới sau khi lưu
        $configAutoData = $this->configAutoRepository->findByConfigAuto('fanpage');

        // Loại bỏ `_id` nếu tồn tại
        if (isset($configAutoData['_id'])) {
            unset($configAutoData['_id']);
        }
        // Cập nhật tất cả các bản ghi trong Account Facebook với cấu hình mới
        $updateResult = $this->fanpageManagerRepository->updateConfigAuto($configAutoData);

        if ($updateResult->getModifiedCount() > 0) {
            return redirect()->route('config.page')->with('success', 'Cấu hình Fanpage đã được cập nhật và áp dụng cho tất cả tài khoản!');
        } else {
            return redirect()->route('config.page')->with('warning', 'Cấu hình được lưu nhưng không có tài khoản nào được cập nhật.');
        }
    }


    public function saveZaloConfig(Request $request)
    {
        // Lấy dữ liệu từ form
        $data = $request->all();

        // Hàm đệ quy để chuyển đổi "1" thành true và "0" thành false
        $convertToBoolean = function (&$data) use (&$convertToBoolean) {
            foreach ($data as $key => &$value) {
                if (is_array($value)) {
                    $convertToBoolean($value); // Đệ quy nếu giá trị là mảng
                } elseif ($value === "1") {
                    $value = true;
                } elseif ($value === "0") {
                    $value = false;
                }
            }
        };

        $convertToBoolean($data);

        $existingConfig = $this->configAutoRepository->findByConfigAuto('zalo');
        $defaultConfig = config('defaultconfigs.defaultConfigZalo');

        if ($existingConfig) {
            $updatedConfig = array_replace_recursive($defaultConfig, $existingConfig, $data);
            $this->configAutoRepository->saveConfig('zalo', $updatedConfig);
        } else {
            $newConfig = array_replace_recursive($defaultConfig, $data);
            $this->configAutoRepository->saveConfig('zalo', $newConfig);
        }

        // Lấy cấu hình mới sau khi lưu
        $configAutoData = $this->configAutoRepository->findByConfigAuto('zalo');

        // Loại bỏ `_id` nếu tồn tại
        if (isset($configAutoData['_id'])) {
            unset($configAutoData['_id']);
        }

        // Cập nhật tất cả các bản ghi trong Account Zalo với cấu hình mới
        $updateResult = app('App\Repositories\Zalo\ZaloRepository')->updateConfigAuto($configAutoData);

        if ($updateResult->getModifiedCount() > 0) {
            return redirect()->route('config.page')->with('success', 'Cấu hình Zalo đã được cập nhật và áp dụng cho tất cả tài khoản!');
        } else {
            return redirect()->route('config.page')->with('warning', 'Cấu hình được lưu nhưng không có tài khoản nào được cập nhật.');
        }
    }

    public function updateAllZaloConfig()
    {
        // Lấy cấu hình từ ConfigAutoRepository
        $configAutoData = $this->configAutoRepository->findByConfigAuto('zalo');

        if (!$configAutoData) {
            throw new \Exception('Cấu hình không tồn tại trong ConfigAuto.');
        }

        // Loại bỏ `_id` nếu tồn tại
        if (isset($configAutoData['_id'])) {
            unset($configAutoData['_id']);
        }

        // Cập nhật tất cả các bản ghi trong Account Zalo với cấu hình mới
        $updateResult = app('App\Repositories\Zalo\ZaloRepository')->updateConfigAuto($configAutoData);

        // Kiểm tra kết quả
        if ($updateResult->getModifiedCount() > 0) {
            return response()->json(['message' => 'Cấu hình được cập nhật thành công cho tất cả tài khoản Zalo.']);
        } else {
            return response()->json(['message' => 'Không có tài khoản nào được cập nhật.']);
        }
    }

    // public function updateAllFacebookConfig()
    // {
    //     // Lấy cấu hình từ ConfigAutoRepository
    //     $configAutoData = $this->configAutoRepository->findByConfigAuto('facebook');

    //     if (!$configAutoData) {
    //         throw new \Exception('Cấu hình không tồn tại trong ConfigAuto.');
    //     }

    //     // Loại bỏ `_id` nếu tồn tại
    //     if (isset($configAutoData['_id'])) {
    //         unset($configAutoData['_id']);
    //     }

    //     // Cập nhật tất cả các bản ghi trong Account Facebook với cấu hình mới
    //     $updateResult = $this->accountRepository->updateConfigAuto($configAutoData);
    //     $updateResult = $this->fanpageManagerRepository->updateConfigAuto($configAutoData);

    //     // Kiểm tra kết quả
    //     if ($updateResult->getModifiedCount() > 0) {
    //         return response()->json(['message' => 'Cấu hình được cập nhật thành công cho tất cả tài khoản Facebook.']);
    //     } else {
    //         return response()->json(['message' => 'Không có tài khoản nào được cập nhật.']);
    //     }
    // }

    public function updateConfigFacebookById(Request $request, $id)
    {
        // Lấy dữ liệu từ request
        $data = $request->all();

        // Hàm đệ quy để chuyển đổi "1" thành true và "0" thành false
        $convertToBoolean = function (&$data) use (&$convertToBoolean) {
            foreach ($data as $key => &$value) {
                if (is_array($value)) {
                    $convertToBoolean($value);
                } elseif ($value === "1") {
                    $value = true;
                } elseif ($value === "0") {
                    $value = false;
                }
            }
        };

        $convertToBoolean($data);

        // Cập nhật cấu hình cho tài khoản Facebook theo ID
        $updateResult = $this->accountRepository->updateConfigAutoById($id, $data);

        if ($updateResult->getModifiedCount() > 0) {
            return redirect()->back()->with('success', 'Cấu hình Facebook đã được cập nhật thành công cho tài khoản có ID: ' . $id);
        } else {
            return redirect()->back()->with('warning', 'Không có tài khoản nào được cập nhật với ID: ' . $id);
        }
    }

    public function updateConfigFanpageById(Request $request, $id)
    {
        // Lấy dữ liệu từ request
        $data = $request->all();

        // Hàm đệ quy để chuyển đổi "1" thành true và "0" thành false
        $convertToBoolean = function (&$data) use (&$convertToBoolean) {
            foreach ($data as $key => &$value) {
                if (is_array($value)) {
                    $convertToBoolean($value);
                } elseif ($value === "1") {
                    $value = true;
                } elseif ($value === "0") {
                    $value = false;
                }
            }
        };
        $convertToBoolean($data);

        // Cập nhật cấu hình cho tài khoản Facebook theo ID

        $updateResult = $this->fanpageManagerRepository->updateConfigAutoById($id, $data);

        if ($updateResult->getModifiedCount() > 0) {
            return redirect()->back()->with('success', 'Cấu hình Facebook đã được cập nhật thành công cho tài khoản có ID: ' . $id);
        } else {
            return redirect()->back()->with('warning', 'Không có tài khoản nào được cập nhật với ID: ' . $id);
        }
    }
}
