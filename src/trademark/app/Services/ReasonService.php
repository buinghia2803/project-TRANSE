<?php

namespace App\Services;

use App\Repositories\ReasonRepository;

class ReasonService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   ReasonRepository $reasonRepository
     */
    public function __construct(
        ReasonRepository $reasonRepository
    )
    {
        $this->repository = $reasonRepository;
    }
}
