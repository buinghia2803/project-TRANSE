<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Http\Requests\User\ChangePassword;
use App\Http\Requests\User\ProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use App\Services\RoleService;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
    /**
     * @var     UserService $userService
     */
    protected $userService;

    /**
     * @var     RoleService
     */
    protected $roleService;

    /**
     * Constructor
     *
     * @param   UserService $userService
     * @param   RoleService $roleService
     * @return  void
     */
    public function __construct(UserService $userService, RoleService $roleService)
    {
        parent::__construct();

        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param   Request $request
     * @return  mixed
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $users = $this->userService->listUser($params, ['roles']);

        if ($users->currentPage() > $users->lastPage()) {
            $params['page'] = $users->lastPage();
            return redirect(route('admin.user.index', $params));
        }

        $status = $this->userService->statusTypes();

        return view('admin.modules.user.index', compact(
            'users',
            'status'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  View
     */
    public function create(): View
    {
        $roles = $this->roleService->list([]);

        $status = $this->userService->statusTypes();

        return view('admin.modules.user.create', compact(
            'roles',
            'status'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param   CreateRequest $request
     * @return  RedirectResponse
     */
    public function store(CreateRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // create user
            $params = $request->all();
            $params['password'] = bcrypt($params['password']);
            $user = $this->userService->create($params);
            $user->roles()->sync($request->role);

            // Commit and return
            DB::commit();
            return redirect()->route('admin.user.' . $request->redirect, $user->id)->with('message', __('messages.create_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', __('messages.create_fail'))->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param   User $user
     * @return  void
     */
    public function show(User $user): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param   User $user
     * @return  View
     */
    public function edit(User $user): View
    {
        return view('admin.modules.user.edit_profile');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param   UpdateRequest $request
     * @param   User          $user
     * @return  RedirectResponse
     */
    public function update(UpdateRequest $request, User $user): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Update
            $user = $this->userService->updateUser($request->all(), $user);
            $user->roles()->sync($request->role);

            // Commit and return
            DB::commit();
            return redirect()->route('admin.user.' . $request->redirect, $user->id)->with('message', __('messages.update_success'));
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

            $user = $this->userService->find($id);

            if ($user == null) {
                return redirect()->back();
            }

            // Destroy User
            $this->userService->destroy($user);

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

    /**
     * Show the form for update profile the specified resource.
     *
     * @return  View
     */
    public function profile(): View
    {
        $user = \Auth::user();
        return view('admin.modules.user.profile', compact('user'));
    }

    /**
     * Update profile the specified resource in storage.
     *
     * @param   ProfileRequest $request
     * @return  RedirectResponse
     */
    public function updateProfile(ProfileRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Update
            $user = \Auth::user();
            $this->userService->updateUser($request->all(), $user);

            // Commit and return
            DB::commit();
            return redirect()->back()->with('message', __('messages.update_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', __('messages.update_fail'))->withInput();
        }
    }

    /**
     * Show the form for change password the specified resource.
     *
     * @return  View
     */
    public function changePassword(): View
    {
        return view('admin.modules.user.change-password');
    }

    /**
     * Update password the specified resource in storage.
     *
     * @param   ChangePassword $request
     * @return  RedirectResponse
     */
    public function updatePassword(ChangePassword $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Check the old password is correct or not
            if (\Hash::check($request->old_password, \Auth::guard('admin')->user()->password)) {
                // Update
                $user = \Auth::user();
                $this->userService->updateUser([ 'password' => $request->new_password ], $user);

                // Commit and return
                DB::commit();
                return redirect()->back()->with('message', __('messages.update_success'));
            }

            return redirect()->back()->with('error', __('messages.wrong_password'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', __('messages.update_fail'))->withInput();
        }
    }
}
