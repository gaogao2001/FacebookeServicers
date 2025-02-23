<?php

namespace App\Modules\Fanpage\Repositories;

use App\Repositories\BaseRepository;
use MongoDB\BSON\ObjectId;

class FanpageManagerRepository extends BaseRepository implements FanpageManagerRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('PageAccount', 'FacebookData');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function searchFanpages(array $filters = [], int $perPage = 100, int $page = 1)
    {
        $options = [
            'limit' => $perPage,
            'skip' => ($page - 1) * $perPage,
            'sort' => ['_id' => 1]
        ];

        $total = $this->model->countDocuments($filters);


        // Kiểm tra nếu không có tài liệu nào thỏa điều kiện
        if ($total === 0) {
            return [
                'data' => [],
                'currentPage' => $page,
                'lastPage' => 0,
                'perPage' => $perPage,
                'total' => 0
            ];
        }


        $cursor = $this->model->find($filters, $options);
        $data = $cursor->toArray();

        $lastPage = ceil($total / $perPage);

        return [
            'data' => $data,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'perPage' => $perPage,
            'total' => $total
        ];
    }


    public function findById($id)
    {
        $objectId = new ObjectId($id);
        return $this->model->findOne(['_id' => $objectId]);
    }

    public function delete($id)
    {
        $objectId = new ObjectId($id);
        return $this->model->deleteOne(['_id' => $objectId]);
    }

    public function deleteMany(array $conditions = [])
    {
        return $this->model->deleteMany($conditions);
    }

    public function update($id, array $data)
    {
        $objectId = new ObjectId($id);
        return $this->model->updateOne(['_id' => $objectId], ['$set' => $data]);
    }

    public function updateConfigAuto(array $configAutoData)
    {
        return $this->model->updateMany(
            [], // Điều kiện: cập nhật tất cả bản ghi
            ['$set' => ['config_auto' => $configAutoData]] // Chỉ cập nhật trường `config_auto`
        );
    }

    public function updateConfigAutoById($id, array $configAutoData)
    {
        $objectId = new ObjectId($id);
        return $this->model->updateOne(
            ['_id' => $objectId],
            ['$set' => ['config_auto' => $configAutoData]]
        );
    }

    public function create(array $data)
    {
        return $this->model->insertOne($data);
    }

    public function findOneFanpage(array $conditions = [])
    {
        return $this->model->findOne($conditions);
    }

    public function countsFanpage(array $conditions = [])
    {
        return $this->model->countDocuments($conditions);
    }
}
