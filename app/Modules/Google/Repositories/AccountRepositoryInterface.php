<?php

namespace App\Modules\Google\Repositories;

use App\Repositories\BaseRepositoryInterface;

interface AccountRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();
    public function findById($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function deleteMany(array $emails);

    public function deleteAll();
    
}