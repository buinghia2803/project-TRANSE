<?php

namespace App\Services;

use App\Repositories\RegisterTrademarkRenewalRepository;

class RegisterTrademarkRenewalService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   RegisterTrademarkRenewalRepository $registerTrademarkRenewalRepository
     */
    public function __construct(
        RegisterTrademarkRenewalRepository $registerTrademarkRenewalRepository
    )
    {
        $this->repository = $registerTrademarkRenewalRepository;
    }
}
