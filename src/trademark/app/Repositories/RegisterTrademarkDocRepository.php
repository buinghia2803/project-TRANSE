<?php

namespace App\Repositories;

use App\Models\RegisterTrademarkDoc;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class RegisterTrademarkDocRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   RegisterTrademarkDoc $registerTrademarkDoc
     * @return  void
     */
    public function __construct(RegisterTrademarkDoc $registerTrademarkDoc)
    {
        $this->model = $registerTrademarkDoc;
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
