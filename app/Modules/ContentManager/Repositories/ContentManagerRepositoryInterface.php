<?php

namespace App\Modules\ContentManager\Repositories;

use App\Repositories\BaseRepositoryInterface;

interface ContentManagerRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();
    public function create(array $data);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
}