<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\BaseController;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Jobs\SendMailTemplateJob;
use App\Models\MailTemplate;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ForgotPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

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
     * @return  View
     */
    public function forgotPassword(): View
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Set forgot password.
     *
     * @param   ForgotPasswordRequest $request
     * @return  RedirectResponse
     */
    public function setForgotPassword(ForgotPasswordRequest $request): RedirectResponse
    {
        try {
            $email = $request->email;

            $user = User::where('email', $email)->first();
            if ($user == null) {
                return redirect()->route('admin.forgot-password')->with('error', __('messages.email_not_exist'))->withInput();
            }

            $resetPassword = DB::table('password_resets')->where('email', $email)->first();

            // Generate random string
            if ($resetPassword == null) {
                $token = Str::random(60);
            } else {
                $token = $resetPassword->token;
            }

            // Link change password
            $link = route('admin.reset-password', ['token' => $token]);

            // Send mail
            SendMailTemplateJob::dispatch(MailTemplate::MAIL_TEMPLATES_PASSWORD_RESET, $email, [
                'link_url' => $link,
            ]);

            // Add record to password_resets table
            if ($resetPassword == null) {
                DB::table('password_resets')->insert([
                    'email' => $email,
                    'token' => $token,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            return redirect()->back()->with('message', __('messages.forgot_password_success'));
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('message', __('messages.error'));
        }
    }
}
