<?php

namespace App\Repositories;

use App\Models\MDistinction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MDistinctionRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MDistinction $mDistinction
     * @return  void
     */
    public function __construct(MDistinction $mDistinction)
    {
        $this->model = $mDistinction;
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
            case 'admin_id':
            case 'name':
                return $query->where($column, $data);
            default:
                return $query;
        }
    }

    /**
     * List nation options
     *
     * @return Collection
     */
    public function listNationOptions()
    {
        return $this->model->pluck('name', 'id');
    }
}
