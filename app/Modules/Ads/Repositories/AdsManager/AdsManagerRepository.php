<?php

namespace App\Modules\Ads\Repositories\AdsManager;

use App\Repositories\BaseRepository;
use App\Modules\Ads\Repositories\AdsManager\AdsManagerRepositoryInterface;
use MongoDB\BSON\ObjectId;

class AdsManagerRepository extends BaseRepository implements AdsManagerRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('Adsmanager', 'FacebookData');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function searchAds(array $filters = [], int $perPage = 10, int $page = 1)
    {
        if (empty($filters)) {
            $filters = session('fanpage_filters', []);
        }

        $mongoFilters = [];
        foreach ($filters as $key => $value) {
            if (!is_null($value) && $value !== '') {
                $mongoFilters[$key] = is_numeric($value) ? (int)$value : $value;
            }
        }

        $options = [
            'limit' => $perPage,
            'skip' => ($page - 1) * $perPage,
            'sort' => ['_id' => 1]
        ];

        try {
            
            $cursor = $this->model->find($mongoFilters, $options);
            $data = $cursor->toArray();
       
        } catch (\Exception $e) {
           
        }

        $total = $this->model->countDocuments($mongoFilters);
        $lastPage = ceil($total / $perPage);

        return [
            'data' => $data,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'perPage' => $perPage,
            'total' => $total,
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
