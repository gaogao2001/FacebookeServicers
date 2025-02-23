<?php

namespace App\Modules\Link\Repositories;

use App\Repositories\BaseRepositoryInterface;
use App\Repositories\BaseRepository;
use MongoDB\BSON\ObjectId;

class LinkRepository extends BaseRepository implements LinkRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('Links' ,'ContentManager');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function create( array $data)
    {
        $this->model->insertOne($data);
    }

    public function findById($id)
    {
        $objectId = new ObjectId($id);
        return $this->model->findOne(['_id' => $objectId]);
    }

    public function update($id, array $data)
    {
        $objectId = new ObjectId($id);
        $this->model->updateOne(['_id' => $objectId], ['$set' => $data]);
    }
    public function delete($id)
    {
        $objectId = new ObjectId($id);

        $this->model->deleteOne(['_id' => $objectId]);
    }

    public function findByMd5($md5)
    {
        return $this->model->findOne(['md5' => $md5]);
    }

    public function findByUserId($userId)
    {
        return $this->model->find(['user_id' => new ObjectId($userId)])->toArray();
    }

    public function findAllPaginated(int $perPage = 100, int $page = 1, array $filters = []): array
    {
        $options = [
            'limit' => $perPage,
            'skip'  => ($page - 1) * $perPage,
            'sort'  => ['_id' => -1] // Sắp xếp giảm dần theo _id
        ];

        $cursor = $this->model->find($filters, $options);
        $data = $cursor->toArray();

        $total = $this->model->countDocuments($filters);
        $lastPage = ceil($total / $perPage);

        return [
            'data'        => $data,
            'currentPage' => $page,
            'lastPage'    => $lastPage,
            'per_page'    => $perPage,
            'total'       => $total
        ];
    }

    public function findByUserIdPaginated(string $userId, int $perPage = 100, int $page = 1): array
    {
        $filters = ['user_id' => new ObjectId($userId)];
        return $this->findAllPaginated($perPage, $page, $filters);
    }
}