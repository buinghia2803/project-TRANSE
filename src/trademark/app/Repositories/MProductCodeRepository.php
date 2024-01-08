<?php

namespace App\Repositories;

use App\Models\MProductCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MProductCodeRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param  MProductCode $mProductCodes
     * @return  void
     */
    public function __construct(MProductCode $mProductCode)
    {
        $this->model = $mProductCode;
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
            case 'm_product_id':
            case 'm_code_id':
                return $query->search($column, $data);
            default:
                return $query;
        }
    }
}
