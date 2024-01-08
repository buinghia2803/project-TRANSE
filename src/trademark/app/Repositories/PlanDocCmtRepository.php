<?php

namespace App\Repositories;

use App\Models\PlanDocCmt;
use Illuminate\Database\Eloquent\Builder;

class PlanDocCmtRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param PlanDocCmt $planDocCmt
     * @return  void
     */
    public function __construct(PlanDocCmt $planDocCmt)
    {
        $this->model = $planDocCmt;
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
            case 'type':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
