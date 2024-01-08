<?php

namespace App\Services;

use App\Repositories\RequiredDocumentCommentRepository;

class RequiredDocumentCommentService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param RequiredDocumentCommentRepository $requiredDocumentCommentRepository
     */
    public function __construct(RequiredDocumentCommentRepository $requiredDocumentCommentRepository)
    {
        $this->repository = $requiredDocumentCommentRepository;
    }
}
