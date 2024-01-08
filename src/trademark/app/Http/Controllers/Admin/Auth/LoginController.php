<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'sysmanagement/top';

    /**
     * @return  void
     */
    public function __construct()
    {
        if (env('APP_ENV') == 'production') {
            $this->redirectTo = env('APP_DIR') . '/' . $this->redirectTo;
        }
        parent::__construct();
        $this->middleware('guest:admin')->except('logout');
    }

    /**
     * Create a new controller instance.
     *
     * @return  View
     */
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param   Request $request
     * @return  Response
     */
    public function login(Request $request)
    {
        $fail = $this->validateLogin($request);

        if (!$fail && $this->attemptLogin($request)) {
            $user = Auth::guard('admin')->user();

            return $this->sendLoginResponse($request);
        }

        return redirect()->back()->with('error', __('messages.general.Login_U001_E001'));
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('admin.home');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  Request $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        $lang = App::getLocale();
        return redirect()->route('admin.login', [
            'setLanguage' => $lang,
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('admin_number', 'password');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return  StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $count = Admin::whereRaw("`admin_number` = CONVERT(? USING binary)", $value)->count();
                    if (!$count) {
                        $fail("Admin number or password incorrect.");
                    }
                },
            ],
            'password' => 'required|string',
        ]);

        return $validator->fails();
    }
}
