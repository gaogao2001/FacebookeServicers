<?php

namespace App\Modules\Ads\Repositories\AdsManager;

use App\Repositories\BaseRepositoryInterface;

interface AdsManagerRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();

    public function searchAds(array $filters = [], int $perPage = 10, int $page = 1);

    public function delete($id);

    public function deleteMany(array $conditions = []);
}
