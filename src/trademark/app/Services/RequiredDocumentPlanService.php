<?php

namespace App\Services;

use App\Repositories\RequiredDocumentPlanRepository;

class RequiredDocumentPlanService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param RequiredDocumentPlanRepository $requiredDocumentPlanRepository
     */
    public function __construct(RequiredDocumentPlanRepository $requiredDocumentPlanRepository)
    {
        $this->repository = $requiredDocumentPlanRepository;
    }
}
