<?php

namespace App\Services;

use App\Repositories\RequiredDocumentMissRepository;

class RequiredDocumentMissService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param RequiredDocumentMissRepository $requiredDocumentMissRepository
     */
    public function __construct(RequiredDocumentMissRepository $requiredDocumentMissRepository)
    {
        $this->repository = $requiredDocumentMissRepository;
    }
}
