<?php

namespace App\Services;

use App\Repositories\AppTrademarkProdRepository;

class AppTrademarkProdService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param AppTrademarkProdRepository $appTrademarkProdRepository
     */
    public function __construct(AppTrademarkProdRepository $appTrademarkProdRepository)
    {
        $this->repository = $appTrademarkProdRepository;
    }
}
