<?php 
namespace App\Modules\History\Repositories\System;
use App\Repositories\BaseRepositoryInterface;

interface SystemHistoryRepositoryInterface extends BaseRepositoryInterface
{
    public function create(array $data);
    public function findById($id);
	//public function findOne(array $filter); // Hoặc kiểu trả về phù hợp
    public function update($id, array $data);
    public function delete($id);
    public function findAll();
    public function findFirst();
	public function deleteAll();
}