<?php

namespace App\Repositories;

use App\Models\ReasonRefNumProd;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ReasonRefNumProdRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   ReasonRefNumProd $reasonRefNumProd
     * @return  void
     */
    public function __construct(ReasonRefNumProd $reasonRefNumProd)
    {
        $this->model = $reasonRefNumProd;
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
            case 'rank':
            case 'reason_no_id':
            case 'is_choice':
                return $query->where($column, $data);
            case 'plan_correspondence_prod_ids':
                return $query->whereIn('plan_correspondence_prod_id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
