<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Reset language if on url there is request setLanguage
            CommonHelper::setLanguage($request->setLanguage);

            // Check Auth to get Permission
            if (Auth::guard('admin')->check()) {
                $adminUser = \Auth::guard(ADMIN_ROLE)->user();
                $permissions = $adminUser->getPermissionsViaRoles();
                $permissions = $permissions->pluck('name')->toArray();
                View::share('adminUser', $adminUser);
                View::share('authPermissions', $permissions);
            }

            return $next($request);
        });
    }
}
