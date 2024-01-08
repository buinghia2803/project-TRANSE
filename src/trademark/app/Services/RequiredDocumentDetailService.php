<?php

namespace App\Services;

use App\Repositories\RequiredDocumentDetailRepository;

class RequiredDocumentDetailService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param RequiredDocumentDetailRepository $requiredDocumentDetailRepository
     */
    public function __construct(RequiredDocumentDetailRepository $requiredDocumentDetailRepository)
    {
        $this->repository = $requiredDocumentDetailRepository;
    }
}
