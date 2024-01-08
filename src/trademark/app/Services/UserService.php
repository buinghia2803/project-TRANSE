<?php

namespace App\Services;

use App\Jobs\SendGeneralMailJob;
use App\Models\AppTrademark;
use App\Models\Authentication;
use App\Models\Payment;
use App\Models\Trademark;
use App\Models\User;
use App\Services\BaseService;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Repositories\AuthenticationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UserService extends BaseService
{
  /**
   * Initializing the instances and variables
   *
   * @param     UserRepository           $userRepository
   * @param    AuthenticationRepository $authenticationRepository
   */
    public function __construct(UserRepository $userRepository, AuthenticationRepository $authenticationRepository)
    {
        $this->repository = $userRepository;
        $this->authenticationRepository = $authenticationRepository;
    }

    /**
     * ListUser
     *
     * @param   mixed $params
     * @return  LengthAwarePaginator
     */
    public function listUser($params): LengthAwarePaginator
    {
        return $this->listPaginate($params, ['roles']);
    }

    /**
     * Update User
     *
     * @param   mixed $params
     * @param   mixed $user
     *
     * @return  Model
     */
    public function updateUser($params, $user): Model
    {
        // display hidden password
        $user = $user->makeVisible('password');
        $params['password'] = isset($params['password']) ? bcrypt($params['password']) : $user->password;
        // update user
        return $this->update($user, $params);
    }

    /**
     * Update token.
     *
     * @param   User $user
     *
     * @return  void
     */
    public function updateToken(User $user)
    {
        $this->repository->updateToken($user);
    }

    /**
     * @param   User  $user
     * @param   array $roles
     * @return  void
     */
    public function syncRoles(User $user, array $roles)
    {
        $this->repository->syncRoles($user, $roles);
    }

    /**
     * Check exists member id
     *
     * @param string $memberId
     *
     * @return  boolean
     */
    public function checkExistsMemberId(string $memberId): bool
    {
        return $this->repository->checkExistsMemberId($memberId);
    }

    /**
     * Update Profile
     *
     * @param  array $inputs
     * @return  boolean
     */
    public function updateProfile(array $inputs): bool
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $data = $this->getDataProfileUpdate($inputs);
            $updated = $this->repository->update($user, $data);
            if ($updated) {
                DB::commit();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }


    /**
     * Get data profile update
     *
     * @param array $inputs
     * @return array
     */
    protected function getDataProfileUpdate(array $inputs): array
    {
        unset($inputs['_token']);
        if ($inputs['info_nation_id'] != NATION_JAPAN_ID) {
            $inputs['info_prefectures_id'] = null;
            $inputs['info_address_second'] = null;
            $inputs['info_address_three'] = null;
        }
        //if change password
        if (!empty($inputs['password'])) {
            $inputs['password'] = Hash::make($inputs['password']);
        } else {
            unset($inputs['password']);
        }

        if ($inputs['contact_type_acc'] == CONTACT_TYPE_ACC_INDIVIDUAL) {
            $inputs['contact_name_department'] = null;
            $inputs['contact_name_department_furigana'] = null;
            $inputs['contact_name_manager'] = null;
            $inputs['contact_name_manager_furigana'] = null;
        }

        if ($inputs['contact_nation_id'] != NATION_JAPAN_ID) {
            $inputs['contact_postal_code'] = null;
            $inputs['contact_prefectures_id'] = null;
            $inputs['contact_address_second'] = null;
        }
        return $inputs;
    }

    /**
     * Get user was deleted by email.
     *
     * @param   string $email.
     * @return  null|Model
     */
    public function getUserDelByEmail(string $email): ?Model
    {
        return $this->repository->getUserDelByEmail($email);
    }

    /**
     * Send mail change email
     *
     * @param string $email
     * @return boolean
     */
    public function sendEditMail(string $email): bool
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            // Code and token generate
            $randomTwoNumber = substr(str_shuffle("0123456789"), 0, 2);
            $randomTwoString = substr(str_shuffle("abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 2);
            $random8NumberOrString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);

            $code = $random8NumberOrString . $randomTwoString . $randomTwoNumber;
            $token = Str::random(60);
            $urlVerify = route('user.profile.change-email.verification', [
                'token' => $token,
            ]);
            // Delete old and create new Authentication
            $this->authenticationRepository->findByCondition([
                'user_id' => $user->id,
                'type' => Authentication::CHANGE_EMAIL
            ])->delete();
            $this->authenticationRepository->create([
                'user_id' => $user->id,
                'type' => Authentication::CHANGE_EMAIL,
                'value' => $email,
                'token' => $token,
                'code' => $code,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
           //send mail
            SendGeneralMailJob::dispatch('emails.edit-email.index', [
                'to' => $email,
                'subject' => __('labels.profile_edit.edit_email.change_email_title2'),
                'code' => $code,
                'url' => $urlVerify,
            ]);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Verify code edit code
     *
     * @param array $inputs
     * @return boolean
     */
    public function editEmailVerifyCode(array $inputs): bool
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            if (empty($inputs['token_verify'])) {
                return false;
            }
            $athenticateChangeMail = $this->authenticationRepository->findByCondition([
                'user_id' => $user->id,
                'type' => Authentication::CHANGE_EMAIL,
                'token' => $inputs['token_verify'],
                'code' => $inputs['code'],
            ])->first();

            //check authenticate
            if ($athenticateChangeMail) {
                //check email new existed
                $checkEmail = $this->repository->findByCondition(['email' => $athenticateChangeMail->value])->exists();
                if ($checkEmail) {
                    return false;
                }
                if ($athenticateChangeMail->token == $inputs['token_verify'] && $athenticateChangeMail->code == $inputs['code']) {
                    $now = strtotime(date('Y-m-d H:i:s'));
                    $timeCreated = strtotime($athenticateChangeMail->created_at);
                    //check time verify 30 minutes
                    if ($now - $timeCreated <= TIME_LIMIT_EDIT_EMAIL_USER) {
                        $user->email = $athenticateChangeMail->value;
                        $updated = $user->save();
                        if ($updated) {
                            // Delete old and create new Authentication
                            $athenticateChangeMail->delete();
                            DB::commit();
                        }
                        return $updated;
                    } else {
                        $athenticateChangeMail->delete();
                        DB::commit();

                        return false;
                    }
                }
            }
            return false;
        } catch (\Exception $e) {
            DB::commit();
            Log::error($e);

            return false;
        }
    }

    /**
     * Get info user
     *
     * @return mixed
     */
    public function getInfoUser()
    {
        return $this->repository->findByCondition(['id' => auth()->user()->id])->select([
            'id',
            'info_type_acc',
            'info_nation_id',
            'info_name',
            'info_name_furigana',
            'info_postal_code',
            'info_prefectures_id',
            'info_address_second',
            'info_address_three',
            'contact_type_acc',
            'contact_name',
            'contact_name_furigana',
            'contact_nation_id',
            'contact_postal_code',
            'contact_prefectures_id',
            'contact_address_second',
            'contact_address_three',
        ])->first();
    }

    /**
     * Pluck Member Id
     *
     * @param  string $valMemberId
     * @return boolean
     */
    public function pluckMemberId($valMemberId): bool
    {
        return $this->repository->pluckMemberId($valMemberId);
    }

    /**
     * Generate User Number
     *
     * @return string
     */
    public function generateUserNumber(): string
    {
        $maxUserNumberGroup = 1000;

        $lastUser = $this->repository->findByCondition([])->orderBy('id', 'desc')->first();
        $userNumber = $lastUser->user_number ?? 'LA000';

        $numberChar = Str::substr($userNumber, 1, 1);
        $number = (int) Str::substr($userNumber, 2, 3);

        $numberUser = null;
        $continue = true;
        while ($continue) {
            $number = $number + 1;

            if ($number == $maxUserNumberGroup) {
                $numberChar = chr(ord($numberChar) + 1);
                $number = 1;
            }

            $numberUser = 'L' . $numberChar . str_pad($number, 3, 0, STR_PAD_LEFT);

            $user = $this->repository->findByCondition([
                'user_number' => $numberUser,
            ])->withTrashed()->first();

            if (empty($user)) {
                $continue = false;
            }
        }

        return $numberUser;
    }

    /**
     * Get Trademark
     *
     * @param int id user id
     *
     * @return Collection
     */
    public function getTrademarksOfUserWithdraw(int $id): ?Collection
    {
        return Trademark::with([
            'notices' => function ($query) {
                return $query->with('noticeDetails')->orderBy('created_at', SORT_BY_DESC);
            }, 'appTrademark.trademarkInfo', 'registerTrademark'
        ])
            ->join('app_trademarks', 'app_trademarks.trademark_id', 'trademarks.id')
            ->leftJoin('comparison_trademark_results', 'comparison_trademark_results.trademark_id', 'trademarks.id')
            ->leftJoin('register_trademarks', 'register_trademarks.trademark_id', 'trademarks.id')
            ->leftJoin('payments', 'payments.trademark_id', 'trademarks.id')
            ->leftJoin('payer_infos', 'payer_infos.id', 'payments.payer_info_id')
            ->whereRaw('
                trademarks.user_id = '. $id .' AND app_trademarks.`status` = '. AppTrademark::STATUS_ADMIN_CONFIRM .'
                AND trademarks.status_management = '. Trademark::TRADEMARK_STATUS_MANAGEMENT .'
                AND (
                    (app_trademarks.is_cancel != '. IS_CANCEL_TRUE .' AND NOT EXISTS (
                        SELECT * FROM comparison_trademark_results WHERE comparison_trademark_results.trademark_id = app_trademarks.trademark_id)
                    )
                    OR comparison_trademark_results.is_cancel != '. IS_CANCEL_TRUE .'
                    OR CASE WHEN comparison_trademark_results.response_deadline IS NOT NULL THEN comparison_trademark_results.response_deadline >= NOW() ELSE FALSE END
                    OR CASE WHEN payer_infos.payment_type = 2 THEN (
                        payments.payment_status != '. Payment::STATUS_PAID .' AND payer_infos.payment_type = '. Payment::BANK_TRANSFER .' AND DATE_ADD(payments.created_at,  INTERVAL 3 MONTH) >= NOW()
                        ) ELSE FALSE END
                    OR DATE_ADD(register_trademarks.response_deadline, INTERVAL 30 DAY) >= NOW()
                    OR DATE_ADD(register_trademarks.deadline_update, INTERVAL 6 MONTH) > NOW()
                )
            ')->select('trademarks.*', 'register_trademarks.register_number')
            ->get();
    }

    /**
     * Get Trademark
     *
     * @param int id user id
     *
     * @return Collection
     */
    public function getTrademarksCompletedOfUser(int $id): ?Collection
    {
        return Trademark::with([
            'notices' => function ($query) {
                return $query->with('noticeDetails')->orderBy('created_at', SORT_BY_DESC);
            }, 'appTrademark.trademarkInfo', 'registerTrademark'
            ])
            ->join('app_trademarks', 'app_trademarks.trademark_id', 'trademarks.id')
            ->leftJoin('comparison_trademark_results', 'comparison_trademark_results.trademark_id', 'trademarks.id')
            ->leftJoin('register_trademarks', 'register_trademarks.trademark_id', 'trademarks.id')
            ->leftJoin('payments', 'payments.trademark_id', 'trademarks.id')
            ->leftJoin('payer_infos', 'payer_infos.id', 'payments.payer_info_id')
            ->whereRaw('
                trademarks.user_id = '. $id .' AND app_trademarks.`status` = '. AppTrademark::STATUS_ADMIN_CONFIRM.'
                AND (
                    trademarks.status_management = '. Trademark::TRADEMARK_STATUS_NOT_MANAGEMENT .'
                    OR app_trademarks.is_cancel = '. IS_CANCEL_TRUE .'
                    OR comparison_trademark_results.is_cancel = '. IS_CANCEL_TRUE .'
                    OR comparison_trademark_results.response_deadline < NOW()
                    OR DATE_ADD(register_trademarks.response_deadline, INTERVAL 30 DAY) < NOW()
                    OR DATE_ADD(register_trademarks.deadline_update, INTERVAL 6 MONTH) < NOW()
                    OR (payments.payment_status != '. Payment::STATUS_PAID .' AND payer_infos.payment_type = '. Payment::BANK_TRANSFER .' AND NOW() >= DATE_ADD(payments.created_at,  INTERVAL 3 MONTH))
                )
            ')->select(
                        'trademarks.*',
                        'register_trademarks.register_number'
                    )
            ->get();
    }

    /**
     * Send a mail to User.
     *
     * @return void
     */
    public function sendMailConfirmWithdraw(): void
    {
        try {
            $user = Auth::user();
            $code = Str::random(12);
            $token = Str::random(60);
            $expiredAt = now()->addHours(1)->timestamp;
            Authentication::create([
                'user_id' => $user->id,
                'type' => Authentication::WITHDRAW,
                'value' => $user->email,
                'token' => $token,
                'code' => $code,
                'create' => now()
            ]);
            Session::put($token, [ 'time' => $expiredAt, 'user_id' => $user->id ]);

            SendGeneralMailJob::dispatch('emails.mail_u000taikai01', [
                'to' => $user->getListMail(),
                'subject' => __('messages.general.withdraw_mail_title'),
                'code' => $code,
                'link' => route('user.withdraw.verification', ['token' => $token])
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            throw new \Exception($e->getMessage());
        }
    }
}
