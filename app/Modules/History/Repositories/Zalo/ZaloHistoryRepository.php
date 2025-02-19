<?php

namespace App\Modules\History\Repositories\Zalo;

use App\Repositories\BaseRepository;
use App\Modules\History\Repositories\Zalo\ZaloHistoryRepositoryInterface;

use MongoDB\BSON\ObjectId;

class ZaloHistoryRepository extends BaseRepository implements ZaloHistoryRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('ZaloHistory', 'History');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }
	
	public function deleteAll()
    {
        return $this->model->drop();
    }
}
