<?php

namespace App\Console\Commands;

use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\Trademark;
use App\Models\TrademarkRenewalNotice;
use App\Repositories\TrademarkRenewalNoticeRepository;
use App\Services\NoticeDetailService;
use App\Services\NoticeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendRenewalDeadlineNoticeAfternoonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notice-renewal:afternoon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send renewal deadline notice';


    protected NoticeService $noticeService;
    protected NoticeDetailService $noticeDetailService;
    protected TrademarkRenewalNoticeRepository $trademarkRenewalNoticeRepository;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        NoticeService $noticeService,
        NoticeDetailService $noticeDetailService,
        TrademarkRenewalNoticeRepository $trademarkRenewalNoticeRepository
    )
    {
        parent::__construct();
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->trademarkRenewalNoticeRepository = $trademarkRenewalNoticeRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Case 3: First renewal deadline has expired.
        $this->firstRenewalDeadlineExpired();

        // Case 5: Second renewal deadline has expired
        $this->secondRenewalDeadlineExpired();

        // // Case 4: Renewal deadline has expired after successful renewal round 1
        $this->renewalDeadlineExpiredAfter1fstSuccessRenewal();

        // Case 6: Expired after successfully renewing the second time.
        $this->expiredAfterSuccessRenewing2ndTime();

        // Case 7: Expired renewal after successfully renewing once for the second time.
        $this->expiredRenewalAfterSuccessRenewing2ndTime();
    }

    /**
     * Expired renewal after successfully renewing once for the second time.
     */
    protected function expiredRenewalAfterSuccessRenewing2ndTime()
    {
        try {
            DB::beginTransaction();

            $trademarks = DB::select('
                SELECT
                    ctr.id as ctr_id,
                    ctr.trademark_id,
                    ctr.response_deadline,
                    trademarks.*
                FROM trademarks
                    JOIN comparison_trademark_results AS ctr ON ctr.trademark_id = trademarks.id
                    WHERE trademarks.id NOT IN (
                        SELECT trademark_renewal_notices.trademark_id FROM trademark_renewal_notices WHERE trademark_renewal_notices.trademark_id = trademarks.id
                        AND trademark_renewal_notices.pattern = '. TrademarkRenewalNotice::PATTERN_EARLY_EXTENSION .'
                        AND trademark_renewal_notices.`type` = '. TrademarkRenewalNotice::TYPE_4 .'
                    )
                    AND NOW() > DATE_SUB(DATE_SUB(ctr.response_deadline, INTERVAL 3 DAY), INTERVAL 12 HOUR)
                    AND trademarks.id IN (
                        SELECT trademarks.id FROM trademarks
                            JOIN register_trademark_renewals ON trademarks.id = register_trademark_renewals.trademark_id
                            WHERE register_trademark_renewals.type = 2 AND register_trademark_renewals.deleted_at IS NULL
                    )
                    AND trademarks.id NOT IN (
                        SELECT trademarks.id FROM trademarks
                            JOIN register_trademarks ON trademarks.id = register_trademarks.trademark_id
                            WHERE register_trademarks.deleted_at IS NULL
                        )
                    AND trademarks.is_refusal = ' . Trademark::IS_REFUSAL_NOT_REFUSAL . '
                    AND trademarks.deleted_at IS NULL
                    AND ctr.deleted_at IS NULL
            ');

            foreach ($trademarks as $key => $trademark) {
                // update notice detail of last notice
                $notice = $this->noticeService->findByCondition([
                    'trademark_id' => $trademark->id,
                ])->whereIn('flow', [
                    Notice::FLOW_RESPONSE_REASON,
                    Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                ])->orderBy('id', SORT_TYPE_DESC)->with('noticeDetails')->first();

                $noticeDetails = $notice->noticeDetails ?? collect([]);
                $noticeDetails->where('type_acc', NoticeDetail::TYPE_USER)
                    ->where('target_id', $trademark->user_id)
                    ->where('type_page', NoticeDetail::TYPE_PAGE_TOP)
                    ->where('is_action', NoticeDetail::IS_ACTION_TRUE)
                    ->where('is_answer', NoticeDetail::IS_NOT_ANSWER)
                    ->map(function ($item) {
                        $item->update([
                            'is_answer' => NoticeDetail::IS_ANSWER,
                        ]);
                    });

                //create notice
                $notice = $this->noticeService->create([
                    'trademark_id' => $trademark->id,
                    'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                    'user_id' => $trademark->user_id,
                    'trademark_info_id' => null,
                    'created_at' => now()
                ]);

                // Get Last Notice Detail
                $lastNoticeDetail = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_id' => $trademark->user_id,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                ])->whereHas('notice', function ($query) use ($trademark) {
                    $query->whereIn('flow', [
                        Notice::FLOW_RESPONSE_REASON,
                        Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                    ])->where('trademark_id', $trademark->id);
                })->where('content', 'NOT LIKE', '%期限日前期間延長のお申込み%')
                    ->where('content', 'NOT LIKE', '%期間外延長のお申込み%')
                    ->where('content', 'NOT LIKE', '%期間外延長のお申込み（2位）%')
                    ->where('content', 'NOT LIKE', '%終了%')
                    ->orderBy('id', SORT_TYPE_DESC)->first();

                //create notice detail show on U-000top
                $noticeDetailShowOnU00top = $this->noticeDetailService->create(
                    [
                        'notice_id' => $notice->id,
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => '',
                        'redirect_page' => $lastNoticeDetail->redirect_page,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => NoticeDetail::IS_ACTION_TRUE,
                        'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                        'response_deadline' => isset($trademark->response_deadline) && $trademark->response_deadline
                            ? Carbon::createFromFormat('Y-m-d H:i:s', $trademark->response_deadline)
                            : null,
                        'content' => $lastNoticeDetail->content . '終了',
                        'created_at' => now()
                    ]
                );

                //create notice detail show on U-000anken_top
                $this->noticeDetailService->create([
                    'notice_id' => $notice->id,
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => '',
                    'redirect_page' => $lastNoticeDetail->redirect_page,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                    'response_deadline' => isset($trademark->response_deadline) && $trademark->response_deadline
                        ? Carbon::createFromFormat('Y-m-d H:i:s', $trademark->response_deadline)
                        : null,
                    'content' => $lastNoticeDetail->content . '終了',
                    'created_at' => now()
                ]);

                //update trademark
                Trademark::find($trademark->id)->update(['block_by' => OVER_05]);

                //insert trademark_renewal_notice
                $this->trademarkRenewalNoticeRepository->create([
                    'trademark_id' => $trademark->id,
                    'notice_detail_id' => $noticeDetailShowOnU00top->id,
                    'pattern' => TrademarkRenewalNotice::PATTERN_EXTENSION_AFTER_TERM,
                    'type' => TrademarkRenewalNotice::TYPE_2,
                    'is_send_notice' => TrademarkRenewalNotice::IS_SEND_NOTICE_TRUE,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }

    /**
     * Expired after successfully renewing the second time.
     */
    protected function expiredAfterSuccessRenewing2ndTime()
    {
        try {
            DB::beginTransaction();
            $trademarks = DB::select('
                SELECT
                    ctr.id as ctr_id,
                    ctr.trademark_id,
                    ctr.response_deadline,
                    trademarks.*
                FROM trademarks
                    JOIN comparison_trademark_results AS ctr ON ctr.trademark_id = trademarks.id
                    WHERE trademarks.id NOT IN (
                        SELECT trademark_renewal_notices.trademark_id FROM trademark_renewal_notices WHERE trademark_renewal_notices.trademark_id = trademarks.id
                        AND trademark_renewal_notices.pattern = '. TrademarkRenewalNotice::PATTERN_EARLY_EXTENSION .'
                        AND trademark_renewal_notices.`type` = '. TrademarkRenewalNotice::TYPE_4 .'
                    )
                    AND NOW() > DATE_SUB(DATE_SUB(DATE_ADD(ctr.response_deadline, INTERVAL 2 MONTH), INTERVAL 3 DAY), INTERVAL 12 HOUR)
                    AND trademarks.id IN (
                        SELECT trademarks.id FROM trademarks
                            JOIN register_trademark_renewals ON trademarks.id = register_trademark_renewals.trademark_id
                            WHERE register_trademark_renewals.type = 1 AND register_trademark_renewals.deleted_at IS NULL
                    )
                    AND trademarks.id NOT IN (
                        SELECT trademarks.id FROM trademarks
                            JOIN register_trademarks ON trademarks.id = register_trademarks.trademark_id
                            WHERE register_trademarks.deleted_at IS NULL
                        )
                    AND trademarks.is_refusal = ' . Trademark::IS_REFUSAL_NOT_REFUSAL . '
                    AND trademarks.deleted_at IS NULL
                    AND ctr.deleted_at IS NULL
            ');

            foreach ($trademarks as $key => $trademark) {
                // update notice detail of last notice
                $notice = $this->noticeService->findByCondition([
                    'trademark_id' => $trademark->id,
                ])->whereIn('flow', [
                    Notice::FLOW_RESPONSE_REASON,
                    Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                ])->orderBy('id', SORT_TYPE_DESC)->with('noticeDetails')->first();

                $noticeDetails = $notice->noticeDetails ?? collect([]);
                $noticeDetails->where('type_acc', NoticeDetail::TYPE_USER)
                    ->where('target_id', $trademark->user_id)
                    ->where('type_page', NoticeDetail::TYPE_PAGE_TOP)
                    ->where('is_action', NoticeDetail::IS_ACTION_TRUE)
                    ->where('is_answer', NoticeDetail::IS_NOT_ANSWER)
                    ->map(function ($item) {
                        $item->update([
                            'is_answer' => NoticeDetail::IS_ANSWER,
                        ]);
                    });

                //create notice
                $notice = $this->noticeService->create([
                    'trademark_id' => $trademark->id,
                    'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                    'user_id' => $trademark->user_id,
                    'trademark_info_id' => null,
                    'created_at' => now()
                ]);

                // Get Last Notice Detail
                $lastNoticeDetail = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_id' => $trademark->user_id,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                ])->whereHas('notice', function ($query) use ($trademark) {
                    $query->whereIn('flow', [
                        Notice::FLOW_RESPONSE_REASON,
                        Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                    ])->where('trademark_id', $trademark->id);
                })->where('content', 'NOT LIKE', '%期限日前期間延長のお申込み%')
                    ->where('content', 'NOT LIKE', '%期間外延長のお申込み%')
                    ->where('content', 'NOT LIKE', '%期間外延長のお申込み（2位）%')
                    ->where('content', 'NOT LIKE', '%終了%')
                    ->orderBy('id', SORT_TYPE_DESC)->first();

                //create notice detail show on U-000top
                $noticeDetailShowOnU00top = $this->noticeDetailService->create([
                    'notice_id' => $notice->id,
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => '',
                    'redirect_page' => $lastNoticeDetail->redirect_page,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                    'response_deadline' => isset($trademark->response_deadline) && $trademark->response_deadline
                        ? Carbon::createFromFormat('Y-m-d H:i:s', $trademark->response_deadline)->addMonths(2)
                        : null,
                    'content' => $lastNoticeDetail->content . '終了',
                    'created_at' => now()
                ]);

                //create notice detail show on U-000anken_top
                $this->noticeDetailService->create([
                    'notice_id' => $notice->id,
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => '',
                    'redirect_page' => $lastNoticeDetail->redirect_page,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                    'response_deadline' => isset($trademark->response_deadline) && $trademark->response_deadline
                        ? Carbon::createFromFormat('Y-m-d H:i:s', $trademark->response_deadline)->addMonths(2)
                        : null,
                    'content' => $lastNoticeDetail->content . '終了',
                    'created_at' => now()
                ]);

                //update trademark
                Trademark::find($trademark->id)->update(['block_by' => OVER_05]);

                //insert trademark_renewal_notice
                $this->trademarkRenewalNoticeRepository->create([
                    'trademark_id' => $trademark->id,
                    'notice_detail_id' => $noticeDetailShowOnU00top->id,
                    'pattern' => TrademarkRenewalNotice::PATTERN_EARLY_EXTENSION,
                    'type' => TrademarkRenewalNotice::TYPE_4,
                    'is_send_notice' => TrademarkRenewalNotice::IS_SEND_NOTICE_TRUE,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }

    /**
     * Renewal deadline has expired after successful renewal round 1
     */
    protected function renewalDeadlineExpiredAfter1fstSuccessRenewal()
    {
        try {
            DB::beginTransaction();
            $trademarks = DB::select('
                SELECT
                    ctr.id as ctr_id,
                    ctr.trademark_id,
                    ctr.response_deadline,
                    trademarks.*
                FROM trademarks
                    JOIN comparison_trademark_results AS ctr ON ctr.trademark_id = trademarks.id
                    WHERE trademarks.id NOT IN (
                        SELECT trademark_renewal_notices.trademark_id FROM trademark_renewal_notices WHERE trademark_renewal_notices.trademark_id = trademarks.id
                        AND trademark_renewal_notices.pattern = '. TrademarkRenewalNotice::PATTERN_EARLY_EXTENSION .'
                        AND trademark_renewal_notices.`type` = '. TrademarkRenewalNotice::TYPE_2 .'
                    )
                    AND ctr.response_deadline > NOW()
                    AND (DATEDIFF(ctr.response_deadline, NOW()) <= 3 AND SUBDATE(SUBDATE(ctr.response_deadline, INTERVAL 3 DAY), INTERVAL 12 HOUR) < NOW())
                    AND DATEDIFF(ctr.response_deadline, NOW()) > 0
                    AND trademarks.id in (
                        SELECT DISTINCT trademarks.id FROM trademarks
                            JOIN register_trademark_renewals ON trademarks.id = register_trademark_renewals.trademark_id
                            WHERE register_trademark_renewals.type = 1 AND register_trademark_renewals.deleted_at IS NULL
                    )
                    AND trademarks.id NOT IN (
                        SELECT trademarks.id FROM trademarks
                            JOIN register_trademarks ON trademarks.id = register_trademarks.trademark_id
                            WHERE register_trademarks.deleted_at IS NULL
                        )
                    AND trademarks.is_refusal = ' . Trademark::IS_REFUSAL_NOT_REFUSAL . '
                    AND trademarks.deleted_at IS NULL
                    AND ctr.deleted_at IS NULL
            ');

            foreach ($trademarks as $key => $trademark) {
                // update notice detail of last notice
                $notice = $this->noticeService->findByCondition([
                    'trademark_id' => $trademark->id,
                ])->whereIn('flow', [
                    Notice::FLOW_RESPONSE_REASON,
                    Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                ])->orderBy('id', SORT_TYPE_DESC)->with('noticeDetails')->first();

                $noticeDetails = $notice->noticeDetails ?? collect([]);
                $noticeDetails->where('type_acc', NoticeDetail::TYPE_USER)
                    ->where('target_id', $trademark->user_id)
                    ->where('type_page', NoticeDetail::TYPE_PAGE_TOP)
                    ->where('is_action', NoticeDetail::IS_ACTION_TRUE)
                    ->where('is_answer', NoticeDetail::IS_NOT_ANSWER)
                    ->map(function ($item) {
                        $item->update([
                            'is_answer' => NoticeDetail::IS_ANSWER,
                        ]);
                    });

                //create notice
                $notice = $this->noticeService->create([
                    'trademark_id' => $trademark->id,
                    'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                    'user_id' => $trademark->user_id,
                    'trademark_info_id' => null,
                    'created_at' => now()
                ]);

                // Get Last Notice Detail
                $lastNoticeDetail = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_id' => $trademark->user_id,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                ])->whereHas('notice', function ($query) use ($trademark) {
                    $query->whereIn('flow', [
                        Notice::FLOW_RESPONSE_REASON,
                        Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                    ])->where('trademark_id', $trademark->id);
                })->where('content', 'NOT LIKE', '%期限日前期間延長のお申込み%')
                    ->where('content', 'NOT LIKE', '%期間外延長のお申込み%')
                    ->where('content', 'NOT LIKE', '%期間外延長のお申込み（2位）%')
                    ->where('content', 'NOT LIKE', '%終了%')
                    ->orderBy('id', SORT_TYPE_DESC)->first();

                //create notice detail show on U-000top
                $noticeDetailShowOnU00top = $this->noticeDetailService->create([
                    'notice_id' => $notice->id,
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => '',
                    'redirect_page' => $lastNoticeDetail->redirect_page,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                    'response_deadline' => isset($trademark->response_deadline) && $trademark->response_deadline
                        ? Carbon::createFromFormat('Y-m-d H:i:s', $trademark->response_deadline)
                        : null,
                    'content' => $lastNoticeDetail->content . '期間外延長のお申込み（2位）',
                    'created_at' => now()
                ]);

                //create notice detail show on U-000anken_top
                $this->noticeDetailService->create([
                    'notice_id' => $notice->id,
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => '',
                    'redirect_page' => $lastNoticeDetail->redirect_page,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                    'response_deadline' => isset($trademark->response_deadline) && $trademark->response_deadline
                        ? Carbon::createFromFormat('Y-m-d H:i:s', $trademark->response_deadline)
                        : null,
                    'content' => $lastNoticeDetail->content . '期間外延長のお申込み（2位）',
                    'created_at' => now()
                ]);

                //update trademark
                Trademark::find($trademark->id)->update(['block_by' => OVER_04]);

                //insert trademark_renewal_notice
                $this->trademarkRenewalNoticeRepository->create([
                    'trademark_id' => $trademark->id,
                    'notice_detail_id' => $noticeDetailShowOnU00top->id,
                    'pattern' => TrademarkRenewalNotice::PATTERN_EARLY_EXTENSION,
                    'type' => TrademarkRenewalNotice::TYPE_2,
                    'is_send_notice' => TrademarkRenewalNotice::IS_SEND_NOTICE_TRUE,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }

    /**
     * Second renewal deadline has expired
     */
    protected function secondRenewalDeadlineExpired()
    {
        try {
            DB::beginTransaction();
            $trademarks = DB::select('
                SELECT
                    ctr.id as ctr_id,
                    ctr.trademark_id,
                    ctr.response_deadline,
                    trademarks.*
                FROM trademarks
                    JOIN comparison_trademark_results AS ctr ON ctr.trademark_id = trademarks.id
                    WHERE trademarks.id NOT IN (
                        SELECT trademark_renewal_notices.trademark_id FROM trademark_renewal_notices WHERE trademark_renewal_notices.trademark_id = trademarks.id
                        AND trademark_renewal_notices.pattern = '. TrademarkRenewalNotice::PATTERN_NEVER_RENEWED .'
                        AND trademark_renewal_notices.`type` = '. TrademarkRenewalNotice::TYPE_4 .'
                    )
                    AND NOW() > DATE_SUB(DATE_SUB(DATE_ADD(ctr.response_deadline, INTERVAL 2 MONTH), INTERVAL 3 DAY), INTERVAL 12 HOUR)
                    AND trademarks.id NOT IN (
                        SELECT trademarks.id FROM trademarks
                            JOIN register_trademark_renewals ON trademarks.id = register_trademark_renewals.trademark_id
                            WHERE register_trademark_renewals.deleted_at IS NULL
                    )
                    AND trademarks.id NOT IN (
                        SELECT trademarks.id FROM trademarks
                            JOIN register_trademarks ON trademarks.id = register_trademarks.trademark_id
                            WHERE register_trademarks.deleted_at IS NULL
                        )
                    AND trademarks.is_refusal = ' . Trademark::IS_REFUSAL_NOT_REFUSAL . '
                    AND trademarks.deleted_at IS NULL
                    AND ctr.deleted_at IS NULL
            ');

            foreach ($trademarks as $key => $trademark) {
                // update notice detail of last notice
                $notice = $this->noticeService->findByCondition([
                    'trademark_id' => $trademark->id,
                ])->whereIn('flow', [
                    Notice::FLOW_RESPONSE_REASON,
                    Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                ])->orderBy('id', SORT_TYPE_DESC)->with('noticeDetails')->first();

                $noticeDetails = $notice->noticeDetails ?? collect([]);
                $noticeDetails->where('type_acc', NoticeDetail::TYPE_USER)
                    ->where('target_id', $trademark->user_id)
                    ->where('type_page', NoticeDetail::TYPE_PAGE_TOP)
                    ->where('is_action', NoticeDetail::IS_ACTION_TRUE)
                    ->where('is_answer', NoticeDetail::IS_NOT_ANSWER)
                    ->map(function ($item) {
                        $item->update([
                            'is_answer' => NoticeDetail::IS_ANSWER,
                        ]);
                    });

                //create notice
                $notice = $this->noticeService->create([
                    'trademark_id' => $trademark->id,
                    'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                    'user_id' => $trademark->user_id,
                    'trademark_info_id' => null,
                    'created_at' => now()
                ]);

                // Get Last Notice Detail
                $lastNoticeDetail = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_id' => $trademark->user_id,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                ])->whereHas('notice', function ($query) use ($trademark) {
                    $query->whereIn('flow', [
                        Notice::FLOW_RESPONSE_REASON,
                        Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                    ])->where('trademark_id', $trademark->id);
                })->where('content', 'NOT LIKE', '%期限日前期間延長のお申込み%')
                    ->where('content', 'NOT LIKE', '%期間外延長のお申込み%')
                    ->where('content', 'NOT LIKE', '%期間外延長のお申込み（2位）%')
                    ->where('content', 'NOT LIKE', '%終了%')
                    ->orderBy('id', SORT_TYPE_DESC)->first();

                //create notice detail show on U-000top
                $noticeDetailShowOnU00top = $this->noticeDetailService->create(
                    [
                        'notice_id' => $notice->id,
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => '',
                        'redirect_page' => $lastNoticeDetail->redirect_page,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => NoticeDetail::IS_ACTION_TRUE,
                        'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                        'response_deadline' => isset($trademark->response_deadline) && $trademark->response_deadline
                            ? Carbon::createFromFormat('Y-m-d H:i:s', $trademark->response_deadline)->addMonths(2)
                            : null,
                        'content' => $lastNoticeDetail->content . '終了',
                        'created_at' => now()
                    ]
                );

                //create notice detail show on U-000anken_top
                $this->noticeDetailService->create([
                    'notice_id' => $notice->id,
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => '',
                    'redirect_page' => $lastNoticeDetail->redirect_page,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                    'response_deadline' => isset($trademark->response_deadline) && $trademark->response_deadline
                        ? Carbon::createFromFormat('Y-m-d H:i:s', $trademark->response_deadline)->addMonths(2)
                        : null,
                    'content' => $lastNoticeDetail->content . '終了',
                    'created_at' => now()
                ]);

                //update trademark
                Trademark::find($trademark->id)->update(['block_by' => OVER_05]);

                //insert trademark_renewal_notice
                $this->trademarkRenewalNoticeRepository->create([
                    'trademark_id' => $trademark->id,
                    'notice_detail_id' => $noticeDetailShowOnU00top->id,
                    'pattern' => TrademarkRenewalNotice::PATTERN_NEVER_RENEWED,
                    'type' => TrademarkRenewalNotice::TYPE_4,
                    'is_send_notice' => TrademarkRenewalNotice::IS_SEND_NOTICE_TRUE,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }

    /**
     * First renewal deadline has expired.
     */
    protected function firstRenewalDeadlineExpired()
    {
        try {
            DB::beginTransaction();
            $trademarks = DB::select('
                SELECT
                    ctr.id as ctr_id,
                    ctr.trademark_id,
                    ctr.response_deadline,
                    trademarks.*
                FROM trademarks
                    JOIN comparison_trademark_results AS ctr ON ctr.trademark_id = trademarks.id
                    WHERE trademarks.id NOT IN (
                        SELECT trademark_renewal_notices.trademark_id FROM trademark_renewal_notices WHERE trademark_renewal_notices.trademark_id = trademarks.id
                        AND trademark_renewal_notices.pattern = '. TrademarkRenewalNotice::PATTERN_NEVER_RENEWED .'
                        AND trademark_renewal_notices.`type` = '. TrademarkRenewalNotice::TYPE_2 .'
                    )
                    AND ctr.response_deadline > NOW()
                    AND (DATEDIFF(ctr.response_deadline, NOW()) <= 3 AND SUBDATE(SUBDATE(ctr.response_deadline, INTERVAL 3 DAY), INTERVAL 12 HOUR) < NOW())
                    AND TIMEDIFF(ctr.response_deadline, NOW()) >= 0
                    AND trademarks.id NOT IN (
                        SELECT trademarks.id FROM trademarks
                            JOIN register_trademark_renewals ON trademarks.id = register_trademark_renewals.trademark_id
                            WHERE register_trademark_renewals.deleted_at IS NULL
                    )
                    AND trademarks.id NOT IN (
                        SELECT trademarks.id FROM trademarks
                            JOIN register_trademarks ON trademarks.id = register_trademarks.trademark_id
                            WHERE register_trademarks.deleted_at IS NULL
                        )
                    AND trademarks.is_refusal = ' . Trademark::IS_REFUSAL_NOT_REFUSAL . '
                    AND trademarks.deleted_at IS NULL
                    AND ctr.deleted_at IS NULL
            ');

            foreach ($trademarks as $key => $trademark) {
                // update notice detail of last notice
                $notice = $this->noticeService->findByCondition([
                    'trademark_id' => $trademark->id,
                ])->whereIn('flow', [
                    Notice::FLOW_RESPONSE_REASON,
                    Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                ])->orderBy('id', SORT_TYPE_DESC)->with('noticeDetails')->first();

                $noticeDetails = $notice->noticeDetails ?? collect([]);
                $noticeDetails->where('type_acc', NoticeDetail::TYPE_USER)
                    ->where('target_id', $trademark->user_id)
                    ->where('type_page', NoticeDetail::TYPE_PAGE_TOP)
                    ->where('is_action', NoticeDetail::IS_ACTION_TRUE)
                    ->where('is_answer', NoticeDetail::IS_NOT_ANSWER)
                    ->map(function ($item) {
                        $item->update([
                            'is_answer' => NoticeDetail::IS_ANSWER,
                        ]);
                    });

                //create notice
                $notice = $this->noticeService->create([
                    'trademark_id' => $trademark->id,
                    'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                    'user_id' => $trademark->user_id,
                    'trademark_info_id' => null,
                    'created_at' => now()
                ]);

                // Get Last Notice Detail
                $lastNoticeDetail = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_id' => $trademark->user_id,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                ])->whereHas('notice', function ($query) use ($trademark) {
                    $query->whereIn('flow', [
                        Notice::FLOW_RESPONSE_REASON,
                        Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
                    ])->where('trademark_id', $trademark->id);
                })->where('content', 'NOT LIKE', '%期限日前期間延長のお申込み%')
                    ->where('content', 'NOT LIKE', '%期間外延長のお申込み%')
                    ->where('content', 'NOT LIKE', '%期間外延長のお申込み（2位）%')
                    ->where('content', 'NOT LIKE', '%終了%')
                    ->orderBy('id', SORT_TYPE_DESC)->first();

                //create notice detail show on U-000top
                $noticeDetailShowOnU00top = $this->noticeDetailService->create(
                    [
                        'notice_id' => $notice->id,
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => '',
                        'redirect_page' => $lastNoticeDetail->redirect_page,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => NoticeDetail::IS_ACTION_TRUE,
                        'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                        'response_deadline' => isset($trademark->response_deadline) && $trademark->response_deadline
                            ? Carbon::createFromFormat('Y-m-d H:i:s', $trademark->response_deadline)
                            : null,
                        'content' => $lastNoticeDetail->content . '期間外延長のお申込み',
                        'created_at' => now()
                    ]
                );

                //create notice detail show on U-000anken_top
                $this->noticeDetailService->create([
                    'notice_id' => $notice->id,
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => '',
                    'redirect_page' => $lastNoticeDetail->redirect_page,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                    'response_deadline' => isset($trademark->response_deadline) && $trademark->response_deadline
                        ? Carbon::createFromFormat('Y-m-d H:i:s', $trademark->response_deadline)
                        : null,
                    'content' => $lastNoticeDetail->content . '期間外延長のお申込み',
                    'created_at' => now()
                ]);

                //update trademark
                Trademark::find($trademark->id)->update(['block_by' => OVER_04]);

                //insert trademark_renewal_notice
                $this->trademarkRenewalNoticeRepository->create([
                    'trademark_id' => $trademark->id,
                    'notice_detail_id' => $noticeDetailShowOnU00top->id,
                    'pattern' => TrademarkRenewalNotice::PATTERN_NEVER_RENEWED,
                    'type' => TrademarkRenewalNotice::TYPE_2,
                    'is_send_notice' => TrademarkRenewalNotice::IS_SEND_NOTICE_TRUE,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }
}
