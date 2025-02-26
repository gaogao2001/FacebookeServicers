<?php

namespace App\Repositories\Notification;

use App\Repositories\BaseRepository;
use App\Repositories\Notification\NotificationRepositoryInterface;
use MongoDB\BSON\ObjectId;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('SystemNotification', 'SiteManager');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function getAllNotificationData(int $perPage, int $page): array
    {
        $collection = $this->model;

        $notifications = $collection->find(
            [],
            [
                'limit' => $perPage,
                'skip' => ($page - 1) * $perPage,
                'sort' => ['time' => -1] // Sắp xếp theo thời gian giảm dần
            ]
        );
        $result = [];

        foreach ($notifications as $notification) {
            $notificationObject = json_decode(json_encode($notification));
            $result[] = $notificationObject;
        }

        return $result;
    }

    public function countNotificationData(): int
    {
        return $this->model->countDocuments([]);
    }

    public function getNotificationsByIds(array $ids): array
    {
        $objectIds = array_map(function ($id) {
            return new ObjectId($id);
        }, $ids);

        $notifications = $this->model->find(
            ['_id' => ['$in' => $objectIds]]
        );
        $result = [];

        foreach ($notifications as $notification) {
            $notificationObject = json_decode(json_encode($notification));
            $result[] = $notificationObject;
        }

        return $result;
    }

    public function markNotificationsAsRead(array $ids): bool
    {
        $objectIds = array_map(function ($id) {
            return new ObjectId($id);
        }, $ids);

        $updateResult = $this->model->updateMany(
            ['_id' => ['$in' => $objectIds]],
            ['$set' => ['is_read' => true]]
        );

        return $updateResult->getModifiedCount() > 0;
    }

    public function markAllNotificationsAsRead(): bool
    {
        $updateResult = $this->model->updateMany(
            [], // không có điều kiện nào, tức là cập nhật toàn bộ
            ['$set' => ['is_read' => true]]
        );

        return $updateResult->getModifiedCount() > 0;
    }

    public function deleteAllNotifications(): bool
    {
        $deleteResult = $this->model->deleteMany([]);

        return $deleteResult->getDeletedCount() > 0;
    }

    public function deleteNotifications(array $ids): bool
    {
        $objectIds = array_map(function ($id) {
            return new ObjectId($id);
        }, $ids);

        $deleteResult = $this->model->deleteMany(
            ['_id' => ['$in' => $objectIds]]
        );

        return $deleteResult->getDeletedCount() > 0;
    }

    public function getLatestNotifications(int $limit): array
    {
        // Giả sử model của bạn hỗ trợ sắp xếp theo create_time giảm dần
        return $this->model->find([], [
            'limit' => $limit,
            'sort' => ['create_time' => -1] // -1: sắp xếp giảm dần
        ])->toArray();
    }
}
