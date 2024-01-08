<?php

namespace App\Services;

use App\Repositories\PrecheckKeepDataProdRepository;
use Illuminate\Database\Eloquent\Builder;

class PrecheckKeepDataProdService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   PrecheckKeepDataProdRepository $precheckKeepDataProdRepository
     */
    public function __construct(PrecheckKeepDataProdRepository $precheckKeepDataProdRepository)
    {
        $this->repository = $precheckKeepDataProdRepository;
    }

    /**
     * Get data precheck keep data (order by distriction)
     *
     * @param array $id
     *
     * @return  Builder
     */
    public function getPrecheckKeepDataProduct($id)
    {
        return $this->repository->getPrecheckKeepDataProduct($id);
    }

    /**
     * Get data precheck keep data (order by code name)
     *
     * @param array $id
     *
     * @return  Builder
     */
    public function getPrecheckKeepDataProduct2($id)
    {
        return $this->repository->getPrecheckKeepDataProduct2($id);
    }
}
