<?php

namespace App\Modules\History\Repositories\Facebook;

use App\Repositories\BaseRepository;
use App\Modules\History\Repositories\Facebook\FacebookHistoryRepositoryInterface;
use MongoDB\BSON\ObjectId;

class FacebookHistoryRepository extends BaseRepository implements FacebookHistoryRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('FacebookHistory', 'History');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
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
    //lấy lịch sử theo uid
    public function getHistoryData(array $uids, int $perPage, int $page): array
    {
        $collection = $this->model;

        $historyData = $collection->find(
            ['uid' => ['$in' => $uids]],
            [
                'limit' => $perPage,
                'skip' => ($page - 1) * $perPage,
                'sort' => ['time' => -1] // Sắp xếp theo thời gian giảm dần
            ]
        );

        $result = [];
        foreach ($historyData as $history) {
            $historyObject = json_decode(json_encode($history));

            // Kiểm tra và sửa thuộc tính ' status' thành 'status'
            if (isset($historyObject->{' status'})) {
                $historyObject->status = $historyObject->{' status'};
                unset($historyObject->{' status'});
            }

            $result[] = $historyObject;
        }

        return $result;
    }
    //đếm lịch sử theo uid
    public function countHistoryData(array $uids): int
    {
        return $this->model->countDocuments(['uid' => ['$in' => $uids]]);
    }


    //lấy tất cả lịch sử
    public function getAllHistoryData(int $perPage, int $page): array
    {
        $collection = $this->model;

        $historyData = $collection->find(
            [],
            [
                'limit' => $perPage,
                'skip' => ($page - 1) * $perPage,
                'sort' => ['time' => -1] // Sắp xếp theo thời gian giảm dần
            ]
        );

        $result = [];
        foreach ($historyData as $history) {
            $historyObject = json_decode(json_encode($history));

            // Kiểm tra và sửa thuộc tính ' status' thành 'status'
            if (isset($historyObject->{' status'})) {
                $historyObject->status = $historyObject->{' status'};
                unset($historyObject->{' status'});
            }

            $result[] = $historyObject;
        }

        return $result;
    }
    //đếm tất cả lịch sử
    public function countAllHistoryData(): int
    {
        return $this->model->countDocuments([]);
    }
}
