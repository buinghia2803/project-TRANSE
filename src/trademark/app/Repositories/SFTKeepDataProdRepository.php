<?php

namespace App\Repositories;

use App\Models\SFTKeepDataProd;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class SFTKeepDataProdRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   SFTKeepDataProd $sftKeepDataProd
     * @return  void
     */
    public function __construct(SFTKeepDataProd $sftKeepDataProd)
    {
        $this->model = $sftKeepDataProd;
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
