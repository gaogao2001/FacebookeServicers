<?php

namespace App\Repositories\Roles;

use App\Repositories\BaseRepository;
use MongoDB\BSON\ObjectId;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('Roles');
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
	
	public function findOneAdmin()
    {
        return $this->model->findOne(['name' => 'Admin']);
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
