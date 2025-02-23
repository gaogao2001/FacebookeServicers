<?php

namespace App\Modules\Country\Repositories;

use App\Repositories\BaseRepository;
use App\Modules\Country\Repositories\CountryRepositoryInterface;
use MongoDB\BSON\ObjectId;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('Country', 'FacebookData');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }


    public function searchCountries(array $filters = [], int $perPage = 300, int $page = 1)
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

    public function delete($id)
    {
        $objectId = new ObjectId($id);
        return $this->model->deleteOne(['_id' => $objectId]);
    }

    public function deleteMany(array $conditions = [])
    {
        return $this->model->deleteMany($conditions);
    }
}
