<?php

namespace App\Services;

use App\Repositories\ReasonRefNumProdRepository;

class ReasonRefNumProdService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param ReasonRefNumProdRepository $reasonRefNumProdRepository
     */
    public function __construct(ReasonRefNumProdRepository $reasonRefNumProdRepository)
    {
        $this->repository = $reasonRefNumProdRepository;
    }
}
