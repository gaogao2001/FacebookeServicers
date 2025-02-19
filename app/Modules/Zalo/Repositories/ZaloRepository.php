<?php

namespace App\Modules\Zalo\Repositories;

use App\Repositories\BaseRepository;
use MongoDB\BSON\ObjectId;
use App\Modules\Zalo\Repositories\ZaloRepositoryInterface;

class ZaloRepository extends BaseRepository implements ZaloRepositoryInterface
{

    public function __construct()
    {
        parent::__construct('Account', 'ZaloData');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function findById($id)
    {
        $objectId = new ObjectId($id);
        return $this->model->findOne(['_id' => $objectId]);
    }

    public function update($id, array $data)
    {
        $objectId = new ObjectId($id);
        return $this->model->updateOne(
            ['_id' => $objectId],
            ['$set' => $data]
        );
    }

    public function updateConfigAuto(array $configAutoData)
    {
        return $this->model->updateMany(
            [], // Điều kiện: cập nhật tất cả bản ghi
            ['$set' => ['config_auto' => $configAutoData]] // Chỉ cập nhật trường `config_auto`
        );
    }
}
