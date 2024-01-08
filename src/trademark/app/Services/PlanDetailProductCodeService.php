<?php

namespace App\Services;

use App\Repositories\PlanDetailProductCodeRepository;

class PlanDetailProductCodeService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param PlanDetailProductCodeRepository $planDetailProductCodeRepository
     */
    public function __construct(PlanDetailProductCodeRepository $planDetailProductCodeRepository)
    {
        $this->repository = $planDetailProductCodeRepository;
    }
}
