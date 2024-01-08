<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\AppTrademark;
use App\Models\AppTrademarkProd;
use App\Models\MailTemplate;
use App\Models\MPriceList;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Models\PayerInfo;
use App\Models\Payment;
use App\Models\Trademark;
use App\Models\TrademarkInfo;
use App\Services\AppTrademarkService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\MyFolderService;
use App\Services\PaymentService;
use App\Services\RegisterTrademarkService;
use App\Services\SupportFirstTimeService;
use Illuminate\Support\Facades\Auth;
use App\Services\MDistinctionService;
use App\Services\PrecheckService;
use App\Services\TrademarkService;
use App\Services\TrademarkInfoService;
use App\Services\PayerInfoService;
use App\Services\Common\TrademarkTableService;
use App\Services\MProductService;
use App\Services\Common\NoticeService;
use App\Services\AppTrademarkProdService;
use App\Services\NoticeDetailService;
use App\Services\MailTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AppTrademarkController extends Controller
{
    protected AppTrademarkService $appTrademarkService;
    protected MNationService $mNationService;
    protected MPrefectureService $mPrefectureService;
    protected MDistinctionService $mDistinctionService;
    protected SupportFirstTimeService $supportFirstTimeService;
    protected PrecheckService $precheckService;
    protected TrademarkService $tradeMarkService;
    protected TrademarkInfoService $trademarkInfoService;
    protected PayerInfoService $payerInfoService;
    protected AppTrademarkProdService $appTrademarkProdService;
    protected MProductService $mProductService;
    protected TrademarkTableService $trademarkTableService;
    protected NoticeService $noticeService;
    protected PaymentService $paymentService;
    protected MyFolderService $myFolderService;
    protected MailTemplateService $mailTemplateService;
    protected NoticeDetailService $noticeDetailService;
    protected RegisterTrademarkService $registerTrademarkService;

    public function __construct(
        AppTrademarkService $appTrademarkService,
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        MDistinctionService $mDistinctionService,
        SupportFirstTimeService $supportFirstTimeService,
        PrecheckService $precheckService,
        TrademarkService $tradeMarkService,
        TrademarkInfoService $trademarkInfoService,
        PayerInfoService $payerInfoService,
        AppTrademarkProdService $appTrademarkProdService,
        MProductService $mProductService,
        TrademarkTableService $trademarkTableService,
        NoticeService $noticeService,
        PaymentService $paymentService,
        MyFolderService $myFolderService,
        NoticeDetailService $noticeDetailService,
        MailTemplateService $mailTemplateService,
        RegisterTrademarkService $registerTrademarkService
    )
    {
        $this->supportFirstTimeService = $supportFirstTimeService;
        $this->appTrademarkService = $appTrademarkService;
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
        $this->paymentService = $paymentService;
        $this->mDistinctionService = $mDistinctionService;
        $this->precheckService = $precheckService;
        $this->tradeMarkService = $tradeMarkService;
        $this->trademarkInfoService = $trademarkInfoService;
        $this->payerInfoService = $payerInfoService;
        $this->appTrademarkProdService = $appTrademarkProdService;
        $this->mProductService = $mProductService;
        $this->trademarkTableService = $trademarkTableService;
        $this->noticeService = $noticeService;
        $this->myFolderService = $myFolderService;
        $this->noticeDetailService = $noticeDetailService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->mailTemplateService = $mailTemplateService;
    }

    /**
     * View cancel app trademark
     *
     * @param $id - app_trademark_id
     * @return View
     */
    public function viewCancelAppTrademark($id): View
    {
        $appTradeMark = $this->appTrademarkService->getAppTradeMarkOfUser($id);
        if (!$appTradeMark) {
            abort(404);
        }
        $isCancelTrue = AppTrademark::IS_CANCEL_TRUE;

        return view('user.modules.app-trademark.cancel', compact('id', 'isCancelTrue'));
    }

    /**
     * Cancel app trademark
     *
     * @param $id - app_trademark_id
     * @return View
     */
    public function cancelAppTrademark($id)
    {
        $res = $this->appTrademarkService->cancelAppTrademark($id);

        if ($res) {
            return redirect()->route('user.top')->with('message', __('messages.general.Cancel_U201_S001'));
        }

        return redirect()->back()->with('error', __('messages.general.Cancel_Error_U201_S001'));
    }

    /**
     * Confirm apptrademark - u032
     *
     * @param $id - trademark_id
     * @return void
     */
    public function confirm($id)
    {
        $appTradeMark = $this->appTrademarkService->findByCondition([
            'trademark_id' => $id,
        ])->first();
        if (!$appTradeMark) {
            abort(404);
        }
        $isBlockScreen = false;
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_3, $id);
        if ($appTradeMark->checkCancellationDeadline() || $appTradeMark->status == STATUS_ADMIN_CONFIRM) {
            $isBlockScreen = true;
        }

        return view('user.modules.app-trademark.confirm', compact('appTradeMark', 'trademarkTable', 'id', 'isBlockScreen'));
    }

    /**
     * Confirm Completed
     *
     * @return void
     */
    public function confirmCompleted()
    {
        return view('user.modules.app-trademark.confirm-completed');
    }

    /**
     * Update App Trademark Confirm
     *
     * @param  mixed $id
     * @return void
     */
    public function updateApptrademarkConfirm(Request $request, $id)
    {
        try {
            $appTrademark = $this->appTrademarkService->findByCondition([
                'trademark_id' => $id,
            ], ['trademark.user'])->first();

            switch ($request['submit_type']) {
                case SUBMIT:
                    $this->sendNoticeApplyTradenarkConfirm($appTrademark);

                    return redirect()->route('user.apply-trademark.confirm.completed');
                case BACK_URL:
                    return redirect()->route('user.application-detail.index', ['id' => $appTrademark->trademark_id]);
                case CANCEL:
                    return redirect()->route('user.apply-trademark.cancel-register', ['id' => $appTrademark->id]);
            }
        } catch (\Exception $e) {
            Log::error($e);

            return redirect()->back();
        }
    }

    /**
     * Send Notice Apply Tradenark Confirm
     *
     * @param  mixed $appTrademark
     * @return void
     */
    public function sendNoticeApplyTradenarkConfirm($appTrademark)
    {
        $parseUrl = parse_url(route('admin.apply-trademark-document-to-check', ['id' => $appTrademark->trademark_id]));
        $url = $parseUrl['path'] . '?type=view';
        if ($appTrademark->status != AppTrademark::STATUS_ADMIN_CONFIRM) {
            $trademark = $this->tradeMarkService->find($appTrademark->trademark_id);
            // Update Notice at a201a (No 104: I J)
            $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                'type_acc' => NoticeDetail::TYPE_USER,
                'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            ])->with('notice')->get()
                ->where('notice.trademark_id', $trademark->id)
                ->where('notice.user_id', $trademark->user_id)
                ->where('notice.flow', Notice::FLOW_APP_TRADEMARK);
            $stepBeforeNotice->map(function ($item) {
                $item->update([
                    'is_answer' => NoticeDetail::IS_ANSWER,
                ]);
            });

            $params['status'] = AppTrademark::STATUS_ADMIN_CONFIRM;
            $this->appTrademarkService->update($appTrademark, $params);
            $redirectPage = route('admin.application-detail.index', ['id' => $appTrademark->trademark_id]);
            $this->noticeService->sendNotice([
                'notices' => [
                    'trademark_id' => $appTrademark->trademark_id,
                    'user_id' => Auth::user()->id,
                    'flow' => Notice::FLOW_APP_TRADEMARK
                ],
                'notice_details' => [
                    // Send Notice Jimu
                    [
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => route('user.apply-trademark.confirm', ['id' => $appTrademark->trademark_id]),
                        'redirect_page' => $redirectPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => NoticeDetail::IS_ACTION_TRUE,
                        'content' => '出願：提出書類提出作業中'
                    ],
                    // Send Notice Jimu
                    [
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => route('user.apply-trademark.confirm', ['id' => $appTrademark->trademark_id]),
                        'redirect_page' => $redirectPage,
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
                        'target_id' => Auth::user()->id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => route('user.apply-trademark.confirm', ['id' => $appTrademark->trademark_id]),
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '出願：提出作業中',
                    ],
                    [
                        'target_id' => Auth::user()->id,
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

    /**
     * Show modal u031 pass
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function showPass(Request $request, int $id): View
    {
        $userId = Auth::user()->id;
        $dataResult = $this->appTrademarkService->getDataProductByAppTrademark($userId);
        $datas = $dataResult['datas'];
        $countProduct = $dataResult['countProduct'];
        $fromPage = $request->from_page;

        return view('user.modules.app-trademark.show-modal-pass', compact('datas', 'countProduct', 'fromPage'));
    }

    /**
     * Screen u031edit
     *
     * @param $id
     * @return mixed
     */
    public function applyTrademarkFreeInput($id = null)
    {
        $isBlockScreen = false;
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $distinctions = $this->mDistinctionService->listDistinctionOptions();
        $setting = $this->supportFirstTimeService->getSetting();

        $pricePackageA = $this->precheckService->getPriceOnePackService(MPriceList::APPLICATION, MPriceList::PACK_A_UP_3_ITEMS);
        $pricePackageEachA = $this->precheckService->getPriceOnePackService(MPriceList::APPLICATION, MPriceList::PACK_A_EACH_3_ITEMS);
        $priceCostBank = $this->precheckService->getPriceOnePackService(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $print5yrs = $this->precheckService->getPriceOnePackService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $products = $this->supportFirstTimeService->searchRecommend([]);
        if ($id) {
            $trademark = $this->tradeMarkService->find($id);
            if (!$trademark) {
                abort(404);
            }

            $trademark = $trademark->load('appTrademark');
            $appTrademark = $trademark->appTrademark ? $trademark->appTrademark : null;
            if (empty($appTrademark)) {
                abort(404);
            }

            if ($appTrademark->status != AppTrademark::STATUS_UNREGISTERED_SAVE) {
                $isBlockScreen = true;
            }

            $trademarkInfos = $appTrademark ? $this->trademarkInfoService->findTrademarkInfo($appTrademark) : null;
            $payerInfo = $this->payerInfoService->findPayerInfoWithAppTrademark($appTrademark->id ?? 0);

            $appTrademarkProds = $this->appTrademarkProdService->findByCondition([
                'app_trademark_id' => $appTrademark->id ?? 0,
            ])->with('mProduct', 'mProduct.mDistinction')->get();

            $appTrademarkProds->map(function ($item) {
                $product = $item->mProduct;
                $mDistinction = $product->mDistinction;

                $item->distinction_id = $mDistinction->id;
                $item->distinction_name = $mDistinction->name;
                $item->name = $product->name;

                return $item;
            });

            $mProductChoose = $appTrademarkProds->groupBy('distinction_name');

            return view('user.modules.app-trademark.u031edit.index', compact(
                'id',
                'products',
                'nations',
                'prefectures',
                'distinctions',
                'setting',
                'pricePackageA',
                'pricePackageEachA',
                'priceCostBank',
                'print5yrs',
                'paymentFee',
                'trademark',
                'trademarkInfos',
                'payerInfo',
                'mProductChoose',
                'isBlockScreen'
            ));
        }

        $dataSession = Session::get(SESSION_APPLY_TRADEMARK_EDIT);

        $mProductChoose = [];
        if (!empty($dataSession['data']) && !empty($dataSession['data']['m_product_ids'])) {
            $idsProduct = $dataSession['data']['m_product_ids'];
            $mProductChoose = $this->mProductService->getDataMproduct($idsProduct);
        }

        return view('user.modules.app-trademark.u031edit.index', compact(
            'id',
            'products',
            'nations',
            'prefectures',
            'isBlockScreen',
            'distinctions',
            'setting',
            'pricePackageA',
            'pricePackageEachA',
            'priceCostBank',
            'print5yrs',
            'paymentFee',
            'mProductChoose'
        ));
    }

    /**
     * Create Apply Trademark Free Input
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function applyTrademarkFreeInputCreate(Request $request)
    {
        $results = $this->appTrademarkService->applyTrademarkFreeInputCreate($request);
        if ($results) {
            switch ($results['redirect_to']) {
                case AppTrademark::REDIRECT_TO_COMMON_PAYMENT:
                    return redirect()->route('user.payment.index', ['s' => $results['key_session']]);
                case AppTrademark::REDIRECT_TO_QUOTE:
                    return redirect()->route('user.quote', ['id' => $results['payment_id']]);
                case AppTrademark::REDIRECT_TO_ANKEN_TOP:
                    if (isset($results['isAjax']) && $results['isAjax']) {
                        return response()->json([
                            'status' => true,
                            'router_redirect' => route('user.top'),
                        ]);
                    }
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E047'));
                    return response()->json([
                        'status' => true,
                        'router_redirect' => route('user.application-detail.index', ['id' => $results['trademark_id'], 'from' => FROM_U000_TOP]),
                    ]);
                    // return redirect()->route('user.application-detail.index', ['id' => $results['trademark_id'], 'from' => FROM_U000_TOP]);
                default:
                    return redirect()->back()->with('error', __('messages.general.update_fail'));
            }
        }
        return redirect()->back()->with('error', __('messages.general.update_fail'));
    }

    /**
     * Redirect u031 edit
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function redirectU031Edit(Request $request, $id = null): JsonResponse
    {
        $inputs = $request->all();
        if (isset($inputs['m_product_is']) && count($inputs['m_product_is'])) {
            Session::put(SESSION_APPLY_TRADEMARK_EDIT, [
                'data' => [
                    'm_product_ids' => $inputs['m_product_is'],
                ],
            ]);
            if (Session::has(SESSION_APPLY_TRADEMARK_EDIT)) {
                return response()->json([
                    'status' => true,
                    'router_redirect' => route('user.apply-trademark-free-input', ['id' => $id]),
                    'message' => 'Success redirect page'
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Error redirect',
            ]);
        }
    }

    /**
     * Redirect u031 edit with number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function redirectU031EditWithNumber(Request $request): JsonResponse
    {
        $inputs = $request->all();
        if (isset($inputs['m_product_ids']) && count($inputs['m_product_ids'])) {
            Session::put(SESSION_APPLY_TRADEMARK_WITH_NUMBER, [
                'data' => [
                    'm_product_ids' => $inputs['m_product_ids'],
                ],
            ]);
            if (Session::has(SESSION_APPLY_TRADEMARK_WITH_NUMBER)) {
                return response()->json([
                    'status' => true,
                    'router_redirect' => route('user.precheck.apply-trademark-with-number', ['id' => $inputs['trademark_id']]),
                    'message' => 'Success redirect page'
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Error redirect',
            ]);
        }
    }

    /**
     * Get info payment u031b
     *
     * @param Request $request
     * @return JsonResponse|void
     */
    public function getInfoPaymentU031b(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            $respon = $this->paymentService->ajaxGetCartInfoPayment($inputs);
            if ($respon) {
                return response()->json([
                    'status' => true,
                    'data' => $respon
                ]);
            }
            return response()->json([
                'status' => false,
                'data' => null
            ]);
        }
    }

    /**
     * Get apply trademark after search -u031b
     *
     * @param Request $request
     * @param string|int $id - trademark_id
     * @return View
     */
    public function getApplyTrademarkAfterSearch(Request $request, $id = null)
    {
        $dataSession = Session::get(SESSION_SUGGEST_PRODUCT);
        $request->name = $dataSession['name_trademark'] ?? null;

        $params = $request->all();
        $params['trademark_id'] = $id;
        $productIds = collect();
        $user = auth()->user();
        //old data if exists params id - trademark_id
        $appTrademark = null;
        $tradeMarkOld = null;
        $payerInfoOld = null;
        $tradeMarkInfosOld = null;
        $appTradeMarkProdsIsApplyIds = null;
        $folderId = null;
        if ($id) {
            //check trademark
            $appTrademark = $this->appTrademarkService->getAppTradeMarkOfUserFindByCondition(['trademark_id' => $params['trademark_id']]);
            $productIds = collect([]);
            if (!$appTrademark) {
                $tradeMarkOld = null;
                $payerInfoOld = null;
                $tradeMarkInfosOld = null;
                $appTradeMarkProdsIsApplyIds = collect([]);
            } else {
                $productIds = $appTrademark->products->pluck('id');
                //old data
                $tradeMarkOld = $this->tradeMarkService->findByCondition([
                    'id' => $id,
                    'user_id' => auth()->user()->id,
                ])->first();

                $payerInfoOld = $this->payerInfoService->findByCondition([
                    'target_id' => $appTrademark->id ?? 0,
                    'type' => Payment::TYPE_TRADEMARK
                ])->first();
                $tradeMarkInfosOld = $this->trademarkInfoService->findByCondition([
                    'target_id' => $appTrademark->id,
                    'type' => TrademarkInfo::TYPE_TRADEMARK
                ])->get();
                //ids product is_apply in app_trademark_prods table
                $appTradeMarkProdsIsApplyIds = $this->appTrademarkProdService->findByCondition(
                    ['app_trademark_id' => $appTrademark->id, 'is_apply' => AppTrademarkProd::IS_APPLY]
                )->pluck('m_product_id');
            }
        }

        //if not has param forder_id
        $myFolder = null;
        if (isset($params['folder_id']) && !empty($params['folder_id'])) {
            $myFolder = $this->myFolderService->findByCondition(['id' => $params['folder_id'], 'user_id' => $user->id])->first();
            if ($myFolder && $myFolder->myFolderProduct) {
                $productIds = $productIds->merge($myFolder->myFolderProduct->pluck('m_product_id'));
                $request->name = $myFolder->name_trademark ?? null;
            }
        } elseif ($dataSession && isset($dataSession['prod_additional_ids'])) {
            $productIds = $productIds->merge(collect(explode(',', $dataSession['prod_additional_ids'])));
        } elseif ($dataSession && isset($dataSession['from_page']) && $dataSession['from_page'] == U020C && isset($dataSession['prod_suggest_ids'])) {
            $productIds = $productIds->merge(collect(explode(',', $dataSession['prod_suggest_ids'])));
        }

        $productIds = $productIds->unique();
        $products = $this->mProductService->getDataMproduct($productIds->toArray());

        $mailRegisterCert = $this->supportFirstTimeService->getMailRegisterCertService(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
        $periodRegistration = $this->supportFirstTimeService->getPeriodRegistrationService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
        $feeSubmit = $this->supportFirstTimeService->getFeeSubmit();
        $setting = $this->supportFirstTimeService->getSetting();
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        $packA = AppTrademark::PACK_A;
        $packB = AppTrademark::PACK_B;
        $packC = AppTrademark::PACK_C;
        $redirectToQuote = AppTrademark::REDIRECT_TO_QUOTE;
        $redirectToAnkenTop = AppTrademark::REDIRECT_TO_ANKEN_TOP;
        $redirectToCommonPayment = AppTrademark::REDIRECT_TO_COMMON_PAYMENT;
        $redirectToU021 = AppTrademark::REDIRECT_TO_U021;
        $pricePackage = $this->supportFirstTimeService->getPricePackService();
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $fees = $this->supportFirstTimeService->getSystemFee(MPriceList::BEFORE_FILING, MPriceList::SFT_SELECT_SUPPORT);

        //Data default pack A, B, C
        //pack A
        $priceBasePackA = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_A_UP_3_ITEMS);
        $priceBasePackAEachThreeItem = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_A_EACH_3_ITEMS);
        $dataDefaultPackA = [
            'cost_service_base' => $priceBasePackA['cost_service_base'],
            'commission' => $priceBasePackA['commission'],
            'cost_service_add_prod' => $priceBasePackAEachThreeItem['cost_service_base'],
        ];
        //pack B
        $priceBasePackB = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_B_UP_3_ITEMS);
        $priceBasePackBEachThreeItem = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_B_EACH_3_ITEMS);
        $dataDefaultPackB = [
            'cost_service_base' => $priceBasePackB['cost_service_base'],
            'commission' => $priceBasePackB['commission'],
            'cost_service_add_prod' => $priceBasePackBEachThreeItem['cost_service_base'],
        ];

        //pack C
        $priceBasePackC = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_C_UP_3_ITEMS);
        $priceBasePackCEachThreeItem = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_C_EACH_3_ITEMS);
        $dataDefaultPackC = [
            'cost_service_base' => $priceBasePackC['cost_service_base'],
            'commission' => $priceBasePackC['commission'],
            'cost_service_add_prod' => $priceBasePackCEachThreeItem['cost_service_base'],
        ];
        $packSession = $dataSession['pack'] ?? $appTrademark->pack ?? null;
        //data return view
        $data = [
            'id' => $id,
            'appTrademark' => $appTrademark,
            'mailRegisterCert' => $mailRegisterCert,
            'periodRegistration' => $periodRegistration,
            'setting' => $setting,
            'feeSubmit' => $feeSubmit,
            'nations' => $nations,
            'prefectures' => $prefectures,
            'products' => $products,
            'pricePackage' => $pricePackage,
            'paymentFee' => $paymentFee,
            'fees' => $fees,
            'packA' => $packA,
            'packB' => $packB,
            'packC' => $packC,
            'redirectToQuote' => $redirectToQuote,
            'redirectToAnkenTop' => $redirectToAnkenTop,
            'redirectToCommonPayment' => $redirectToCommonPayment,
            'redirectToU021' => $redirectToU021,
            //old data
            'tradeMarkOld' => $tradeMarkOld,
            'payerInfoOld' => $payerInfoOld,
            'tradeMarkInfosOld' => $tradeMarkInfosOld,
            'appTradeMarkProdsIsApplyIds' => $appTradeMarkProdsIsApplyIds,
            'myFolder' => $myFolder,
            'dataDefaultPackA' => $dataDefaultPackA,
            'dataDefaultPackB' => $dataDefaultPackB,
            'dataDefaultPackC' => $dataDefaultPackC,
            'packSession' => $packSession
        ];

        return view('user.modules.app-trademark.u031b.index', $data);
    }

    /**
     * Apply trademark with number -u031d
     *
     * @param Request $request
     * @param string|int $id - trademark_id
     * @return View
     */
    public function applyTrademarkWithoutNumber(Request $request, $id = null)
    {
        if (!$request->has('s') && !$request->s) {
            return abort(403);
        }
        $data = Session::get($request->s);
        $params = $request->all();
        $params['trademark_id'] = $data['trademark_id'];
        $productIds = collect();
        $user = auth()->user();

        //old data if exists params id - trademark_id
        $appTrademark = null;
        $tradeMarkOld = null;
        $payerInfoOld = null;
        $tradeMarkInfosOld = null;
        $appTradeMarkProdsIsApplyIds = null;
        $folderId = null;
        $productIsChoice = [];
        if (isset($data['from_page']) && $data['from_page'] == U031D) {
            $appTrademark = $this->appTrademarkService->getAppTradeMarkOfUserFindByCondition(['trademark_id' => $params['trademark_id']]);
            if (!$appTrademark) {
                abort(404);
            }
            $productIds = $appTrademark->products->pluck('id');
            //old data
            $tradeMarkOld = $this->tradeMarkService->find($params['trademark_id']);
            $payerInfoOld = $this->payerInfoService->findByCondition([
                'target_id' => $appTrademark->id ?? 0,
                'type' => Payment::TYPE_TRADEMARK
            ])->first();
            $tradeMarkInfosOld = $this->trademarkInfoService->findByCondition([
                'target_id' => $appTrademark->id,
                'type' => TrademarkInfo::TYPE_TRADEMARK
            ])->get();
            $appTradeMarkProdsIsApplyIds = $this->appTrademarkProdService->findByCondition(
                ['app_trademark_id' => $appTrademark->id, 'is_apply' => AppTrademarkProd::IS_APPLY]
            )->pluck('m_product_id');
        } else {
            $tradeMarkOld = $this->tradeMarkService->find($params['trademark_id']);
            $payerInfoOld = $this->payerInfoService->findByCondition([
                'target_id' => 0,
                'type' => Payment::TYPE_TRADEMARK
            ])->first();
            $tradeMarkInfosOld = $this->trademarkInfoService->findByCondition([
                'target_id' => 0,
                'type' => TrademarkInfo::TYPE_TRADEMARK
            ])->get();
            $appTradeMarkProdsIsApplyIds = [];
        }
        //ids product is_apply in app_trademark_prods table

        $products = [];
        if (isset($data['from_page']) && $data['from_page'] == U207Kyo) {
            $products = $data['products'];
        } else {
            if (isset($data['from_page']) && $data['from_page'] == U031D) {
                $mProductIds = $data['m_product_ids'];
                $productIsChoice = $data['m_product_ids_choose'];
            } else {
                $mProductIds = [];
            }
            $appTrademark = $tradeMarkOld->appTrademark;
            $appTrademarkProdIds = $appTrademark->appTrademarkProd->pluck('id')->toArray();
            $dataListProducts = $this->mProductService->getProductAppTrademarkByAppTrademarkProd($mProductIds, $appTrademarkProdIds);
            //if not has param forder_id
            $products = $dataListProducts;
        }
        $mailRegisterCert = $this->supportFirstTimeService->getMailRegisterCertService(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
        $periodRegistration = $this->supportFirstTimeService->getPeriodRegistrationService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
        $feeSubmit = $this->supportFirstTimeService->getFeeSubmit();
        $setting = $this->supportFirstTimeService->getSetting();
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        $packA = AppTrademark::PACK_A;
        $packB = AppTrademark::PACK_B;
        $packC = AppTrademark::PACK_C;
        $redirectToQuote = AppTrademark::REDIRECT_TO_QUOTE;
        $redirectToAnkenTop = AppTrademark::REDIRECT_TO_ANKEN_TOP;
        $redirectToCommonPayment = AppTrademark::REDIRECT_TO_COMMON_PAYMENT;
        $redirectToU021 = AppTrademark::REDIRECT_TO_U021;
        $pricePackage = $this->supportFirstTimeService->getPricePackService();
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $fees = $this->supportFirstTimeService->getSystemFee(MPriceList::BEFORE_FILING, MPriceList::SFT_SELECT_SUPPORT);

        //Data default pack A, B, C
        //pack A
        $priceBasePackA = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_A_UP_3_ITEMS);
        $priceBasePackAEachThreeItem = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_A_EACH_3_ITEMS);
        $dataDefaultPackA = [
            'cost_service_base' => $priceBasePackA['cost_service_base'],
            'commission' => $priceBasePackA['commission'],
            'cost_service_add_prod' => $priceBasePackAEachThreeItem['cost_service_base'],
        ];
        //pack B
        $priceBasePackB = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_B_UP_3_ITEMS);
        $priceBasePackBEachThreeItem = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_B_EACH_3_ITEMS);
        $dataDefaultPackB = [
            'cost_service_base' => $priceBasePackB['cost_service_base'],
            'commission' => $priceBasePackB['commission'],
            'cost_service_add_prod' => $priceBasePackBEachThreeItem['cost_service_base'],
        ];

        //pack C
        $priceBasePackC = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_C_UP_3_ITEMS);
        $priceBasePackCEachThreeItem = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_C_EACH_3_ITEMS);
        $dataDefaultPackC = [
            'cost_service_base' => $priceBasePackC['cost_service_base'],
            'commission' => $priceBasePackC['commission'],
            'cost_service_add_prod' => $priceBasePackCEachThreeItem['cost_service_base'],
        ];

        //data return view
        $data = [
            'id' => $id,
            'appTrademark' => $appTrademark,
            'mailRegisterCert' => $mailRegisterCert,
            'periodRegistration' => $periodRegistration,
            'setting' => $setting,
            'feeSubmit' => $feeSubmit,
            'nations' => $nations,
            'prefectures' => $prefectures,
            'products' => $products,
            'pricePackage' => $pricePackage,
            'paymentFee' => $paymentFee,
            'fees' => $fees,
            'packA' => $packA,
            'packB' => $packB,
            'packC' => $packC,
            'redirectToQuote' => $redirectToQuote,
            'redirectToAnkenTop' => $redirectToAnkenTop,
            'redirectToCommonPayment' => $redirectToCommonPayment,
            'redirectToU021' => $redirectToU021,
            //old data
            'tradeMarkOld' => $tradeMarkOld,
            'payerInfoOld' => $payerInfoOld,
            'tradeMarkInfosOld' => $tradeMarkInfosOld,
            'appTradeMarkProdsIsApplyIds' => $appTradeMarkProdsIsApplyIds,
            'dataDefaultPackA' => $dataDefaultPackA,
            'dataDefaultPackB' => $dataDefaultPackB,
            'dataDefaultPackC' => $dataDefaultPackC,
            'productIsChoice' => $productIsChoice
        ];

        return view('user.modules.app-trademark.u031d.index', $data);
    }

    /**
     * Post apply trademark after search - u031d
     *
     * @param Request $request
     * @param $id
     * @return void
     */
    public function postApplyTrademarkWithoutNumber(Request $request, $id = null)
    {
        $params = $request->all();
        $params['trademark_id'] = $id;
        $params['m_product_ids'] = array_unique($params['m_product_ids'] ?? []);
        $params['m_product_ids_choose'] = array_unique($params['m_product_ids_choose'] ?? []);
        $params['sum_distintion'] = $params['total_distinction'];
        $params['fee_submit_register_year'] = $params['value_fee_submit_ole'];
        if ($params['type_trademark'] == Trademark::TRADEMARK_TYPE_OTHER && $params['redirect_to'] == AppTrademark::REDIRECT_TO_U021) {
            return redirect()->back()->with('error', __('messages.general.support_U011_E008'));
        }
        $results = $this->appTrademarkService->saveDataApplyTrademarkWithNumber($params);
        if ($results) {
            switch ($results['redirect_to']) {
                case AppTrademark::REDIRECT_TO_COMMON_PAYMENT:
                    return redirect()->route('user.payment.index', ['s' => $results['key_session']]);
                case AppTrademark::REDIRECT_TO_QUOTE:
                    return redirect()->route('user.quote', ['id' => $results['payment_id']]);
                case AppTrademark::REDIRECT_TO_ANKEN_TOP:
                    return redirect()->route('user.application-detail.index', ['id' => $results['trademark_id'], 'from' => FROM_U000_TOP])
                        ->with('message', __('messages.general.Common_E047'));
                case AppTrademark::REDIRECT_TO_U021:
                    return redirect()->route('user.precheck.register-precheck', ['id' => $results['trademark_id'], 's' => $results['key_session']]);
                default:
                    return redirect()->back()->withInput()->with('error', __('messages.common.errors.Common_E025'));
            }
        }

        return redirect()->back()->withInput()->with('error', __('messages.common.errors.Common_E025'));
    }

    /**
     * Post apply trademark after search - u031b
     *
     * @param Request $request
     * @param $id
     * @return void
     */
    public function postApplyTrademarkAfterSearch(Request $request, $id = null)
    {
        $params = $request->all();
        $params['trademark_id'] = $id;
        $params['m_product_ids'] = array_unique($params['m_product_ids'] ?? []);
        $params['m_product_ids_choose'] = array_unique($params['m_product_ids_choose'] ?? []);

        $trademarkOldData = [];
        foreach ($params['data'] as $key => $value) {
            $newModel = new TrademarkInfo();
            $newModel->fill($value);
            $trademarkOldData[] = $newModel;
        }

        $payerOldData = new PayerInfo($params);
        if (count($params['m_product_ids']) <= 0) {
            abort(404);
        }

        if (!isset($params['pack'])) {
            return redirect()->back()->withInput()->with(['pack' => __('messages.common.errors.Common_E025'), 'oldRequest' => $trademarkOldData, 'payerOldData' => $payerOldData]);
        }

        if ($params['type_trademark'] == Trademark::TRADEMARK_TYPE_OTHER && $params['redirect_to'] == AppTrademark::REDIRECT_TO_U021) {
            return redirect()->back()->with('error', __('messages.general.support_U011_E008'));
        }
        $results = $this->appTrademarkService->saveDataApplyTrademarkAfterSearch($params);

        if ($results) {
            switch ($results['redirect_to']) {
                case AppTrademark::REDIRECT_TO_COMMON_PAYMENT:
                    return redirect()->route('user.payment.index', ['s' => $results['key_session']]);
                case AppTrademark::REDIRECT_TO_QUOTE:
                    return redirect()->route('user.quote', ['id' => $results['payment_id']]);
                case AppTrademark::REDIRECT_TO_ANKEN_TOP:
                    return redirect()->route('user.application-detail.index', ['id' => $results['trademark_id'], 'from' => FROM_U000_TOP])
                        ->with('message', __('messages.general.Common_E047'));
                case AppTrademark::REDIRECT_TO_U021:
                    return redirect()->route('user.precheck.register-precheck', ['id' => $results['trademark_id'], 's' => $results['key_session']]);
                default:
                    return redirect()->back()->withInput()->with('error', __('messages.common.errors.Common_E025'));
            }
        }

        return redirect()->back()->withInput()->with(['error' => __('messages.common.errors.Common_E025'), 'oldRequest' => $trademarkOldData]);
    }

    /**
     * Redirect to search ai from u031b
     *
     * @param Request $request
     * @return void
     */
    public function redirectToSearchAiFromU031b(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            $dataSearchAI = [
                'type_trademark' => null,
                'name_trademark' => null,
                'image_trademark' => null,
                'keyword' => [],
            ];
            if (!empty($inputs['trademark_id'])) {
                $trademark = $this->tradeMarkService->findByCondition([
                    'id' => $inputs['trademark_id'],
                    'user_id' => auth()->user()->id,
                ])->first();
                $dataSearchAI = [
                    'type_trademark' => $trademark->type_trademark ?? null,
                    'name_trademark' => $trademark->name_trademark ?? null,
                    'image_trademark' => $trademark->image_trademark ?? null,
                    'keyword' => [],
                ];
            } elseif (!empty($inputs['folder_id'])) {
                $folder = $this->myFolderService->find($inputs['folder_id']);
                $dataSearchAI = [
                    'type_trademark' => $folder->type_trademark ?? null,
                    'name_trademark' => $folder->name_trademark ?? null,
                    'image_trademark' => $folder->image_trademark ?? null,
                    'keyword' => [],
                ];
            }

            // Set session search_ai
            Session::put(SESSION_SEARCH_AI, $dataSearchAI);
            Session::put(SESSION_ADDITION_PRODUCT, $inputs['m_product_is']);

            // Set session referer
            Session::put(SESSION_REFERER_SEARCH_AI, [
                'referer' => FROM_U031B,
            ]);

            //Session form U031b
            Session::put(SESSION_U031B_REDIRECT_U020B, $inputs);

            return response()->json([
                'status' => true,
                'router_redirect' => route('user.search-ai.result'),
                'message' => 'Success redirect page'
            ]);
        }
    }
    /**
     * Redirect to search ai from u031d
     *
     * @param Request $request
     * @return void
     */
    public function redirectToSearchAiFromU031d(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            Session::put(SESSION_ADDITION_PRODUCT, $inputs['m_product_is']);

            // Set session referer
            Session::put(SESSION_REFERER_SEARCH_AI, [
                'referer' => FROM_SEARCH_AI,
            ]);

            return response()->json([
                'status' => true,
                'router_redirect' => route('user.search-ai.result'),
                'message' => 'Success redirect page'
            ]);
        }
    }

    /**
     * Apply trademark with number -u031c
     *
     * @param Request $request
     * @param string|int $id - trademark_id
     * @return View
     */
    public function applyTrademarkWithProductCopied(Request $request, $id = null)
    {
        if (!$request->has('s') && !$request->s) {
            return abort(403);
        }
        $data = Session::get($request->s);
        $params['trademark_id'] = $data['trademark_id'];
        $productIds = collect();
        $user = auth()->user();

        //old data if exists params id - trademark_id
        $appTrademark = null;
        $tradeMarkOld = null;
        $payerInfoOld = null;
        $tradeMarkInfosOld = null;
        $appTradeMarkProdsIsApplyIds = null;
        $folderId = null;

        //check trademark
        if (isset($data['from_page']) && $data['from_page'] == U031C) {
            $appTrademark = $this->appTrademarkService->getAppTradeMarkOfUserFindByCondition(['trademark_id' => $params['trademark_id']]);
            if (!$appTrademark) {
                abort(404);
            }
            $productIds = $appTrademark->products->pluck('id');
            //old data
            $tradeMarkOld = $this->tradeMarkService->find($params['trademark_id']);
            $payerInfoOld = $this->payerInfoService->findByCondition([
                'target_id' => $appTrademark->id ?? 0,
                'type' => Payment::TYPE_TRADEMARK
            ])->first();
            $tradeMarkInfosOld = $this->trademarkInfoService->findByCondition([
                'target_id' => $appTrademark->id,
                'type' => TrademarkInfo::TYPE_TRADEMARK
            ])->get();
            $appTradeMarkProdsIsApplyIds = $this->appTrademarkProdService->findByCondition(
                ['app_trademark_id' => $appTrademark->id, 'is_apply' => AppTrademarkProd::IS_APPLY]
            )->pluck('m_product_id');
        } else {
            $tradeMarkOld = $this->tradeMarkService->find($params['trademark_id']);
            $payerInfoOld = $this->payerInfoService->findByCondition([
                'target_id' => 0,
                'type' => Payment::TYPE_TRADEMARK
            ])->first();
            $tradeMarkInfosOld = $this->trademarkInfoService->findByCondition([
                'target_id' => 0,
                'type' => TrademarkInfo::TYPE_TRADEMARK
            ])->get();
            $appTradeMarkProdsIsApplyIds = [];
        }
        //ids product is_apply in app_trademark_prods table
        $productIsChoice = [];

        if (isset($data['from_page']) && $data['from_page'] == U207Kyo) {
            $products = $data['products'];
        } else {
            $tradeMarkOld = $this->tradeMarkService->find($params['trademark_id']);
            $appTrademark = $tradeMarkOld->appTrademark;
            if (isset($data['from_page']) && $data['from_page'] == U031C) {
                $productIsChoice = $data['m_product_ids_choose'];
                $mProductIds = $data['m_product_ids'];
            } elseif (isset($data['from_page']) && $data['from_page'] == U021C) {
                $mProductIds = $data['products'];
            } elseif (isset($data['from_page']) && $data['from_page'] == U304) {
                $products = $data['products'];

                $mProductIds = [];
                foreach ($products as $product) {
                    $productIDs = $product->pluck('id')->toArray();
                    $mProductIds = array_merge($mProductIds, $productIDs);
                }
            } else {
                $mProductIds = $data['products'];
                $productIsChoice = [];
            }
            $appTradeMarkProds = $appTrademark ? $appTrademark->appTradeMarkProds : null;
            if (!$appTrademark || empty($appTradeMarkProds)) {
                $appTradeMarkProds = [];
            }
            $dataListProducts = $this->mProductService->getProductAppTrademarkByAppTrademarkProd($mProductIds, $appTradeMarkProds);
            //if not has param forder_id
            $products = $dataListProducts;
        }
        $mailRegisterCert = $this->supportFirstTimeService->getMailRegisterCertService(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
        $periodRegistration = $this->supportFirstTimeService->getPeriodRegistrationService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
        $feeSubmit = $this->supportFirstTimeService->getFeeSubmit();
        $setting = $this->supportFirstTimeService->getSetting();
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        $packA = AppTrademark::PACK_A;
        $packB = AppTrademark::PACK_B;
        $packC = AppTrademark::PACK_C;
        $redirectToQuote = AppTrademark::REDIRECT_TO_QUOTE;
        $redirectToAnkenTop = AppTrademark::REDIRECT_TO_ANKEN_TOP;
        $redirectToCommonPayment = AppTrademark::REDIRECT_TO_COMMON_PAYMENT;
        $redirectToU021 = AppTrademark::REDIRECT_TO_U021;
        $pricePackage = $this->supportFirstTimeService->getPricePackService();
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $fees = $this->supportFirstTimeService->getSystemFee(MPriceList::BEFORE_FILING, MPriceList::SFT_SELECT_SUPPORT);

        //Data default pack A, B, C
        //pack A
        $priceBasePackA = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_A_UP_3_ITEMS);
        $priceBasePackAEachThreeItem = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_A_EACH_3_ITEMS);
        $dataDefaultPackA = [
            'cost_service_base' => $priceBasePackA['cost_service_base'],
            'commission' => $priceBasePackA['commission'],
            'cost_service_add_prod' => $priceBasePackAEachThreeItem['cost_service_base'],
        ];
        //pack B
        $priceBasePackB = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_B_UP_3_ITEMS);
        $priceBasePackBEachThreeItem = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_B_EACH_3_ITEMS);
        $dataDefaultPackB = [
            'cost_service_base' => $priceBasePackB['cost_service_base'],
            'commission' => $priceBasePackB['commission'],
            'cost_service_add_prod' => $priceBasePackBEachThreeItem['cost_service_base'],
        ];

        //pack C
        $priceBasePackC = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_C_UP_3_ITEMS);
        $priceBasePackCEachThreeItem = $this->supportFirstTimeService->getSystemFee(MPriceList::APPLICATION, MPriceList::PACK_C_EACH_3_ITEMS);
        $dataDefaultPackC = [
            'cost_service_base' => $priceBasePackC['cost_service_base'],
            'commission' => $priceBasePackC['commission'],
            'cost_service_add_prod' => $priceBasePackCEachThreeItem['cost_service_base'],
        ];
        //data return view
        $data = [
            'id' => $id,
            'appTrademark' => $appTrademark,
            'mailRegisterCert' => $mailRegisterCert,
            'periodRegistration' => $periodRegistration,
            'setting' => $setting,
            'feeSubmit' => $feeSubmit,
            'nations' => $nations,
            'prefectures' => $prefectures,
            'products' => $products,
            'pricePackage' => $pricePackage,
            'paymentFee' => $paymentFee,
            'fees' => $fees,
            'packA' => $packA,
            'packB' => $packB,
            'packC' => $packC,
            'redirectToQuote' => $redirectToQuote,
            'redirectToAnkenTop' => $redirectToAnkenTop,
            'redirectToCommonPayment' => $redirectToCommonPayment,
            'redirectToU021' => $redirectToU021,
            //old data
            'tradeMarkOld' => $tradeMarkOld,
            'payerInfoOld' => $payerInfoOld,
            'tradeMarkInfosOld' => $tradeMarkInfosOld,
            'appTradeMarkProdsIsApplyIds' => $appTradeMarkProdsIsApplyIds,
            'dataDefaultPackA' => $dataDefaultPackA,
            'dataDefaultPackB' => $dataDefaultPackB,
            'dataDefaultPackC' => $dataDefaultPackC,
            'productIsChoice' => $productIsChoice
        ];

        return view('user.modules.app-trademark.u031c.index', $data);
    }

    /**
     * Post apply trademark after search - u031b
     *
     * @param Request $request
     * @param $id
     * @return void
     */
    public function postApplyTrademarkWithProductCopied(Request $request, $id = null)
    {
        $params = $request->all();
        $params['trademark_id'] = $id;
        $params['m_product_ids'] = array_unique($params['m_product_ids']);
        $params['m_product_ids_choose'] = array_unique($params['m_product_ids_choose']);
        $params['sum_distintion'] = $params['total_distinction'];
        $params['fee_submit_register_year'] = $params['value_fee_submit_ole'];
        if ($params['type_trademark'] == Trademark::TRADEMARK_TYPE_OTHER && $params['redirect_to'] == 'U021') {
            return redirect()->back()->with('error', __('messages.general.support_U011_E008'));
        }
        $results = $this->appTrademarkService->saveDataApplyTrademarkWithProductCopied($params);
        if ($results) {
            switch ($results['redirect_to']) {
                case AppTrademark::REDIRECT_TO_COMMON_PAYMENT:
                    return redirect()->route('user.payment.index', ['s' => $results['key_session']]);
                case AppTrademark::REDIRECT_TO_QUOTE:
                    return redirect()->route('user.quote', ['id' => $results['payment_id']]);
                case AppTrademark::REDIRECT_TO_ANKEN_TOP:
                    return redirect()->route('user.application-detail.index', ['id' => $results['trademark_id'], 'from' => FROM_U000_TOP])
                        ->with('message', __('messages.general.Common_E047'));
                case AppTrademark::REDIRECT_TO_U021:
                    return redirect()->route('user.precheck.register-precheck', ['id' => $results['trademark_id'], 's' => $results['key_session']]);
                default:
                    return redirect()->back()->withInput()->with('error', __('messages.common.errors.Common_E025'));
            }
        }

        return redirect()->back()->withInput()->with('error', __('messages.common.errors.Common_E025'));
    }

    /**
     * Redirect to search ai from u031c
     *
     * @param Request $request
     * @return void
     */
    public function redirectToSearchAiFromU031c(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            Session::put(SESSION_ADDITION_PRODUCT, $inputs['m_product_is']);

            // Set session referer
            Session::put(SESSION_REFERER_SEARCH_AI, [
                'referer' => FROM_SEARCH_AI,
            ]);

            return response()->json([
                'status' => true,
                'router_redirect' => route('user.search-ai.result'),
                'message' => 'Success redirect page'
            ]);
        }
    }
}
