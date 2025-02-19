<?php

namespace App\Modules\History\Repositories\Request;

use App\Repositories\BaseRepository;
use App\Modules\History\Repositories\Request\RequestHistoryRepositoryInterface as RequestHistoryRepositoryInterface;
use MongoDB\BSON\ObjectId;

class RequestHistoryRepository extends BaseRepository implements RequestHistoryRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('RequestHistory', 'History');
    }

    public function findAll(int $perPage = 100, int $page = 1)
    {
        $filters = []; // Không có bộ lọc, lấy tất cả
        return $this->searchRequests($filters, $perPage, $page);
    }

    public function delete($id)
    {
        $objectId = new ObjectId($id);

        return $this->model->deleteOne(['_id' => $objectId]);
    }

    public function deleteMany($filter)
    {
        return $this->model->deleteMany($filter);
    }
	
	public function deleteAll()
    {
        return $this->model->drop();
    }

    public function findById($id)
    {
        $objectId = new ObjectId($id);

        return $this->model->findOne(['_id' => $objectId]);
    }

    public function searchRequests(array $filters = [], int $perPage = 100, int $page = 1)
    {
        $options = [
            'limit' => $perPage,
            'skip' => ($page - 1) * $perPage,
            'sort' => ['_id' => 1]
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
