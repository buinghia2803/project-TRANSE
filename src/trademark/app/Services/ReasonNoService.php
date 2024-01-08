<?php

namespace App\Services;

use App\Repositories\ReasonNoRepository;

class ReasonNoService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   ReasonNoRepository $reasonNoRepository
     */
    public function __construct(
        ReasonNoRepository $reasonNoRepository
    )
    {
        $this->repository = $reasonNoRepository;
    }
}
