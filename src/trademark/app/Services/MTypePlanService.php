<?php

namespace App\Services;

use App\Repositories\MTypePlanRepository;

class MTypePlanService extends BaseService
{

    /**
     * Initializing the instances and variables
     *
     * @param MTypePlanRepository $mTypePlanRepository
     */
    public function __construct(
        MTypePlanRepository $mTypePlanRepository
    )
    {
        $this->repository = $mTypePlanRepository;
    }

    /**
     * Get all type plan
     */
    public function getAllTypePlan()
    {
        return $this->repository->getAllTypePlan();
    }
}
