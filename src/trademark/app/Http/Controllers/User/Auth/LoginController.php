<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TrademarkService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Services\AppTrademarkService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Http\Requests\Auth\LoginUserRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
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
    protected $redirectTo = '/top';
    protected TrademarkService $trademarkService;
    protected AppTrademarkService $appTrademarkService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        TrademarkService $trademarkService,
        AppTrademarkService $appTrademarkService
    )
    {
        $this->trademarkService = $trademarkService;
        $this->appTrademarkService = $appTrademarkService;
    }

    /**
     * Create a new controller instance - show user login form.
     *
     * @return mixed
     */
    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.top');
        }
        return view('user.auth.login');
    }

    /**
     * Login end user
     *
     * @param LoginUserRequest $request
     * @return RedirectResponse
     */
    public function login(Request $request)
    {
        $fail = $this->validateLogin($request);

        if (!$fail && $this->attemptLogin($request)) {
            $user = Auth::guard('web')->user();
            if ($user && $user->status_withdraw == User::STATUS_WITHDRAW_ACTIVE) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->back()->with('error', __('messages.login_user.ID_password_invalid'));
            }
            if ($user->status == User::TENTATIVE || $user->status_withdraw == User::STATUS_WITHDRAW_ACTIVE) {
                $this->logout($request);
                return redirect()->back()->with('error', __('messages.login_denied'));
            }
            return $this->sendLoginResponse($request);
        } else {
            return redirect()->back()->withInput()->with('error', __('messages.login_user.ID_password_invalid'));
        }
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
            'info_member_id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $count = User::whereRaw("`info_member_id` = CONVERT(? USING binary)", $value)->count();
                    if (!$count) {
                        $fail("User number or password incorrect.");
                    }
                },
            ],
            'password' => 'required|string',
        ]);

        return $validator->fails();
    }

    /**
     * Logout end user
     *
     * @param Request $request
     * @return Redirect
     */
    public function loggedOut(Request $request)
    {
        return redirect()->route('auth.login');
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
        return redirect()->route('user.top');
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        $this->redirectTo = route('user.top');

        return property_exists($this, 'redirectTo') ? $this->redirectTo : route('user.comming-soon');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('info_member_id', 'password');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return  StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('web');
    }
}
