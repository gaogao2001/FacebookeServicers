<?php
namespace App\Modules\Network\Repositories\ProxyV6;

use App\Repositories\BaseRepositoryInterface;

interface ProxyV6RepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();

    public function searchProxies(array $filters = [], int $perPage = 500, int $page = 1);

    public function deleteAllProxies();
    public function list($filters = [], $options = []);
    public function insertOne($data);
    public function findById($id);
    public function updateOne($id, $data);
    public function deleteOne($id);
    public function countProxies($conditions);
    public function getProxiesV6ByIds($ids, $select = ["*"]);
    
}