<?php

namespace App\Services;

use App\Repositories\SettingRepository;

class SettingService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param SettingRepository $settingRepository
     */
    public function __construct(SettingRepository $settingRepository)
    {
        $this->repository = $settingRepository;
    }
}
