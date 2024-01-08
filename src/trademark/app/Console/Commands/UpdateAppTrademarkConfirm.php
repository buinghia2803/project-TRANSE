<?php

namespace App\Console\Commands;

use App\Models\AppTrademark;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Services\AppTrademarkService;
use App\Services\NoticeDetailService;
use App\Services\Common\NoticeService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class UpdateAppTrademarkConfirm extends Command
{
    protected NoticeService $noticeService;
    protected NoticeDetailService $noticeDetailService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-app-trademark-confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $appTrademarkService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        AppTrademarkService $appTrademarkService,
        NoticeService $noticeService,
        NoticeDetailService $noticeDetailService
    )
    {
        parent::__construct();
        $this->appTrademarkService = $appTrademarkService;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appTrademarks = $this->appTrademarkService
            ->findByCondition(['status' => AppTrademark::STATUS_WAITING_FOR_USER_CONFIRM])
            ->with('trademark')
            ->where('cancellation_deadline', '!=', null)
            ->where('cancellation_deadline', '<', now()->format('Y-m-d H:i:s'))
            ->where('is_cancel', false)
            ->get();

        foreach ($appTrademarks as $appTrademark) {
            $trademark = $appTrademark->trademark;
            $deadlineCancel = Carbon::parse($appTrademark->cancellation_deadline)->format('YmdHis');
            $now = Carbon::now()->format('YmdHis');
            $parseUrl = parse_url(route('admin.apply-trademark-document-to-check', ['id' => $appTrademark->trademark_id]));
            $url = $parseUrl['path'] . '?type=view';

            if ($now > $deadlineCancel) {
                $params['status'] = AppTrademark::STATUS_ADMIN_CONFIRM;

                // Update notice old no 105
                $stepNoticeBefore = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                ])->with('notice')->get()
                    ->where('notice.trademark_id', $trademark->id)
                    ->where('notice.user_id', $trademark->user_id)
                    ->where('notice.flow', Notice::FLOW_APP_TRADEMARK);
                $stepNoticeBefore->map(function ($item) {
                    $item->update([
                        'is_answer' => NoticeDetail::IS_ANSWER,
                    ]);
                });

                $this->appTrademarkService->update($appTrademark, $params);

                $this->noticeService->sendNotice([
                    'notices' => [
                        'trademark_id' => $appTrademark->trademark_id,
                        'user_id' => $appTrademark->trademark->user_id,
                        'flow' => Notice::FLOW_APP_TRADEMARK
                    ],
                    'notice_details' => [
                        // Send Notice Jimu
                        [
                            'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                            'target_page' => route('user.apply-trademark.confirm', ['id' => $appTrademark->trademark_id]),
                            'redirect_page' => route('admin.application-detail.index', ['id' => $appTrademark->trademark_id]),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'is_action' => NoticeDetail::IS_ACTION_TRUE,
                            'content' => '出願：提出書類提出作業中'
                        ],
                        // Send Notice Jimu
                        [
                            'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                            'target_page' => route('user.apply-trademark.confirm', ['id' => $appTrademark->trademark_id]),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'content' => '出願：提出書類提出作業中',
                            'attribute' => '特許庁へ',
                            'buttons' => [
                                [
                                    "btn_type"  => NoticeDetailBtn::BTN_CREATE_HTML,
                                    "url"  => $url,
                                    "from_page" => FROM_PAGE_U032,
                                ],
                            ],
                        ],
                        // Send noti user
                        [
                            'target_id' => $appTrademark->trademark->user_id,
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'target_page' => route('user.apply-trademark.confirm', ['id' => $appTrademark->trademark_id]),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'content' => '出願：提出作業中',
                        ],
                        [
                            'target_id' => $appTrademark->trademark->user_id,
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'target_page' => route('user.apply-trademark.confirm', ['id' => $appTrademark->trademark_id]),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'content' => '出願：提出作業中',
                        ],
                    ],
                ]);
            }
        }
    }
}
