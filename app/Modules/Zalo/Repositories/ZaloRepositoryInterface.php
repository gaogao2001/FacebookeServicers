<?php

namespace App\Modules\Zalo\Repositories;

use App\Repositories\BaseRepositoryInterface;

interface ZaloRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();
    public function findById($id);
    public function update($id, array $data);
    public function updateConfigAuto(array $data);
}