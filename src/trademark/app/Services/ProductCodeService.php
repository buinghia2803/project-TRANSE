<?php

namespace App\Services;

use App\Repositories\ProductCodeRepository;

class ProductCodeService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   ProductCodeRepository $productCodeRepository
     */
    public function __construct(ProductCodeRepository $productCodeRepository)
    {
        $this->repository = $productCodeRepository;
    }
}
