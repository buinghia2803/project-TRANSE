<?php

namespace App\Http\Controllers\User\Auth;

use App\Jobs\SendGeneralMailJob;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RecoverId\RecoverIdNoEmailRequest;
use Illuminate\Support\Facades\Mail;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RecoverIdController extends Controller
{
    protected UserService $userService;
    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Recover id by question answer
     *
     * @param Request $request
     * @return mixed
     */
    public function recoverIdByAnswer(Request $request)
    {
        $user = $this->userService->findByCondition([
            'id' => $request->user_id,
            'status' => User::ENABLED,
            'status_withdraw' => User::STATUS_WITHDRAW_INACTIVE,
        ])->firstOrFail();

        $validator = Validator::make(
            $request->all(),
            [
                'answer' => 'required|max:50|regex:/^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/',
            ],
            [
                'answer.required' => __('messages.common.errors.Common_E001'),
                'answer.max' => __('messages.common.errors.Register_U001_E007'),
                'answer.regex' => __('messages.common.errors.Register_U001_E007'),
            ]
        );
        if ($validator->fails()) {
            return view('user.modules.recover_id.secret_question', compact('user'))->withErrors($validator);
        }

        if ($request->has('answer') && $request->answer && $request->answer == $user->info_answer) {
            return view('user.modules.recover_id.show', compact('user'));
        }

        $error = __('messages.common.errors.Forgot_ID_U000_E002');

        return view('user.modules.recover_id.secret_question', compact('user', 'error'))->with('error', $error);
    }

    /**
     * Showing recover id with no Email.
     */
    public function showRecoverIdNoEmail()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.top');
        }

        return view('user.modules.recover_id.no_email');
    }

    /**
     * Check user password to recover id
     *
     * @param RecoverIdNoEmailRequest $request
     * @return mixed
     */
    public function recoverIdNoEmail(RecoverIdNoEmailRequest $request)
    {
        $user = $this->userService->findByCondition([
            'email' => $request->email,
            'status' => User::ENABLED,
            'status_withdraw' => User::STATUS_WITHDRAW_INACTIVE,
        ])->first();

        if (!$user) {
            return redirect()->back()->with([
                'error' => __('messages.common.errors.Common_E003'),
            ]);
        }

        if (Auth::validate(['email' => $request->email, 'password' => $request->password])) {
            return view('user.modules.recover_id.secret_question', compact('user'));
        }

        return redirect()->back()->with([
            'error' => __('messages.common.errors.Common_E003'),
        ]);
    }

    /**
     * Showing recover id screen.
     *
     * @return mixed
     */
    public function showRecoverId()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.top');
        }

        return view('user.modules.recover_id.index');
    }

    /**
     * Action send mail for user
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function recoverId(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email|max:255',
                ],
                [
                    'required' => __('messages.common.errors.Common_E001'),
                    'email' => __('messages.common.errors.Common_E002'),
                ]
            );
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $user = $this->userService->findByCondition(['email' => $request->email])->withTrashed()->first();

            if (empty($user)) {
                return redirect()->back()->with([
                    'error' => __('messages.common.errors.Common_E003'),
                ]);
            }

            SendGeneralMailJob::dispatch('emails.recover-id-mail', [
                'to' => $user->getListMail(),
                'subject' => __('labels.recover_id.title_send_mail'),
                'member_id' => $user->info_member_id,
            ]);

            if (Mail::failures()) {
                return redirect()->back()->with([
                    'error' => "System Error",
                ]);
            }

            return redirect()->route('auth.show-recover-id')->with([
                'message' => __('messages.general.Recover_ID_U000_S001'),
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return redirect()->back()->with([
                'error' => "System Error",
            ]);
        }
    }
}
