<?php

namespace App\Services;

use App\Repositories\ReasonCommentRepository;

class ReasonCommentService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param ReasonCommentRepository $reasonCommentRepository
     */
    public function __construct(ReasonCommentRepository $reasonCommentRepository)
    {
        $this->repository = $reasonCommentRepository;
    }
}
