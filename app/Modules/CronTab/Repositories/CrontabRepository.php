<?php

namespace App\Modules\CronTab\Repositories;

use App\Repositories\BaseRepository;
use MongoDB\BSON\ObjectId;

class CrontabRepository extends BaseRepository implements CrontabRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('CronTabPaths');
    }

    public function create(array $data)
    {
        return $this->model->insertOne($data);
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function findByIds($id)
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
  
}