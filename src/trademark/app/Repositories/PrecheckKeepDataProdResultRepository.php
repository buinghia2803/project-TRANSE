<?php

namespace App\Repositories;

use App\Models\PrecheckKeepDataProdResult;
use Illuminate\Database\Eloquent\Builder;

class PrecheckKeepDataProdResultRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PrecheckKeepDataProdResult $precheckKeepDataProdResult
     * @return  void
     */
    public function __construct(PrecheckKeepDataProdResult $precheckKeepDataProdResult)
    {
        $this->model = $precheckKeepDataProdResult;
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
            case 'precheck_keep_data_prod_id':
                return $query->where($column, $data);
            case 'precheck_keep_data_prod_ids':
                return $query->whereIn('precheck_keep_data_prod_id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
