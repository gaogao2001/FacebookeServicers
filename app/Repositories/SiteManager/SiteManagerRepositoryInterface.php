<?php 
namespace App\Repositories\SiteManager;

use App\Repositories\BaseRepositoryInterface;

interface SiteManagerRepositoryInterface extends BaseRepositoryInterface
{
    public function create(array $data);
    public function findById($id);
	//public function findOne(array $filter); // Hoặc kiểu trả về phù hợp
    public function update($id, array $data);
    public function delete($id);
    public function findAll();
    public function findFirst();
}