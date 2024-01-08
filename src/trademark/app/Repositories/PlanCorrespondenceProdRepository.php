<?php

namespace App\Repositories;

use App\Models\PlanCorrespondenceProd;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PlanCorrespondenceProdRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PlanCorrespondenceProd $planCorrespondenceProd
     * @return  void
     */
    public function __construct(PlanCorrespondenceProd $planCorrespondenceProd)
    {
        $this->model = $planCorrespondenceProd;
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
            case 'plan_correspondence_id':
            case 'is_register':
            case 'app_trademark_prod_id':
            case 'completed_evaluation':
                return $query->where($column, $data);
            case 'app_trademark_prod_ids':
                return $query->whereIn('app_trademark_prod_id', $data);
            default:
                return $query;
        }
    }
}
