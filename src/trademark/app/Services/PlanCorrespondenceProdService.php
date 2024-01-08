<?php

namespace App\Services;

use App\Repositories\PlanCorrespondenceProdRepository;

class PlanCorrespondenceProdService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param PlanCorrespondenceProdRepository $planCorrespondenceProdRepository
     */
    public function __construct(PlanCorrespondenceProdRepository $planCorrespondenceProdRepository)
    {
        $this->repository = $planCorrespondenceProdRepository;
    }
}
