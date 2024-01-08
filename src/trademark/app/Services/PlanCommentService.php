<?php

namespace App\Services;

use App\Repositories\PlanCommentRepository;

class PlanCommentService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param PlanCommentRepository $planCommentRepository
     */
    public function __construct(PlanCommentRepository $planCommentRepository)
    {
        $this->repository = $planCommentRepository;
    }
}
