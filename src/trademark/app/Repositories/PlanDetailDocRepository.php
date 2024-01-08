<?php

namespace App\Repositories;

use App\Models\PlanDetailDoc;
use Illuminate\Database\Eloquent\Builder;

class PlanDetailDocRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param PlanDetailDoc $planDetailDoc
     * @return  void
     */
    public function __construct(PlanDetailDoc $planDetailDoc)
    {
        $this->model = $planDetailDoc;
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
            case 'plan_detail_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
