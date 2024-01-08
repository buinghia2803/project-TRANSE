<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Precheck\PrecheckRegisterTimeNRequest;
use App\Models\AppTrademark;
use App\Models\MPriceList;
use App\Models\MProduct;
use App\Models\Payment;
use App\Models\Precheck;
use App\Models\PrecheckProduct;
use App\Models\PrecheckResult;
use App\Models\Setting;
use App\Models\Trademark;
use App\Services\PayerInfoService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\MDistinctionService;
use App\Services\MProductService;
use App\Services\PrecheckService;
use App\Services\SettingService;
use App\Services\SupportFirstTimeService;
use App\Services\TrademarkService;
use App\Services\TrademarkInfoService;
use App\Repositories\AppTrademarkProdRepository;
use App\Services\Common\TrademarkTableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;

class PrecheckController extends Controller
{
    protected $mNationService;
    protected $mPrefectureService;
    protected $mDistinctionService;
    protected $mProductService;
    protected $tradeMarkService;
    protected $precheckService;
    protected $supportFirstTimeService;
    protected TrademarkTableService $trademarkTableService;
    protected TrademarkInfoService $trademarkInfoService;
    protected PayerInfoService $payerInfoService;
    protected AppTrademarkProdRepository $appTrademarkProdRepository;
    protected SettingService $settingService;

    /**
     * Constructor
     *
     * @param MNationService $mNationService
     * @param MPrefectureService $mPrefectureService
     * @param MDistinctionService $mDistinctionService
     * @param MProductService $mProductService
     * @param TrademarkService $tradeMarkService
     * @param PrecheckService $precheckService
     * @param AppTrademarkProdRepository $appTrademarkProdRepository
     * @param SettingService $settingService
     *
     * @return  void
     */

    public function __construct(
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        MDistinctionService $mDistinctionService,
        MProductService $mProductService,
        TrademarkService $tradeMarkService,
        PrecheckService $precheckService,
        SupportFirstTimeService $supportFirstTimeService,
        TrademarkTableService $trademarkTableService,
        TrademarkInfoService $trademarkInfoService,
        PayerInfoService $payerInfoService,
        AppTrademarkProdRepository $appTrademarkProdRepository,
        SettingService $settingService
    )
    {
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
        $this->mDistinctionService = $mDistinctionService;
        $this->mProductService = $mProductService;
        $this->tradeMarkService = $tradeMarkService;
        $this->precheckService = $precheckService;
        $this->supportFirstTimeService = $supportFirstTimeService;
        $this->trademarkTableService = $trademarkTableService;
        $this->trademarkInfoService = $trademarkInfoService;
        $this->payerInfoService = $payerInfoService;
        $this->appTrademarkProdRepository = $appTrademarkProdRepository;
        $this->settingService = $settingService;
    }

    /**
     * Register precheck first time - u021
     *
     * @param Request $request
     * @param int $id - trademark_id
     * @return View
     */
    public function registerPrecheck(Request $request, int $id): View
    {
        $secretKey = $request->s;
        $checkExits = $this->tradeMarkService->find($id);
        //check data existst
        if (!$checkExits) {
            abort(404);
        }
        $tradeMark = $this->tradeMarkService->findByCondition(['id' => $id, 'user_id' => auth()->user()->id])
            ->select('id', 'name_trademark', 'reference_number', 'trademark_number', 'created_at', 'type_trademark')
            ->first();
        //check permission view page
        if (!$tradeMark || !Session::has($secretKey)) {
            abort(403);
        }

        $preheckOld = $tradeMark->prechecks->first();
        $dataSession = Session::get($secretKey);

        $idsProduct = [];
        // session return from u011b
        if (!empty($dataSession['data']) && !empty($dataSession['data']['m_product_ids'])) {
            $idsProduct = $dataSession['data']['m_product_ids'];
        }

        if (!empty($dataSession['data']) && !empty($dataSession['data']['products'])) {
            $productArray = collect($dataSession['data']['products']);
            $idsProduct = $productArray->pluck('id')->toArray();
        }

        // session return from common payment
        if (isset($dataSession['m_product_ids']) && $dataSession['m_product_ids']) {
            $idsProduct = $dataSession['m_product_ids'];
        }

        $mProductChoose = $this->mProductService->getDataMproduct($idsProduct);
        if (count($mProductChoose) == 0) {
            abort(404);
        }

        $precheckProductIdsOld = [];

        if (!empty($productArray)) {
            foreach ($mProductChoose as $productData) {
                foreach ($productData as $product) {
                    $productInfo = $productArray->where('id', $product->id)->first();

                    if (!empty($productInfo) && $productInfo['is_apply'] == 'true') {
                        $precheckProductIdsOld[] = $productInfo['id'];
                    }
                }
            }
        }

        $payerInfo = null;
        if ($preheckOld) {
            $payerInfo = $this->payerInfoService
                ->findByCondition(['target_id' => $preheckOld->id, 'type' => Payment::TYPE_PRECHECK])
                ->get()
                ->last();
            //precheck_products is choose
            $precheckProductIdsOld = $preheckOld->precheckProduct()->where('is_register_product', PrecheckProduct::IS_PRECHECK_PRODUCT)
                ->pluck('m_product_id')
                ->toArray();
        }

        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_2, $tradeMark->id, [
            'input_name_trademark' => true,
            'input_name' => 'name_trademark'
        ]);
        $paymentFee = $this->getPaymentFee();
        $typePrecheckSimple = Precheck::TYPE_CHECK_SIMPLE;
        $typePrecheckSelect = Precheck::TYPE_CHECK_SELECT;
        $statusRegisterTrue = Precheck::HAS_STATUS_REGISTER;

