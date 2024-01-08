<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   User $user
     * @return  void
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Get status
     *
     * @return  array
     */
    public function statusTypes(): array
    {
        $status = $this->model->statusTypes;
        return [
            $status['active'] => __('labels.active'),
            $status['inactive'] => __('labels.inactive'),
        ];
    }

    /**
     * Merge query
     *
     * @param   mixed $query
     * @param   mixed $column
     * @param   mixed $data
     * @return  mixed
     */
    public function mergeQuery($query, $column, $data)
    {
        switch ($column) {
            case 'id':
            case 'status':
            case 'status_withdraw':
            case 'user_number':
                return $query->where($column, $data);
            case 'name':
                return $query->search(DB::raw("concat(first_name, ' ', last_name)"), $data);
            case 'email':
                return $query->search($column, $data);
            case 'created_at_start':
                $data = Carbon::createFromFormat('Y-m-d', $data)->startOfDay();
                return $query->where('created_at', '>=', $data);
            case 'created_at_end':
                $data = Carbon::createFromFormat('Y-m-d', $data)->endOfDay();
                return $query->where('created_at', '<=', $data);
            default:
                return $query;
        }
    }

    /**
     * @param   User $user
     * @return  void
     */
    public function updateToken(User $user)
    {
        $user->access_token = $user->createToken($user->email)->accessToken;
    }

    /**
     * @param   User  $user
     * @param   array $roles
     * @return  void
     */
    public function syncRoles(User $user, array $roles)
    {
        $user->syncRoles($roles);
    }

    /**
     * @param   User $user
     * @return  void
     */
    public function getPermissions(User $user)
    {
        $user->permissions = $user->getPermissionsViaRoles();
    }

    /**
     * Get user is deleted by email.
     *
     * @param integer $memberId
     * @return boolean
     */
    public function checkExistsMemberId(string $memberId): bool
    {
        return $this->model
            ->where('id', '!=', auth()->user()->id)
            ->where('info_member_id', $memberId)
            ->exists();
    }
    /**
     * Get user is deleted by email.
     *
     * @param string $email
     * @return null|Model
     */
    public function getUserDelByEmail(string $email): ?Model
    {
        return $this->model->newQuery()->withTrashed()->where('email', $email)->first();
    }

    /**
     * Pluck Member Id
     *
     * @param string $valMemberId
     * @return boolean
     */
    public function pluckMemberId($valMemberId): bool
    {
        return $this->model->where('info_member_id', $valMemberId)->exists();
    }
}
