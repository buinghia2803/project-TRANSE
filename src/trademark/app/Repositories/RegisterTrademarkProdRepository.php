<?php

namespace App\Repositories;

use App\Models\RegisterTrademarkProd;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class RegisterTrademarkProdRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   RegisterTrademarkProd $registerTrademarkProd
     * @return  void
     */
    public function __construct(RegisterTrademarkProd $registerTrademarkProd)
    {
        $this->model = $registerTrademarkProd;
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
            case 'user_id':
            case 'admin_id':
            case 'question_type':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
