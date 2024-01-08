<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ForgotPasswordRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PasswordReset $passwordReset
     * @return  void
     */
    public function __construct(PasswordReset $passwordReset)
    {
        $this->model = $passwordReset;
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
            case 'email':
            case 'content':
                return $query->search($column, $data);
            default:
                return $query;
        }
    }
}
