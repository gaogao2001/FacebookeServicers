<?php

namespace App\Modules\History\Repositories\Request;

use App\Repositories\BaseRepositoryInterface;

interface RequestHistoryRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();

    public function delete($id);

    public function deleteMany($filter);
    
    public function findById($id);

    public function searchRequests(array $filters = [], int $perPage = 300, int $page = 1);
	public function deleteAll();
}