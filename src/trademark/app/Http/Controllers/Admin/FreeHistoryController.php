<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Helpers\FileHelper;
use App\Jobs\SendGeneralMailJob;
use App\Models\Admin;
use App\Models\FreeHistory;
use App\Models\MailTemplate;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Services\Common\NoticeService;
use App\Services\Common\TrademarkTableService;
use App\Services\FreeHistoryService;
use App\Services\MatchingResultService;
use App\Services\NoticeDetailService;
use App\Services\MailTemplateService;
use App\Services\TrademarkService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FreeHistoryController extends BaseController
{
    private TrademarkService $trademarkService;
    private TrademarkTableService $trademarkTableService;
    private FreeHistoryService $freeHistoryService;
    private MatchingResultService $matchingResultService;
    private NoticeService $noticeService;
    private NoticeDetailService $noticeDetailService;
    private MailTemplateService $mailTemplateService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        TrademarkService $trademarkService,
        TrademarkTableService $trademarkTableService,
        FreeHistoryService $freeHistoryService,
        MatchingResultService $matchingResultService,
        NoticeService $noticeService,
        MailTemplateService $mailTemplateService,
        NoticeDetailService $noticeDetailService
    )
    {
        parent::__construct();

        $this->trademarkService = $trademarkService;
        $this->trademarkTableService = $trademarkTableService;
        $this->freeHistoryService = $freeHistoryService;
        $this->matchingResultService = $matchingResultService;
        $this->noticeService = $noticeService;
        $this->mailTemplateService = $mailTemplateService;
        $this->noticeDetailService = $noticeDetailService;

        // Check permission
        $this->middleware('permission:free_histories.store')->only(['store']);
        $this->middleware('permission:free_histories.update')->only(['update']);
        $this->middleware('permission:free_histories.postReConfirm')->only(['postReConfirm']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param int $id
     * @return View|RedirectResponse
     */
    public function create(Request $request, int $id)
    {
        $trademark = $this->trademarkService->find($id);

        if (empty($trademark)) {
            abort(CODE_ERROR_404);
        }

        $trademark = $trademark->load('freeHistories');
        $freeHistories = $trademark->freeHistories ?? [];
        $isMaxData = count($freeHistories) >= 20;
        $isSubmit = true;
        if ($isMaxData) {
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.free_histories.max_20'));
        }

        // Check last history
        $lastFreeHistories = $freeHistories->last();
        if (!empty($lastFreeHistories) && empty($request->free_history_id) && empty($request->maching_result_id)) {
            if ($lastFreeHistories->flag_role == FreeHistory::FLAG_ROLE_1) {
                return redirect()->route('admin.free-history.create', [
                    'id' => $trademark->id,
                    'free_history_id' => $lastFreeHistories->id,
                ]);
            }
        }

        // Check exist maching result
        if (!empty($request->maching_result_id)) {
            $freeHistoryWithMachingResult = $this->freeHistoryService->findByCondition([
                'maching_result_id' => $request->maching_result_id,
                'flag_role' => FreeHistory::FLAG_ROLE_1,
            ])->first();

            if (!empty($freeHistoryWithMachingResult)) {
                return redirect()->route('admin.free-history.create', [
                    'id' => $trademark->id,
                    'free_history_id' => $freeHistoryWithMachingResult->id,
                ]);
            }
        }

        // Trademark info table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_2, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        // Default Data
        $types = $this->freeHistoryService->types();
        $properties = $this->freeHistoryService->properties();
        $currentAdmin = \Auth::guard('admin')->user();

        // set Free History
        $freeHistoryData = [
            'free_history_id' => null,
            'maching_result_id' => null,
            'type' => FreeHistory::TYPE_1,
            'user_response_deadline' => null,
            'XML_delivery_date' => Carbon::now()->format('Y/m/d'),
            'property' => null,
            'status_name' => null,
            'patent_response_deadline' => null,
            'create_name' => $currentAdmin->name,
            'amount_type' => null,
            'amount' => null,
            'internal_remark' => null,
            'comment' => null,
            'is_cancel' => null,
            'attachment' => [],
        ];

        if (!empty($request->free_history_id)) {
            $freeHistory = $this->freeHistoryService->find($request->free_history_id);
            if (!empty($freeHistory)) {
                $freeHistory = $freeHistory->load(['adminCreate']);

                $freeHistoryData['free_history_id'] = $freeHistory->id;
                $freeHistoryData['type'] = $freeHistory->type;
                $freeHistoryData['user_response_deadline'] = $freeHistory->user_response_deadline ?? $freeHistoryData['user_response_deadline'];
                $freeHistoryData['XML_delivery_date'] = $freeHistory->XML_delivery_date ?? $freeHistoryData['XML_delivery_date'];
                $freeHistoryData['property'] = $freeHistory->property ?? $freeHistoryData['property'];
                $freeHistoryData['status_name'] = $freeHistory->status_name ?? $freeHistoryData['status_name'];
                $freeHistoryData['patent_response_deadline'] = $freeHistory->patent_response_deadline ?? $freeHistoryData['patent_response_deadline'];
                $freeHistoryData['create_name'] = $freeHistory->adminCreate->name ?? $freeHistoryData['create_name'];
                $freeHistoryData['amount_type'] = $freeHistory->amount_type ?? $freeHistoryData['amount_type'];
                $freeHistoryData['amount'] = ($freeHistory->amount_type == FreeHistory::AMOUNT_TYPE_CUSTOM) ? $freeHistory->amount : $freeHistoryData['amount'];
                $freeHistoryData['internal_remark'] = $freeHistory->internal_remark ?? $freeHistoryData['internal_remark'];
                $freeHistoryData['comment'] = $freeHistory->comment ?? $freeHistoryData['comment'];
                $freeHistoryData['is_cancel'] = $freeHistory->is_cancel ?? $freeHistoryData['is_cancel'];

                $attachment = !empty($freeHistory->attachment) ? json_decode($freeHistory->attachment) : $freeHistoryData['attachment'];
                $attachment = collect($attachment)->map(function ($item) {
                    $fileName = explode('/', $item);
                    $fileName = $fileName[count($fileName) - 1];

                    return [
                        'filepath' => $item,
                        'filename' => $fileName,
                    ];
                })->toArray();
                $freeHistoryData['attachment'] = $attachment;

                if ($freeHistory->flag_role == FreeHistory::FLAG_ROLE_2) {
                    $isSubmit = false;
                }
            }
        } elseif (!empty($request->maching_result_id)) {
            $machingResult = $this->matchingResultService->find($request->maching_result_id);

            if (!empty($machingResult)) {
                $piDDDate = $machingResult->pi_dd_date;
                $piTfrPeriod = (int) $machingResult->pi_tfr_period ?? null;
                if (empty($piTfrPeriod)) {
                    $piTfrPeriod = 40;
                }
                $piDDDateAfterSomeDay = Carbon::parse($piDDDate)->addDay($piTfrPeriod ?? 40)->format('Y-m-d');

                $freeHistoryData['property'] = FreeHistory::PROPERTY_1;
                $freeHistoryData['maching_result_id'] = $machingResult->id;
                $freeHistoryData['user_response_deadline'] = $piDDDateAfterSomeDay ?? $freeHistoryData['user_response_deadline'];
                $freeHistoryData['XML_delivery_date'] = $piDDDate ?? $freeHistoryData['XML_delivery_date'];
                $freeHistoryData['status_name'] = $machingResult->pi_document_name ?? $freeHistoryData['status_name'];
                $freeHistoryData['patent_response_deadline'] = $piDDDateAfterSomeDay ?? $freeHistoryData['patent_response_deadline'];
            }
        }

        return view('admin.modules.free_histories.create', compact(
            'trademark',
            'trademarkTable',
            'types',
            'properties',
            'freeHistoryData',
            'isSubmit',
            'isMaxData',
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function store(Request $request, $id): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $trademark = $this->trademarkService->find($id);
            $currentAdmin = \Auth::guard('admin')->user();

            $trademark = $trademark->load('freeHistories');
            $freeHistories = $trademark->freeHistories ?? [];
            $isMaxData = count($freeHistories) >= 20;
            if ($isMaxData == true) {
                return redirect()->back();
            }

            $params = $request->all();

            // Param
            $params['flag_role'] = FreeHistory::FLAG_ROLE_1;
            $params['is_check_amount'] = true;
            $params['patent_response_deadline'] = $params['patent_response_deadline'] ?? null;
            if (isset($params[CONFIRM])) {
                $params['flag_role'] = FreeHistory::FLAG_ROLE_2;
            }
            if (!empty($params['amount_type'])) {
                if ($params['amount_type'] == FreeHistory::AMOUNT_TYPE_NO_FREE) {
                    $params['amount'] = 5000;
                } elseif ($params['amount_type'] == FreeHistory::AMOUNT_TYPE_FREE) {
                    $params['amount'] = null;
                }
            }
            if (!empty($params['type']) && $params['type'] != FreeHistory::TYPE_4) {
                $params['user_response_deadline'] = null;
            }

            // Remove Attachment
            if (!empty($params['attachment_remove']) && count($params['attachment_remove']) > 0) {
                foreach ($params['attachment_remove'] as $attachRemove) {
                    FileHelper::unlink($attachRemove);
                }
            }

            // Attachment
            $attachment = [];
            if (!empty($params['attachment_input']) && count($params['attachment_input']) > 0) {
                foreach ($params['attachment_input'] as $filepath) {
                    if (str_contains($filepath, FOLDER_TEMP)) {
                        $path = FileHelper::moveTempToPath($filepath, '/uploads/free_histories');
                        if (!empty($path)) {
                            $attachment[] = $path;
                        }
                    } else {
                        $attachment[] = $filepath;
                    }
                }
            }
            $params['attachment'] = json_encode($attachment);

            $freeHistoryId = $params['free_history_id'] ?? null;
            if (!empty($freeHistoryId)) {
                $freeHistory = $this->freeHistoryService->find($freeHistoryId);

                $this->freeHistoryService->update($freeHistory, $params);
            } else {
                $params['trademark_id'] = $trademark->id;
                $params['admin_id_create'] = $currentAdmin->id;

                $freeHistory = $this->freeHistoryService->create($params);
            }

            DB::commit();

            if (isset($params[CONFIRM])) {
                $targetPage = route('admin.free-history.create', [
                    'id' => $trademark->id,
                    'free_history_id' => $freeHistory->id,
                ]);
                $redirectPage = route('admin.free-history.edit', $freeHistory->id);

                // Send Notice
                $notice = [
                    'flow' => Notice::FLOW_FREE_HISTORY,
                    'trademark_id' => $trademark->id,
                    'user_id' => $trademark->user_id,
                    'trademark_info_id' => null,
                ];

                $noticeDetails = [
                    // Send for seki
                    [
                        'target_id' => null,
                        'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                        'target_page' => $targetPage,
                        'redirect_page' => $redirectPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => true,
                        'content' => '責任者　フリー履歴追加　修正・承認',
                        'comment' => null,
                        'attribute' => null,
                    ],
                    [
                        'target_id' => null,
                        'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                        'target_page' => $targetPage,
                        'redirect_page' => $redirectPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'is_action' => false,
                        'content' => '責任者　フリー履歴追加　修正・承認',
                        'comment' => $params['comment'] ?? null,
                        'attribute' => '所内処理',
                        'response_deadline' => $freeHistory->user_response_deadline ?? null,
                    ],
                ];

                $this->noticeService->sendNotice([
                    'notices' => $notice,
                    'notice_details' => $noticeDetails,
                ]);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E034'));
                return redirect(route('admin.home'));
            } else {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
                return redirect()->route('admin.free-history.create', [
                    'id' => $trademark->id,
                    'free_history_id' => $freeHistory->id,
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function edit(Request $request, int $id): View
    {
        $freeHistory = $this->freeHistoryService->find($id);

        if (empty($freeHistory)) {
            abort(404);
        }

        $freeHistory = $freeHistory->load(['adminCreate']);

        $trademark = $this->trademarkService->find($freeHistory->trademark_id);

        // Trademark info table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_2, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        // Default Data
        $types = $this->freeHistoryService->types();
        $properties = $this->freeHistoryService->properties();

        // set Free History
        $freeHistoryData = [
            'type' => $freeHistory->type ?? FreeHistory::TYPE_1,
            'user_response_deadline' => $freeHistory->user_response_deadline ?? null,
            'XML_delivery_date' => $freeHistory->XML_delivery_date ?? null,
            'property' => $freeHistory->property ?? null,
            'status_name' => $freeHistory->status_name ?? null,
            'patent_response_deadline' => $freeHistory->patent_response_deadline ?? null,
            'create_name' => $freeHistory->adminCreate->name ?? null,
            'amount_type' => $freeHistory->amount_type ?? FreeHistory::AMOUNT_TYPE_NO_FREE,
            'amount' => $freeHistory->amount ?? null,
            'amount_display' => 0,
            'internal_remark' => $freeHistory->internal_remark ?? null,
            'comment' => $freeHistory->comment ?? null,
            'attachment' => [],
            'is_check_amount' => $freeHistory->is_check_amount ?? false,
        ];

        // Set amount display
        if ($freeHistoryData['amount_type'] == FreeHistory::AMOUNT_TYPE_NO_FREE) {
            $freeHistoryData['amount_display'] = 5000;
        } elseif ($freeHistoryData['amount_type'] == FreeHistory::AMOUNT_TYPE_CUSTOM) {
            $freeHistoryData['amount_display'] = $freeHistoryData['amount'] ?? 0;
        }
        $freeHistoryData['amount_display'] = CommonHelper::formatPrice($freeHistoryData['amount_display']);

        // Set attachment
        $attachment = !empty($freeHistory->attachment) ? json_decode($freeHistory->attachment) : $freeHistoryData['attachment'];
        $attachment = collect($attachment)->map(function ($item) {
            $fileName = explode('/', $item);
            $fileName = $fileName[count($fileName) - 1];

            return [
                'filepath' => $item,
                'filename' => $fileName,
            ];
        })->toArray();
        $freeHistoryData['attachment'] = $attachment;

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.free-history.edit', $freeHistory->id);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        return view('admin.modules.free_histories.edit', compact(
            'freeHistory',
            'trademark',
            'trademarkTable',
            'types',
            'properties',
            'freeHistoryData',
            'backUrl',
        ));
    }

    /**
     * Update the form for editing the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            DB::beginTransaction();

            $freeHistory = $this->freeHistoryService->find($id);
            $freeHistory->load('trademark.user');
            if (empty($freeHistory)) {
                abort(404);
            }

            if ($freeHistory->is_confirm == true) {
                abort(404);
            }

            $params = $request->all();
            // Param
            $params['is_check_amount'] = $params['is_check_amount'] ?? false;

            if (isset($params[CONFIRM])) {
                $params['admin_id_confirm'] = Auth::guard('admin')->id();
                $params['is_confirm'] = true;
            }

            // Remove Attachment
            if (!empty($params['attachment_remove']) && count($params['attachment_remove']) > 0) {
                foreach ($params['attachment_remove'] as $attachRemove) {
                    FileHelper::unlink($attachRemove);
                }
            }

            // Attachment
            $attachment = [];
            if (!empty($params['attachment_input']) && count($params['attachment_input']) > 0) {
                foreach ($params['attachment_input'] as $filepath) {
                    if (str_contains($filepath, FOLDER_TEMP)) {
                        $path = FileHelper::moveTempToPath($filepath, '/uploads/free_histories');
                        if (!empty($path)) {
                            $attachment[] = $path;
                        }
                    } else {
                        $attachment[] = $filepath;
                    }
                }
            }
            $params['attachment'] = json_encode($attachment);

            $this->freeHistoryService->update($freeHistory, $params);

            if (isset($params[CONFIRM])) {
                $targetPageOld = route('admin.free-history.create', [
                    'id' => $freeHistory->trademark->id,
                    'maching_result_id' => $freeHistory->maching_result_id,
                    'free_history_id' => $freeHistory->id,
                ]);
                $targetPageOld = str_replace(request()->root(), '', $targetPageOld);

                $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $targetPageOld,
                    'completion_date' => null,
                ])->with('notice')->get()
                    ->where('notice.trademark_id', $freeHistory->trademark->id)
                    ->where('notice.user_id', $freeHistory->trademark->user_id)
                    ->where('notice.flow', Notice::FLOW_FREE_HISTORY);
                $stepBeforeNotice->map(function ($item) {
                    $item->update([
                        'completion_date' => now(),
                    ]);
                });
                switch ($freeHistory->type) {
                    case FreeHistory::TYPE_1:
                        $this->noticeType1($freeHistory, $params);
                        CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.free_histories.message_type_1'));
                        break;
                    case FreeHistory::TYPE_2:
                        $this->noticeType2($freeHistory, $params);
                        CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.free_histories.message_type_2'));
                        break;
                    case FreeHistory::TYPE_3:
                        $this->noticeType3($freeHistory, $params);
                        $dataSendMail = [
                            'user' => $freeHistory->trademark->user ?? null,
                            'from_page' => A000FREE_S,
                        ];
                        $this->mailTemplateService->sendMailRequest($dataSendMail, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);

                        CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.free_histories.has_send_user'));
                        break;
                    case FreeHistory::TYPE_4:
                        $freeHistory = $freeHistory->load(['trademark.user']);
                        $trademark = $freeHistory->trademark;
                        $user = $trademark->user;

                        // Send Mail
                        SendGeneralMailJob::dispatch('emails.mail_a000free_remind', [
                            'to' => $user->getListMail(),
                            'subject' => '【AMS】商標出願包括管理システム フリー履歴対応完了',
                        ]);

                        $this->noticeType4($freeHistory, $params);
                        CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.free_histories.has_send_user'));
                        break;
                }

                $redirect = route('admin.home');
            } else {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
                $redirect = route('admin.free-history.edit', $freeHistory->id);
            }

            DB::commit();

            return redirect($redirect);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Send Notice each type
     *
     * @param Model $freeHistory
     * @param array $params
     * @return void
     */
    public function noticeType1(Model $freeHistory, array $params)
    {
        $freeHistory = $freeHistory->load(['trademark']);
        $trademark = $freeHistory->trademark;

        if (empty($trademark)) {
            return;
        }
        // Update Notice at a000free (No 1: G F)

        $targetPage = route('admin.free-history.edit', $freeHistory->id);
        $redirectPage = route('admin.application-detail.index', $trademark->id);

        $notice = [
            'flow' => Notice::FLOW_FREE_HISTORY,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // Send Notice Jimu
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '事務担当　(書類名)提出書類HTML生成、提出作業中',
                'attribute' => null,
                'response_deadline' => null,
            ],
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '事務担当　(書類名)提出書類HTML生成、提出作業中',
                'comment' => $params['comment'] ?? null,
                'attribute' => '特許庁へ',
                'response_deadline' => null,
                'buttons' => [
                    [
                        'btn_type' => NoticeDetailBtn::BTN_CREATE_HTML,
                        'url' => route('admin.free-history.edit', [
                            'id' => $freeHistory->id,
                            'type' => VIEW,
                        ]),
                        'from_page' => A000FREE_S,
                    ],
                ],
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send Notice each type
     *
     * @param Model $freeHistory
     * @param array $params
     * @return void
     */
    public function noticeType2(Model $freeHistory, array $params)
    {
        $freeHistory = $freeHistory->load(['trademark']);
        $trademark = $freeHistory->trademark;

        if (empty($trademark)) {
            return;
        }
        $this->updateNoticeFreeS($freeHistory);
        $notice = [
            'flow' => Notice::FLOW_FREE_HISTORY,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $targetPage = route('admin.free-history.edit', $freeHistory->id);

        $noticeDetails = [
            // Send Notice Seki
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => 'フリー履歴追加完了',
                'comment' => $params['comment'] ?? null,
                'attribute' => '所内処理',
                'response_deadline' => null,
                'completion_date' => now(),
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send Notice each type
     *
     * @param Model $freeHistory
     * @param array $params
     * @return void
     */
    public function noticeType3(Model $freeHistory, array $params)
    {
        $freeHistory = $freeHistory->load(['trademark']);
        $trademark = $freeHistory->trademark;

        if (empty($trademark)) {
            return;
        }
        $this->updateNoticeFreeS($freeHistory);
        $notice = [
            'flow' => Notice::FLOW_FREE_HISTORY,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $targetPage = route('admin.free-history.edit', $freeHistory->id);

        $noticeDetails = [
            // Send Notice Seki
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '(書類名)連絡済',
                'comment' => $params['comment'] ?? null,
                'attribute' => 'お客様へ',
                'response_deadline' => null,
                'completion_date' => now(),
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send Notice each type
     *
     * @param Model $freeHistory
     * @param array $params
     * @return void
     */
    public function noticeType4(Model $freeHistory, array $params)
    {
        $freeHistory = $freeHistory->load(['trademark']);
        $trademark = $freeHistory->trademark;

        if (empty($trademark)) {
            return;
        }
        $this->updateNoticeFreeS($freeHistory);
        $notice = [
            'flow' => Notice::FLOW_FREE_HISTORY,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $targetPage = route('admin.free-history.edit', $freeHistory->id);
        $redirectPage = route('user.free-history.show-create', $freeHistory->id);

        $noticeDetails = [
            // Send Notice Seki
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '(書類名)連絡済',
                'comment' => $params['comment'] ?? null,
                'attribute' => 'お客様へ',
                'response_deadline' => null,
                'completion_date' => now(),
            ],
            // Send Notice User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => 'フリー履歴：ご回答をお願いします',
                'attribute' => null,
                'response_deadline' => null,
                'response_deadline_ams' => $freeHistory->user_response_deadline ?? null,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => true,
                'content' => 'フリー履歴：ご回答をお願いします',
                'attribute' => null,
                'response_deadline' => null,
                'response_deadline_ams' => $freeHistory->user_response_deadline ?? null,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Update Notice Free_S
     *
     * @param  mixed $trademark
     * @return void
     */
    public function updateNoticeFreeS($freeHistory)
    {
        $trademark = $freeHistory->trademark;
        // Update Notice at a000free_s (No 2: G F)
        $targetPageOld = route('admin.free-history.edit', [
            'id' => $freeHistory->id,
        ]);
        $targetPageOld = str_replace(request()->root(), '', $targetPageOld);

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
            'completion_date' => null,
            'target_page' => $targetPageOld,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_FREE_HISTORY);

        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => now(),
            ]);
        });
    }

    /**
     * Show the form for re-confirm the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function reConfirm(Request $request, int $id): View
    {
        $freeHistory = $this->freeHistoryService->find($id);
        if (empty($freeHistory)) {
            abort(404);
        }

        if ($freeHistory->is_answer != FreeHistory::IS_ANSWER_TRUE) {
            abort(404);
        }

        $freeHistory = $freeHistory->load(['adminCreate']);

        $trademark = $this->trademarkService->find($freeHistory->trademark_id);

        // Trademark info table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_2, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        // Default Data
        $types = $this->freeHistoryService->types();
        $properties = $this->freeHistoryService->properties();

        // set Free History
        $freeHistoryData = [
            'type' => $freeHistory->type ?? FreeHistory::TYPE_1,
            'user_response_deadline' => $freeHistory->user_response_deadline ?? null,
            'XML_delivery_date' => $freeHistory->XML_delivery_date ?? null,
            'property' => $freeHistory->property ?? null,
            'status_name' => $freeHistory->status_name ?? null,
            'patent_response_deadline' => $freeHistory->patent_response_deadline ?? null,
            'create_name' => $freeHistory->adminCreate->name ?? null,
            'amount_type' => $freeHistory->amount_type ?? FreeHistory::AMOUNT_TYPE_NO_FREE,
            'amount' => $freeHistory->amount ?? null,
            'amount_display' => 0,
            'internal_remark' => $freeHistory->internal_remark ?? null,
            'comment' => $freeHistory->comment ?? null,
            'comment_free02' => $freeHistory->comment_free02 ?? null,
            'content_answer' => $freeHistory->content_answer ?? null,
            'attachment' => [],
            'is_check_amount' => $freeHistory->is_check_amount ?? false,
        ];

        // Set amount display
        if ($freeHistoryData['amount_type'] == FreeHistory::AMOUNT_TYPE_NO_FREE) {
            $freeHistoryData['amount_display'] = 5000;
        } elseif ($freeHistoryData['amount_type'] == FreeHistory::AMOUNT_TYPE_CUSTOM) {
            $freeHistoryData['amount_display'] = $freeHistoryData['amount'] ?? 0;
        }
        $freeHistoryData['amount_display'] = CommonHelper::formatPrice($freeHistoryData['amount_display']);

        // Set attachment
        $attachment = !empty($freeHistory->attachment) ? json_decode($freeHistory->attachment) : $freeHistoryData['attachment'];
        $attachment = collect($attachment)->map(function ($item) {
            $fileName = explode('/', $item);
            $fileName = $fileName[count($fileName) - 1];

            return [
                'filepath' => $item,
                'filename' => $fileName,
            ];
        })->toArray();
        $freeHistoryData['attachment'] = $attachment;

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.free-history.edit', $freeHistory->id);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        // if ($freeHistory->is_confirm == true && !isset(request()->type)) {
        //     $request->session()->put('message_confirm', [
        //         'url' => $backUrl ?? $urlBackDefault,
        //         'content' => __('messages.general.Common_E035'),
        //         'btn' => __('labels.back'),
        //     ]);
        // }

        return view('admin.modules.free_histories.re-confirm', compact(
            'freeHistory',
            'trademark',
            'trademarkTable',
            'types',
            'properties',
            'freeHistoryData',
            'backUrl',
        ));
    }

    /**
     * Post for re-confirm the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postReConfirm(Request $request, $id)
    {
        $freeHistory = $this->freeHistoryService->find($id);
        if (empty($freeHistory)) {
            abort(404);
        }
        $freeHistory->load(['trademark']);
        $trademark = $freeHistory->trademark;
        if ($freeHistory->is_answer != FreeHistory::IS_ANSWER_TRUE) {
            abort(404);
        }
        $params = $request->all();
        $freeHistory->update(['comment_free02' => $params['comment_free02']]);
        if (isset($params[CONFIRM])) {
            $freeHistory->update(['is_completed' => FreeHistory::IS_ANSWER_TRUE]);
            $targetPageOld = route('admin.free-history.create', [
                'id' => $freeHistory->trademark->id,
                'maching_result_id' => $freeHistory->maching_result_id,
                'free_history_id' => $freeHistory->id,
            ]);
            $targetPageOld = str_replace(request()->root(), '', $targetPageOld);

            $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPageOld,
            ])->whereNull('completion_date')->with('notice')->get()
                ->where('notice.trademark_id', $freeHistory->trademark->id)
                ->where('notice.user_id', $freeHistory->trademark->user_id)
                ->where('notice.flow', Notice::FLOW_FREE_HISTORY);

            $stepBeforeNotice2 = $this->noticeDetailService->findByCondition([
                'target_page' => str_replace(request()->root(), '', route('user.free-history.show-create', ['id' => $freeHistory->id])),
            ])->whereIn('type_acc', [NoticeDetail::TYPE_OFFICE_MANAGER, NoticeDetail::TYPE_USER])
                ->whereNull('completion_date')->with('notice')->get()
                ->where('notice.trademark_id', $freeHistory->trademark->id)
                ->where('notice.user_id', $freeHistory->trademark->user_id)
                ->where('notice.flow', Notice::FLOW_FREE_HISTORY);

            $stepBeforeNotice->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });

            $stepBeforeNotice2->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });

            $freeHistory = $freeHistory->load(['trademark']);
            $trademark = $freeHistory->trademark;

            if (empty($trademark)) {
                return;
            }
            // Update Notice at a000free (No 1: G F)

            $targetPage = route('admin.free-history.re-confirm', $freeHistory->id);
            $redirectPage = route('admin.application-detail.index', $trademark->id);

            $notice = [
                'flow' => Notice::FLOW_FREE_HISTORY,
                'trademark_id' => $trademark->id,
                'user_id' => $trademark->user_id,
                'trademark_info_id' => null,
            ];
            $jimu = Admin::where('role', Admin::ROLE_ADMIN_JIMU)->first();

            $noticeDetails = [
                // Send Notice Jimu
                [
                    'target_id' => $jimu->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '事務担当　(書類名)提出書類HTML生成、提出作業中',
                    'attribute' => null,
                    'response_deadline' => null,
                ],
                [
                    'target_id' => $jimu->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '事務担当　(書類名)提出書類HTML生成、提出作業中',
                    'comment' => $params['comment'] ?? null,
                    'attribute' => '特許庁へ',
                    'response_deadline' => null,
                    'buttons' => [
                        [
                            'btn_type' => NoticeDetailBtn::BTN_CREATE_HTML,
                            'url' => route('admin.free-history.edit', [
                                'id' => $freeHistory->id,
                                'type' => VIEW,
                            ]),
                            'from_page' => A000FREE02,
                        ],
                    ],
                ],
            ];

            $this->noticeService->sendNotice([
                'notices' => $notice,
                'notice_details' => $noticeDetails,
            ]);
            CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E035'));
        }

        return redirect()->route('admin.application-detail.index', $freeHistory->trademark_id);
    }
}
