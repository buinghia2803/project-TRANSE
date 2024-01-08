<?php

namespace App\Repositories;

use App\Models\PlanDetailDistinct;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PlanDetailDistinctRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PlanDetailDistinct $planDetailDistinct
     * @return  void
     */
    public function __construct(PlanDetailDistinct $planDetailDistinct)
    {
        $this->model = $planDetailDistinct;
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
            case 'is_add':
            case 'is_distinct_settlement_edit':
            case 'is_distinct_settlement':
            case 'plan_detail_id':
            case 'm_distinction_id':
                return $query->where($column, $data);
            case 'ids':
                return $query->whereIn('id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
