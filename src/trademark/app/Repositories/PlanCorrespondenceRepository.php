<?php

namespace App\Repositories;

use App\Models\PlanCorrespondence;
use App\Models\PlanDetailDistinct;
use App\Models\PlanDetailProduct;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PlanCorrespondenceRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PlanCorrespondence $planCorrespondence
     * @return  void
     */
    public function __construct(PlanCorrespondence $planCorrespondence)
    {
        $this->model = $planCorrespondence;
    }


    /**
     * @param   Builder $query
     * @param   string  $column
     * @param   mixed   $data
     *
     * @return  Builder
     */
    public function mergeQuery(Builder $query, string $column, $data): Builder
    {
        switch ($column) {
            case 'id':
            case 'comparison_trademark_result_id':
            case 'type':
            case 'is_ext_period':
            case 'is_ext_period_2':
            case 'round':
                return $query->where($column, $data);
            default:
                return $query;
        }
    }

    /**
     * Get Number Product
     *
     * @param  mixed $planDetail
     * @return array
     */
    public function getNumberProduct($planDetail): array
    {
        $numberPlanDetailProducts = [];
        if ($planDetail) {
            $planDetail->load('planDetailProducts.planDetailDistinct');
            $planDetailProducts = $planDetail->planDetailProducts->whereIn('role_add', [ROLE_MANAGER, ROLE_SUPERVISOR]);
            foreach ($planDetailProducts as $planDetailProduct) {
                if ($planDetailProduct->planDetailDistinct->is_distinct_settlement == PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_TRUE
                    && $planDetailProduct->is_deleted == false
                    && !in_array($planDetailProduct->leave_status, [PlanDetailProduct::LEAVE_STATUS_7, PlanDetailProduct::LEAVE_STATUS_3])) {
                    $numberPlanDetailProducts[] = $planDetailProduct->m_product_id;
                }
            }
        }

        return $numberPlanDetailProducts;
    }
}
