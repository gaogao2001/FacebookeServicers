<?php

namespace App\Modules\ConfigAuto\Repositories;

use App\Repositories\BaseRepository;
use App\Modules\ConfigAuto\Repositories\ConfigAutoRepositoryInterface;



class ConfigAutoRepository extends BaseRepository implements ConfigAutoRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('SeedingSettings', 'ConfigAuto');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function findByConfigAuto($configAuto)
    {
        $result = $this->model->findOne(['config_auto' => $configAuto]);

        // Kiểm tra nếu kết quả không rỗng, chuyển đổi thành mảng
        return $result ? json_decode(json_encode($result), true) : null;
    }

    public function findByInteractLimit()
    {
        $result = $this->model->findOne(['interact_limit' => ['$exists' => true]]);
        return $result ? json_decode(json_encode($result), true) : null;
    }

   
    // Lưu cấu hình
    public function saveConfig($configAuto, $data)
    {
        // Lấy cấu hình hiện có
        $existingConfig = $this->findByConfigAuto($configAuto);

        // Hàm đệ quy để chuyển đổi các giá trị "1" và "0" thành true/false
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
        // Chuyển đổi dữ liệu từ form
        $convertToBoolean($data);

        if ($existingConfig) {
            // Loại bỏ `_id` khỏi dữ liệu cập nhật nếu nó tồn tại
            if (isset($data['_id'])) {
                unset($data['_id']);
            }

            // Cập nhật dữ liệu
            $this->model->updateOne(
                ['config_auto' => $configAuto], // Điều kiện tìm kiếm
                ['$set' => $data] // Dữ liệu cần cập nhật
            );
        } else {
            // Gán `config_auto` vào dữ liệu mới
            $data['config_auto'] = $configAuto;

            // Thêm mới vào database
            $this->model->insertOne($data);
        }
    }


    public function saveInteractLimit(array $data)
    {

        if (isset($data['_token'])) {
            unset($data['_token']);
        }
        // Ép kiểu dữ liệu đầu vào về integer (vì dữ liệu input là dạng chuỗi)
        foreach ($data as $key => $value) {
            $data[$key] = (int) $value;
        }

        // Lấy cấu hình mặc định từ file config
        $defaultLimit = config('defaultconfigs.defaultConfigInteractLimit');
        // Gộp dữ liệu mặc định với dữ liệu input (input ghi đè giá trị mặc định nếu có)
        $newConfig = array_merge($defaultLimit, $data);

        // Tìm record đã có field "interact_limit"
        $existingConfig = $this->model->findOne(['interact_limit' => ['$exists' => true]]);

        if ($existingConfig) {
            // Cập nhật record với cấu hình mới
            $this->model->updateOne(
                ['_id' => $existingConfig['_id']],
                ['$set' => ['interact_limit' => $newConfig]]
            );
        } else {
            // Thêm mới record với một mảng chứa key interact_limit
            $this->model->insertOne(['interact_limit' => $newConfig]);
        }
    }
}
