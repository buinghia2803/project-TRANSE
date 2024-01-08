<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Services\RoleService;
use App\Http\Requests\Role\RoleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends BaseController
{
    /**
     * @var     RoleService $roleService
     */
    protected RoleService $roleService;

    /**
     * Constructor
     *
     * @param   RoleService $roleService
     * @return  void
     */
    public function __construct(RoleService $roleService)
    {
        parent::__construct();

        $this->roleService = $roleService;

        $this->middleware('permission:role.index')->only(['index']);
        $this->middleware('permission:role.store')->only(['create', 'store']);
        $this->middleware('permission:role.show')->only(['show']);
        $this->middleware('permission:role.update')->only(['edit', 'update']);
        $this->middleware('permission:role.destroy')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param   Request $request
     * @return  mixed
     */
    public function index(Request $request)
    {
        $roles = $this->roleService->listRole($request->all());

        if ($roles->currentPage() > $roles->lastPage()) {
            $params['page'] = $roles->lastPage();
            return redirect(route('admin.role.index', $params));
        }

        return view('admin.modules.role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $permissions = config('permission.data');

        return view('admin.modules.role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param   RoleRequest $request
     * @return  RedirectResponse
     */
    public function store(RoleRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Create role and sync permission
            $request['guard_name'] = 'web';
            $role = $this->roleService->create($request->all());
            $this->roleService->syncPermissions($role, $request->permissions ?? []);

            // Commit and return
            DB::commit();
            return redirect()->route('admin.role.' . $request->redirect, $role->id)->with('message', __('messages.create_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', __('messages.create_fail'))->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param   Role $role
     * @return  View
     */
    public function edit(Role $role): View
    {
        $role = $this->roleService->detail($role, ['permissions']);
        $permissions = config('permission.data');

        return view('admin.modules.role.edit', compact(
            'role',
            'permissions'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param   RoleRequest $request
     * @param   Role        $role
     * @return  RedirectResponse
     */
    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Create role and sync permission
            $this->roleService->update($role, $request->only('name'));
            $this->roleService->syncPermissions($role, $request->permissions ?? []);

            // Commit and return
            DB::commit();
            return redirect()->route('admin.role.' . $request->redirect, $role->id)->with('message', __('messages.update_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', __('messages.update_fail'))->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param   integer $id
     * @return  RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $role = $this->roleService->find($id);

            if ($role == null) {
                return redirect()->back();
            }

            // Check user of role
            $userOfRoles = $role->users()->get();
            if ($userOfRoles->count() > 0) {
                CommonHelper::setMessage(request(), MESSAGE_ERROR, __('messages.delete_role_denied'));
                return redirect()->back();
            }

            // Destroy Role
            $this->roleService->delete($role);

            // Commit and return
            DB::commit();

            CommonHelper::setMessage(request(), MESSAGE_SUCCESS, __('messages.delete_success'));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage(request(), MESSAGE_ERROR, __('messages.delete_fail'));
            return redirect()->back();
        }
    }
}
