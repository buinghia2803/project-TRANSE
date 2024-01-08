<?php

namespace App\Repositories;

use App\Models\SFTKeepDataProdCode;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class SFTKeepDataProdCodeRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   SFTKeepDataProdCode $model
     * @return  void
     */
    public function __construct(SFTKeepDataProdCode $model)
    {
        $this->model = $model;
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
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
