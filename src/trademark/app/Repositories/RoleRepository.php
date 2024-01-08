<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;

class RoleRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   Role $role
     * @return  void
     */
    public function __construct(Role $role)
    {
        $this->model = $role;
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
            case 'name':
                return $query->search($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * @param   mixed $role
     * @return  boolean|null
     */
    public function delete($role): ?bool
    {
        DB::table('model_has_roles')->where('role_id', $role->id)->delete();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $role->syncPermissions();
        return $role->delete();
    }

    /**
     * @param   Role  $role
     * @param   array $permissions
     * @return  void
     */
    public function syncPermissions(Role $role, array $permissions)
    {
        $oldNames = $role->permissions()->pluck('name')->toArray();
        $role->syncPermissions($permissions);
    }

    /**
     * Get permission list.
     *
     * @return Collection
     */
    public function getPermissions(): Collection
    {
        return Permission::get();
    }
}
