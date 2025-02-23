<?php

namespace App\Modules\History\Repositories\Zalo;

use App\Repositories\BaseRepositoryInterface;

interface ZaloHistoryRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();
	public function deleteAll();
    
}