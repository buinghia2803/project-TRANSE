<?php

namespace App\Repositories;

use App\Models\TrademarkRenewalNotice;
use Illuminate\Database\Eloquent\Builder;

class TrademarkRenewalNoticeRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   TrademarkRenewalNotice $trademarkRenewalNotice
     * @return  void
     */
    public function __construct(TrademarkRenewalNotice $trademarkRenewalNotice)
    {
        $this->model = $trademarkRenewalNotice;
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
