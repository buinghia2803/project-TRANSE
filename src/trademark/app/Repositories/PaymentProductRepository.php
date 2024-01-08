<?php

namespace App\Repositories;

use App\Models\PaymentProd;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PaymentProductRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param PaymentProd $planCorrespondence
     * @return  void
     */
    public function __construct(PaymentProd $paymentProd)
    {
        $this->model = $paymentProd;
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
            case 'payment_id':
                return $query->whereRaw($data);
            case 'm_product_id':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
