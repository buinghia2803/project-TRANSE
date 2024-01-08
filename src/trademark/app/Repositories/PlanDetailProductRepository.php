<?php

namespace App\Repositories;

use App\Models\PlanDetailProduct;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PlanDetailProductRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PlanDetailProduct $planDetailProduct
     * @return  void
     */
    public function __construct(PlanDetailProduct $planDetailProduct)
    {
        $this->model = $planDetailProduct;
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
            case 'plan_detail_distinct_id':
            case 'is_choice':
            case 'plan_detail_id':
                return $query->where($column, $data);
            case 'ids':
                return $query->whereIn('id', $data);
            case 'plan_detail_ids':
                return $query->whereIn('plan_detail_id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
