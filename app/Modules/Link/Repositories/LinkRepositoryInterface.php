<?php

namespace App\Modules\Link\Repositories;

use App\Repositories\BaseRepositoryInterface;

interface LinkRepositoryInterface extends BaseRepositoryInterface
{
    public function create(array $data);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
    public function findAll();
    public function findByMd5($md5);
    public function findByUserId($userId);

    public function findAllPaginated(int $perPage = 100, int $page = 1, array $filters = []): array;
    public function findByUserIdPaginated(string $userId, int $perPage = 100, int $page = 1): array;
}
