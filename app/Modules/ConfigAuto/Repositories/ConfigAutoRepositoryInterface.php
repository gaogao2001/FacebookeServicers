<?php

namespace App\Modules\ConfigAuto\Repositories;

use App\Repositories\BaseRepositoryInterface;

interface ConfigAutoRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();

    public function findByConfigAuto($configAuto);

    public function saveConfig($configAuto, $data);

    public function saveInteractLimit(array $data);
    public function findByInteractLimit();
}