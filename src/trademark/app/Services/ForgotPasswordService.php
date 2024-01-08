<?php

namespace App\Services;

use App\Jobs\SendGeneralMailJob;
use App\Models\Authentications;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ForgotPasswordService extends BaseService
{
    /**
     * Reset Password
     *
     * @param  mixed $request
     * @return void
     */
    public function resetPassword($request)
    {
        try {
            $code = $request['code'];
            $password = $request['password'];
            // Check code exist
            $authentication = Authentications::where('code', $code)
                ->where('type', Authentications::FORGOT_PASSWORD)
                ->first();
            if (!$authentication) {
                return redirect()->back()->with([
                    'error' => __('messages.forgot_password.Forgot_ID_U000_E001'),
                ]);
            }

            $user = User::where('id', $authentication->user_id)
                ->where('info_member_id', $request['info_member_id'])
                ->where('email', $request['email'])
                ->where('status', User::ENABLED)
                ->where('status_withdraw', User::STATUS_WITHDRAW_INACTIVE)
                ->first();
            if (!$user) {
                return false;
            }

            // Update password and delete password_resets
            $user->update([
                'password' => bcrypt($password),
            ]);

            SendGeneralMailJob::dispatch('emails.reset-password-success-mail', [
                'to' => $user->getListMail(),
                'subject' => __('labels.recover_id.title_send_email_password_reset'),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('message', __('messages.error'));
        }
    }
}
