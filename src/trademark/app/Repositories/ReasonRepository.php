<?php

namespace App\Repositories;

use App\Models\Reason;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ReasonRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   Reason $reason
     * @return  void
     */
    public function __construct(Reason $reason)
    {
        $this->model = $reason;
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
            case 'reason_no_id':
            case 'reason_name':
            case 'question_type':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
