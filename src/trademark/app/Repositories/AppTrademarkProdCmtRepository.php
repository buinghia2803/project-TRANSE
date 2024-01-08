<?php

namespace App\Repositories;

use App\Models\AppTrademarkProdCmt;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class AppTrademarkProdCmtRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   AppTrademarkProdCmt $appTrademarkProdCmt
     * @return  void
     */
    public function __construct(AppTrademarkProdCmt $appTrademarkProdCmt)
    {
        $this->model = $appTrademarkProdCmt;
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
            case 'app_trademark_id':
            case 'm_distinction_id':
            case 'internal_remark':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
