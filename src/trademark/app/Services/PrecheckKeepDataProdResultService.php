<?php

namespace App\Services;

use App\Repositories\PrecheckKeepDataProdResultRepository;

class PrecheckKeepDataProdResultService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   PrecheckKeepDataProdResultRepository $precheckKeepDataProdResultRepository
     */
    public function __construct(PrecheckKeepDataProdResultRepository $precheckKeepDataProdResultRepository)
    {
        $this->repository = $precheckKeepDataProdResultRepository;
    }
}
