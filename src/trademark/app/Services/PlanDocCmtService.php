<?php

namespace App\Services;

use App\Repositories\PlanDocCmtRepository;

class PlanDocCmtService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param PlanDocCmtRepository $planDetailRepository
     */
    public function __construct(PlanDocCmtRepository $planDocCmtRepository)
    {
        $this->repository = $planDocCmtRepository;
    }
}
