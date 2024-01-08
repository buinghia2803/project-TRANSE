<?php

namespace App\Services;

use App\Models\GMOPayment;
use App\Services\BaseService;
use App\Repositories\GMOPaymentRepository;

class GMOPaymentService extends BaseService
{
  /**
   * Initializing the instances and variables
   *
   * @param     GMOPaymentRepository $gmoPaymentRepository
   */
    public function __construct(GMOPaymentRepository $gmoPaymentRepository)
    {
        $this->repository = $gmoPaymentRepository;
    }
}
