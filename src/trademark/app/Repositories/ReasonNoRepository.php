<?php

namespace App\Repositories;

use App\Models\ReasonNo;
use Illuminate\Database\Eloquent\Builder;

class ReasonNoRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   ReasonNo $reasonNo
     * @return  void
     */
    public function __construct(ReasonNo $reasonNo)
    {
        $this->model = $reasonNo;
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
            case 'bigger_id':
                return $query->where('id', '>', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
