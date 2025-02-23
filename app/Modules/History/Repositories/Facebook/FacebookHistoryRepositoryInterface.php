<?php

namespace App\Modules\History\Repositories\Facebook;

use App\Repositories\BaseRepositoryInterface;

interface FacebookHistoryRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();

    public function delete($id);

    public function deleteMany($filter);

    public function getHistoryData(array $uids, int $perPage, int $page): array;

    public function countHistoryData(array $uids): int;

    public function getAllHistoryData(int $perPage, int $page): array;

    public function countAllHistoryData(): int;

    public function deleteAll();

}