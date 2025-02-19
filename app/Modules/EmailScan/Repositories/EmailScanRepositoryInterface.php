<?php
namespace App\Modules\EmailScan\Repositories;

use App\Repositories\BaseRepositoryInterface;

interface EmailScanRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();
    public function create(array $data);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
    public function findByUid($uid);
    public function searchEmailScans(array $filters = [], int $perPage = 500, int $page = 1);
}