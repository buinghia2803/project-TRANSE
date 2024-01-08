<?php

namespace App\Repositories;

use App\Models\Authentication;
use Illuminate\Database\Eloquent\Builder;

class AuthenticationRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   Authentication $authentication
     * @return  void
     */
    public function __construct(Authentication $authentication)
    {
        $this->model = $authentication;
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
            case 'token':
            case 'user_id':
            case 'code':
            case 'type':
                return $query->where($column, $data);
            default:
                return $query;
        }
    }
}
