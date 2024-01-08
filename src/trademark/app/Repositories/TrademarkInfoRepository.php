<?php

namespace App\Repositories;

use App\Models\TrademarkInfo;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class TrademarkInfoRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   TrademarkInfo $trademarkInfo
     * @return  void
     */
    public function __construct(TrademarkInfo $trademarkInfo)
    {
        $this->model = $trademarkInfo;
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
            case 'type':
            case 'target_id':
            case 'app_trademark_id':
            case 'm_distinction_id':
            case 'from_page':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
