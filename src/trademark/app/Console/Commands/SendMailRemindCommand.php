<?php

namespace App\Console\Commands;

use App\Models\AppTrademark;
use App\Models\FreeHistory;
use App\Models\MailTemplate;
use App\Models\RegisterTrademark;
use App\Models\User;
use App\Services\MailTemplateService;
use App\Services\XMLProcedures\ProcedureInfomation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendMailRemindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-mail:remind';

    protected MailTemplateService $mailTemplateService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail remind with U202, U202n, U203, U203n, u204, u204n, u302, U402, U402tsuino, u302_402tsuino_5yr_kouki, u302_402_5yr_kouki';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MailTemplateService $mailTemplateService)
    {
        parent::__construct();
        $this->mailTemplateService = $mailTemplateService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Send email when overdue to answer questions at u202 // U202, U202n -> 回答要・回答期限を超えたらリマインド
        $this->remindPreQuestionRefusal();

        // Send email when overdue to answer questions at u203 // U203, U203n -> 回答要・回答期限を超えたらリマインド
        $this->remindChoicePlanRefusal();

        // Send email when overdue to answer questions at u204 // U204 ->  回答要・回答期限を超えたらリマインド
        $this->remindSubmissionFirstRefusal();

        // Send email when overdue to answer questions at u204n // U204n ->  回答要・回答期限を超えたらリマインド
        $this->remindSubmissionRefusal();

        // Send email when overdue to answer questions at U302 // U302 ->  回答要・回答期限を超えたらリマインド
        $this->remindRegisterTrademarks();

        // Send email when overdue to answer questions at U000free // U000free -> 回答要・回答期限を超えたらリマインド
        $this->remindAnswerQuestionHistory();

        // Send email when overdue to answer questions at U402,U402tsuino // U402,U402tsuino -> 回答要・回答期限を超えたらリマインド
        // $this->remindRenewalTrademark();

        // Send email when overdue to answer questions at u302_402_5yr_kouki,u302_402tsuino_5yr_kouki // u302_402_5yr_kouki,u302_402tsuino_5yr_kouki-> 回答要・回答期限を超えたらリマインド
        $this->remindRenewalFiveYearTrademark();
    }

    /**
     * U202, U202n -> 回答要・回答期限を超えたらリマインド
     */
    protected function remindPreQuestionRefusal()
    {
        try {
            $now = now()->format('Y-m-d');
            $dataRaw = DB::select("
                SELECT
                    ctr.id AS ctr_id,
                    t.id AS trademark_id,
                    rqn.id AS reason_question_no_id,
                    rqn.user_response_deadline,
                    t.user_id,
                    ap.pack,
                    CASE
                        WHEN EXISTS(SELECT * FROM reason_question_details as rqd WHERE rqd.reason_question_no_id = rqn.id AND rqd.is_answer = 0) THEN 1 ELSE 0
                    END AS exist_rq_detail
                FROM trademarks AS t
                JOIN app_trademarks ap on ap.trademark_id = t.id
                JOIN comparison_trademark_results AS ctr ON ctr.trademark_id = t.id
                JOIN plan_correspondences AS pc ON pc.comparison_trademark_result_id = ctr.id
                JOIN reason_questions AS rq ON rq.plan_correspondence_id = pc.id
                JOIN reason_question_no AS rqn ON rqn.reason_question_id  = rq.id
                WHERE ctr.is_cancel = 0
                AND ap.pack = " . AppTrademark::PACK_C . "
                AND rqn.user_response_deadline = '$now'
                ORDER BY ctr.id DESC ,rqn.id DESC
            ");
            $results = collect($dataRaw)->where('exist_rq_detail', 1);

            foreach ($results as $trademark) {
                $user = User::find($trademark->user_id);
                $params = [
                    'from_page' => U202,
                    'user' => $user,
                ];
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * U203, U203n ->  回答要・回答期限を超えたらリマインド
     */
    protected function remindChoicePlanRefusal()
    {
        try {
            $now = now()->format('Y-m-d');
            $results = DB::select("
                SELECT
                    t.user_id,
                    t.id AS trademark_id,
                    tp.id AS trademark_plan_id
                FROM trademarks t
                JOIN app_trademarks ap on ap.trademark_id = t.id
                JOIN comparison_trademark_results ctr ON ctr.trademark_id = t.id
                JOIN plan_correspondences pc ON pc.comparison_trademark_result_id = ctr.id
                JOIN trademark_plans tp ON tp.plan_correspondence_id = pc.id
                WHERE tp.is_register = 0
                AND tp.response_deadline = '$now'
                AND tp.is_cancel = 0
                AND ap.pack = " . AppTrademark::PACK_C . "
                ORDER BY tp.id DESC
            ");

            foreach ($results as $trademark) {
                $user = User::find($trademark->user_id);
                $params = [
                    'from_page' => U203,
                    'user' => $user,
                ];
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * U204 ->  回答要・回答期限を超えたらリマインド
     */
    protected function remindSubmissionFirstRefusal()
    {
        try {
            $now = now()->format('Y-m-d');
            $dataRaw = DB::select("
                SELECT
                    t.user_id,
                    ctr.trademark_id,
                    (SELECT COUNT(*) FROM required_documents AS rd
                        WHERE rd.trademark_plan_id = tp.id
                        OR ( rd.trademark_plan_id = tp.id AND rd.is_send = 0)) AS required_document_count
                FROM trademarks t
                JOIN comparison_trademark_results ctr ON ctr.trademark_id = t.id
                JOIN plan_correspondences pc ON pc.comparison_trademark_result_id = ctr.id
                JOIN trademark_plans tp ON tp.plan_correspondence_id = pc.id
                AND tp.is_cancel = 0
                AND tp.response_deadline = '$now'
                ORDER BY ctr.id DESC
            ");
            $results = collect($dataRaw)->where('required_document_count', '<=', 1);

            foreach ($results as $trademark) {
                $user = User::find($trademark->user_id);
                $params = [
                    'from_page' => U204,
                    'user' => $user,
                ];
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * U204n ->  回答要・回答期限を超えたらリマインド
     */
    protected function remindSubmissionRefusal()
    {
        try {
            $now = now()->format('Y-m-d');
            $results = DB::select("SELECT
                    t.user_id,
                    ctr.trademark_id
                FROM trademarks t
                JOIN comparison_trademark_results ctr ON ctr.trademark_id = t.id
                JOIN plan_correspondences pc ON pc.comparison_trademark_result_id = ctr.id
                JOIN trademark_plans tp ON tp.plan_correspondence_id = pc.id
                WHERE EXISTS(SELECT * FROM required_documents AS rd WHERE rd.trademark_plan_id = tp.id OR rd.is_send = 0 ORDER BY id DESC)
                AND tp.is_cancel = 0
                AND tp.sending_docs_deadline = '$now'
                ORDER BY t.id DESC
            ");

            foreach ($results as $trademark) {
                $user = User::find($trademark->user_id);
                $params = [
                    'from_page' => U204,
                    'user' => $user,
                ];
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * U302 ->  回答要・回答期限を超えたらリマインド
     */
    protected function remindRegisterTrademarks()
    {
        try {
            $now = now()->format('Y-m-d');
            // CASE 1: send mail for the 1st, 2nd time
            $documentName = ProcedureInfomation::REGISTRATION_ASSESSMENT;

            $results = DB::select("SELECT
                    t.user_id,
                    mr.id AS comparison_trademark_result_id,
                    DATE_FORMAT(ADDDATE(mr.pi_dd_date, INTERVAL 21 DAY), '%Y-%m-%d') AS add_21_day,
                    DATE_FORMAT(ADDDATE(mr.pi_dd_date, INTERVAL 24 DAY), '%Y-%m-%d') AS add_24_day
                FROM trademarks t
                LEFT JOIN register_trademarks rt ON t.id = rt.trademark_id
                JOIN app_trademarks apt ON apt.trademark_id = t.id
                JOIN maching_results mr ON mr.trademark_id = t.id
                WHERE rt.is_register = 0
                AND apt.is_cancel = 0
                AND mr.pi_document_name = '$documentName'
                AND (DATE_FORMAT(ADDDATE(mr.pi_dd_date, INTERVAL 21 DAY), '%Y-%m-%d') = '$now'
                OR DATE_FORMAT(ADDDATE(mr.pi_dd_date, INTERVAL 24 DAY), '%Y-%m-%d') = '$now')
                ORDER BY mr.id");
            foreach ($results as $trademark) {
                $user = User::find($trademark->user_id);
                $params = [
                    'from_page' => U302 . '_1_2',
                    'user' => $user,
                ];
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
            }

            // CASE 2: send mail for the 3rd time
            $results = DB::select("SELECT
                    t.user_id,
                    mr.id AS comparison_trademark_result_id,
                DATE_FORMAT(ADDDATE(mr.pi_dd_date, INTERVAL 30 DAY), '%Y-%m-%d') AS add_30_day
                FROM trademarks t
                LEFT JOIN register_trademarks rt ON t.id = rt.trademark_id
                JOIN app_trademarks apt ON apt.trademark_id = t.id
                JOIN maching_results mr ON mr.trademark_id = t.id
                WHERE rt.is_register = 0
                AND apt.is_cancel = 0
                AND mr.pi_document_name = '$documentName'
                AND (DATE_FORMAT(ADDDATE(mr.pi_dd_date, INTERVAL 30 DAY), '%Y-%m-%d') = '$now')
                ORDER BY mr.id");

            foreach ($results as $trademark) {
                $user = User::find($trademark->user_id);
                $params = [
                    'from_page' => U302 . '_3',
                    'user' => $user,
                ];
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * U000free -> 回答要・回答期限を超えたらリマインド
     */
    protected function remindAnswerQuestionHistory()
    {
        try {
            $now = now()->format('Y-m-d');
            $results = DB::select("SELECT
                    fh.id AS free_history_id,
                    t.user_id
                FROM free_histories fh
                JOIN trademarks t ON t.id = fh.trademark_id
                WHERE fh.is_answer = " . FreeHistory::IS_ANSWER_FALSE . "
                AND fh.is_cancel = " . FreeHistory::IS_NOT_CANCEL . "
                AND fh.`type` = " . FreeHistory::TYPE_4 . "
                AND fh.user_response_deadline = '$now'
            ");

            foreach ($results as $trademark) {
                $user = User::find($trademark->user_id);
                $params = [
                    'from_page' => U000FREE,
                    'user' => $user,
                ];
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * U402, U402tsuino-> 回答要・回答期限を超えたらリマインド
     */
    protected function remindRenewalTrademark()
    {
        try {
            $typePage1 = RegisterTrademark::TYPE_PAGE_1;
            $typePage2 = RegisterTrademark::TYPE_PAGE_2;
            $typePage3 = RegisterTrademark::TYPE_PAGE_3;
            $isRegister = RegisterTrademark::IS_REGISTER;
            $isNotCancel = RegisterTrademark::IS_NOT_CANCEL;
            // CASE 1: The reminder email to remind the brand renewal deadline is approaching.
            // U402: At this time, the trademark is approaching the renewal date
            // U402tsuino: At this time, the trademark is overdue and requires renewal registration.
            $now = now()->format('Y-m-d');
            $results = DB::select("SELECT
                rt.id AS register_trademark_id,
                t.user_id,
                rt.trademark_id,
                rt.`type`,
                ADDDATE(rt.deadline_update, INTERVAL 1 DAY) AS deadline_update_add_1,
                rt.type_page
            FROM trademarks t
                JOIN register_trademarks rt ON rt.trademark_id = t.id
                WHERE rt.type_page = $typePage2
                AND rt.is_register = $isRegister
                AND ADDDATE(rt.deadline_update, INTERVAL 1 DAY) = '$now'
                AND rt.is_cancel = $isNotCancel
                ORDER BY rt.id DESC;
            ");

            foreach ($results as $trademark) {
                $exist = RegisterTrademark::where([
                    'trademark_id' => $trademark->trademark_id,
                    'is_register' => $isRegister,
                ])->where('deadline_update', '>=', $now)->whereRaw("(type_page = $typePage3 OR type_page = $typePage1)")->count();

                if (!$exist) {
                    $user = User::find($trademark->user_id);
                    $params = [
                        'from_page' => U402,
                        'user' => $user,
                    ];
                    $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
                }
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Page u302_402_5yr_kouki,u302_402tsuino_5yr_kouki-> 回答要・回答期限を超えたらリマインド
     */
    protected function remindRenewalFiveYearTrademark()
    {
        try {
            $typePage1 = RegisterTrademark::TYPE_PAGE_1;
            $typePage3 = RegisterTrademark::TYPE_PAGE_3;
            $isRegister = RegisterTrademark::IS_REGISTER;
            $isNotCancel = RegisterTrademark::IS_NOT_CANCEL;
            $now = now()->format('Y-m-d');

            // CASE 1: When just past the renewal deadline.
            // $results = DB::select("SELECT
            //     rt.id AS register_trademark_id,
            //     t.user_id,
            //     rt.trademark_id,
            //     rt.is_register,
            //     rt.type_page,
            //     rt.`type`,
            //     ADDDATE(rt.deadline_update, INTERVAL 1 DAY) AS deadline_update_add_1
            // FROM trademarks t
            //     JOIN register_trademarks rt ON rt.trademark_id = t.id
            //     WHERE (rt.type_page = $typePage1 OR rt.type_page = $typePage3)
            //     AND rt.is_register = $isRegister
            //     AND rt.is_cancel = $isNotCancel
            //     AND ADDDATE(rt.deadline_update, INTERVAL 1 DAY) = '$now'
            //     ORDER BY rt.trademark_id ASC
            // ");
            //
            // foreach ($results as $trademark) {
            //     $exist = RegisterTrademark::where([
            //         'trademark_id' => $trademark->trademark_id,
            //         'is_register' => $isRegister,
            //         'type_page' => RegisterTrademark::TYPE_PAGE_2
            //     ])->where('deadline_update', '>=', $now)->count();
            //
            //     if(!$exist) {
            //         $user = User::find($trademark->user_id);
            //         $params = [
            //             'from_page' => U302_402_5YR_KOUKI,
            //             'user' => $user
            //         ];
            //
            //         $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
            //     }
            // }

            // CASE 2: When just past the 6-month renewal deadline.
            $trademarkPast6Month = DB::select("SELECT
                rt.id AS register_trademark_id,
                t.user_id,
                rt.trademark_id,
                rt.is_register,
                rt.type_page,
                rt.`type`,
                rt.deadline_update,
                ADDDATE(rt.deadline_update, INTERVAL 6 MONTH) AS deadline_update_add_6_month
            FROM trademarks t
                JOIN register_trademarks rt ON rt.trademark_id = t.id
                WHERE (rt.type_page = $typePage1 OR rt.type_page = $typePage3)
                AND rt.is_register = $isRegister
                AND rt.is_cancel = $isNotCancel
                AND ADDDATE(rt.deadline_update, INTERVAL 6 MONTH) = '$now'
                ORDER BY rt.trademark_id ASC
            ");

            foreach ($trademarkPast6Month as $trademark) {
                $exist = RegisterTrademark::where([
                    'trademark_id' => $trademark->trademark_id,
                    'is_register' => $isRegister,
                    'type_page' => RegisterTrademark::TYPE_PAGE_2,
                ])->where('deadline_update', '>=', $now)->count();

                if (!$exist) {
                    $user = User::find($trademark->user_id);

                    $params = [
                        'from_page' => U302_402_5YR_KOUKI . '_6_months',
                        'user' => $user,
                    ];
                    $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
                }
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }
}
