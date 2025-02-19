<?php
namespace App\Services;

use App\Repositories\Account\AccountRepositoryInterface;

class AccountService extends BaseService
{
    public function __construct(AccountRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}