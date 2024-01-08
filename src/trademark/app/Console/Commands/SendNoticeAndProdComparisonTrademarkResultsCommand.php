<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AppTrademark;
use App\Models\AppTrademarkProd;
use App\Models\ComparisonTrademarkResult;
use App\Models\MatchingResult;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\PlanCorrespondence;
use App\Models\PlanCorrespondenceProd;
use App\Services\Common\NoticeService as CommonNoticeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendNoticeAndProdComparisonTrademarkResultsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-notice:update-send-notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command send notice and prod comparison trademark results';

    protected $commonNoticeService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CommonNoticeService $commonNoticeService)
    {
        parent::__construct();
        $this->commonNoticeService = $commonNoticeService;
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
            $nowDateSubTreeDay = Carbon::now()->subDay(3);

            $comparisonTrademarkResults = ComparisonTrademarkResult::where('created_at', '<', $nowDateSubTreeDay)->doesntHave('planCorrespondence')->get();

            foreach ($comparisonTrademarkResults as $comparisonTrademarkResult) {
                $relation = ComparisonTrademarkResult::find($comparisonTrademarkResult->id)->load(['machingResult', 'trademark.appTrademark.appTrademarkProd']);

                $trademark = $relation->trademark;
                $machingResult = $relation->machingResult;
                $appTrademark = $relation->trademark->appTrademark;
                $appTrademarkProds = $relation->trademark->appTrademark->appTrademarkProd->where('is_apply', AppTrademarkProd::IS_APPLY)->pluck('id');

                $planCorrespondence = PlanCorrespondence::updateOrCreate([
                    'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                    'type' => PlanCorrespondence::TYPE_3,
                ]);

                foreach ($appTrademarkProds as $value) {
                    PlanCorrespondenceProd::updateOrCreate([
                        'plan_correspondence_id' => $planCorrespondence->id,
                        'is_register' => PlanCorrespondenceProd::IS_REGISTER,
                        'app_trademark_prod_id' => $value
                    ]);
                }

                $tanTou = Admin::where('role', Admin::ROLE_ADMIN_TANTO)->first();
                $targetPage = route('user.refusal.plans.pack', ['id' => $comparisonTrademarkResult->id]);
                $responseDeadline = Carbon::parse($machingResult->pi_dd_date)->addMonths($machingResult->pi_tfr_period)->subDays(MatchingResult::MINUS_3_DAY);
                $packAppTrademark = $appTrademark->pack;
                $redirectPage = route('admin.refusal.eval-report.create-reason', ['id' => $comparisonTrademarkResult->id]);

                $noticeDetail = [
                    // A-000top
                    [
                        'target_id' => null,
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $targetPage,
                        'redirect_page' => $redirectPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => true,
                        'content' => '担当者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
                        'response_deadline' => $responseDeadline,
                    ],
                    // A-000anken_top
                    [
                        'target_id' => null,
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $targetPage,
                        'redirect_page' => $redirectPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'attribute' => '所内処理',
                        'content' => '担当者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
                        'response_deadline' => $responseDeadline,
                    ],
                ];

                if ($packAppTrademark == AppTrademark::PACK_C) {
                    $noticeDetail[] = [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '審査結果のお知らせ（拒絶理由通知）、今後の流れのご説明',
                        'response_deadline' => $responseDeadline,
                    ];

                    $noticeDetail[] = [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '審査結果のお知らせ（拒絶理由通知）、今後の流れのご説明',
                        'response_deadline' => $responseDeadline,
                    ];
                }

                $this->commonNoticeService->sendNotice([
                    'notices' => [
                        'trademark_id' => $trademark->id,
                        'trademark_info_id' => null,
                        'user_id' => $trademark->user_id,
                        'flow' => Notice::FLOW_RESPONSE_REASON,
                        'step' => Notice::STEP_1
                    ],
                    'notice_details' => $noticeDetail,
                ]);
            }

            DB::commit();
            $this->info('Cron job request notice and prod to user success');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            $this->info('Cron job request notice and prod to user error');
        }
    }
}
