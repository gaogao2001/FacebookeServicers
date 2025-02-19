<?php

namespace App\Modules\Fanpage\Repositories;

use App\Repositories\BaseRepositoryInterface;

interface FanpageManagerRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();

    public function searchFanpages(array $filters = [], int $perPage = 10, int $page = 1);
    public function delete($id);
    
    public function deleteMany(array $conditions = []);
    public function findById($id);
    public function update($id, array $data);

    public function updateConfigAuto(array $data);

    public function updateConfigAutoById($id, array $data);

    public function create(array $data);

    public function findOneFanpage(array $conditions = []);

    public function countsFanpage(array $conditions = []);
}
