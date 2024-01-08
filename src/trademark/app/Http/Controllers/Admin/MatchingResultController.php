<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\AppTrademark;
use App\Models\MatchingResult;
use App\Models\MCode;
use App\Models\MPriceList;
use App\Models\MProduct;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Models\RegisterTrademark;
use App\Models\RegisterTrademarkProd;
use App\Notices\CommonNotice;
use App\Repositories\ComparisonTrademarkResultRepository;
use App\Services\AppTrademarkProdService;
use App\Services\Common\NoticeService;
use App\Services\NoticeDetailBtnService;
use Illuminate\Http\Request;
use App\Services\MatchingResultService;
use App\Services\Common\TrademarkTableService;
use App\Services\MProductService;
use App\Services\NoticeDetailService;
use App\Services\RegisterTrademarkService;
use App\Services\PrecheckService;
use App\Services\RegisterTrademarkProdService;
use App\Services\TrademarkService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use IntlDateFormatter;
use Illuminate\Support\Str;

class MatchingResultController extends Controller
{
    protected MatchingResultService $matchingResultService;
    protected TrademarkTableService $trademarkTableService;
    protected ComparisonTrademarkResultRepository $comparisonTrademarkResultRepository;
    protected NoticeDetailService $noticeDetailService;
    protected NoticeService $noticeService;
    protected RegisterTrademarkService $registerTrademarkService;
    protected MProductService $mProductService;
    protected PrecheckService $precheckService;
    protected RegisterTrademarkProdService $registerTrademarkProdService;
    protected TrademarkService $trademarkService;
    protected NoticeDetailBtnService $noticeDetailBtnService;

    public function __construct(
        MatchingResultService                $matchingResultService,
        TrademarkTableService                $trademarkTableService,
        ComparisonTrademarkResultRepository  $comparisonTrademarkResultRepository,
        NoticeService                        $noticeService,
        NoticeDetailService                  $noticeDetailService,
        RegisterTrademarkService             $registerTrademarkService,
        MProductService                      $mProductService,
        PrecheckService                      $precheckService,
        RegisterTrademarkProdService         $registerTrademarkProdService,
        TrademarkService                     $trademarkService,
        NoticeDetailBtnService               $noticeDetailBtnService
    )
    {
        $this->matchingResultService = $matchingResultService;
        $this->trademarkTableService = $trademarkTableService;
        $this->comparisonTrademarkResultRepository = $comparisonTrademarkResultRepository;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->mProductService = $mProductService;
        $this->precheckService = $precheckService;
        $this->registerTrademarkProdService = $registerTrademarkProdService;
        $this->trademarkService = $trademarkService;
        $this->noticeDetailBtnService = $noticeDetailBtnService;

        // Check permission
        $this->middleware('permission:maching_results.refusalRequestReviewCreate')->only(['refusalRequestReviewCreate']);
        $this->middleware('permission:maching_results.showDocumentModification')->only(['showDocumentModification']);
        $this->middleware('permission:maching_results.redirectPageHosei')->only(['redirectPageHosei']);
        $this->middleware('permission:maching_results.showDocumentModificationProduct')->only(['showDocumentModificationProduct']);
        $this->middleware('permission:maching_results.registrationDocumentModificationSkip')->only(['registrationDocumentModificationSkip']);
        $this->middleware('permission:maching_results.postRegistrationDocument')->only(['postRegistrationDocument']);
    }

