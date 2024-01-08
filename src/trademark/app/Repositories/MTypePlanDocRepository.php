<?php

namespace App\Repositories;

use App\Models\MTypePlanDoc;
use Illuminate\Database\Query\Builder;

class MTypePlanDocRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MTypePlan $mTypePlan
     * @return  void
     */
    public function __construct(MTypePlanDoc $mTypePlanDoc)
    {
        $this->model = $mTypePlanDoc;
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
            case 'name':
            case 'm_type_plan_id':
            case 'description':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get all type plan doc
     */
    public function getAllTypePlanDoc()
    {
        return $this->model->newQuery()->get();
    }
}
