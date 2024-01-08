<?php

namespace App\Repositories;

use App\Models\PlanDetail;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PlanDetailRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PlanDetail $planDetail
     * @return  void
     */
    public function __construct(PlanDetail $planDetail)
    {
        $this->model = $planDetail;
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
            case 'plan_id':
                return $query->where($column, $data);
            case 'plan_ids':
                return $query->whereIn('plan_id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
