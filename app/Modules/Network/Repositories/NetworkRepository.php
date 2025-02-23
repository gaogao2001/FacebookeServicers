<?php
namespace App\Modules\Network\Repositories;

use App\Repositories\BaseRepository;
use MongoDB\BSON\ObjectId;

class NetworkRepository extends BaseRepository implements NetworkRepositoryInterface
{

    public function __construct()
    {
        parent::__construct('Setting', 'NetworkControler');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function updateOne($filter, $update, $options = [])
    {
        return $this->model->updateOne($filter, $update, $options);
    }
	
	public function findOne($filters = [])
    {
        return $this->model->findOne($filters);
    }
	
	public function insertOne($data)
    {
        return $this->model->insertOne($data);
    }

    public function findByArray(array $interfaces, array $options = [])
    {
        $query = [];
    
        // Kiểm tra từng interface trong mảng đầu vào
        foreach ($interfaces as $interface) {
            $query[] = [
                $interface => [
                    '$exists' => true
                ]
            ];
        }
    
        return $this->model->find(
            ['$or' => $query], // Tìm các bản ghi mà bất kỳ interface nào tồn tại
            $options
        )->toArray();
    }
    
}