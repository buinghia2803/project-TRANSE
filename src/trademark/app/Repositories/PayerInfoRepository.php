<?php

namespace App\Repositories;

use App\Models\PayerInfo;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PayerInfoRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PayerInfo $payerInfo
     * @return  void
     */
    public function __construct(PayerInfo $payerInfo)
    {
        $this->model = $payerInfo;
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
            case 'payment_id':
            case 'gmo_order_id':
            case 'pay_type':
            case 'access_id':
            case 'forward':
            case 'approve':
            case 'tran_id':
            case 'status':
            case 'type':
            case 'target_id':
            case 'from_page':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
