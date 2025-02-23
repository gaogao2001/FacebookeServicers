<?php

namespace App\Modules\ContentManager\Repositories;

use App\Repositories\BaseRepository;
use MongoDB\BSON\ObjectId;


class ContentManagerRepository extends BaseRepository implements ContentManagerRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('Content','ContentManager');
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

    
}