<?php

namespace App\Services;

use App\Models\RequiredDocument;
use App\Repositories\RequiredDocumentRepository;

class RequiredDocumentService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param RequiredDocumentRepository $requiredDocumentRepository
     */
    public function __construct(RequiredDocumentRepository $requiredDocumentRepository)
    {
        $this->repository = $requiredDocumentRepository;
    }
}
