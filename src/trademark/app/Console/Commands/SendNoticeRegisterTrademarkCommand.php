<?php

namespace App\Console\Commands;

use App\Helpers\CommonHelper;
use App\Models\MailTemplate;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\RegisterTrademark;
use App\Models\Trademark;
use App\Services\MailTemplateService;
use App\Services\NoticeDetailService;
use App\Services\NoticeService;
use App\Services\TrademarkService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendNoticeRegisterTrademarkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-notice-register-trademark';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notice register trademark';

    protected NoticeService $noticeService;
    protected NoticeDetailService $noticeDetailService;
    protected TrademarkService $trademarkService;
    protected MailTemplateService $mailTemplateService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        NoticeService $noticeService,
        NoticeDetailService $noticeDetailService,
        TrademarkService $trademarkService,
        MailTemplateService $mailTemplateService
    )
    {
        parent::__construct();
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->trademarkService = $trademarkService;
        $this->mailTemplateService = $mailTemplateService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $trademarks = Trademark::query()->whereHas('registerTrademarks', function ($query) {
                $query->where('is_register', RegisterTrademark::IS_REGISTER);
            })->get();
            foreach ($trademarks as $trademark) {
                $registerTrademarkIsRegisterTrues = $trademark->registerTrademarks;
                $registerTrademarkNew = $registerTrademarkIsRegisterTrues->last();
                $typePage = $registerTrademarkNew->type_page;
                if (count($registerTrademarkIsRegisterTrues) > 0 && $registerTrademarkNew->is_cancel == RegisterTrademark::IS_NOT_CANCEL) {
                    if (in_array($typePage, [RegisterTrademark::TYPE_PAGE_1, RegisterTrademark::TYPE_PAGE_3])
                        && $registerTrademarkNew->period_registration == RegisterTrademark::PERIOD_REGISTRATION_5_YEAR
                    ) {
                        $titleFunctionNotice = 'sendNoticeU302402';
                        $this->updateRegisterTrademark($registerTrademarkNew, $titleFunctionNotice);
                    } elseif ($typePage == RegisterTrademark::TYPE_PAGE_2
                        && $registerTrademarkNew->period_registration == RegisterTrademark::PERIOD_REGISTRATION_5_YEAR
                        || in_array($typePage, [RegisterTrademark::TYPE_PAGE_1, RegisterTrademark::TYPE_PAGE_3])
                        && $registerTrademarkNew->period_registration == RegisterTrademark::PERIOD_REGISTRATION_10_YEAR
                    ) {
                        $titleFunctionNotice = 'sendNoticeU402';
                        $this->updateRegisterTrademark($registerTrademarkNew, $titleFunctionNotice);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back();
        }
    }

    /**
     * Update register trademark.
     */
    protected function updateRegisterTrademark($registerTrademark, $titleFunctionNotice)
    {
        $deadLineUpdate = Carbon::parse($registerTrademark->deadline_update)->toDateString();
        $now = Carbon::parse(now()->format('Y-m-d'))->toDateString();
        $typeNotice = $registerTrademark->type_notices;
        $deadlineUpdateSubFourMonth = Carbon::parse($registerTrademark->deadline_update)->subMonths(4)->toDateString();
        $deadlineUpdateSubSixMonth = Carbon::parse($registerTrademark->deadline_update)->subMonths(6)->toDateString();
        $deadlineUpdateSubTwoMonth = Carbon::parse($registerTrademark->deadline_update)->subMonths(2)->toDateString();
        $deadlineUpdateSubOneMonth = Carbon::parse($registerTrademark->deadline_update)->subMonths(1)->toDateString();
        $deadlineUpdateSubFourteenDays = Carbon::parse($registerTrademark->deadline_update)->subDays(14)->toDateString();

        $trademark = $registerTrademark->trademark ?? null;
        $user = $trademark->user ?? null;

        // Set FromPage
        $fromPage = null;
        if ($titleFunctionNotice == 'sendNoticeU302402') {
            $fromPage = U302_402_5YR_KOUKI;
        } elseif ($titleFunctionNotice == 'sendNoticeU402') {
            $fromPage = U402;
        }

        if ($typeNotice == null && $now == $deadlineUpdateSubSixMonth) {
            $registerTrademark->update([
                'type_notices' => RegisterTrademark::TYPE_NOTICE_1,
            ]);
            $this->{$titleFunctionNotice}($registerTrademark);
        } elseif ($typeNotice == RegisterTrademark::TYPE_NOTICE_1 && $now == $deadlineUpdateSubFourMonth) {
            $registerTrademark->update([
                'type_notices' => RegisterTrademark::TYPE_NOTICE_2,
            ]);
            // $this->{$titleFunctionNotice}($registerTrademark);

            $mailData = [
                'from_page' => $fromPage,
                'user' => $user
            ];
        } elseif ($typeNotice == RegisterTrademark::TYPE_NOTICE_2 && $now == $deadlineUpdateSubTwoMonth) {
            $registerTrademark->update([
                'type_notices' => RegisterTrademark::TYPE_NOTICE_3,
            ]);
            // $this->{$titleFunctionNotice}($registerTrademark);

            $mailData = [
                'from_page' => $fromPage,
                'user' => $user
            ];
        } elseif ($typeNotice == RegisterTrademark::TYPE_NOTICE_3 && $now == $deadlineUpdateSubOneMonth) {
            $registerTrademark->update([
                'type_notices' => RegisterTrademark::TYPE_NOTICE_4,
            ]);
            // $this->{$titleFunctionNotice}($registerTrademark);

            $mailData = [
                'from_page' => $fromPage,
                'user' => $user
            ];
        } elseif ($typeNotice == RegisterTrademark::TYPE_NOTICE_4 && $now == $deadlineUpdateSubFourteenDays) {
            $registerTrademark->update([
                'type_notices' => RegisterTrademark::TYPE_NOTICE_5,
            ]);
            // $this->{$titleFunctionNotice}($registerTrademark);

            $mailData = [
                'from_page' => $fromPage,
                'user' => $user
            ];
        } elseif ($typeNotice == RegisterTrademark::TYPE_NOTICE_5 && $now == $deadLineUpdate) {
            $registerTrademark->update([
                'type_notices' => RegisterTrademark::TYPE_NOTICE_6,
            ]);
            $this->{$titleFunctionNotice . 'Tsuino'}($registerTrademark);
        }

        if (!empty($mailData)) {
            $this->mailTemplateService->sendMailRequest($mailData, MailTemplate::TYPE_REMIND_JOB, MailTemplate::GUARD_TYPE_ADMIN);
        }
    }

    /**
     * Send notice u302xxx.
     */
    protected function sendNoticeU302402($registerTrademark)
    {
        $trademark = $this->trademarkService->find($registerTrademark->trademark_id);

        $noticeStepBefores = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->whereHas('notice', function ($query) use ($trademark) {
            $query->where('trademark_id', $trademark->id)
                ->where('user_id', $trademark->user_id)
                ->whereIn('flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS]);
        })->get();
        $noticeStepBefores->map(function ($item) {
            $item->update([
              'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $notice = $this->noticeService->create([
           'trademark_id' => $trademark->id,
           'user_id' => $trademark->user_id,
           'flow' => Notice::FLOW_RENEWAL
        ]);

        $redirectPage = str_replace(request()->root(), '', route('user.registration.notice-latter-period', ['id' => $registerTrademark->id]));

        $this->noticeDetailService->create([
           'notice_id' => $notice->id,
           'target_id' => $trademark->user_id,
           'type_acc' => NoticeDetail::TYPE_USER,
           'target_page' => 'U302_402_5yr_kouki',
           'redirect_page' => $redirectPage,
           'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
           'type_page' => NoticeDetail::TYPE_PAGE_TOP,
           'is_action' => NoticeDetail::IS_ACTION_TRUE,
           'content' => '後期納付期限のお知らせ・納付手続きのお申込',
           'response_deadline' => $registerTrademark->deadline_update,
        ]);
        $this->noticeDetailService->create([
            'notice_id' => $notice->id,
            'target_id' => $trademark->user_id,
            'type_acc' => NoticeDetail::TYPE_USER,
            'target_page' => 'U302_402_5yr_kouki',
            'redirect_page' => $redirectPage,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
            'content' => '後期納付期限のお知らせ・納付手続きのお申込',
            'response_deadline' => $registerTrademark->deadline_update,
        ]);
    }

    /**
     * Send notice u302xxxTsuino.
     */
    protected function sendNoticeU302402Tsuino($registerTrademark)
    {
        $trademark = $this->trademarkService->find($registerTrademark->trademark_id);
        $noticeStepBefores = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->whereHas('notice', function ($query) use ($trademark) {
            $query->where('trademark_id', $trademark->id)
                ->where('user_id', $trademark->user_id)
                ->whereIn('flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS]);
        })->get();
        $noticeStepBefores->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $deadlineUpdateAddSixMonth = Carbon::parse($registerTrademark->deadline_update)->addMonths(6);

        $notice = $this->noticeService->create([
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RENEWAL
        ]);
        $redirectPage = str_replace(request()->root(), '', route('user.registration.notice-latter-period.overdue', ['id' => $registerTrademark->id]));
        $this->noticeDetailService->create([
            'notice_id' => $notice->id,
            'target_id' => $trademark->user_id,
            'type_acc' => NoticeDetail::TYPE_USER,
            'target_page' => 'U302_402_5yr_tsuino_kouki',
            'redirect_page' => $redirectPage,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
            'is_action' => NoticeDetail::IS_ACTION_TRUE,
            'content' => '期限経過後納付サービス・納付手続きのお申込み',
            'response_deadline' => $deadlineUpdateAddSixMonth,
        ]);
        $this->noticeDetailService->create([
            'notice_id' => $notice->id,
            'target_id' => $trademark->user_id,
            'type_acc' => NoticeDetail::TYPE_USER,
            'target_page' => 'U302_402_5yr_tsuino_kouki',
            'redirect_page' => $redirectPage,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
            'content' => '期限経過後納付サービス・納付手続きのお申込み',
            'response_deadline' => $deadlineUpdateAddSixMonth,
        ]);
    }

    /**
     * Send notice u402.
     */
    protected function sendNoticeU402($registerTrademark)
    {
        $trademark = $this->trademarkService->find($registerTrademark->trademark_id);
        $noticeStepBefores = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->whereHas('notice', function ($query) use ($trademark) {
            $query->where('trademark_id', $trademark->id)
                ->where('user_id', $trademark->user_id)
                ->whereIn('flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS]);
        })->get();
        $noticeStepBefores->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });
        $deadlineUpdate = $registerTrademark->deadline_update;
        $notice = $this->noticeService->create([
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RENEWAL
        ]);

        $this->noticeDetailService->create([
            'notice_id' => $notice->id,
            'target_id' => $trademark->user_id,
            'type_acc' => NoticeDetail::TYPE_USER,
            'target_page' => 'U402',
            'redirect_page' => '/update/notify-procedure/' . $registerTrademark->id,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
            'is_action' => NoticeDetail::IS_ACTION_TRUE,
            'content' => '更新期限のお知らせ・更新手続きのお申込み',
            'response_deadline' => $deadlineUpdate,
        ]);
        $this->noticeDetailService->create([
            'notice_id' => $notice->id,
            'target_id' => $trademark->user_id,
            'type_acc' => NoticeDetail::TYPE_USER,
            'target_page' => 'U402',
            'redirect_page' => '/update/notify-procedure/' . $registerTrademark->id,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
            'content' => '更新期限のお知らせ・更新手続きのお申込み',
            'response_deadline' => $deadlineUpdate,
        ]);
    }

    /**
     * Send notice u402tsuino.
     */
    protected function sendNoticeU402Tsuino($registerTrademark)
    {
        $trademark = $this->trademarkService->find($registerTrademark->trademark_id);
        $noticeStepBefores = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->whereHas('notice', function ($query) use ($trademark) {
            $query->where('trademark_id', $trademark->id)
                ->where('user_id', $trademark->user_id)
                ->whereIn('flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS]);
        })->get();
        $noticeStepBefores->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $deadlineUpdateAddSixMonth = Carbon::parse($registerTrademark->deadline_update)->addMonths(6);
        $notice = $this->noticeService->create([
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RENEWAL
        ]);

        $this->noticeDetailService->create([
            'notice_id' => $notice->id,
            'target_id' => $trademark->user_id,
            'type_acc' => NoticeDetail::TYPE_USER,
            'target_page' => 'U402',
            'redirect_page' => '/update/notify-procedure/overdue/' . $registerTrademark->id,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
            'is_action' => NoticeDetail::IS_ACTION_TRUE,
            'content' => '期限経過後更新サービス・更新手続きのお申込み',
            'response_deadline' => $deadlineUpdateAddSixMonth,
        ]);
        $this->noticeDetailService->create([
            'notice_id' => $notice->id,
            'target_id' => $trademark->user_id,
            'type_acc' => NoticeDetail::TYPE_USER,
            'target_page' => 'U402',
            'redirect_page' => '/update/notify-procedure/overdue/' . $registerTrademark->id,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
            'content' => '期限経過後更新サービス・更新手続きのお申込み',
            'response_deadline' => $deadlineUpdateAddSixMonth,
        ]);
    }
}
