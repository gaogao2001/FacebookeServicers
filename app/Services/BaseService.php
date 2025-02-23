<?php
namespace App\Services;


class BaseService {
    protected $repository;

    public function findOne()
    {
        return $this->repository->findOne();
    }
}