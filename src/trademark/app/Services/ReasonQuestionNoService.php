<?php

namespace App\Services;

use App\Repositories\ReasonQuestionNoRepository;

class ReasonQuestionNoService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param ReasonQuestionNoRepository $reasonQuestionNoRepository
     */
    public function __construct(ReasonQuestionNoRepository $reasonQuestionNoRepository)
    {
        $this->repository = $reasonQuestionNoRepository;
    }
}
