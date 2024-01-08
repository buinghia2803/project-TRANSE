<?php

namespace App\Repositories;

use App\Models\SFTContentProduct;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class SFTContentProductRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   SFTContentProduct $sftContentProduct
     * @return  void
     */
    public function __construct(SFTContentProduct $sftContentProduct)
    {
        $this->model = $sftContentProduct;
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
            case 'is_choice_admin':
            case 'support_first_time_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
