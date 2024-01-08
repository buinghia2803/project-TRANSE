<?php

namespace App\Repositories;

use App\Models\PrecheckKeepData;
use App\Models\PrecheckKeepDataProd;
use App\Models\PrecheckKeepDataProdResult;
use Illuminate\Database\Eloquent\Builder;

class PrecheckKeepDataRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PrecheckKeepData $precheckKeepData
     * @return  void
     */
    public function __construct(PrecheckKeepData $precheckKeepData)
    {
        $this->model = $precheckKeepData;
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
            case 'precheck_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
