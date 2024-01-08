<?php

namespace App\Services;

use App\Repositories\ReasonQuestionRepository;

class ReasonQuestionService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param ReasonQuestionRepository $reasonQuestionRepository
     */
    public function __construct(ReasonQuestionRepository $reasonQuestionRepository)
    {
        $this->repository = $reasonQuestionRepository;
    }
}
