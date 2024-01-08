<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\UserService;
use App\Services\TrademarkService;
use App\Services\AuthenticationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\EditEmailRequest;
use App\Http\Requests\User\Profile\EditEmailVerifyCodeRequest;
use App\Http\Requests\User\Profile\ProfileUpdateRequest;
use App\Models\Authentication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Http\Requests\User\Withdraw\ConfirmVerifyRequest;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected MNationService $mNationService;
    protected UserService $userService;
    protected MPrefectureService $mPrefectureService;
    protected TrademarkService $trademarkService;
    protected AuthenticationService $authenticationService;

    public function __construct(
        MNationService $mNationService,
        UserService $userService,
        MPrefectureService $mPrefectureService,
        AuthenticationService $authenticationService,
        TrademarkService $trademarkService
    )
    {
        $this->mNationService = $mNationService;
        $this->userService = $userService;
        $this->mPrefectureService = $mPrefectureService;
        $this->authenticationService = $authenticationService;
        $this->trademarkService = $trademarkService;
    }

    /**
     * Check exists member id
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array                    $data
     * @return \Illuminate\Http\Response
     */
    public function checkExistsMemberId(Request $request)
    {
        $inputs = $request->all();
        if ($request->ajax()) {
            $res = $this->userService->checkExistsMemberId($inputs['info_member_id']);
            return response()->json(['res' => $res], 200);
        }
    }

    /**
     * Show form edit profile user
     *
     * @return View
     */
    public function getProfileEdit(Request $request): View
    {
        $secretKey = $request->s ?? '';
        $params = (array) json_decode(Session::get($secretKey));

        $user = auth('web')->user();
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $listContactTypeAcc = User::listContactTypeAcc();
        $data = [
            'user' => $user,
            'nations' => $nations,
            'prefectures' => $prefectures,
            'listContactTypeAcc' => $listContactTypeAcc,
            'params' => $params
        ];

        return view('user.modules.profiles.edit_profile', $data);
    }

    /**
     * Send info profile edit to confirm
     *
     * @param   ProfileUpdateRequest $request
     * @return  RedirectResponse
     */
    public function sendInfoProfile(ProfileUpdateRequest $request): RedirectResponse
    {
        $inputs = $request->all();
        $secretKey = Str::random(11);
        Session::put($secretKey, json_encode($inputs));

        return redirect()->route('user.profile.edit.confirm', ['s' => $secretKey]);
    }

    /**
     * Confirm info profile after edit
     */
    public function confirmUpdateProfile(Request $request)
    {
        $secretKey = $request->s ?? null;

        if (!$secretKey || !Session::get($secretKey)) {
            return redirect()->route('user.profile.edit');
        }

        $dataConfirm = (array) json_decode(Session::get($secretKey));
        $listContactTypeAcc = User::listContactTypeAcc();
        $nation = $this->mNationService->find($dataConfirm['info_nation_id']);
        $prefectureName = '';
        if (isset($dataConfirm['info_prefectures_id'])) {
            $prefecture = $this->mPrefectureService->find($dataConfirm['info_prefectures_id']);
            if ($prefecture) {
                $prefectureName = $prefecture->name;
            }
        }

        $contactNation = $this->mNationService->find($dataConfirm['contact_nation_id']);
        $contactPrefectureName = '';
        if (isset($dataConfirm['contact_prefectures_id'])) {
            $contactPrefecture = $this->mPrefectureService->find($dataConfirm['contact_prefectures_id']);
            if ($contactPrefecture) {
                $contactPrefectureName = $contactPrefecture->name;
            }
        }

        $listGenderOptions = User::listGenderOptions();
        $data = [
            'dataConfirm' => $dataConfirm,
            'listContactTypeAcc' => $listContactTypeAcc,
            'nationName' => $nation->name,
            'prefectureName' => $prefectureName,
            'contactNationName' => $contactNation->name,
            'contactPrefectureName' => $contactPrefectureName,
            'listGenderOptions' => $listGenderOptions
        ];

        return view('user.modules.profiles.edit_profile_confirm', $data);
    }

    /**
     * Update info profile of user
     *
     * @param   ProfileUpdateRequest $request
     * @return  mixed
     */
    public function updateProfile(ProfileUpdateRequest $request)
    {
        $inputs = $request->all();
        $updated = $this->userService->updateProfile($inputs);
        if ($updated) {
            Session::forget($inputs['s']);
        }

        return redirect()->route('user.profile.edit')->with([
            'message_confirm' => [
                'title' => __('labels.update_profile.submit_title_confirm'),
                'content' => __('labels.update_profile.submit_text_confirm'),
                'btn' => __('labels.update_profile.submit_btn_confirm'),
                'url' => route('user.top')
            ],
        ])->withInput();
    }

    /**
     * Show form edit email user
     *
     * @return View
     */
    public function editEmail()
    {
        return view('user.modules.profiles.edit_email01');
    }

    /**
     * Send email new to edit
     *
     * @param EditEmailRequest $request
     */
    public function editEmailSendInfo(EditEmailRequest $request)
    {
        $inputs = $request->all();
        $respon = $this->userService->sendEditMail($inputs['email']);
        if ($respon) {
            return redirect()->route('user.profile.change-email.confirm');
        }

        return redirect()->back();
    }

    /**
     * Screen notify send email edit success
     *
     * @return View
     */
    public function editEmailSendInfoSuccess(): View
    {
        return view('user.modules.profiles.edit_email02');
    }

    /**
     * Show form verify code change email
     *
     * @param Request $request
     * @return mixed
     */
    public function editEmailConfirm(Request $request)
    {
        $authentication = $this->authenticationService->findByCondition([
            'token' => $request->token ?? '',
        ])->first();

        // Check exist $authentication
        if ($authentication == null) {
            return redirect()->route('user.profile.change-email.index')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
        }

        // Validate Expired Time
        if ($authentication->hasExpiredTime()) {
            $authentication->delete();

            return redirect()->route('user.profile.change-email.index')->with('error', __('messages.general.Forgot_Password_U000_E007'))->withInput();
        }

        return view('user.modules.profiles.edit_email03');
    }

    /**
     * Verify code change email
     *
     * @param EditEmailVerifyCodeRequest $request
     * @return View
     */
    public function editEmailVerifyCode(EditEmailVerifyCodeRequest $request)
    {
        $inputs = $request->all();
        $statusVerify = $this->userService->editEmailVerifyCode($inputs);
        if ($statusVerify) {
            return redirect()->route('user.profile.change-email.finish.get');
        }
        return redirect()->back()->withInput()->with('error', __('messages.profile_edit.validate.Common_E013'));
    }

    /**
     * Verify code change email
     *
     * @param EditEmailVerifyCodeRequest $request
     * @return View
     */
    public function editEmailVerifyCodeFinish()
    {
        return view('user.modules.profiles.edit_email04');
    }

    /**
     * Get info user ajax
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array $data
     * @return \Illuminate\Http\Response
     */
    public function getInfoUserAjax()
    {
        $infoUser = null;
        if (auth()->check()) {
            $infoUser = $this->userService->getInfoUser();
        }
        return response()->json(['response' => $infoUser], 200);
    }

    /**
     * Showing withdraw confirm.
     *
     * @param Request request The request object.
     * @param int id user id
     */
    public function showWithdrawConfirm(Request $request)
    {
        $user = Auth::user();

        $trademarks = $this->userService->getTrademarksCompletedOfUser($user->id);

        foreach ($trademarks as $key => $trademark) {
            $noticeDetail = $trademark->notices->pluck('noticeDetails')->flatten()->sortBy(['id' => SORT_BY_DESC])->first();
            $trademark->noticeDetail = $noticeDetail;
            $trademark->notice_created_at = $noticeDetail->created_at;
        }

        $trademarks->sortByDesc('notice_created_at');

        return view('user.modules.withdraw.u000list_taikai_kakunin', compact('trademarks'));
    }

    /**
     * I want to get the data from the database and sort it by the field that the user chooses
     *
     * @param Request request Request
     * @param int id user id
     */
    public function getTrademarksOfUserWithdrawAjax(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$request->has('type') || !$request->has('sort_field') || !$request->type || $request->sort_field) {
                return response()->json(null, 400);
            }

            $trademarks = $this->userService->getTrademarksCompletedOfUser($user->id);

            foreach ($trademarks as $key => $trademark) {
                $noticeDetail = $trademark->notices->pluck('noticeDetails')->flatten()->sortBy(['id' => SORT_BY_DESC])->first();
                $trademark->noticeDetail = $noticeDetail;
                $trademark->notice_created_at = $noticeDetail->created_at;
            }

            $trademarks->sortBy([
                [$request->sort_field, $request->type],
            ]);

            $html = view('user.modules.withdraw.partials.taikai_kakunin_table', ['trademarks' => $trademarks])->toHtml();

            return response()->json(['html' => $html], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(null, 400);
        }
    }

    /**
     * The function showUserInfo() returns the view u000taikai01.blade.php with the user object.
     *
     * @return View view is being returned.
     */
    public function showUserInfo()
    {
        $user = Auth::user();

        return view('user.modules.withdraw.u000taikai01', compact('user'));
    }

    /**
     * Select all trademarks that are not being managed, have expired, or have been cancelled.
     *
     * @param int id id of user
     */
    public function confirmCapabilityWithdrawUser()
    {
        $user = Auth::user();

        $trademarks = $this->userService->getTrademarksOfUserWithdraw($user->id);

        // Can't withdraw
        if ($trademarks->count()) {
            return redirect()->route('user.withdraw.confirm-ng');
        } else {
            return redirect()->route('user.withdraw.confirm');
        }
    }


    /**
     * `showConfirm()` returns the string `'coming soon'`
     *
     * @return The string 'coming soon'
     */
    public function showConfirm()
    {
        if (url()->previous() == route('user.withdraw.index') || url()->previous() == route('user.withdraw.confirm')) {
            return view('user.modules.withdraw.u000taikai01kakunin');
        }

        return redirect()->route('auth.login');
    }

    /**
     * Send mail withdraw system.
     *
     * @return Redirect
     */
    public function sendMailConfirmWithdraw()
    {
        try {
            $this->userService->sendMailConfirmWithdraw();

            return redirect()->route('user.withdraw.confirm')->with('message', __('messages.general.withdrawal_U000_S001'));
        } catch (\Exception $e) {
            Log::error($e);

            return redirect()->route('user.withdraw.confirm')->with('message', __('messages.import_xml.system_error'));
        }
    }

    /**
     * Verification code.
     *
     * @param Request $request
     */
    public function verificationCode(Request $request)
    {
        if ($request->has('token') && $request->token) {
            $session = Session::get($request->token);
            if (empty($session)) {
                abort(CODE_ERROR_404);
            }
            $expiredTime = $session['time'];
            $authentication = $this->authenticationService->findByCondition([
                'user_id' => $session['user_id'],
                'type' => Authentication::WITHDRAW,
                'token' => $request->token
            ])->first();

            if (!$authentication) {
                abort(CODE_ERROR_404);
            }

            if (now()->timestamp < $expiredTime) {
                return view('user.modules.withdraw.u000taikai02');
            } else {
                abort(CODE_ERROR_404);
            }
        } else {
            abort(CODE_ERROR_404);
        }
    }

    /**
     * If the user has no trademarks, then return true
     *
     * @param ConfirmVerifyRequest request
     * @return Response
     */
    public function preConfirmWithdraw(ConfirmVerifyRequest $request)
    {
        $user = Auth::user();
        $authenticate = Authentication::where([
            'token' => $request->token,
            'type' => Authentication::WITHDRAW,
            'user_id' => $user->id,
        ])->first();
        if ($authenticate->code != $request->code) {
            return response()->json(['status' => false, 'message' => __('messages.general.Common_E013')]);
        }

        if ($user->info_member_id != $request->info_member_id) {
            return response()->json(['status' => false, 'message' => __('messages.general.Common_E006')]);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['status' => false, 'message' => __('messages.general.Common_E004')]);
        }

        $trademarks = $this->userService->getTrademarksOfUserWithdraw($user->id);

        // Can't withdraw
        if ($trademarks->count()) {
            return response()->json(['status' => false, 'message' => __('messages.general.Common_E059')]);
        }

        return response()->json(['status' => true]);
    }


    /**
     * I want to check if the user is logged in, if the user is logged in, I want to check if the
     * user's info_member_id is equal to the info_member_id in the request, if it is, I want to check
     * if the password in the request is equal to the password in the database, if it is, I want to
     * check if the token in the request is equal to the token in the database, if it is, I want to
     * check if the code in the request is equal to the code in the database, if it is, I want to check
     * if the time in the session is less than the current time, if it is, I want to update the user's
     * status_withdraw and reason_withdraw in the database
     *
     * @param ConfirmVerifyRequest request The request object.
     */
    public function confirmVerifyCode(ConfirmVerifyRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            if ($user->info_member_id != $request->info_member_id) {
                return redirect()->back()->with('error', __('messages.general.Common_E006'))->withInput();
            }

            if (!Hash::check($request->password, $user->password)) {
                return redirect()->back()->with('error', __('messages.general.Common_E004'))->withInput();
            }

            $authenticate = Authentication::where([
                'token' => $request->token,
                'type' => Authentication::WITHDRAW,
                'user_id' => $user->id,
            ])->first();

            if (!$authenticate) {
                abort(CODE_ERROR_404);
            }

            $session = Session::get($request->token);

            if (($authenticate->code) && (isset($session['time']) && $session['time'] < now()->timestamp)) {
                return redirect()->back()->with('error', __('messages.general.Common_E013'))->withInput();
            }

            $user->update([
                'status_withdraw' => User::STATUS_WITHDRAW_ACTIVE,
                'reason_withdraw' => $request->reason_withdraw,
            ]);

            DB::commit();

            Auth::logout();
            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect()->route('user.auth.login')->with('message', __('messages.withdraw_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return redirect()->back()->with('message', __('messages.import_xml.system_error'));
        }
    }

    /**
     * It returns the string 'coming soon'.
     *
     * @return A string.
     */
    public function showConfirmNG()
    {
        $user = Auth::user();

        return view('user.modules.withdraw.u000taikai01ng', compact('user'));
    }
}
