<?php

namespace App\Repositories;

use App\Models\RegisterTrademarkRenewal;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class RegisterTrademarkRenewalRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   RegisterTrademarkRenewal $registerTrademarkProd
     * @return  void
     */
    public function __construct(RegisterTrademarkRenewal $registerTrademarkRenewal)
    {
        $this->model = $registerTrademarkRenewal;
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
            case 'register_trademark_id':
            case 'type':
            case 'registration_period':
            case 'is_send_mail':
            case 'start_date':
            case 'end_date':
            case 'status':
                return $query->where($column, $data);
            case 'ids':
                return $query->whereIn('id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
