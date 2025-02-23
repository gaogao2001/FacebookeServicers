<?php
namespace App\Modules\History\Repositories\System;

use App\Repositories\BaseRepository;
use MongoDB\BSON\ObjectId;

class SystemHistoryRepository extends BaseRepository implements SystemHistoryRepositoryInterface
{

    public function __construct()
    {
        parent::__construct('History');
    }

    public function findFirst()
    {
        $cursor = $this->model->find([]);
        foreach ($cursor as $document) {
            return $document; // Trả về phần tử đầu tiên
        }
        return null; // Nếu không tìm thấy dữ liệu
    }

    public function findAll()
    {
        return $this->model->find()->toArray();

    }

	public function deleteAll()
    {
        return $this->model->drop();
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
}