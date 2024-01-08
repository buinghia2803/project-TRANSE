<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CheckUserInAnswerRequest;
use App\Http\Requests\User\ForgotPassword\SendMailForgotPasswordRequest;
use App\Http\Requests\User\ForgotPassword\SetNewPassNoEmailRequest;
use App\Http\Requests\User\ForgotPassword\VerifyUserNoEmailRequest;
use App\Jobs\SendGeneralMailJob;
use App\Models\Authentications;
use App\Models\User;
use App\Services\AuthenticationService;
use App\Services\ForgotPasswordService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Constructor
     *
     * @param   ForgotPasswordService $forgotPasswordService
     * @return  void
     */
    public function __construct(
        ForgotPasswordService $forgotPasswordService,
        UserService $userService,
        AuthenticationService $authenticationService
    )
    {
        $this->forgotPasswordService = $forgotPasswordService;
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.top');
        }
        return view('user.modules.auth.forgot-password.U-000pass01');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showKakunin()
    {
        return view('user.modules.auth.forgot-password.U-000pass01_kakunin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPass02($token)
    {
        $authentication = $this->authenticationService->findByCondition([
            'token' => $token,
        ])->first();

        // Check exist $authentication
        if ($authentication == null) {
            return redirect()->route('auth.forgot-password.index')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
        }

        // Validate Expired Time
        if ($authentication->hasExpiredTime()) {
            $authentication->delete();

            return redirect()->route('auth.forgot-password.index')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
        }

        return view('user.modules.auth.forgot-password.u000pass02', compact(
            'token'
        ));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPass02Kakunin()
    {
        return view('user.modules.auth.forgot-password.u000pass02_kakunin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPass01NoEmail()
    {
        return view('user.modules.auth.forgot-password.u000pass01no_email');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPass02NoEmail()
    {
        $sessionResetPasswordRegistrationEmail = SESSION_RESET_PASSWORD_REGISTRATION_EMAIL;
        $id = json_decode(Session::get($sessionResetPasswordRegistrationEmail));
        if (!$id) {
            abort(404);
        }

        $user = User::where('id', $id)->first();
        if (!$user) {
            return redirect()->back()->with('error', __('messages.email_not_exist'))->withInput();
        }
        Session::forget($sessionResetPasswordRegistrationEmail);

        return view('user.modules.auth.forgot-password.u000pass02no_email', compact('user'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPass03NoEmail()
    {
        $sessionResetPasswordRegistrationEmail = SESSION_RESET_PASSWORD_REGISTRATION_EMAIL;
        $id = json_decode(Session::get($sessionResetPasswordRegistrationEmail));
        if (!$id) {
            abort(404);
        }

        $user = User::where('id', $id)->first();
        if (!$user) {
            return redirect()->back()->with('error', __('messages.email_not_exist'))->withInput();
        }
        Session::forget($sessionResetPasswordRegistrationEmail);

        return view('user.modules.auth.forgot-password.u000pass03no_email', compact('user'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPass03NoEmailKakunin()
    {
        $sessionResetPasswordRegistrationEmail = SESSION_RESET_PASSWORD_REGISTRATION_EMAIL;
        $id = json_decode(Session::get($sessionResetPasswordRegistrationEmail));
        Session::forget($sessionResetPasswordRegistrationEmail);

        return view('user.modules.auth.forgot-password.u000pass03no_email_kakunin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPass04NoEmail($token)
    {
        $authentication = $this->authenticationService->findByCondition([
            'token' => $token,
        ])->first();

        // Check exist $authentication
        if ($authentication == null) {
            return redirect()->route('auth.forgot-password.no-email')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
        }

        // Validate Expired Time
        if ($authentication->hasExpiredTime()) {
            $authentication->delete();

            return redirect()->route('auth.forgot-password.no-email')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
        }

        return view('user.modules.auth.forgot-password.u000pass04no_email', compact('authentication'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPass05NoEmail($token)
    {
        $authentication = $this->authenticationService->findByCondition([
            'token' => $token,
        ])->first();

        // Check exist $authentication
        if ($authentication == null) {
            return redirect()->route('auth.forgot-password.no-email')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
        }

        // Validate Expired Time
        if ($authentication->hasExpiredTime()) {
            $authentication->delete();

            return redirect()->route('auth.forgot-password.no-email')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
        }

        return view('user.modules.auth.forgot-password.u000pass05no_email', compact('authentication'));
    }

    /**
     * Send Mail Forgot Password
     *
     * @param  mixed $request
     * @return void
     */
    public function sendMailForgotPassword(SendMailForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)
            ->where('info_member_id', $request->info_member_id)
            ->where('status', User::ENABLED)
            ->where('status_withdraw', User::STATUS_WITHDRAW_INACTIVE)
            ->first();
        $token = Str::random(60);
        $code = $this->getRandomCode();
        if ($user == null) {
            return redirect()->back()->with('error', __('messages.general.Forgot_Password_U000_E008'))->withInput();
        } else {
            DB::table('authentications')->where('user_id', $user->id)->delete();
            DB::table('authentications')->insert([
                'user_id' => $user->id,
                'type' => Authentications::FORGOT_PASSWORD,
                'token' => $token,
                'code' => $code,
                'value' => $user->email,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        // Send mail
        $link = route('auth.forgot-password.reset', ['token' => $token]);

        SendGeneralMailJob::dispatch('emails.forgot-password-mail_u000pass01', [
            'to' => $user->getListMail(),
            'subject' => __('labels.recover_id.title_send_email_forgot_password'),
            'link' => $link,
            'code' => $code,
        ]);

        if (Mail::failures()) {
            return redirect()->back()->with([
                'error' => "System Error",
            ]);
        }

        return redirect()->route('auth.forgot-password.confirm')->with('message', __('messages.general.Send_Mail_Success'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $authentication = $this->authenticationService->findByCondition([
                'token' => $request->token_authen,
            ])->first();

            // Check exist $authentication
            if ($authentication == null) {
                return redirect()->route('auth.forgot-password.index')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
            }

            // Validate Expired Time
            if ($authentication->hasExpiredTime()) {
                $authentication->delete();

                return redirect()->route('auth.forgot-password.index')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
            }

            $user = $this->forgotPasswordService->resetPassword($request->all());
            if (!$user) {
                return redirect()->back()->with([
                    'error' => __('messages.general.Forgot_Password_U000_E008'),
                ]);
            }

            $authentication->delete();

            DB::commit();
            return redirect()->route('auth.forgot-password.reset.confirm');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', __('messages.create_fail'))->withInput();
        }
    }

    /**
     * Check User In Active
     *
     * @param  mixed $request
     * @return void
     */
    public function checkUserInActive(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where('info_member_id', $request->info_member_id)
            ->where('status', User::ENABLED)
            ->where('status_withdraw', User::STATUS_WITHDRAW_INACTIVE)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', __('messages.forgot_password.Forgot_Password_U000_E008'))->withInput();
        }

        $id = $user->id ?? '';

        $sessionResetPasswordRegistrationEmail = SESSION_RESET_PASSWORD_REGISTRATION_EMAIL;
        Session::put($sessionResetPasswordRegistrationEmail, json_encode($id));


        return redirect()->route('auth.forgot-password.no-email.secret-answer');
    }

    /**
     * Check User Info Answer
     *
     * @param  mixed $request
     * @return void
     */
    public function checkUserInfoAnswer(CheckUserInAnswerRequest $request)
    {
        $sessionResetPasswordRegistrationEmail = SESSION_RESET_PASSWORD_REGISTRATION_EMAIL;
        Session::put($sessionResetPasswordRegistrationEmail, json_encode($request->user_id));

        $user = User::where('id', $request->user_id)
            ->where('info_answer', $request->info_answer)
            ->where('info_question', $request->info_question)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', __('messages.forgot_password.Forgot_Password_U000_E009'))->withInput();
        }

        return redirect()->route('auth.forgot-password.no-email.other-email');
    }

    /**
     * Set New Email
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function setNewEmail(Request $request)
    {
        $id = $request->user_id;
        DB::beginTransaction();
        try {
            $checkEmailUser = User::where('email', $request->value)->first();
            if ($checkEmailUser) {
                $sessionResetPasswordRegistrationEmail = SESSION_RESET_PASSWORD_REGISTRATION_EMAIL;
                Session::put($sessionResetPasswordRegistrationEmail, json_encode($id));

                return redirect()->back()->with('error', __('messages.forgot_password.email_already_exists'))->withInput();
            }
            $token = Str::random(60);
            $code = $this->getRandomCode();
            DB::table('authentications')->where('user_id', $id)->delete();
            DB::table('authentications')->insert([
                'user_id' => $id,
                'type' => Authentications::FORGOT_PASSWORD_NO_EMAIL,
                'token' => $token,
                'code' => $code,
                'value' => $request->value,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $getAuthentications = Authentications::where('user_id', $id)->where('type', Authentications::FORGOT_PASSWORD_NO_EMAIL)->first();


            $condition = ['email' => $getAuthentications->value];

            $expiresAt = Carbon::now()->addMinutes(30);
            Cache::add('key', $getAuthentications->token, $expiresAt);
            $cacheToken = Cache::pull('key');
            $link = route('auth.forgot-password.no-email.verification', ['token' => $cacheToken]);

            SendGeneralMailJob::dispatch('emails.forgot-password-mail-u000pass01no_email', [
                'to' => $getAuthentications->value,
                'subject' => __('labels.recover_id.title_send_email_password_and_registered_email_address_reset'),
                'link' => $link,
                'code' => $getAuthentications->code,
            ]);

            if (Mail::failures()) {
                return redirect()->back()->withErrors([
                    'message' => "System Error",
                ]);
            }

            DB::commit();
            return redirect()->route('auth.forgot-password.no-email.other-email.confirm')
                ->with('message', __('messages.forgot_password.email_already_exists'))->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->with('error', __('messages.error'));
        }
    }

    /**
     * Verify User No Email
     *
     * @param  mixed $request
     * @return void
     */
    public function verifyUserNoEmail(VerifyUserNoEmailRequest $request, $token)
    {
        $authentication = $this->authenticationService->findByCondition([
            'token' => $token,
        ])->first();

        // Check exist $authentication
        if ($authentication == null) {
            return redirect()->route('auth.forgot-password.no-email')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
        }

        // Validate Expired Time
        if ($authentication->hasExpiredTime()) {
            $authentication->delete();

            return redirect()->route('auth.forgot-password.no-email')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
        }

        $user = User::whereHas('authentications', function ($query) use ($request) {
            return $query->where('code', $request->code);
        })
            ->where('status', User::ENABLED)
            ->where('status_withdraw', User::STATUS_WITHDRAW_INACTIVE)
            ->where('email', $request->email)
            ->where('info_member_id', $request->info_member_id)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', __('messages.general.Forgot_Password_U000_E008'))->withInput();
        }

        $authentication = Authentications::where('user_id', $user->id)->where('type', Authentications::FORGOT_PASSWORD_NO_EMAIL)->first();

        return redirect()->route('auth.forgot-password.no-email.reset', ['token' => $authentication->token])->withInput();
    }

    /**
     * Set New Pass No Email
     *
     * @param  mixed $request
     * @param  mixed $token
     * @return void
     */
    public function setNewPassNoEmail(SetNewPassNoEmailRequest $request, $token)
    {
        try {
            $password = $request['password'];

            $authentication = $this->authenticationService->findByCondition([
                'token' => $token,
                'type' => Authentications::FORGOT_PASSWORD_NO_EMAIL
            ])->first();

            // Check exist $authentication
            if ($authentication == null) {
                return redirect()->route('auth.forgot-password.no-email')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
            }

            // Validate Expired Time
            if ($authentication->hasExpiredTime()) {
                $authentication->delete();

                return redirect()->route('auth.forgot-password.no-email')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
            }

            $user = User::where('id', $authentication->user_id)->first();

            if (empty($user)) {
                return redirect()->back()->with('error', 'Not Found User')->withInput();
            }

            // Update password and delete password_resets
            $updated = $user->update([
                'email' => $authentication->value,
                'password' => bcrypt($password),
            ]);

            SendGeneralMailJob::dispatch('emails.pass-word-reset-no-email-mail', [
                'to' => $user->getListMail(),
                'subject' => __('labels.recover_id.title_send_email_comprehensive_password_reset'),
            ]);

            if ($updated) {
                $authentication->delete();
            }

            return redirect()->route('auth.login')->with('message', __('messages.general.Forgot_Password_U000_S001'))->withInput();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('message', __('messages.error'));
        }
    }

    /**
     * Generate Code
     *
     * @param  mixed $request
     * @return string
     */
    public function getRandomCode(): string
    {
        $randomInt = random_int(1000, 9999);
        $randomStr = Str::random(8);
        $code = $randomInt . $randomStr;

        return $code;
    }
}
