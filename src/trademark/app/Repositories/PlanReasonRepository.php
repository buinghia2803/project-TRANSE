<?php

namespace App\Repositories;

use App\Models\PlanReason;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PlanReasonRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PlanReason $planReason
     * @return  void
     */
    public function __construct(PlanReason $planReason)
    {
        $this->model = $planReason;
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
