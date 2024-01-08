<?php

namespace App\Services;

use App\Models\Permission;
use App\Services\BaseService;
use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class RoleService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->repository = $roleRepository;
    }

    /**
     * Get list.
     *
     * @param   mixed $params
     * @return  LengthAwarePaginator
     */
    public function listRole($params): LengthAwarePaginator
    {
        return $this->listPaginate($params, ['permissions']);
    }

    /**
     * Show.
     *
     * @param   mixed $params
     * @return  Model
     */
    public function detail($params): Model
    {
        return $this->show($params, ['permissions']);
    }

    /**
     * @param   Role $role
     * @return  boolean|null
     */
    public function delete(Role $role): ?bool
    {
        return $this->repository->delete($role);
    }

    /**
     * @param Role  $role
     * @param array $permissions
     * @return boolean
     */
    public function syncPermissions(Role $role, array $permissions): bool
    {
        try {
            $permissions = Permission::whereIn('name', $permissions)->get();
            $permissionIds = $permissions->pluck('id')->toArray();
            $role->permissions()->sync($permissionIds);

            return true;
        } catch (\Exception $e) {
            Log::error('[RoleService->syncPermissions:' . __LINE__ . ']' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get permission of role.
     *
     * @return Collection
     */
    public function getPermissions(): Collection
    {
        return $this->repository->getPermissions();
    }
}
