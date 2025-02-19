<?php   

namespace App\Modules\CronTab\Repositories;

use App\Repositories\BaseRepositoryInterface;

interface CrontabRepositoryInterface extends BaseRepositoryInterface
{
   public function create(array $data);
   public function findAll();
   public function findByIds($id);
   public function update($id, array $data);
   public function delete($id);
}