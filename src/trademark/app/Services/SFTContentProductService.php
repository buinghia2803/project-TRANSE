<?php

namespace App\Services;

use App\Repositories\SFTContentProductRepository;

class SFTContentProductService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param SFTContentProductRepository $sftContentProductRepository
     */
    public function __construct(SFTContentProductRepository $sftContentProductRepository)
    {
        $this->repository = $sftContentProductRepository;
    }
}
