<?php

namespace App\Services;

use App\Repositories\PlanReasonRepository;

class PlanReasonService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param PlanReasonRepository $planReasonRepository
     */
    public function __construct(PlanReasonRepository $planReasonRepository)
    {
        $this->repository = $planReasonRepository;
    }
}
