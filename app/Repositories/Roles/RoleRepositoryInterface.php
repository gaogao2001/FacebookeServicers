<?php

namespace App\Repositories\Roles;

use App\Repositories\BaseRepositoryInterface;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();
    public function create(array $data);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);

   
}