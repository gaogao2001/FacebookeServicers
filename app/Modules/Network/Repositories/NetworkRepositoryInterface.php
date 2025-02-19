<?php 
namespace App\Modules\Network\Repositories;

use App\Repositories\BaseRepositoryInterface;

interface NetworkRepositoryInterface extends BaseRepositoryInterface
{
    public function updateOne($filter, $update, $options = []);

    public function findAll();
}