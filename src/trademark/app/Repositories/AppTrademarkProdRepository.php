<?php

namespace App\Repositories;

use App\Models\AppTrademarkProd;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AppTrademarkProdRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   AppTrademarkProd $appTrademarkProd
     * @return  void
     */
    public function __construct(AppTrademarkProd $appTrademarkProd)
    {
        $this->model = $appTrademarkProd;
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
            case 'm_product_id':
            case 'is_apply':
            case 'is_remove':
            case 'is_block':
            case 'is_new_prod':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get Trademark By Ids
     *
     * @param  collection $appTrademarkProdIds
     * @return collection|null
     */
    public function getTrademarkByIds($appTrademarkProdIds): ?Collection
    {
        return $this->model->whereIn('id', $appTrademarkProdIds)->get();
    }
}
