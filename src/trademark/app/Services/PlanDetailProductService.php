<?php

namespace App\Services;

use App\Repositories\PlanDetailProductRepository;

class PlanDetailProductService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param PlanDetailProductRepository $planDetailProductRepository
     */
    public function __construct(PlanDetailProductRepository $planDetailProductRepository)
    {
        $this->repository = $planDetailProductRepository;
    }
}
