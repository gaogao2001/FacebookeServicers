<?php

namespace App\Modules\Country\Repositories;

use App\Repositories\BaseRepositoryInterface;

interface CountryRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();
    public function searchCountries(array $filters = [], int $perPage = 300, int $page = 1);
    public function delete($id);
    public function deleteMany(array $conditions = []);
}