        //data get fee product by type precheck
        $dataFeeDefaultPrecheck = $this->precheckService->getDataFeeDefaultPrecheck();

        $data = [
            'nations' => $nations,
            'prefectures' => $prefectures,
            'mProductChoose' => $mProductChoose,
            'tradeMark' => $tradeMark,
            'from_page' => $dataSession['from_page'] ?? null,
            'trademarkTable' => $trademarkTable,
            'paymentFee' => $paymentFee,
            'payerInfo' => $payerInfo,
            'typePrecheckSimple' => $typePrecheckSimple,
            'typePrecheckSelect' => $typePrecheckSelect,
            'statusRegister' => $preheckOld->status_register ?? 0,
            'preheckOld' => $preheckOld,
            'precheckProductIdsOld' => $precheckProductIdsOld,
            'statusRegisterTrue' => $statusRegisterTrue,
            'dataFeeDefaultPrecheck' => $dataFeeDefaultPrecheck,
        ];

        return view('user.modules.prechecks.register-precheck.index', $data);
    }

    /**
     * Get payment fee
     *
     * @return array
     */
    public function getPaymentFee()
    {
        $taxData = $this->settingService->findByCondition(['key' => SETTING::KEY_TAX])->first();
        $priceCommonFeeBank = $this->precheckService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $paymentFee['cost_service_base'] = $priceCommonFeeBank->base_price + $priceCommonFeeBank->base_price * $taxData->value / 100;

        return $paymentFee;
    }

    /**
     * Ajax get info payment
     *
     * @param Request $request
     * @return void
     */
    public function ajaxGetInfoPayment(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            $respon = $this->precheckService->getInfoPaymentPrecheck($inputs);

            return response()->json([
                'status' => true,
                'data' => $respon
            ]);
        }
    }

    /**
     * Register for precheck-n for the nth time - u021n
     *
     * @param Request $request
     * @param integer $trademarkId - trademark_id
     * @return View
     */
    public function getRegisterTimeN(Request $request, int $trademarkId): View
    {
        $inputs = $request->all();
        if (!isset($inputs['precheck_id']) && empty($inputs['precheck_id'])) {
            abort(404);
        }

        //check exists trademark
        $trademark = $this->tradeMarkService->findByCondition([
            'id' => $trademarkId,
            'user_id' => auth()->user()->id,
        ])->first();

        $prechecks = $this->precheckService->findByCondition([
            'trademark_id' => $trademarkId,
        ])->get();

        $precheckOld = $prechecks->where('id', $inputs['precheck_id'])->first();

        if (!$trademark || !$precheckOld) {
            abort(404);
        }

        $precheckBefore = $prechecks->where('id', '<', $precheckOld->id);
        if (!empty($precheckBefore)) {
            $precheckBefore = $precheckBefore->load(['precheckProduct.precheckResult']);
        }

        $precheckNext = $prechecks->where('id', '>', $precheckOld->id)->first();

        // Precheck History
        $precheckHistories = $prechecks->where('id', '<=', $precheckOld->id)->load(['precheckProduct']);
        $registerProduct = [
            'simple' => [],
            'detail' => [],
        ];
        foreach ($precheckHistories as $precheckHistory) {
            $precheckProducts = $precheckHistory->precheckProduct;
            $registerProds = $precheckProducts->where('is_register_product', PrecheckProduct::IS_PRECHECK_PRODUCT);
            $registerProdIDs = $registerProds->pluck('m_product_id')->toArray();

            if ($precheckHistory->type_precheck == Precheck::TYPE_CHECK_SIMPLE) {
                $registerProduct['simple'] = array_merge($registerProduct['simple'], $registerProdIDs);
            } elseif ($precheckHistory->type_precheck == Precheck::TYPE_CHECK_SELECT) {
                $registerProduct['detail'] = array_merge($registerProduct['detail'], $registerProdIDs);
            }
        }

        //check permission edit or view precheck
        $flugEditPrecheck = $this->checkPermissionEditPrecheck($trademark, $precheckOld);

        //info payer info
        $payerInfo = $this->payerInfoService->findByCondition([
            'target_id' => $precheckNext->id ?? 0,
            'type' => Payment::TYPE_PRECHECK
        ])->first();

        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        //info precheck show table
        $infoPrecheckTable = $this->precheckService->getInfoPrecheckShowTable($precheckOld->id);

        //data price basic and tax
        $dataPriceBasicTax = [
            $this->supportFirstTimeService->getSystemFee(MPriceList::BEFORE_FILING, MPriceList::PRECHECK1_SERVICE_UP_3_PRODS),
            $this->supportFirstTimeService->getSystemFee(MPriceList::BEFORE_FILING, MPriceList::PRECHECK1_SERVICE_EACH_3_PRODS),
            $this->supportFirstTimeService->getSystemFee(MPriceList::BEFORE_FILING, MPriceList::PRECHECK2_SERVICE_UP_3_PRODS),
            $this->supportFirstTimeService->getSystemFee(MPriceList::BEFORE_FILING, MPriceList::PRECHECK2_SERVICE_EACH_3_PRODS),
        ];

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_3, $trademarkId);
        $paymentFee = $this->getPaymentFee();
        $typePrecheckSimple = Precheck::TYPE_CHECK_SIMPLE;

        $data = [
            'trademark' => $trademark,
            'nations' => $nations,
            'prefectures' => $prefectures,
            'infoPrecheckTable' => $infoPrecheckTable,
            'dataPriceBasicTax' => $dataPriceBasicTax,
            'trademarkTable' => $trademarkTable,
            'paymentFee' => $paymentFee,
            'typePrecheckSimple' => $typePrecheckSimple,
            'payerInfo' => $payerInfo,
            'precheckOld' => $precheckOld,
            'precheckBefore' => $precheckBefore,
            'precheckNext' => $precheckNext,
            'registerProduct' => $registerProduct,
            'flugEditPrecheck' => $flugEditPrecheck
        ];

        return view('user.modules.prechecks.precheck-n.index', $data);
    }

    /***
     * Check Permission Edit Precheck
     *
     * @param $trademark
     * @param $precheck
     * @return bool
     */
    public function checkPermissionEditPrecheck($trademark, $precheck): bool
    {
        $trademark->load(['appTrademark', 'prechecks']);
        $appTrademark = $trademark->appTrademark;
        //check app_trademark
        if ($appTrademark && $appTrademark->status != AppTrademark::STATUS_UNREGISTERED_SAVE) {
            return false;
        }
        $prechecks = $trademark->prechecks;
        foreach ($prechecks as $k => $item) {
            if ($item->id == $precheck->id) {
                if ($prechecks->has($k + 1)) {
                    $precheckNext = $prechecks[$k + 1];
                    if ($precheckNext && $precheckNext->status_register == Precheck::HAS_STATUS_REGISTER) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Post register precheck time N - data request form page:u021 or u021n
     *
     * @param PrecheckRegisterTimeNRequest $request
     * @param int $trademarkId
     * @return mixed
     */
    public function postRegisterPrecheckTimeN(PrecheckRegisterTimeNRequest $request, int $trademarkId)
    {
        $inputs = $request->all();
        if ($inputs['from_page'] == U021N) {
            if (!isset($inputs['precheck_id']) && empty($inputs['precheck_id'])) {
                abort(404);
            }
            //check exists trademark
            $trademark = $this->tradeMarkService->findByCondition([
                'id' => $trademarkId,
                'user_id' => auth()->user()->id,
            ])->first();

            $precheckOld = $this->precheckService->findByCondition([
                'id' => $inputs['precheck_id'],
                'trademark_id' => $trademarkId,
            ])->first();

            if (!$trademark || !$precheckOld) {
                abort(404);
            }
            //check permission edit or view precheck
            $flugEditPrecheck = $this->checkPermissionEditPrecheck($trademark, $precheckOld);
            if (!$flugEditPrecheck) {
                abort(403);
            }
        }
        $dataPayment = $this->precheckService->savePrecheckPost($inputs, $trademarkId);

        if ($dataPayment && !empty($dataPayment['redirect_to'])) {
            return redirect()->to($dataPayment['redirect_to']);
        }

        return redirect()->back()->with('messages', __('messages.common.errors.Common_E025'));
    }

    /**
     * Register precheck by trademark
     *
     * Form page: u021b_31, u021b, u011b_31, u011b, u031edit_width_number
     * To page: u031c, u021, AIu020b, u011
     *
     * @param Request $request
     * @param int $id - id_trademark
     *
     * @return View
     */
    public function registerDifferentBrand(Request $request, int $id): View
    {
        $trademark = $this->tradeMarkService->findByCondition([
            'id' => $id,
            'user_id' => auth()->user()->id,
        ])->first();
        // Check params session, check session exists
        if (!$request->s || !Session::has($request->s) || !$trademark) {
            abort(404);
        }

        $key = $request->s;
        $sessionData = Session::get($key);
        $sessionData['from_page'] = U021C;
        $sessionData['trademark_id'] = $id;
        $sessionData['products'] = $sessionData['data']['m_product_ids'];
        Session::put($key, $sessionData);

        $data = [
            'key' => $key,
            'trademark_id' => $id,
        ];

        return view('user.modules.prechecks.register-different-brand.index', $data);
    }

    /**
     * Showing report precheck from AMS
     *
     * @param int $id
     * @return View
     */
    public function reportPrecheckAMS(int $id): View
    {
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        $precheck = $this->precheckService->getPrecheck($id);

        if (!$precheck) {
            abort(404);
        }

        $getProductOfDistinction = $this->precheckService->getProductOfDistinction($precheck->id);
        $pricePackage = $this->precheckService->getPricePackService();
        $mailRegisterCert = $this->precheckService->getMailRegisterCertService();
        $periodRegistration = $this->precheckService->getPeriodRegistrationService();

        return view('user.modules.precheck.report_precheck_from_AMS', compact(
            'precheck',
            'getProductOfDistinction',
            'pricePackage',
            'mailRegisterCert',
            'periodRegistration',
            'nations',
            'prefectures'
        ));
    }

    /**
     * Showing report precheck from AMS
     * Screen u021b
     *
     * @param Request $request
     * @param string|int $id - trademark_id?precheck_id={prechecks.id}
     * @return View
     */
    public function applicationTrademark(Request $request, $id): View
    {
        $precheckId = $request->precheck_id;
        if (!isset($precheckId)) {
            abort(404);
        }
        $trademark = Trademark::find($id);
        if (!$trademark) {
            abort(404);
        }
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }
        $trademark = $trademark->load('appTrademark');
        $appTrademark = $trademark->appTrademark ?? null;

        $checkDataPrecheck = false;
        $statusUnregisteredSave = AppTrademark::STATUS_UNREGISTERED_SAVE;
        // if (!empty($appTrademark) && $appTrademark->status != AppTrademark::STATUS_UNREGISTERED_SAVE) {
        //     $checkDataPrecheck = true;
        // }
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        $precheck = $this->precheckService->getPrecheckWithId($id, $precheckId); //login
        if (!$precheck) {
            abort(404);
        }

        $precheckBefore = $this->precheckService->findByCondition(['trademark_id' => $trademark->id])
            ->where('id', '<', $precheck->id)->get();
        if (!empty($precheckBefore)) {
            $precheckBefore = $precheckBefore->load(['precheckProduct.precheckResult']);
        }

        $precheckNext = $this->precheckService->findByCondition(['trademark_id' => $trademark->id])->where('id', '>', $precheckId)->first();
        if ($precheckNext && $precheckNext->status_register == Precheck::HAS_STATUS_REGISTER) {
            $checkDataPrecheck = true;
        }

        $getProductOfDistinction = $this->precheckService->getProductOfDistinction($precheck->id);
        $countDistinct = count($getProductOfDistinction);
        $pricePackage = $this->supportFirstTimeService->getPricePackService();
        $mailRegisterCert = $this->supportFirstTimeService->getSystemFee(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
        $registerTermChange = $this->supportFirstTimeService->getPeriodRegistrationService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
        $periodRegistration = $this->supportFirstTimeService->getPeriodRegistrationService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
        $setting = $this->supportFirstTimeService->getSetting();
        $feeSubmit = $this->supportFirstTimeService->getFeeSubmit();
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_3, $id);
        $fromPageTrademarkInfo = 'u021n_' . $precheckId;
        $trademarkInfos = $appTrademark ? $this->trademarkInfoService->findTrademarkInfo($appTrademark, $fromPageTrademarkInfo) : null;
        $payerInfo = $this->payerInfoService->findPayerInfoWithAppTrademark($appTrademark->id ?? 0);
        $routeCancel = null;
        if ($appTrademark) {
            $routeCancel = route('user.apply-trademark.cancel-register', $appTrademark->id); //change before start
        }

        return view('user.modules.precheck.u021b', compact(
            'id',
            'setting',
            'nations',
            'precheck',
            'precheckBefore',
            'trademark',
            'payerInfo',
            'feeSubmit',
            'paymentFee',
            'routeCancel',
            'prefectures',
            'appTrademark',
            'pricePackage',
            'countDistinct',
            'trademarkInfos',
            'trademarkTable',
            'mailRegisterCert',
            'checkDataPrecheck',
            'periodRegistration',
            'registerTermChange',
            'getProductOfDistinction',
            'statusUnregisteredSave'
        ));
    }

    /**
     * Create payment for precheck
     *
     * @param  Request $request
     * @return void
     */
    public function redirectU020b(Request $request)
    {
        $inputs = $request->all();
        if (isset($inputs['m_product_is']) && count($inputs['m_product_is'])) {
            //if session isset precheck_id
            if (isset($inputs['precheck_id'])) {
                Session::put(SESSION_REFERER_SEARCH_AI, [
                    'referer' => FROM_PRECHECK,
                    'precheck_id' => $inputs['precheck_id'],
                    'trademark_id' => $inputs['trademark_id'],
                ]);
                Session::put(SESSION_SUGGEST_PRODUCT, $inputs['m_product_is']);
            }
            if (Session::has(SESSION_REFERER_SEARCH_AI) && Session::has(SESSION_SUGGEST_PRODUCT)) {
                return response()->json([
                    'status' => true,
                    'router_redirect' => route('user.search-ai.result'),
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
     * Create payment for precheck
     *
     * @param  Request $request
     * @return void
     */
    public function redirectU021(Request $request)
    {
        $inputs = $request->all();
        $data['data'] = $inputs;
        if (isset($data['data']['m_product_ids']) && count($data['data']['m_product_ids'])) {
            //if session isset precheck_id
            $secretKey = Str::random(11);

            if (isset($data['data']['trademark_id'])) {
                Session::put($secretKey, $data);
            }
            if (Session::has($secretKey)) {
                return response()->json([
                    'status' => true,
                    'router_redirect' => route('user.precheck.register-precheck', ['id' => $data['data']['trademark_id'], 's' => $secretKey]),
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
     * Create payment for precheck
     *
     * @param  Request $request
     * @return void
     */
    public function postApplicationTrademark(Request $request)
    {
        $results = $this->precheckService->createPaymentPrecheck($request->all());

        if ($results) {
            switch ($results['redirect_to']) {
                case Precheck::REDIRECT_TO_COMMON_PAYMENT:
                    return redirect()->route('user.payment.index', ['s' => $results['key_session']]);
                case Precheck::REDIRECT_TO_QUOTE:
                    return redirect()->route('user.quote', ['id' => $results['payment_id']]);
                case Precheck::REDIRECT_TO_ANKEN_TOP:
                    return redirect()->route('user.application-detail.index', ['id' => $results['trademark_id'], 'from' => FROM_U000_TOP]);
                default:
                    return redirect()->back()->with('error', __('messages.common.errors.Common_E025'));
            }
        }

        return redirect()->back()->with('error', __('messages.common.errors.Common_E025'));
    }

    /**
     * Showing report precheck from AMS
     * Screen u021b
     *
     * @param Request $request
     * @param string|int $id
     * @return View
     */
    public function applicationTrademarkV2(Request $request, $id): View
    {
        $trademark = Trademark::find($id);
        if (!$trademark) {
            abort(404);
        }
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }
        $trademark = $trademark->load('appTrademark');
        $appTrademark = $trademark->appTrademark ?? null;
        if (!empty($appTrademark) && $appTrademark->status != AppTrademark::STATUS_UNREGISTERED_SAVE) {
            abort(404);
        }
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $precheck = $this->precheckService->getPrecheck($id);
        if (!$precheck) {
            abort(404);
        }

        $precheckBefore = $this->precheckService->findByCondition(['trademark_id' => $trademark->id])
            ->where('id', '<', $precheck->id)->get();
        if (!empty($precheckBefore)) {
            $precheckBefore = $precheckBefore->load(['precheckProduct.precheckResult']);
        }

        $getProductOfDistinction = $this->precheckService->getProductOfDistinction($precheck->id);

        $countDistinct = count($getProductOfDistinction);
        $pricePackage = $this->supportFirstTimeService->getPricePackService();
        $mailRegisterCert = $this->supportFirstTimeService->getSystemFee(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
        $registerTermChange = $this->supportFirstTimeService->getPeriodRegistrationService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
        $periodRegistration = $this->supportFirstTimeService->getPeriodRegistrationService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
        $setting = $this->supportFirstTimeService->getSetting();
        $feeSubmit = $this->supportFirstTimeService->getFeeSubmit();
        $trademarkInfos = $appTrademark ? $this->trademarkInfoService->findTrademarkInfo($appTrademark) : null;
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_3, $id);

        //const
        $typeSimple = Precheck::TYPE_PRECHECK_SIMPLE_REPORT;
        $typeDetailed = Precheck::TYPE_PRECHECK_DETAILED_REPORT;
        $listResultSmilarSimpleOptions = PrecheckResult::listResultSmilarSimpleOptions();
        $listResultIdentificationDetailOptions = PrecheckResult::listResultIdentificationDetailOptions();
        $listResultSimilarDetailOptions = PrecheckResult::listResultSimilarDetailOptions();
        $payerInfo = $this->payerInfoService->findPayerInfoWithAppTrademark($appTrademark->id ?? 0);
        $routeCancel = null;
        if ($appTrademark) {
            $routeCancel = route('user.apply-trademark.cancel-register', $appTrademark->id); //change before start
        }
        return view('user.modules.precheck.u021b_31', compact(
            'id',
            'setting',
            'nations',
            'precheck',
            'precheckBefore',
            'feeSubmit',
            'typeSimple',
            'appTrademark',
            'typeDetailed',
            'routeCancel',
            'trademark',
            'payerInfo',
            'paymentFee',
            'prefectures',
            'pricePackage',
            'countDistinct',
            'trademarkInfos',
            'trademarkTable',
            'mailRegisterCert',
            'registerTermChange',
            'periodRegistration',
            'getProductOfDistinction',
            'listResultSmilarSimpleOptions',
            'listResultSimilarDetailOptions',
            'listResultIdentificationDetailOptions',
        ));
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
                'from_page' => U021B_31,
                'data' => [
                    'm_product_ids' => $inputs['m_product_ids'],
                    'precheck_id' => $inputs['precheck_id'] ?? null,
                    'sft_id' => $inputs['sft_id'] ?? null,
                    'trademark_id' => $inputs['trademark_id'] ?? null
                ]
            ]);
            if (Session::has(SESSION_APPLY_TRADEMARK_WITH_NUMBER)) {
                return response()->json([
                    'status' => true,
                    'router_redirect' => route('user.precheck.apply-trademark-with-number', ['id' => $inputs['trademark_id']]),
                    'message' => 'Success redirect page'
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Error redirect',
        ]);
    }

    /**
     * Screen u031edit_with_number
     *
     * @param int $id
     * @return View
     */
    public function applyTrademarkWithNumber(int $id): View
    {
        $trademark = $this->tradeMarkService->find($id);

        if (empty($trademark)) {
            abort(404);
        }
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }

        $trademark = $trademark->load(['appTrademark', 'supportFirstTime']);
        $appTrademark = $trademark->appTrademark ?? null;
        $sft = $trademark->supportFirstTime ?? null;

        $dataSession = Session::get(SESSION_APPLY_TRADEMARK_WITH_NUMBER);
        $payerInfo = $this->payerInfoService->findPayerInfoWithAppTrademark($appTrademark->id ?? 0);

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_2, $id);
        $trademarkInfos = $appTrademark ? $this->trademarkInfoService->findTrademarkInfo($appTrademark) : null;

        $idsProduct = [];
        $mProductChoose = [];
        $products = $this->supportFirstTimeService->searchRecommend([]);

        if (!empty($dataSession['data']) && !empty($dataSession['data']['m_product_ids'])) {
            $mProductIds = $dataSession['data']['m_product_ids'];
            $mProductIds = collect($mProductIds);
            $idsProduct = $mProductIds->pluck('id')->toArray();

            $mProductChoose = $this->mProductService->getDataMproduct($idsProduct);

            foreach ($mProductChoose as $products) {
                foreach ($products as $keyItem => $item) {
                    $isChoiceUser = $mProductIds->where('id', $item->id)->first();
                    $isChoiceUser = $isChoiceUser['is_choice_user'] ?? false;

                    $item->is_apply = $isChoiceUser == 'true';
                }
            }
        } else {
            $appTrademarkProds = $this->appTrademarkProdRepository->findByCondition([
                'app_trademark_id' => $appTrademark->id ?? 0,
            ])->with('mProduct', 'mProduct.mDistinction')->get();
            $appTrademarkProds->map(function ($item) {
                $product = $item->mProduct;
                $mDistinction = $product->mDistinction;

                $item->id = $product->id;
                $item->distinction_id = $mDistinction->id;
                $item->distinction_name = $mDistinction->name;
                $item->name = $product->name;

                return $item;
            });

            $mProductChoose = $appTrademarkProds->groupBy('distinction_name');
        }

        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $distinctions = $this->mDistinctionService->listDistinctionOptions();

        $setting = $this->supportFirstTimeService->getSetting();
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);

        $pricePackageA = $this->precheckService->getPriceOnePackService(MPriceList::APPLICATION, MPriceList::PACK_A_UP_3_ITEMS);
        $pricePackageEachA = $this->precheckService->getPriceOnePackService(MPriceList::APPLICATION, MPriceList::PACK_A_EACH_3_ITEMS);
        $priceCostBank = $this->precheckService->getPriceOnePackService(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $print5yrs = $this->precheckService->getPriceOnePackService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
        $routeCancel = null;
        if ($appTrademark) {
            $routeCancel = route('user.apply-trademark.cancel-register', $appTrademark->id); //change before start
        }

        return view('user.modules.prechecks.u031edit_with_number.index', compact(
            'id',
            'products',
            'sft',
            'nations',
            'setting',
            'print5yrs',
            'payerInfo',
            'paymentFee',
            'routeCancel',
            'prefectures',
            'distinctions',
            'trademarkInfos',
            'priceCostBank',
            'pricePackageA',
            'trademarkTable',
            'mProductChoose',
            'pricePackageEachA',
            'appTrademark'
        ));
    }

    /**
     * Create Apply Trademark With Number
     *
     * @param  Request $request
     * @return void
     */
    public function applyTrademarkWithNumberCreate(Request $request)
    {
        $results = $this->precheckService->applyTrademarkWithNumberCreate($request->all());
        if ($results) {
            switch ($results['redirect_to']) {
                case Precheck::REDIRECT_TO_COMMON_PAYMENT:
                    return redirect()->route('user.payment.index', ['s' => $results['key_session']]);
                case Precheck::REDIRECT_TO_QUOTE:
                    return redirect()->route('user.quote', ['id' => $results['payment_id']]);
                case Precheck::REDIRECT_TO_ANKEN_TOP:
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E047'));
                    return redirect()->route('user.application-detail.index', ['id' => $results['trademark_id'], 'from' => FROM_U000_TOP]);
                default:
                    return redirect()->back()->with('error', __('messages.common.errors.Common_E025'));
            }
        }
        return redirect()->back()->with('error', __('messages.common.errors.Common_E025'));
    }

    /**
     * ApplyTrademarkRegister - Screen u031
     *
     * @param $id
     * @param Request $request
     * @return View
     */
    public function applyTrademarkRegister(Request $request, $id = null): View
    {
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
        $mProductChoose = null;
        $fromPage = null;
        if (isset($request->from_page)) {
            $fromPage = $request->from_page;
        }
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
                abort(404);
            }

            $trademarkInfos = $appTrademark ? $this->trademarkInfoService->findTrademarkInfo($appTrademark) : null;
            $payerInfo = $this->payerInfoService->findPayerInfoWithAppTrademark($appTrademark->id ?? 0);

            $idsProduct = $this->appTrademarkProdRepository->findByCondition([
                'app_trademark_id' => $appTrademark->id,
            ])->get()->pluck('m_product_id')->toArray();

            $appTrademarkProds = $this->appTrademarkProdRepository->findByCondition([
                'app_trademark_id' => $appTrademark->id ?? 0,
            ])->with('mProduct', 'mProduct.mDistinction')->get()->sortBy('mProduct.mDistinction.name');
            $appTrademarkProds->map(function ($item) {
                $product = $item->mProduct;
                $mDistinction = $product->mDistinction;

                $item->app_trademark_prod_id = $item->id;
                $item->id = $product->id;
                $item->distinction_id = $mDistinction->id;
                $item->distinction_name = $mDistinction->name;
                $item->name = $product->name;

                return $item;
            });

            $mProductChoose = $appTrademarkProds->groupBy('distinction_name');

            return view('user.modules.prechecks.u031.index', compact(
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
                'fromPage',
                'appTrademarkProds'
            ));
        }

        return view('user.modules.prechecks.u031.index', compact(
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
            'mProductChoose',
            'fromPage',
        ));
    }

    /**
     * Create Apply Trademark Register
     *
     * @param  Request $request
     * @return void
     */
    public function applyTrademarkRegisterCreate(Request $request)
    {
        $results = $this->precheckService->applyTrademarkRegisterCreate($request);
        if ($results) {
            switch ($results['redirect_to']) {
                case Precheck::REDIRECT_TO_COMMON_PAYMENT:
                    return redirect()->route('user.payment.index', ['s' => $results['key_session']]);
                case Precheck::REDIRECT_TO_QUOTE:
                    return redirect()->route('user.quote', ['id' => $results['payment_id']]);
                case Precheck::REDIRECT_TO_ANKEN_TOP:
                    if (isset($results['isAjax']) && $results['isAjax']) {
                        CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.update_success'));
                        // if ($request->has('submit_type') && $request->submit_type == 'draft') {
                        //     return response()->json([
                        //         'status' => true,
                        //         'router_redirect' => route('user.apply-trademark-register', ['id' => $results['trademark_id']]),
                        //     ]);
                        // }
                        return response()->json([
                            'status' => true,
                            'router_redirect' => route('user.apply-trademark-register', ['id' => $results['trademark_id']]),
                        ]);
                    }
                    return redirect()->route('user.application-detail.index', ['id' => $results['trademark_id'], 'from' => FROM_U000_TOP]);
                default:
                    if (isset($results['isAjax']) && $results['isAjax']) {
                        return response()->json([
                            'status' => false,
                            'message' => __('messages.general.Trademark_over_50_record'),
                        ]);
                    }
                    return redirect()->back()->with('error', __('messages.general.Trademark_over_50_record'));
            }
        }
        return redirect()->back()->with('error', __('messages.general.update_fail'));
    }

    /**
     * SearchRecommend
     *
     * @param Request $request
     * @return Response
     */
    public function searchRecommend(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->all();
            $product = $this->supportFirstTimeService->searchRecommend($input);
            $html = '<div class="search-suggest" id="suggest_search">
            <div class="search-suggest__list">';
            if ($product->count()) {
                foreach ($product as $value) {
                    $html = $html . '
                    <div class="item" data-id="' . $value->id . '" prod_value="' . $value->name . '" product-id="' . $value->id . '" key_item="' . $request->prod .
                        '" key_type="' . $value->type . '" >' . $value->name . '</div>
                ';
                }
            } else {
                $html = $html . '<div class="no_item">no item</div>';
            }

            $html = $html . '</div></div>';

            return response()->json([
                'status' => true,
                'data' => [
                    'html' => $html,
                    'timer' => $request->timer ?? null,
                    'prod' => $request->prod,
                ],
            ], 200);
        }
        return response()->json([
            'status' => false,
        ], 200);
    }

    /**
     * SearchRecommendGetItem
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchRecommendGetItem(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->all();
            $product = $this->supportFirstTimeService->searchRecommendGetItem($input);

            return response()->json([
                'status' => true,
                'data' => $product,
            ], 200);
        }
        return response()->json([
            'status' => false,
        ], 200);
    }

    /**
     * Redirect U021c
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function redirectU021c(Request $request): JsonResponse
    {
        $inputs = $request->all();
        return $this->precheckService->redirectU021c($inputs);
    }

    /**
     * Redirect with session form u201c to u020b
     *
     * @param Request $request
     * @return mixed
     */
    public function redirectToU020b(Request $request)
    {
        $inputs = $request->all();
        if (isset($inputs['s'])) {
            $key = $inputs['s'];
            if (!Session::has($key)) {
                return redirect()->back();
            }

            $dataSession = Session::get($key);
            if (!empty($dataSession['data'])) {
                //if session isset support_first_time_id
                if (!empty($dataSession['data']['support_first_time_id'])) {
                    Session::put(SESSION_REFERER_SEARCH_AI, [
                        'referer' => FROM_SUPPORT_FIRST_TIME,
                        'support_first_time_id' => $dataSession['data']['support_first_time_id'],
                        'trademark_id' => $dataSession['data']['trademark_id'],
                    ]);
                    Session::put(SESSION_SUGGEST_PRODUCT, $dataSession['data']['m_product_ids']);

                    return redirect()->route('user.search-ai.result');
                }
                //if session isset precheck_id
                if (isset($dataSession['data']['precheck_id'])) {
                    Session::put(SESSION_REFERER_SEARCH_AI, [
                        'referer' => FROM_PRECHECK,
                        'support_first_time_id' => $dataSession['data']['precheck_id'],
                        'trademark_id' => $dataSession['data']['trademark_id'],
                    ]);
                    Session::put(SESSION_SUGGEST_PRODUCT, $dataSession['data']['m_product_ids']);

                    return redirect()->route('user.search-ai.result');
                }
            }
        }

        return redirect()->back();
    }


    /**
     * Redirect to u011
     *
     * @param Request $request
     * @param $id - trademark_id
     * @return mixed
     */
    public function redirectToU011(Request $request, $id)
    {
        $trademark = $this->tradeMarkService->findByCondition([
            'id' => $id,
            'user_id' => auth()->user()->id,
        ])->first();
        if (!$trademark) {
            abort(404);
        }

        Session::forget(SESSION_MPRODUCT_FORM_U021C);
        // if trademark has support first time
        $sft = $trademark->supportFirstTime;
        if ($sft) {
            $sftContentProds = $sft->StfContentProduct->toArray();
            $products = $this->mProductService->getSftSuitableProducts($sft->id)->toArray();
            Session::put(SESSION_MPRODUCT_FORM_U021C, [
                'from_page' => U021C,
                'data' => [
                    'products' => $products,
                    'sftContentProds' => $sftContentProds,
                ]
            ]);
        }

        return redirect()->route('user.sft.index');
    }
}
