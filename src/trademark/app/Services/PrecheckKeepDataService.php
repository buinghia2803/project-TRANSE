<?php

namespace App\Services;

use App\Repositories\PrecheckKeepDataRepository;

class PrecheckKeepDataService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   PrecheckKeepDataRepository $precheckKeepDataRepository
     */
    public function __construct(PrecheckKeepDataRepository $precheckKeepDataRepository)
    {
        $this->repository = $precheckKeepDataRepository;
    }
}
