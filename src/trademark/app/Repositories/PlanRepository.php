<?php

namespace App\Repositories;

use App\Models\MProduct;
use App\Models\Plan;
use App\Models\PlanDetailProductCode;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PlanRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param Plan $plan
     * @return  void
     */
    public function __construct(Plan $plan)
    {
        $this->model = $plan;
    }


    /**
     * @param Builder $query
     * @param string $column
     * @param mixed $data
     *
     * @return  Builder
     */
    public function mergeQuery(Builder $query, string $column, $data): Builder
    {
        switch ($column) {
            case 'id':
            case 'trademark_plan_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get data product plan supervisor
     *
     * @param array $trademarkPlanDetailIds
     * @return Collection
     */
    public function getDataProductPlanSupervisor(array $trademarkPlanDetailIds): Collection
    {
        $data = MProduct::with(['mDistinction'])
            ->join('plan_detail_products', 'plan_detail_products.m_product_id', 'm_products.id')
            ->join('m_product_codes', 'm_product_codes.m_product_id', 'm_products.id')
            ->leftJoin(DB::raw(
                '(SELECT
                        reason_ref_num_prods.rank,
                        app_trademark_prods.m_product_id
                FROM app_trademark_prods
                JOIN plan_correspondence_prods ON plan_correspondence_prods.app_trademark_prod_id = app_trademark_prods.id
                JOIN reason_ref_num_prods ON reason_ref_num_prods.plan_correspondence_prod_id = plan_correspondence_prods.id) tmpTbl'
            ), function ($join) {
                $join->on('m_products.id', '=', 'tmpTbl.m_product_id');
            })
            ->select(
                'm_products.m_distinction_id',
                'm_products.id',
                'm_products.products_number',
                'tmpTbl.rank',
                'm_products.name',
                'm_products.type',
                'm_products.block',
                'plan_detail_products.id as plan_detail_product_id',
                'plan_detail_products.leave_status',
                'plan_detail_products.leave_status_other',
                'plan_detail_products.is_choice',
                'plan_detail_products.plan_detail_id',
                'plan_detail_products.role_add'
            )->distinct()
            ->whereIn('plan_detail_products.plan_detail_id', $trademarkPlanDetailIds)
            ->where('plan_detail_products.is_deleted', false)
            ->whereNull('plan_detail_products.deleted_at')
            ->get();

        if (count($data) > 0) {
            $planDetailProductIds = $data->pluck('plan_detail_product_id')->unique()->toArray();
            $planDetailProductCodes = PlanDetailProductCode::whereIn('plan_detail_product_id', $planDetailProductIds)->with(['mCode'])->get();

            $data = $data->map(function ($item) use ($planDetailProductCodes) {
                $item->m_code_names = $planDetailProductCodes->where('plan_detail_product_id', $item->plan_detail_product_id)->pluck('mCode.name');

                return $item;
            });
        }

        return $data;
    }

    /**
     * Get data of a203
     *
     * @param int $trademarkPlanId
     */
    public function getData($trademarkPlanId)
    {
        $plans = $this->model->where('trademark_plan_id', $trademarkPlanId)->get();
    }
}
