<?php

namespace App\Modules\Google\Repositories;

use App\Repositories\BaseRepository;
use App\Modules\Google\Repositories\AccountRepositoryInterface;
use MongoDB\BSON\ObjectId;

class AccountRepository extends BaseRepository implements AccountRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('Account', 'Google');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function findById($id)
    {
        return $this->model->findOne(['_id' => new ObjectId($id)]);
    }

    public function create($data)
    {
        return $this->model->insertOne($data);
    }

    public function update($id, $data)
    {
        return $this->model->updateOne(['_id' => new ObjectId($id)], ['$set' => $data]);
    }

    public function delete($id)
    {
        return $this->model->deleteOne(['_id' => new ObjectId($id)]);
    }

    public function deleteMany(array $emails)
    {
        return $this->model->deleteMany(['username' => ['$in' => $emails]]);
    }

    public function deleteAll()
    {
        return $this->model->deleteMany([]);
    }
}
