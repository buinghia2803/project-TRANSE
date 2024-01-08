<?php

namespace App\Repositories;

use App\Models\TrademarkPlan;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TrademarkPlanRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   TrademarkPlan $trademarkPlan
     * @return  void
     */
    public function __construct(TrademarkPlan $trademarkPlan)
    {
        $this->model = $trademarkPlan;
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
            case 'flag_role':
            case 'is_reject':
            case 'plan_correspondence_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
