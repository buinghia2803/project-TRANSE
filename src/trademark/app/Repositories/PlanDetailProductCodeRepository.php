<?php

namespace App\Repositories;

use App\Models\PlanDetailProductCode;
use Illuminate\Database\Eloquent\Builder;

class PlanDetailProductCodeRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PlanDetailProductCode $planDetailProductCode
     * @return  void
     */
    public function __construct(PlanDetailProductCode $planDetailProductCode)
    {
        $this->model = $planDetailProductCode;
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
            case 'plan_detail_product_id':
            case 'm_code_id':
            case 'code_name_edit':
                return $query->where($column, $data);
            case 'plan_detail_product_ids':
                return $query->whereIn('plan_detail_product_id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
