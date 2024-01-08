<?php

namespace App\Repositories;

use App\Models\MTypePlan;
use Illuminate\Database\Query\Builder;

class MTypePlanRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MTypePlan $mTypePlan
     * @return  void
     */
    public function __construct(MTypePlan $mTypePlan)
    {
        $this->model = $mTypePlan;
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
            case 'description':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    public function getAllTypePlan()
    {
        return $this->model->newQuery()->get();
    }
}
