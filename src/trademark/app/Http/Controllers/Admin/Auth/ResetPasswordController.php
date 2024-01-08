<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\BaseController;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = 'admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
    }

    /**
     * Show forgot password form.
     *
     * @param   Request $request
     * @return  mixed
     */
    public function resetPassword(Request $request)
    {
        $token = $request->token;

        // Check token exist
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (empty($passwordReset)) {
            return redirect(route('admin.login'));
        }

        // Check time around some day
        $day = 1;
        $time = strtotime(date('Y-m-d H:i:s'));
        $resetTime = strtotime($passwordReset->created_at);
        if ($time - $resetTime >= $day * 60 * 60 * 24) {
            PasswordReset::where('token', $token)->delete();
            return redirect(route('admin.login'));
        }

        return view('admin.auth.reset-password');
    }

    /**
     * Reset Password.
     *
     * @param   ResetPasswordRequest $request
     * @return  RedirectResponse
     */
    public function setPassword(ResetPasswordRequest $request): RedirectResponse
    {
        try {
            $token = $request->token;
            $password = $request->password;

            // Check token exist
            $passwordReset = PasswordReset::where('token', $token)->first();
            if (empty($passwordReset)) {
                return redirect(route('admin.login'));
            }

            $user = User::where('email', $passwordReset->email)->first();

            if (empty($user)) {
                return redirect(route('admin.login'));
            }

            // Update password and delete password_resets
            $user->update([
                'password' => bcrypt($password),
            ]);
            PasswordReset::where('email', $user->email)->delete();

            return redirect(route('admin.login'))->with('message', __('messages.reset_password_success'));
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('message', __('messages.error'));
        }
    }
}
