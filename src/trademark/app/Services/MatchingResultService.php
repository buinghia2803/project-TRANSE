<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\MatchingResult;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Models\RegisterTrademark;
use App\Models\Trademark;
use App\Repositories\MatchingResultRepository;
use App\Repositories\NoticeDetailRepository;
use Illuminate\Support\Carbon;
use App\Services\Common\NoticeService;
use Exception;
use Illuminate\Support\Facades\Log;

class MatchingResultService extends BaseService
{
    protected MatchingResultRepository  $matchingResultRepository;
    protected NoticeDetailRepository    $noticeDetailRepository;
    protected NoticeService             $noticeService;

    /**
     * Initializing the instances and variables
     *
     * @param MatchingResultRepository $matchingResultRepository
     */
    public function __construct(
        MatchingResultRepository $matchingResultRepository,
        NoticeDetailRepository $noticeDetailRepository,
        NoticeService $noticeService
    )
    {
        $this->repository = $matchingResultRepository;
        $this->noticeDetailRepository = $noticeDetailRepository;
        $this->noticeService = $noticeService;
    }

    /**
     * Get Data Matching Result
     *
     * @param  int $id
     * @return array
     */
    public function getDataMatchingResult($id): array
    {
        $matchingResult = $this->find($id);
        $matchingResult->load('trademark', 'comparisonTrademarkResult');
        $trademark = $matchingResult->trademark;
        $trademark->load('appTrademark.trademarkInfo', 'registerTrademark');
        $trademarkInfo = $trademark->appTrademark->trademarkInfo->sortByDesc('id')->first();
        $registerTrademark = $trademark->registerTrademark;
        if ($trademarkInfo) {
            $trademarkInfo->load('mNation', 'mPrefecture');
        }
        if ($registerTrademark) {
            $registerTrademark->load('mNation');
        }
        $data = [
            'trademark_id' => $trademark->id ?? '',
            'trademark_info_id' => $trademarkInfo->id ?? '',
            'register_trademark_id' => $registerTrademark->id ?? '',
            'comparison_trademark_result_id' => $matchingResult->comparisonTrademarkResult->id ?? '',
            'matching_result_id' => $matchingResult->id ?? '',
            'user_id' => $trademark->user_id ?? '',
            'trademark_info_name' => $trademarkInfo->name ?? '',
            'nation_name' => $trademarkInfo->mNation->name ?? '',
            'nation_id' => $trademarkInfo->m_nation_id ?? '',
            'prefectures_name' => $trademarkInfo->mPrefecture->name ?? '',
            'address_second' => $trademarkInfo->address_second ?? '',
            'address_three' => $trademarkInfo->address_three ?? '',
            'regist_cert_nation_name' => $registerTrademark->mNation->name ?? '',
            'regist_cert_postal_code' => $registerTrademark->regist_cert_postal_code ?? '',
            'regist_cert_address' => $registerTrademark->regist_cert_address ?? '',
            'regist_cert_payer_name' => $registerTrademark->regist_cert_payer_name ?? '',
            'register_number' => $registerTrademark->register_number ?? '',
            'pi_dd_date' => isset($matchingResult->pi_dd_date) ? Carbon::parse($matchingResult->pi_dd_date)->format('Y-m-d') : '',
            'checkDisabled' => (isset($registerTrademark) && $registerTrademark->is_update_info_register == RegisterTrademark::IS_REGISTER) ? true : false
        ];

        return $data;
    }

    /**
     * Send notice for A302 screen when has submit action
     */
    public function sendNoticeA302(Trademark $trademark, MatchingResult $matchingResult)
    {
        try {
            $this->noticeDetailRepository->findByCondition([
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'completion_date' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            ])->with('notice', function ($query) use ($trademark) {
                $query->where('trademark_id', $trademark->id)
                    ->where('user_id', $trademark->user_id)
                    ->where('flow', Notice::FLOW_REGISTER_TRADEMARK);
            })->whereHas('notice', function ($query) use ($trademark) {
                $query->where('trademark_id', $trademark->id)
                    ->where('user_id', $trademark->user_id)
                    ->where('flow', Notice::FLOW_REGISTER_TRADEMARK);
            })->update([
                'completion_date' => now(),
            ]);

            $jimu = Admin::where('role', ROLE_OFFICE_MANAGER)->first();

            $notice = [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_REGISTER_TRADEMARK,
            ];

            // Set content
            $registerTrademark = $trademark->registerTrademark ?? null;
            $content = '事務担当　登録査定：商標登録料納付書提出作業中';
            if (!empty($registerTrademark) && $registerTrademark->isChangeStatus()) {
                $content = '事務担当　登録査定：手続補正書(内容)提出作業中';
            }

            $noticeDetails = [
                // A-000top
                [
                    'target_id' => $jimu->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('admin.registration.document', ['id' => $matchingResult->id, 'register_trademark_id' => $trademark->registerTrademark->id]),
                    'redirect_page' => route('admin.application-detail.index', ['id' => $trademark->id]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action ' => true,
                    'content' => $content,
                ],
                // A000Anken_top
                [
                    'target_id' => $jimu->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('admin.registration.document', ['id' => $matchingResult->id, 'register_trademark_id' => $trademark->registerTrademark->id]),
                    'redirect_page' => route('admin.application-detail.index', ['id' => $trademark->id]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action ' => true,
                    'attribute ' => '特許庁へ',
                    'content' => $content,
                    'buttons' => [
                        [
                            'btn_type' => NoticeDetailBtn::BTN_CREATE_HTML,
                            'from_page' => A302,
                            'url' => route('admin.registration.document', ['id' => $matchingResult->id, 'register_trademark_id' => $trademark->registerTrademark->id]),
                        ],
                    ],
                ],
            ];

            $this->noticeService->sendNotice([
                'notices' => $notice,
                'notice_details' => $noticeDetails,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            throw new Exception($e->getMessage());
        }
    }
}