    /**
     * Refusal request review.
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function refusalRequestReview(Request $request, int $id)
    {
        if (!isset($request->maching_result_id)) {
            abort(404);
        }

        $machingResult = $this->matchingResultService->findByCondition([
            'id' => $request->maching_result_id,
            'trademark_id' => $id,
        ])->with(['trademark.appTrademark', 'comparisonTrademarkResult'])->first();
        if (!$machingResult) {
            abort(404);
        }

        $trademark = $machingResult->trademark;
        if (!$trademark) {
            abort(404);
        }

        $appTrademark = $machingResult->trademark->appTrademark;
        if ($appTrademark == null) {
            abort(404);
        }

        $comparisonTrademarkResult = $machingResult->comparisonTrademarkResult;
        $checkSubmit = false;
        if (isset($comparisonTrademarkResult) && $comparisonTrademarkResult) {
            $checkSubmit = true;
        }

        $pack = $appTrademark->pack;
        $packA = AppTrademark::PACK_A;
        $packB = AppTrademark::PACK_B;
        $packC = AppTrademark::PACK_C;

        $dateUserResponseDeadLine = Carbon::now()->addDays(3);
        if (!empty($comparisonTrademarkResult->user_response_deadline)) {
            $dateUserResponseDeadLine = Carbon::parse($comparisonTrademarkResult->user_response_deadline);
        }

        $piDDDateAddMonth = Carbon::parse($machingResult->pi_dd_date)->addDays($machingResult->pi_tfr_period);
        $dateComparisonTrademarkResults = $piDDDateAddMonth->format('Y/m/d');

        $now = Carbon::parse(now()->format('Y-m-d'));
        $piDDDateAddMonthOnlyDate = $piDDDateAddMonth->format('Y-m-d');

        $textRed = $now->greaterThan($piDDDateAddMonthOnlyDate);

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        return view('admin.modules.maching-result.index', compact(
            'id',
            'pack',
            'packA',
            'packB',
            'packC',
            'textRed',
            'checkSubmit',
            'machingResult',
            'trademarkTable',
            'dateUserResponseDeadLine',
            'dateComparisonTrademarkResults'
        ));
    }

    /**
     * Refusal request review create
     *
     * @param Request $request
     * @return mixed
     */
    public function refusalRequestReviewCreate(Request $request)
    {
        $machingResult = $this->matchingResultService->findByCondition([
            'id' => $request->maching_result_id,
            'trademark_id' => $request->trademark_id,
        ])->with('trademark.appTrademark')->first();

        if (!$machingResult) {
            abort(404);
        }

        $trademark = $machingResult->trademark;
        if (!$trademark) {
            abort(404);
        }

        $appTrademark = $trademark->appTrademark;
        if ($appTrademark == null) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            $userResponseDeadline = null;
            if ($request->user_response_deadline) {
                $userResponseDeadline = Carbon::createFromFormat('Y年m月d日', $request->user_response_deadline);
                $userResponseDeadline = Carbon::parse($userResponseDeadline)->format('Y-m-d 23:59:59');
            }
            $dateComparisonTrademarkResults = Carbon::parse($machingResult->pi_dd_date)->addDays($machingResult->pi_tfr_period)->format('Y-m-d 23:59:59');
            $sendingNotiRejectionDate = Carbon::parse($machingResult->pi_dd_date)->format('Y-m-d 23:59:59');
            $this->comparisonTrademarkResultRepository->updateOrCreate([
                'maching_result_id' => $request->maching_result_id,
                'trademark_id' => $request->trademark_id,
            ], [
                'admin_id' => auth()->guard('admin')->user()->id,
                'sending_noti_rejection_date' => $sendingNotiRejectionDate,
                'response_deadline' => $dateComparisonTrademarkResults,
                'user_response_deadline' => $userResponseDeadline
            ]);

            $trademark = $machingResult->trademark;

            // Update Notice at Import02 (No 15: G F)
            $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                'completion_date' => null,
            ])->with('notice')->get()
                ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
                ->where('notice.trademark_id', $trademark->id)
                ->where('notice.user_id', $trademark->user_id)
                ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
                ->filter(function ($item) {
                    if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_1) {
                        return true;
                    } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                        return true;
                    }

                    return false;
                });

            $stepBeforeNotice->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });

            $stepBeforeNoticeAnkenTop = $stepBeforeNotice->where('type_page', NoticeDetail::TYPE_PAGE_ANKEN_TOP)->last();

            $this->noticeDetailBtnService->updateOrCreate([
                'notice_detail_id' => $stepBeforeNoticeAnkenTop->id,
                'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                'from_page' => A201A,
            ], [
                'date_click' => null,
            ]);

            DB::commit();

            return redirect()->route('admin.application-detail.index', ['id' => $request->trademark_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return redirect()->back();
        }
    }

    /**
     * Show Document Modification
     *
     * @param  Request $request
     * @param  int $id
     * @return view
     */
    public function showDocumentModification(Request $request, $id): View
    {

        $matchingResult = $this->matchingResultService->find($id);
        if (!$matchingResult) {
            abort(404);
        }
        $matchingResult->load('trademark');
        $trademark = $matchingResult->trademark;
        $registerTrademark = null;

        $mDistincts = collect([]);
        $redirectTo = null;

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_4, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        $registerTrademark = $this->registerTrademarkService->findByCondition([
            'id' => $request['register_trademark_id'],
            'trademark_id' => $trademark->id,
        ])->first();
        if (!$registerTrademark) {
            abort(404);
        }
        if (isset($registerTrademark) && $registerTrademark) {
            $registerTrademark->load('registerTrademarkProds');
            $mProductIds = $registerTrademark->registerTrademarkProds->pluck('m_product_id')->toArray();
            $registerTrademarkProdIds = $registerTrademark->registerTrademarkProds->pluck('id')->toArray();
            $mDistincts = $this->mProductService->getProductAppTrademark($mProductIds, $registerTrademarkProdIds);
            $countRegisterTrademarkProds = $registerTrademark->registerTrademarkProds->count();
            $countRegisterTrademarkProdIsNotApply = $registerTrademark->registerTrademarkProds->where('is_apply', RegisterTrademarkProd::IS_NOT_APPLY)->count();

            if ($countRegisterTrademarkProds > $countRegisterTrademarkProdIsNotApply && $countRegisterTrademarkProdIsNotApply > 0) {
                $redirectTo = A302_HOSEI02;
            } else {
                $redirectTo = A302_HOSEI02_SKIP;
            }
        }

        $key = Str::random(11);
        Session::put($key, $mDistincts);

        return view('admin.modules.document_modification.index', compact(
            'key',
            'redirectTo',
            'id',
            'registerTrademark',
            'trademarkTable'
        ));
    }

    /**
     * Redirect Page Hosei
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function redirectPageHosei(Request $request, $id): RedirectResponse
    {
        $mProductIds = $this->registerTrademarkProdService->findByCondition([
            'register_trademark_id' => $request->id,
            'is_apply' => RegisterTrademarkProd::IS_APPLY
        ])->pluck('m_product_id')->toArray();
        $mProducts = $this->mProductService->findByCondition(['ids' => $mProductIds])->with('mCode')->get();
        foreach ($mProducts as $mProduct) {
            if (in_array($mProduct->type, [MProduct::TYPE_CREATIVE_CLEAN, MProduct::TYPE_SEMI_CLEAN])) {
                $mProduct->update(['type' => MProduct::TYPE_REGISTERED_CLEAN]);
            }
            $mCodes = $mProduct->mCode;
            foreach ($mCodes as $mCode) {
                if (in_array($mCode->type, [MCode::TYPE_CREATIVE_CLEAN, MCode::TYPE_SEMI_CLEAN])) {
                    $mCode->update(['type' => MCode::TYPE_REGISTERED_CLEAN]);
                }
            }
        }
        switch ($request['redirect_to']) {
            case A302_HOSEI02:
                return redirect()->route('admin.registration.document.modification.product', ['id' => $id, 'register_trademark_id' => $request['register_trademark_id']]);
            case A302_HOSEI02_SKIP:
                return redirect()->route('admin.registration.document.modification.skip', ['id' => $id, 'register_trademark_id' => $request['register_trademark_id']]);
            default:
                return redirect()->back();
        }
    }

    /**
     * Show Document Modification Product
     *
     * @param  Request $request
     * @param  int $id - matching_result_id?register_trademark_id
     * @return view
     */
    public function showDocumentModificationProduct(Request $request, $id): View
    {
        $matchingResult = $this->matchingResultService->find($id);
        if (!$matchingResult) {
            abort(CODE_ERROR_404);
        }

        if (!isset($request->register_trademark_id)) {
            abort(CODE_ERROR_404);
        }

        $matchingResult->load('trademark');
        $trademark = $matchingResult->trademark;
        $trademark->load('appTrademark', 'registerTrademark');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_4, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        $data = $this->trademarkService->getDataTrademarkRegister($trademark, $request->all());
        $registerTrademark = $data['register_trademark'] ?? null;

        $hasSubmit = $registerTrademark->hasSubmitAt(A302_HOSEI02);

        return view('admin.modules.document_modification.product', compact(
            'data',
            'trademarkTable',
            'matchingResult',
            'registerTrademark',
            'id',
            'hasSubmit'
        ));
    }

    /**
     * Show Document Modification Product
     *
     * @param  Request $request
     * @param  int $id - matching_result_id?register_trademark_id
     * @return RedirectResponse
     */
    public function postShowDocumentModificationProduct(Request $request, $id)
    {
        $registerTrademarkId = $request->register_trademark_id;
        if (empty($registerTrademarkId)) {
            abort(404);
        }

        $machingResult = $this->matchingResultService->find($id);
        if (!$machingResult) {
            abort(404);
        }

        $relation = $machingResult->load('trademark.registerTrademark');
        $trademark = $relation->trademark;
        if (!$trademark) {
            abort(404);
        }

        $registerTrademark = $trademark->registerTrademark;
        if (!$registerTrademark) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            $registerTrademark->updateIsSubmitAt($request->from_page);

            $commonNotice = app(CommonNotice::class);
            $commonNotice->noticeA302hosei02Group($machingResult, $request->all());

            DB::commit();

            return redirect()->route('admin.registration.document', [
                'id' => $machingResult->id,
                'register_trademark_id' => $registerTrademark->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Show Iframe List Product
     *
     * @param  Request $request
     * @return view
     */
    public function showIframeListProduct(Request $request): View
    {
        $mDistincts = Session::get($request->s);

        return view('admin.modules.document_modification.iframe.list_product', compact('mDistincts'));
    }

    /**
     * Registration document modification skip. a302hosei02skip
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function registrationDocumentModificationSkip(Request $request, int $id): View
    {
        $registerTrademarkId = $request->register_trademark_id;
        if (empty($registerTrademarkId)) {
            abort(404);
        }

        $machingResult = $this->matchingResultService->find($id);
        if (!$machingResult) {
            abort(404);
        }

        $relation = $machingResult->load('trademark.registerTrademark');
        $trademark = $relation->trademark;
        if (!$trademark) {
            abort(404);
        }

        $trademark = $trademark->whereHas('registerTrademark', function ($q) use ($registerTrademarkId) {
            return $q->where('id', $registerTrademarkId);
        })->first();
        if (!$trademark) {
            abort(404);
        }

        $registerTrademark = $trademark->registerTrademark;
        if (!$registerTrademark) {
            abort(404);
        }

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_4, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        $hasSubmit = $registerTrademark->hasSubmitAt(A302_HOSEI02_SKIP);

        return view('admin.modules.document_modification.skip', compact(
            'trademarkTable',
            'machingResult',
            'registerTrademark',
            'hasSubmit',
        ));
    }

    /**
     * Post Registration document modification skip. a302hosei02skip
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postRegistrationDocumentModificationSkip(Request $request, int $id)
    {
        $registerTrademarkId = $request->register_trademark_id;
        if (empty($registerTrademarkId)) {
            abort(404);
        }

        $machingResult = $this->matchingResultService->find($id);
        if (!$machingResult) {
            abort(404);
        }

        $relation = $machingResult->load('trademark.registerTrademark');
        $trademark = $relation->trademark;
        if (!$trademark) {
            abort(404);
        }

        $registerTrademark = $trademark->registerTrademark;
        if (!$registerTrademark) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            $registerTrademark->updateIsSubmitAt($request->from_page);

            $commonNotice = app(CommonNotice::class);
            $commonNotice->noticeA302hosei02Group($machingResult, $request->all());

            DB::commit();

            return redirect()->route('admin.registration.document', [
                'id' => $machingResult->id,
                'register_trademark_id' => $registerTrademark->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Show Registration Input
     *
     * @param  Request $request
     * @param  int $id - matching_result_id?register_trademark_id=
     * @return view
     */
    public function showRegistrationInput(Request $request, $id): View
    {
        $data = $this->matchingResultService->getDataMatchingResult($id);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $data['trademark_id'], [
            SHOW_LINK_ANKEN_TOP => true,
        ]);
        $now = Carbon::now()->format('Y-m-d');

        return view('admin.modules.registration.input_number', compact('data', 'trademarkTable', 'now'));
    }

    /**
     * Save Data Registration Input
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function saveDataRegistrationInput(Request $request): RedirectResponse
    {
        $registerTrademarkID = $request['register_trademark_id'] ?? null;
        $request->validate([
            'register_number' => 'unique:register_trademarks,register_number,' . $registerTrademarkID,
        ], [
            'register_number.unique' => __('messages.general.Register_trademark_A303_E007'),
        ]);

        $dateRegister = date("Y-m-d", strtotime($request->date_register));
        $dataRegisterTrademark = [
            'date_register' => $dateRegister,
            'register_number' => $request->register_number,
            'is_update_info_register' => RegisterTrademark::IS_REGISTER_CHANGE_INFO,
        ];
        if (isset($request['register_trademark_id']) && $request['register_trademark_id']) {
            $registerTrademark = $this->registerTrademarkService->find($request['register_trademark_id']);
            if ($registerTrademark->is_update_info_register == RegisterTrademark::IS_NOT_REGISTER) {
                $this->sendNoticeRegistrationInput($request->all());
            }
            $registerTrademark->update($dataRegisterTrademark);
            return redirect()->route('admin.application-detail.index', ['id' => $request['trademark_id']])->with('message', __('messages.general.update_success'))->withInput();
        }
    }

    /**
     * Send Notice Registration Input
     *
     * @param  array $params
     * @return void
     */
    public function sendNoticeRegistrationInput($params)
    {
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $params['trademark_id'])
            ->where('notice.user_id', $params['user_id'])
            ->where('notice.flow', Notice::FLOW_REGISTER_TRADEMARK);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => now(),
            ]);
        });

        // Update notice detail is send for user.
        $stepBefore = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $params['trademark_id'])
            ->where('notice.user_id', $params['user_id'])
            ->where('notice.flow', Notice::FLOW_REGISTER_TRADEMARK);
        $stepBefore->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $this->noticeService->sendNotice([
            'notices' => [
                'flow' => Notice::FLOW_REGISTER_TRADEMARK,
                'user_id' => $params['user_id'],
                'trademark_id' => $params['trademark_id'],
            ],
            'notice_details' => [
                // Send Notice jimu
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('admin.registration.input_number', [
                        'id' => $params['matching_result_id'],
                        'register_trademark_id' => $params['register_trademark_id'],
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '登録証：入力内容チェック',
                    'attribute' => '所内処理',
                    'buttons' => [
                        [
                            "btn_type"  => NoticeDetailBtn::BTN_PDF_UPLOAD,
                            "from_page" => A303,
                        ],
                    ]
                ],
            ],
        ]);
    }

    /**
     * Registration document.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function registrationDocument(Request $request, int $id): View
    {
        $registerTrademarkId = $request->register_trademark_id;
        if (empty($registerTrademarkId)) {
            abort(404);
        }
        $machingResult = $this->matchingResultService->find($id);
        if (!$machingResult) {
            abort(404);
        }
        $relation = $machingResult->load('trademark');

        $trademark = $relation->trademark;
        if (!$trademark) {
            abort(404);
        }

        $trademark = $trademark->getAgentAndCheckExistenceRegisterTrademark($registerTrademarkId);
        if (!$trademark) {
            abort(404);
        }

        $registerTrademark = $trademark->registerTrademark;
        if (!$registerTrademark) {
            abort(404);
        }

        $checkIsSend = false;
        if ($registerTrademark->is_send == RegisterTrademark::IS_SEND) {
            $checkIsSend = true;
        }

        $appTrademark = $trademark->appTrademark;
        if (!$appTrademark) {
            abort(404);
        }

        $registerTrademarkProds = $registerTrademark->registerTrademarkProds;
        $countDistinctionIsApply = $this->registerTrademarkProdService->countDistinctionRegistrationDocument($registerTrademarkProds, APPLY);
        $countDistinctionIsNotApply = $this->registerTrademarkProdService->countDistinctionWithCondition($registerTrademarkProds, NOT_APPLY);
        $countDistinction = $this->registerTrademarkProdService->countDistinctionWithCondition($registerTrademarkProds, ALL_APPLY);

        $agent = null;
        $agentGroup = $appTrademark->agentGroup;
        if ($agentGroup) {
            $collectAgent = $agentGroup->collectAgent->first();
            $agent = $collectAgent ? $collectAgent->agent : null;
        }

        $law = false;
        if ($countDistinctionIsNotApply > 0) {
            $law = true;
        }
        $changeName = false;
        if ($registerTrademark->trademark_info_change_status == RegisterTrademark::STATUS_APPLICANT_NAME ||
            $registerTrademark->trademark_info_change_status == RegisterTrademark::STATUS_NAME_AND_ADDRESS
        ) {
            $changeName = true;
        }
        $changeAddress = false;
        if ($registerTrademark->trademark_info_change_status == RegisterTrademark::STATUS_ADDRESS ||
            $registerTrademark->trademark_info_change_status == RegisterTrademark::STATUS_NAME_AND_ADDRESS
        ) {
            $changeAddress = true;
        }

        if ($checkIsSend == true) {
            $displayInfoStatus = $registerTrademark->display_info_status ?? null;
            $displayInfoStatus = !empty($displayInfoStatus) ? json_decode($displayInfoStatus) : [];

            $law = in_array(LAW, $displayInfoStatus);
            $changeName = in_array(CHANGE_NAME, $displayInfoStatus);
            $changeAddress = in_array(CHANGE_ADDRESS, $displayInfoStatus);
        }

        $print1stRegistration = $this->precheckService->getPriceOnePackService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
        $printDistinction = 0;
        if ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_5_YEAR) {
            $printDistinction = $print1stRegistration['pof_1st_distinction_5yrs'] * $countDistinctionIsApply;
        } elseif ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_10_YEAR) {
            $printDistinction = $print1stRegistration['pof_1st_distinction_10yrs'] * $countDistinctionIsApply;
        }

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_4, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.registration.document', ['id' => $machingResult->id, 'register_trademark_id' => $registerTrademark->id]);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        return view('admin.modules.document.index', compact(
            'law',
            'agent',
            'backUrl',
            'trademark',
            'changeName',
            'checkIsSend',
            'changeAddress',
            'machingResult',
            'trademarkTable',
            'countDistinction',
            'countDistinctionIsApply',
            'printDistinction',
            'registerTrademark'
        ));
    }

    /**
     * Post registration document.
     *
     * @param  Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postRegistrationDocument(Request $request, int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $registerTrademarkId = $request->register_trademark_id;
            if (empty($registerTrademarkId)) {
                abort(404);
            }

            $machingResult = $this->matchingResultService->find($id);
            if (!$machingResult) {
                abort(404);
            }

            $relation = $machingResult->load('trademark');

            $trademark = $relation->trademark;
            if (!$trademark) {
                abort(404);
            }

            $trademark = $trademark->whereHas('registerTrademark', function ($q) use ($registerTrademarkId) {
                return $q->where('id', $registerTrademarkId);
            })->with('registerTrademark')->first();
            if (!$trademark) {
                abort(404);
            }

            $registerTrademark = $trademark->registerTrademark;
            if (!$registerTrademark) {
                abort(404);
            }

            $displayInfoStatus = $request->display_info_status ?? null;
            $registerTrademark->update([
                'is_send' => RegisterTrademark::IS_SEND,
                'display_info_status' => !empty($displayInfoStatus) ? json_encode($displayInfoStatus) : '',
            ]);

            // Send notice
            $this->matchingResultService->sendNoticeA302($trademark, $machingResult);

            DB::commit();

            return redirect()->route('admin.application-detail.index', ['id' => $trademark->id])->with('message', __('messages.update_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return redirect()->back()->with('error', __('messages.error'));
        }
    }
}
