<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class AdminRepository extends BaseRepository
{
    /**
     * @var Admin $admin
     */
    protected $admin;

    /**
     * Initializing the instances and variables
     *
     * @param   Admin $admin
     * @return  void
     */

    public function __construct(Admin $admin)
    {
        $this->model = $admin;
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
            case 'role':
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
     * @param   User  $user
     * @param   array $roles
     * @return  void
     */
    public function syncRoles(Admin $admin, array $roles)
    {
        $admin->syncRoles($roles);
    }

    /**
     * @param   Admin $admin
     * @return  void
     */
    public function getPermissions(Admin $admin)
    {
        $admin->permissions = $admin->getPermissionsViaRoles();
    }
}
