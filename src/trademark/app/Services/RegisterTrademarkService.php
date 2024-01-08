<?php

namespace App\Services;

use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Models\RegisterTrademark;
use App\Repositories\MProductRepository;
use App\Repositories\NoticeDetailRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\RegisterTrademarkRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Common\NoticeService;
use Illuminate\Support\Str;

class RegisterTrademarkService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param RegisterTrademarkRepository $registerTrademarkRepository
     * @param NoticeDetailRepository $noticeDetailRepository
     * @param NoticeService $noticeService
     * @param PaymentRepository $paymentRepository
     * @param MProductRepository $mProductRepository
     */
    public function __construct(
        RegisterTrademarkRepository $registerTrademarkRepository,
        NoticeDetailRepository $noticeDetailRepository,
        NoticeService $noticeService,
        PaymentRepository $paymentRepository,
        MProductRepository $mProductRepository
    )
    {
        $this->repository = $registerTrademarkRepository;
        $this->noticeDetailRepository = $noticeDetailRepository;
        $this->noticeService = $noticeService;
        $this->paymentRepository = $paymentRepository;
        $this->mProductRepository = $mProductRepository;
    }

    /**
     * Get Product Trademark
     *
     * @param  mixed $id
     * @return Collection
     */
    public function getProductTrademark($id): Collection
    {
        return $this->repository->getProductTrademark($id);
    }

    /**
     * Format Data Notice Detail
     *
     * @param Collection $data
     * @return  Collection
     */
    public function formatListUser(Collection $data): Collection
    {
        $data = $data->load([
            'trademark'
        ]);

        $data->map(function ($item) {
            // Deadline update
            $deadlineUpdate = $item->deadline_update ?? null;
            $deadlineUpdateBefore1Day = null;
            if (!empty($deadlineUpdate)) {
                $deadlineUpdate = Carbon::parse($deadlineUpdate);
                $deadlineUpdateBefore1Day = $deadlineUpdate->subDays(1)->format('Y/m/d');
            }
            $item->deadline_update_before_1_day = $deadlineUpdateBefore1Day;

            return $item;
        });

        return $data;
    }

    /**
     * Get Register Trademark Of User
     *
     * @param int $registerTrademarkId
     * @return mixed
     */
    public function getRegisterTrademarkOfUser(int $registerTrademarkId)
    {
        return $this->repository->getRegisterTrademarkOfUser($registerTrademarkId);
    }

    /**
     * Cancel Trademark Post
     *
     * @param $registerTrademark
     * @return false|\Illuminate\Database\Eloquent\Model
     */
    public function cancelTrademarkPost($registerTrademark)
    {
        try {
            DB::beginTransaction();
            $registerTrademark = $this->repository->update($registerTrademark, [
                'is_cancel' => RegisterTrademark::IS_CANCEL,
            ]);
            $user = auth()->user();
            //send notice
            //1.update notice
            $this->noticeDetailRepository->findByCondition([
                'type_acc' => NoticeDetail::TYPE_USER,
                'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            ], ['notice'])->get()
                ->where('notice.trademark_id', $registerTrademark->trademark_id)
                ->where('notice.user_id', auth()->user()->id)
                ->whereIn('flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS])
                ->map(function ($item) {
                    $item->update([
                        'is_answer' => NoticeDetail::IS_ANSWER,
                    ]);
                });

            // Send Notice Seki
            $this->noticeService->sendNotice([
                'notices' => [
                    'trademark_id' => $registerTrademark->trademark_id,
                    'user_id' => auth()->user()->id,
                    'flow' => Notice::FLOW_RENEWAL,
                ],
                'notice_details' => [
                    //A000anken_top
                    [
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => route('user.register-trademark.cancel-trademark', $registerTrademark->id),
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '更新手続き：不要指示受領',
                        'attribute' => 'お客様から',
                        'completion_date' => now(),
                    ],
                    //U-000top
                    [
                        'target_id' => $user->id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => route('user.registration.notice-latter-period', $registerTrademark->id),
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '更新手続き：依頼しない',
                    ],
                    //U-000anken_top
                    [
                        'target_id' => $user->id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => route('user.registration.notice-latter-period', $registerTrademark->id),
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '更新手続き：依頼しない',
                    ],
                ],
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
     * Post Regis Procedure LatterPeriodDocument
     *
     * @param array $inputs
     * @return void
     */
    public function postRegisProcedureLatterPeriodDocument(array $inputs)
    {
        try {
            DB::commit();
            $registerTrademark = $inputs['registerTrademark'];
            $dataUpdate = [
                'is_send' => RegisterTrademark::IS_SEND,
            ];
            $dataUpdate['is_register_change_info'] = $inputs['is_register_change_info'] ?? RegisterTrademark::IS_NOT_REGISTER_CHANGE_INFO;
            $this->repository->update($registerTrademark, $dataUpdate);
            //send notice

            //update is_answer
            $stepBeforeNotice = $this->noticeDetailRepository->findByCondition([
                'completion_date' => null,
            ])->with('notice')->get()
                ->where('notice.trademark_id', $registerTrademark->trademark_id)
                ->where('notice.user_id', $registerTrademark->trademark->user_id)
                ->where('notice.flow', Notice::FLOW_RENEWAL);
            $stepBeforeNotice->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });

            $targetPage = route('admin.registration.procedure-latter-period.document', $registerTrademark->id);
            $redirectRoute = route('admin.application-detail.index', $registerTrademark->trademark_id);
            //insert notice
            $this->noticeService->sendNotice([
                'notices' => [
                    'trademark_id' => $registerTrademark->trademark_id,
                    'user_id' => $registerTrademark->trademark->user_id,
                    'flow' => Notice::FLOW_RENEWAL,
                ],
                'notice_details' => [
                    //A000top
                    [
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => $targetPage,
                        'redirect_page' => $redirectRoute,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => NoticeDetail::IS_ANSWER,
                        'content' => '事務担当　後期納付手続き：【商標登録料納付書】作成',
                    ],
                    //A000anken_top
                    [
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => $targetPage,
                        'redirect_page' => $redirectRoute,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '事務担当　後期納付手続き：【商標登録料納付書】作成',
                        'attribute' => '所内処理',
                        'buttons' => [
                            [
                                "btn_type"  => NoticeDetailBtn::BTN_CREATE_HTML,
                                "url"  => $targetPage . '?type=' . VIEW,
                                'from_page' => A302_402_5YR_KOUKI,
                            ],
                        ]
                    ],
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Cancel Trademark Post
     *
     * @param $registerTrademark
     * @return false|\Illuminate\Database\Eloquent\Model
     */
    public function sendNoticeOfA402hosoku02($registerTrademark)
    {
        $trademark = $registerTrademark->trademark;
        $NoticeBefore = $this->noticeDetailRepository->findByCondition([
            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
            'completion_date' => null,
        ])->whereHas('notice', function ($query) use ($trademark) {
            $query->whereIn('flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS])
                 ->where('trademark_id', $trademark->id)
                 ->where('user_id', $trademark->user_id);
        })->get();
        $NoticeBefore->map(function ($item) {
            $item->update([
               'completion_date' => now(),
            ]);
        });

        // Send Notice Seki
        $this->noticeService->sendNotice([
            'notices' => [
                'trademark_id' => $trademark->id,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_RENEWAL,
            ],
            'notice_details' => [
                //A000top
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('admin.update.document.modification.product.document', $registerTrademark->id),
                    'redirect_page' => route('admin.application-detail.index', $trademark->id),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'content' => '事務担当　更新手続き：【商標権存続期間更新登録申請書】付属【補足書】および【委任状】作成',
                ],
                //A000anken_top
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('admin.update.document.modification.product.document', $registerTrademark->id),
                    'redirect_page' => route('admin.application-detail.index', $trademark->id),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'attribute' => '所内処理',
                    'content' => '事務担当　更新手続き：【商標権存続期間更新登録申請書】付属【補足書】および【委任状】作成',
                    'buttons' => [
                        [
                            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                            'from_page' => A402_HOSOKU_02,
                            'url' => route('admin.update.document.modification.product.document', $registerTrademark->id),
                        ],
                    ]
                ],
            ],
        ]);
    }

    /**
     * Post update procedure document post - A402
     *
     * @param array $inputs
     * @return void
     */
    public function updateProcedureDocumentPost(array $inputs)
    {
        try {
            DB::commit();
            $registerTrademark = $inputs['registerTrademark'];
            $dataUpdate = [
                'is_send' => RegisterTrademark::IS_SEND,
            ];
            $this->repository->update($registerTrademark, $dataUpdate);
            //send notice

            //update is_answer
            $stepBeforeNotice = $this->noticeDetailRepository->findByCondition([
                'completion_date' => null,
            ])->with('notice')->get()
                ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
                ->where('notice.trademark_id', $registerTrademark->trademark_id)
                ->where('notice.user_id', $registerTrademark->trademark->user_id)
                ->whereIn('notice.flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS]);
            $stepBeforeNotice->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });

            $targetPage = route('admin.update.procedure.document', $registerTrademark->id);
            $redirectRoute = route('admin.application-detail.index', $registerTrademark->trademark_id);
            $routeA402forSubmit = route('admin.registration.procedure-latter-period.document.submit', $registerTrademark->id);

            //insert notice
            $this->noticeService->sendNotice([
                'notices' => [
                    'trademark_id' => $registerTrademark->trademark_id,
                    'user_id' => $registerTrademark->trademark->user_id,
                    'flow' => Notice::FLOW_RENEWAL,
                ],
                'notice_details' => [
                    //A000top
                    [
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => $targetPage,
                        'redirect_page' => $redirectRoute,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => NoticeDetail::IS_ANSWER,
                        'content' => '事務担当　更新手続き：商標権存続期間更新登録申請書提出作業中',
                    ],

                    //A000anken_top
                    [
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => $targetPage,
                        'redirect_page' => $redirectRoute,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '事務担当　更新手続き：商標権存続期間更新登録申請書提出作業中',
                        'attribute' => '所内処理',
                        'buttons' => [
                            [
                                "btn_type"  => NoticeDetailBtn::BTN_CREATE_HTML,
                                "url"  => $routeA402forSubmit,
                                'from_page' => A402,
                            ],
                        ]
                    ],
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Get Total Amount
     *
     * @param Model $registerTrademark
     * @param int $type
     * @param array $fromPage
     * @param int $round
     * @return float|int
     */
    public function getTotalAmount(Model $registerTrademark, int $type, int $round)
    {
        $totalAmount = 0;
        $fromPage = null;
        if ($registerTrademark) {
            $typeRegis = $registerTrademark->type;
            switch ($typeRegis) {
                //a302_402_5yr_kouki
                case Str::startsWith($typeRegis, U302_402 . '_'):
                    $fromPage = U302_402_5YR_KOUKI. '_'. $round;
                    break;
                //a302_402_5yr_kouki
                case Str::startsWith($typeRegis, U302_402TSUINO . '_'):
                    $fromPage = U302_402TSUINO_5YR_KOUKI. '_'. $round;
                    break;
                //a402 || a402for_submit
                case Str::startsWith($typeRegis, U402 . '_'):
                    $fromPage = U402. '_'. $round;
                    break;
                //a402 || a402for_submit
                case Str::startsWith($typeRegis, U402TSUINO . '_'):
                    $fromPage = U402TSUINO. '_'. $round;
                    break;
            }
            if ($fromPage) {
                $payment = $this->paymentRepository->findByCondition([
                    'target_id' => $registerTrademark->id,
                    'type' => $type,
                    'from_page' => $fromPage,
                ], ['paymentProds'])->first();

                if ($payment && $payment->paymentProds->count() > 0) {
                    $mProductIs = $payment->paymentProds()->pluck('m_product_id')->toArray();
                    $mDistinction = $this->mProductRepository->findByCondition(['ids' => $mProductIs])->pluck('m_distinction_id');
                    $totalDis = (int) $mDistinction->unique()->count();

                    //check 5 year or 10 year
                    $totalAmount += ($payment->cost_print_name ?? 0) + ($payment->cost_print_address ?? 0);
                    if ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_5_YEAR) {
                        $totalAmount += ($payment->cost_5_year_one_distintion ?? 0) * $totalDis;
                    } elseif ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_10_YEAR) {
                        $totalAmount += ($payment->cost_10_year_one_distintion ?? 0) * $totalDis;
                    }
                }
            }
        }

        return $totalAmount;
    }
}
