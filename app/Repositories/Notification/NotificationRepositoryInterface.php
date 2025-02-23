<?php

namespace App\Repositories\Notification;

use App\Repositories\BaseRepositoryInterface;

interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();

    public function getAllNotificationData(int $perPage, int $page): array;

    public function countNotificationData(): int;

    public function getNotificationsByIds(array $ids): array;

    public function markNotificationsAsRead(array $ids): bool;

    public function markAllNotificationsAsRead(): bool;

    public function deleteAllNotifications(): bool;

    public function deleteNotifications( array $ids): bool;
    
    public function getLatestNotifications(int $limit): array;
}