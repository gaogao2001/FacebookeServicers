<?php

namespace App\Modules\EmailScan\Repositories;

use App\Repositories\BaseRepository;
use App\Modules\EmailScan\Repositories\EmailScanRepositoryInterface as EmailScanRepositoryInterface;
use MongoDB\BSON\ObjectId;


class EmailScanRepository extends BaseRepository implements EmailScanRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('EmailScan', 'FacebookData');
    }
    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function create(array $data)
    {
        return $this->model->insertOne($data);
    }

    public function findById($id)
    {
        $objectId = new ObjectId($id);

        return $this->model->findOne(['_id' => $objectId]);
    }

    public function update($id, array $data)
    {
        $objectId = new ObjectId($id);

        return $this->model->updateOne(['_id' => $objectId], ['$set' => $data]);
    }

    public function delete($id)
    {
        $objectId = new ObjectId($id);

        return $this->model->deleteOne(['_id' => $objectId]);
    }

    public function findByUid($uid)
    {
        return $this->model->findOne(['uid' => $uid]);
    }

    public function searchEmailScans(array $filters = [], int $perPage = 500, int $page = 1)
    {
        $options = [
            'limit' => $perPage,
            'skip' => ($page - 1) * $perPage,
            'sort' => ['_id' => -1]
        ];

        $cursor = $this->model->find($filters, $options);
        $data = $cursor->toArray();

        $total = $this->model->countDocuments($filters);
        $lastPage = ceil($total / $perPage);

        return [
            'data' => $data,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'per_page' => $perPage,
            'total' => $total
        ];
    }
}
