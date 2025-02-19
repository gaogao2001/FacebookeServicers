<?php
namespace App\Repositories;


class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(string $table, string $databaseName = 'SiteManager')
    {
        $mongo = app('mongo');

        $this->model = $mongo->$databaseName->$table;
    }

    public function findOne()
    {
        return $this->model->findOne();
    }
}