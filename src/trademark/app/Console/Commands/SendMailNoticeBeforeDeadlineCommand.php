<?php

namespace App\Console\Commands;

use App\Jobs\SendGeneralMailJob;
use App\Models\ComparisonTrademarkResult;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\RegisterTrademark;

class SendMailNoticeBeforeDeadlineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-before-deadline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a mail before deadline';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // TYPE = 6
        // Opposition argument to the reason for refusal
        // when the opposition deadline is 03 days left (comparison_trademark_results(latest of trademark).response_deadline - now() = 3 days).
        $comparisonTrademarkResults = DB::select('
            SELECT
                ctr.id,
                ctr.maching_result_id,
                ctr.admin_id,
                ctr.trademark_id,
                ctr.sending_noti_rejection_date,
                ctr.response_deadline,
                ctr.user_response_deadline,
                ctr.is_cancel,
                ctr.is_send_mail,
                ctr.step,
                DATEDIFF(ctr.response_deadline, NOW()) as days,
                users.email,
                users.id as user_id
            FROM comparison_trademark_results as ctr
                JOIN trademarks ON trademarks.id = ctr.trademark_id
                JOIN users ON users.id = trademarks.user_id
                WHERE DATEDIFF(ctr.response_deadline, NOW()) = 3
                AND ctr.is_send_mail = 0
        ');

        foreach ($comparisonTrademarkResults as $comTrademarkResult) {
            $user = User::find($comTrademarkResult->user_id);

            SendGeneralMailJob::dispatch('emails.notice-response-deadline', [
                'to' => $user->getListMail(),
                'subject' => __('拒絶理由通知期限のお知らせ'),
            ]);

            ComparisonTrademarkResult::find($comTrademarkResult->id)->update([
                'is_send_mail' => true,
            ]);
        }

        // TYPE = 7
        // Registration trademark.
        // When the opposition deadline is 01 day left (Send email 10 days before: register_trademarks.response_deadline - now() = 10 days and register_trademarks (latest).is_register = 0).
        $registerTrademarks = DB::select('
            SELECT
                rt.id,
                rt.trademark_id,
                rt.user_response_deadline,
                rt.period_registration,
                rt.is_send_mail,
                users.email
            FROM register_trademarks as rt
                JOIN trademarks ON trademarks.id = rt.trademark_id
                JOIN users ON users.id = trademarks.user_id
                WHERE DATEDIFF(rt.response_deadline, NOW()) = 10
                AND rt.is_register = 0
                AND rt.is_send_mail = 0
        ');

        foreach ($registerTrademarks as $registerTrademark) {
            // $title = '登録期限のお知らせ';
            // $message = 'Test - trademark_id: '. $registerTrademark->trademark_id;
            // Mail::to($registerTrademark->email)->send(new NoticeResponseDeadlineMailTemplate($title, $message));

            RegisterTrademark::find($registerTrademark->id)->update([
                'is_send_mail' => true,
            ]);
        }

        // TYPE = 8
        // Update extension
        // legal deadline (not additional payment) is 01 day left (register_trademarks.deadline_update - now() = 1 and register_trademarks(latest).is_register = 1).
        $registerTrademarkRenewals = DB::select('
            SELECT
                rtr.id,
                rtr.register_trademark_id,
                rtr.type,
                rt.deadline_update,
                rtr.is_send_mail,
                rtr.trademark_id,
                users.email,
                DATEDIFF(rt.deadline_update, NOW()) as days
            FROM register_trademark_renewals AS rtr
            JOIN register_trademarks AS rt ON rt.id = rtr.register_trademark_id
            JOIN trademarks ON trademarks.id = rt.trademark_id
            JOIN users ON users.id = trademarks.user_id
            WHERE DATEDIFF(rt.deadline_update, NOW()) = 1
            AND rt.is_register = 1
            AND rtr.is_send_mail = 0
        ');

        foreach ($registerTrademarkRenewals as $registerTrademarkRenewal) {
            // $title = '更新期限のお知らせ';
            // $message = 'Test - trademark_id: '. $registerTrademarkRenewal->trademark_id;
            // Mail::to($registerTrademarkRenewal->email)->send(new NoticeResponseDeadlineMailTemplate($title, $message));

            RegisterTrademark::find($registerTrademarkRenewal->register_trademark_id)->update([
                'is_send_mail' => true,
            ]);
        }
    }
}
