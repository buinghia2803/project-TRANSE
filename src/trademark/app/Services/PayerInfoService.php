<?php

namespace App\Services;

use App\Models\PayerInfo;
use App\Models\Payment;
use App\Models\SupportFirstTime;
use App\Repositories\PayerInfoRepository;
use App\Services\GMO\GMOService;

class PayerInfoService extends BaseService
{
    /**
     * @var     PayerInfoRepository $payerInfoRepository
     * @var     GMOService $gmoService
     */
    protected $payerInfoRepository;

    /**
     * Initializing the instances and variables
     *
     * @param   PayerInfoRepository $payerInfoRepository
     */
    public function __construct(
        PayerInfoRepository $payerInfoRepository
    )
    {
        $this->repository = $payerInfoRepository;
    }

    /**
     * Find Payer with support first time.
     *
     * @param SupportFirstTime $sft
     */
    public function findPayerWithSFT(SupportFirstTime $sft)
    {
        try {
            $sft->load('StfSuitableProduct');
            if ($sft->StfSuitableProduct) {
                $payerInfo = $this->repository->findByCondition([
                    'type' => TYPE_SFT,
                    'target_id' => $sft->id,
                ])->first();

                return $payerInfo;
            }
            return null;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Find Payer with support first time.
     *
     * @param SupportFirstTime $sft
     */
    public function findPayerWithPayment(Payment $payment)
    {
        try {
            $payment->load('payerInfo');
            if ($payment->payerInfo) {
                return $payment->payerInfo;
            }

            return null;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Find Payer with precheck.
     *
     * @param string $precheck
     */
    public function findPayerWithPrecheck(string $precheck)
    {
        try {
            if ($precheck) {
                $payerInfo = $this->repository->findByCondition([
                    'type' => TYPE_PRE_CHECK,
                    'target_id' => $precheck,
                ])->first();

                return $payerInfo;
            }
            return null;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Find Payer with precheck.
     *
     * @param int $id
     */
    public function findPayerInfoWithAppTrademark(int $id)
    {
        return $this->repository->findByCondition([
            'type' => TYPE_APP_TRADEMARK,
            'target_id' => $id, // Id app trademark
        ])->first();
    }
}
