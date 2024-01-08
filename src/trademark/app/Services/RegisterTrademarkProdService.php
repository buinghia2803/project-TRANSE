<?php

namespace App\Services;

use App\Models\RegisterTrademarkProd;
use App\Repositories\RegisterTrademarkProdRepository;
use Illuminate\Database\Eloquent\Collection;

class RegisterTrademarkProdService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param RegisterTrademarkProdRepository $registerTrademarkProdRepository
     */
    public function __construct(
        RegisterTrademarkProdRepository $registerTrademarkProdRepository
    )
    {
        $this->repository = $registerTrademarkProdRepository;
    }

    /**
     * Count distinction with condition.
     *
     * @param Collection $registerTrademarkProds
     * @param string $isApply
     * @return int
     */
    public function countDistinctionWithCondition($registerTrademarkProds, $isApply = ALL_APPLY)
    {
        $registerTrademarkProds = $this->getRegisterTradeMarkApply($registerTrademarkProds, $isApply);

        return $registerTrademarkProds->pluck('m_distinction_id')->unique()->count() ?? 0;
    }

    /**
     * Get Register TradeMark Apply
     *
     * @param  mixed $registerTrademarkProds
     * @param  mixed $isApply
     * @return void
     */
    public function getRegisterTradeMarkApply($registerTrademarkProds, $isApply)
    {
        $condition = $registerTrademarkProds;
        if ($isApply == NOT_APPLY) {
            $condition = $registerTrademarkProds->where('is_apply', RegisterTrademarkProd::IS_NOT_APPLY);
        } elseif ($isApply == APPLY) {
            $condition = $registerTrademarkProds->where('is_apply', RegisterTrademarkProd::IS_APPLY);
        } elseif ($isApply == ALL_APPLY) {
            $condition = $registerTrademarkProds;
        }

        $registerTrademarkProds = $condition->map(function ($item) {
            $item->m_distinction_id = $item->appTrademarkProd->mProduct->m_distinction_id ?? null;

            return $item;
        });

        return $registerTrademarkProds;
    }

    /**
     * Count Distinction Registration Document
     *
     * @param  mixed $registerTrademarkProds
     * @param  mixed $isApply
     * @return void
     */
    public function countDistinctionRegistrationDocument($registerTrademarkProds, $isApply = ALL_APPLY)
    {
        $registerTrademarkProds = $this->getRegisterTradeMarkApply($registerTrademarkProds, $isApply);

        return $registerTrademarkProds->groupBy('m_distinction_id')->count() ?? 0;
    }
}
