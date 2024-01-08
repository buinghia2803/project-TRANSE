<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\ProfileCreateRequest;
use App\Http\Requests\User\SignUpRequest;
use App\Http\Requests\User\VerifyCodeRequest;
use App\Jobs\SendGeneralMailJob;
use App\Models\Authentication;
use App\Models\MProduct;
use App\Models\RegisterTrademarkProd;
use App\Models\Trademark;
use App\Models\User;
use App\Services\AuthenticationService;
use App\Services\Common\TrademarkTableService;
use App\Services\MProductService;
use App\Services\RegisterTrademarkService;
use App\Services\TrademarkService;
use App\Services\UserService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class RegistrationController extends Controller
{
    /**
     * @var UserService $userService
     */
    private UserService $userService;

    /**
     * @var AuthenticationService $authenticationService
     */
    private AuthenticationService $authenticationService;

    /**
     * @var MNationService $mNationService
     */
    private MNationService $mNationService;

    /**
     * @var MPrefectureService $mPrefectureService
     */
    private MPrefectureService $mPrefectureService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        UserService $userService,
        AuthenticationService $authenticationService,
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        TrademarkService $trademarkService,
        RegisterTrademarkService $registerTrademarkService,
        TrademarkTableService $trademarkTableService,
        MProductService $mProductService
    )
    {
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
        $this->trademarkService = $trademarkService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->trademarkTableService = $trademarkTableService;
        $this->mProductService = $mProductService;
    }

    /**
     * Show sign up form
     *
     * @return View
     */
    public function showSignUpForm(): View
    {
        return view('user.auth.sign-up');
    }

    /**
     * @param   SignUpRequest $request
     * @return  RedirectResponse
     */
    public function register(SignUpRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $nameTrademark = $request->name_trademark ?? null;
            $isImageTrademark = $request->is_image_trademark ?? User::NO_IMAGE_TRADEMARK;
            $email = $request->email ?? null;

            // Find user with email
            $user = $this->userService->findByCondition([
                'email' => $email,
            ])->first();

            // Check exist user and user ENABLED
            if (!empty($user) && $user->status == User::ENABLED && $user->status_withdraw == User::STATUS_WITHDRAW_INACTIVE) {
                $validator = Validator::make([], []);
//                $validator->getMessageBag()->add('email', __('messages.signup.form.exist_email'));

                return redirect()->back()->with('error', __('messages.signup.form.exist_email'))->withInput($request->all());
            }

            // Create user if not exist
            if (empty($user)) {
                $userNumber = $this->userService->generateUserNumber();

                $user = $this->userService->create([
                    'user_number' => $userNumber,
                    'name_trademark' => $nameTrademark,
                    'is_image_trademark' => $isImageTrademark,
                    'email' => $email,
                    'status' => User::TENTATIVE
                ]);
            } else {
                $user->update([
                    'name_trademark' => $nameTrademark,
                    'is_image_trademark' => $isImageTrademark,
                    'email' => $email,
                    'status' => User::TENTATIVE,
                    'status_withdraw' => User::STATUS_WITHDRAW_INACTIVE,
                ]);
            }

            // Code and token generate
            $randomTwoNumber = substr(str_shuffle("0123456789"), 0, 2);
            $randomTwoString = substr(str_shuffle("abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 2);
            $random8NumberOrString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);

            $code = $random8NumberOrString . $randomTwoString . $randomTwoNumber;
            $token = Str::random(60);
            $urlVerify = route('auth.verify-code', [
                'token' => $token,
            ]);

            // Delete old and create new Authentication
            $this->authenticationService->findByCondition([
                'user_id' => $user->id,
                'type' => Authentication::REGISTER
            ])->delete();
            $this->authenticationService->create([
                'user_id' => $user->id,
                'type' => Authentication::REGISTER,
                'token' => $token,
                'code' => $code,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            // Send Mail
            SendGeneralMailJob::dispatch('emails.user-sign-up', [
                'to' => $email,
                'subject' => __('labels.signup.mail.subject'),
                'code' => $code,
                'url' => $urlVerify,
            ]);

            // Commit
            DB::commit();

            return redirect()->route('auth.signup-success');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show sign-up success page
     *
     * @return View
     */
    public function success(): view
    {
        return view('user.auth.sign-up-success');
    }

    /**
     * Check Expired Time for authentication
     *
     * @param $authentication
     * @return boolean
     */
    public function hasExpiredTime($authentication): bool
    {
        // Check time around 30 minute
        $time = strtotime(date('Y-m-d H:i:s'));
        $createdAt = strtotime($authentication->created_at);
        if ($time - $createdAt >= 60 * 30) {
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function showVerifyCodeForm(Request $request)
    {
        $token = $request->token ?? null;

        $authentication = $this->authenticationService->findByCondition([
            'token' => $token,
        ])->first();
        // Check exist $authentication
        if ($authentication == null) {
            return redirect()->route('auth.signup')->with('error', __('messages.signup.expired_authentication_v2'))->withInput();
        }

        // Validate Expired Time
        if ($this->hasExpiredTime($authentication)) {
            $authentication->delete();
            return redirect()->route('auth.signup')->with('error', __('messages.signup.expired_authentication_v2'))->withInput();
        }

        return view('user.auth.verify-code', compact(
            'authentication'
        ));
    }

    /**
     * @param   VerifyCodeRequest $request
     * @return  RedirectResponse
     */
    public function verifyCode(VerifyCodeRequest $request): RedirectResponse
    {
        $token = $request->token ?? null;
        $code = $request->code ?? null;

        $authentication = $this->authenticationService->findByCondition([
            'token' => $token,
        ])->first();

        // Check exist $authentication
        if ($authentication == null) {
            return redirect()->route('auth.signup');
        }

        // Validate Expired Time
        if ($this->hasExpiredTime($authentication)) {
            $authentication->delete();

            return redirect()->route('auth.signup')->with('error', __('messages.signup.expired_authentication'))->withInput();
        }

        // Compare code and authentication code
        if ($code != $authentication->code) {
            $validator = Validator::make([], []);
            $validator->getMessageBag()->add('code', __('messages.signup.form.invalid_code'));

            return redirect()->back()->withErrors($validator);
        }

        return redirect()->route('auth.form-update-profile', ['token' => $token]);
    }

    /**
     * Get FormUpdateProfile
     *
     * @param  Request $request
     * @return mixed
     */
    public function getFormUpdateProfile(Request $request)
    {
        $secretKey = $request->s ?? '';
        $params = (array) json_decode(Session::get($secretKey));

        $token = $request->token ?? null;

        $authentication = $this->authenticationService->findByCondition([
            'token' => $token,
        ])->first();

        if (!$authentication) {
            return redirect()->route('auth.signup');
        }

        // Validate Expired Time
        if ($this->hasExpiredTime($authentication)) {
            $authentication->delete();

            return redirect()->route('auth.signup')->with('error', __('messages.signup.expired_authentication'))->withInput();
        }

        //nations
        $nations = $this->mNationService->list(['id' => 'ASC']);

        //prefectures
        $prefectures = $this->mPrefectureService->list(['mNationId' => NATION_JAPAN_ID]);

        $email = $this->userService->findOrFail($authentication->user_id)->email;

        //data session old value birthday
        $dataBirthDayOld = Session::get(OLD_VALUE_BIRTHDAY_USER);

        return view('user.auth.update-profile', compact(
            'nations',
            'prefectures',
            'email',
            'params',
            'dataBirthDayOld'
        ));
    }

    /**
     * Update Profile
     *
     * @param  ProfileCreateRequest $request
     * @return RedirectResponse
     */
    public function updateProfile(ProfileCreateRequest $request): RedirectResponse
    {
        $authentication = $this->authenticationService->findByCondition([
            'token' => $request->token_authen,
        ])->first();

        if (!$authentication) {
            return redirect()->route('auth.signup');
        }

        // Validate Expired Time
        if ($this->hasExpiredTime($authentication)) {
            $authentication->delete();

            return redirect()->route('auth.signup')->with('error', __('messages.signup.expired_authentication'))->withInput();
        }

        $user = $authentication->user;
        if (!$user) {
            return redirect()->route('auth.signup');
        }
        $data = $request->all();

        $secretKey = Str::random(11);
        Session::put($secretKey, json_encode($data));

        if (!empty($data['info_birthday'])) {
            //old value birthday
            Session::put(OLD_VALUE_BIRTHDAY_USER, [
                'year' => $data['year'],
                'month' => $data['month'],
                'day' => $data['day'],
            ]);
        } else {
            Session::remove(OLD_VALUE_BIRTHDAY_USER);
        }

        return redirect()->route('auth.view-confirm', ['token' => $request->token_authen, 's' => $secretKey]);
    }


    /**
     * View Confirm
     *
     * @param  Request $request
     *
     * @return mixed
     */
    public function viewConfirm(Request $request)
    {
        $token = $request->token;
        $secretKey = $request->s ?? null;

        if (!$secretKey) {
            return redirect()->back();
        }
        $params = (array) json_decode(Session::get($secretKey));

        $authentication = $this->authenticationService->findByCondition([
            'token' => $params['token_authen'] ?? '',
        ])->first();

        if (!$authentication) {
            return redirect()->route('auth.signup');
        }

        // Validate Expired Time
        if ($this->hasExpiredTime($authentication)) {
            $authentication->delete();

            return redirect()->route('auth.signup')->with('error', __('messages.signup.expired_authentication'))->withInput();
        }

        $user = $authentication->user;

        if (!$user) {
            return redirect()->route('auth.signup');
        }

        //nations
        $nations = $this->mNationService->list([]);

        //prefectures
        $prefectures = $this->mPrefectureService->list([]);

        $email = $user->email;
        //const type
        $typeAccGroup = User::INFO_TYPE_ACC_GROUP;
        $typeAccIndividual = User::INFO_TYPE_ACC_INDIVIDUAL;


        return view('user.auth.update-profile-confirm', compact(
            'params',
            'nations',
            'prefectures',
            'email',
            'typeAccGroup',
            'typeAccIndividual'
        ));
    }

    /**
     * Update Profile Confirm
     *
     * @param  ProfileCreateRequest $request
     * @return RedirectResponse
     */
    public function updateProfileConfirm(ProfileCreateRequest $request): RedirectResponse
    {
        $params = $request->all();

        $authentication = $this->authenticationService->findByCondition([
            'token' => $request->token_authen,
        ])->first();

        if (!$authentication) {
            return redirect()->route('auth.signup');
        }

        // Validate Expired Time
        if ($this->hasExpiredTime($authentication)) {
            $authentication->delete();

            return redirect()->route('auth.signup')->with('error', __('messages.signup.expired_authentication'))->withInput();
        }

        $user = $authentication->user;
        if (!$user) {
            return redirect()->route('auth.signup');
        }

        $params['password'] = bcrypt($request->password);
        $params['status'] = User::ENABLED;

        $secretKey = $params['s'] ?? null;

        if (!$secretKey) {
            return redirect()->back();
        }

        $updated = $this->userService->update($user, $params);
        if ($updated) {
            Session::forget($secretKey);
        }
        $authentication->delete();

        // Send Mail
        SendGeneralMailJob::dispatch('emails.user-sign-up-success', [
            'to' => $user->getListMail(),
            'subject' => __('labels.signup.mail.subject_success'),
        ]);

        return redirect()->route('auth.register-finish');
    }

    /**
     * Register Finish
     *
     * @return View
     */
    public function registerFinish()
    {
        return view('user.auth.register-finish');
    }

    /**
     * Check Member Id
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function checkMemberId(Request $request): JsonResponse
    {
        $authentication = $this->authenticationService->findByCondition([
            'token' => $request->tokenAuthen,
        ])->first();

        if (!$authentication) {
            return response()->json([
                'status' => false,
                'message' => __('messages.update_profile.system_error'),
            ]);
        }

        $status = $this->userService->pluckMemberId($request->valMemberId);

        return response()->json(['response' => $status], 200);
    }
}
