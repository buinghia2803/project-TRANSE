<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\MPriceList;
use App\Models\Notice;
use App\Models\Payment;
use App\Models\RegisterTrademark;
use App\Models\RegisterTrademarkRenewal;
use App\Models\Trademark;
use App\Models\TrademarkDocument;
use App\Services\Common\TrademarkTableService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\MPriceListService;
use App\Services\PaymentService;
use App\Services\RegisterTrademarkRenewalService;
use App\Services\RegisterTrademarkService;
use App\Services\TrademarkDocumentService;
use App\Services\TrademarkService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TrademarkController extends Controller
{
    private TrademarkService $trademarkService;
    private RegisterTrademarkService $registerTrademarkService;
    private MPrefectureService $mPrefectureService;
    private MNationService $mNationService;
    private PaymentService $paymentService;
    private MPriceListService $mPriceListService;
    private TrademarkTableService $trademarkTableService;
    private RegisterTrademarkRenewalService $registerTrademarkRenewalService;
    private TrademarkDocumentService $trademarkDocumentService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        TrademarkService $trademarkService,
        RegisterTrademarkService $registerTrademarkService,
        MPrefectureService $mPrefectureService,
        MNationService $mNationService,
        PaymentService $paymentService,
        MPriceListService $mPriceListService,
        TrademarkTableService $trademarkTableService,
        RegisterTrademarkRenewalService $registerTrademarkRenewalService,
        TrademarkDocumentService $trademarkDocumentService
    )
    {
        $this->trademarkService = $trademarkService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->mPrefectureService = $mPrefectureService;
        $this->mNationService = $mNationService;
        $this->paymentService = $paymentService;
        $this->mPriceListService = $mPriceListService;
        $this->trademarkTableService = $trademarkTableService;
        $this->registerTrademarkRenewalService = $registerTrademarkRenewalService;
        $this->trademarkDocumentService = $trademarkDocumentService;
    }

    /**
     * Get list trademark of user
     *
     * @param Request $request
     * @return View
     */
    public function list(Request $request): View
    {
        $currenUser = Auth::guard('web')->user();

        // Register Trademark
        $orderRegisField = $request->orderRegisField ?? 'created_at';
        $orderRegisType = $request->orderRegisType ?? SORT_TYPE_DESC;

        if (in_array($orderRegisField, ['trademark_number'])) {
            $orderRegisField = 'trademarks.' . $orderRegisField;
        } else {
            $orderRegisField = 'register_trademarks.' . $orderRegisField;
        }

        $registerTrademarks = $this->registerTrademarkService->findByCondition([])
            ->join('trademarks', 'trademarks.id', 'register_trademarks.trademark_id')
            ->where('trademarks.user_id', $currenUser->id ?? 0)
            ->where('register_trademarks.is_register', RegisterTrademark::IS_REGISTER)
            ->where('register_trademarks.is_update_info_register', RegisterTrademark::IS_REGISTER_CHANGE_INFO)
            ->select('register_trademarks.*')
            ->orderBy($orderRegisField, $orderRegisType)
            ->with(['trademark.trademarkDocuments'])
            ->get();
        $registerTrademarks = $registerTrademarks->unique('trademark_id')->filter(function ($item) {
            $trademarkDocuments = $item->trademark->trademarkDocuments ?? collect([]);
            $trademarkDocumentType8 = $trademarkDocuments
                ->where('type', TrademarkDocument::TYPE_8)
                ->where('url', '<>', '');

            return count($trademarkDocumentType8) > 0;
        });
        $registerTrademarks = $this->registerTrademarkService->formatListUser($registerTrademarks);
        $trademarkIDs = $registerTrademarks->pluck('trademark_id')->toArray();

        // Trademark
        $trademarks = $this->trademarkService->findByCondition([
            'status_management' => true,
            'user_id' => $currenUser->id ?? 0,
        ])
            ->whereNotIn('id', $trademarkIDs)
            ->where('trademark_number', 'like', 'Q%')
            ->get();
        $trademarks = $this->trademarkService->formatListUser($trademarks);
        $trademarks = $trademarks->where('is_expired', false);

        $orderTrademarkField = $request->orderTrademarkField ?? 'soft_created_at';
        $orderTrademarkType = $request->orderTrademarkType ?? SORT_TYPE_DESC;
        $trademarks = CommonHelper::softCollection($trademarks, $orderTrademarkField, $orderTrademarkType);

        // Url Back
        $urlBackDefault = route('user.top');
        $checkUrl = route('user.application-list');
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        return view('user.modules.trademarks.list', compact(
            'trademarks',
            'registerTrademarks',
            'backUrl'
        ));
    }

    /**
     * Withdraw application list
     *
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function applicationList(Request $request): View
    {
        $userId = auth()->guard('web')->user()->id;
        // Trademark
        $trademarks = $this->trademarkService->findByCondition([
            'user_id' => $userId ?? 0,
        ])
            ->doesnthave('registerTrademark')
            ->get();
        $trademarks = $this->trademarkService->listTrademark($trademarks);
        $trademarks = $trademarks->where('is_status_app_trademark', '!=', 0)->where('is_expired', true);

        $orderTrademarkField = $request->orderTrademarkField ?? 'soft_created_at';
        $orderTrademarkType = $request->orderTrademarkType ?? SORT_TYPE_DESC;
        $trademarks = CommonHelper::softCollection($trademarks, $orderTrademarkField, $orderTrademarkType);

        // Register Trademark
        $registerTrademarks = $this->trademarkService->findByCondition([
            'user_id' => auth()->guard('web')->user()->id ?? 0,
        ])
            ->has('registerTrademark')
            ->get();
        $registerTrademarks = $this->trademarkService->listRegisterTrademark($registerTrademarks);

        $orderRegisField = $request->orderRegisField ?? 'soft_created_at';
        $orderRegisType = $request->orderRegisType ?? SORT_TYPE_DESC;
        $registerTrademarks = CommonHelper::softCollection($registerTrademarks, $orderRegisField, $orderRegisType);

        $page = $request->pageTrademarkRegister ?? 1;
        $registerTrademarks = CommonHelper::paginate($registerTrademarks, PAGE_LIMIT_50, $page, [
            'pageName' => 'pageTrademarkRegister',
        ]);

        // Url Back
        $urlBackDefault = route('user.top');
        $checkUrl = route('user.withdraw.application-list');
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        return view('user.modules.withdraw.application-list', compact(
            'backUrl',
            'trademarks',
            'registerTrademarks'
        ));
    }

    /**
     * Withdraw application list post
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function applicationListPost(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $userLogin = auth()->guard('web')->user()->id;

            foreach ($request->data ?? [] as $value) {
                $trademark = $this->trademarkService->find($value['trademark_id'])->load('payment');
                $payment = $trademark->payment;
                if ($trademark->user_id == $userLogin) {
                    $trademark->update([
                        'status_management' => $value['status_management'] ?? Trademark::TRADEMARK_STATUS_MANAGEMENT,
                    ]);
                    if ($payment && $payment->payment_status == Payment::STATUS_WAITING_PAYMENT) {
                        $payment->update([
                            'is_treatment' => Payment::IS_TREATMENT_DONE,
                        ]);
                    }
                }
            }

            CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.update_success'));
            DB::commit();

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.general.Common_E027'));
            return redirect()->back();
        }
    }

    /**
     * Show Extension Period Alert
     *
     * @param  mixed $request
     * @param  int $id - comparison_trademark_result_id
     * @return void
     */
    public function showExtensionPeriodAlert(Request $request, $id)
    {
        $trademark = $this->trademarkService->findByCondition([
            'id' => $id,
            'user_id' => Auth::user()->id,
        ])->with('comparisonTrademarkResult')
            ->first();
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
        if (!$comparisonTrademarkResult) {
            abort(CODE_ERROR_404);
        }
        $trademark->load([
            'registerTrademarkRenewals',
        ]);

        $registerTrademarkRenewals = $trademark->registerTrademarkRenewals
            ->where('type', RegisterTrademarkRenewal::TYPE_EXTENSION_PERIOD_BEFORE_DEADLINE)
            ->where('status', '!=', RegisterTrademarkRenewal::SAVE_DRAFT);

        $fromPage = U210_ALERT_02;
        $paymentDraft = $this->paymentService->getPaymentDraft($trademark->id, TYPE_EXTENSION_OF_PERIOD, $fromPage);
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $setting = $this->mPriceListService->getSetting();
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $id, [
            U210_ALERT_02 => true,
        ]);
        $routeAjax = route('user.ajax_extension_period');
        $packageTypeCostService = MPriceList::REGISTER_BEFORE_DEADLINE;
        $dataAjax = $this->dataAjax($packageTypeCostService);

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        return view('user.modules.extension-period.alert', compact(
            'trademarkTable',
            'comparisonTrademarkResult',
            'setting',
            'nations',
            'prefectures',
            'paymentDraft',
            'trademark',
            'routeAjax',
            'dataAjax',
            'packageTypeCostService',
            'registerTrademarkRenewals',
            'trademarkDocuments'
        ));
    }

    /**
     * Show Extension Period Alert
     *
     * @param  mixed $request
     * @param  int $id - trademark_id
     * @return void
     */
    public function showExtensionPeriodOver(Request $request, $id)
    {
        $trademark = $this->trademarkService->findByCondition([
            'id' => $id,
            'user_id' => Auth::user()->id,
        ])
            ->with('comparisonTrademarkResult')
            ->first();
        if (!$trademark) {
            abort(404);
        }
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
        $trademark->load([
            'trademarkDocuments' => function ($query) {
                return $query->where('type', TrademarkDocument::TYPE_1);
            },
            'registerTrademarkRenewals',
        ]);
        $registerTrademarkRenewals = $trademark->registerTrademarkRenewals
            ->where('type', RegisterTrademarkRenewal::TYPE_EXTENSION_OUTSIDE_PERIOD)
            ->where('status', '!=', RegisterTrademarkRenewal::SAVE_DRAFT);
        $fromPage = U210_OVER_02;
        $paymentDraft = $this->paymentService->getPaymentDraft($trademark->id, TYPE_EXTENSION_OF_PERIOD, $fromPage);
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $setting = $this->mPriceListService->getSetting();
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $id, [
            U210_OVER_02 => true,
        ]);
        $routeAjax = route('user.ajax_extension_period');
        $packageTypeCostService = MPriceList::EXTENDED_SERVICE_OUTSIDE_PERIOD;
        $dataAjax = $this->dataAjax($packageTypeCostService);

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        return view('user.modules.extension-period.over', compact(
            'trademarkTable',
            'comparisonTrademarkResult',
            'setting',
            'nations',
            'prefectures',
            'paymentDraft',
            'trademark',
            'routeAjax',
            'dataAjax',
            'packageTypeCostService',
            'registerTrademarkRenewals',
            'trademarkDocuments'
        ));
    }

    /**
     * ShowEncho
     *
     * @param  int $id - trademark_id ? register_trademark_renewal_id
     * @return view
     */
    public function showEncho(Request $request, $id): View
    {
        $trademark = $this->trademarkService->findByCondition([
            'id' => $id,
            'user_id' => Auth::user()->id,
        ])
            ->with('comparisonTrademarkResult', 'registerTrademarkRenewals', 'trademarkDocuments')
            ->first();
        if (!$trademark) {
            abort(404);
        }
        $trademarkDocImport = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_4,
        ]);

        $trademarkDocUpload = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $registerTrademarkRenewal = $this->registerTrademarkRenewalService->findByCondition([
            'id' => $request['register_trademark_renewal_id'],
            'trademark_id' => $trademark->id,
        ])
            ->orderBy('id', SORT_TYPE_DESC)
            ->first();
        if (!$registerTrademarkRenewal) {
            abort(404);
        }
        if ($registerTrademarkRenewal->status != RegisterTrademarkRenewal::COMPLEDTED) {
            abort(404);
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $id, [
            U210_Encho => true,
        ]);

        return view('user.modules.extension-period.encho', compact(
            'trademark',
            'comparisonTrademarkResult',
            'registerTrademarkRenewal',
            'trademarkTable',
            'trademarkDocImport',
            'trademarkDocUpload'
        ));
    }

    /**
     * Data Ajax
     *
     * @return void
     */
    public function dataAjax($packageTypeCostService)
    {
        $costBankTransfer = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $costService = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, $packageTypeCostService);
        $setting = $this->mPriceListService->getSetting();
        $dataAjax = [
            'cost_service' => $costService,
            'cost_bank_transfer' => $costBankTransfer->base_price,
            'tax' => $setting->value,
        ];

        return $dataAjax;
    }

    /**
     * Save Data Extension Period
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function saveDataExtensionPeriod(Request $request, $id)
    {
        $dataAjax = $this->dataAjax($request->package_type);
        $dataAjax['payment_type'] = $request->payment_type;
        $dataPayment = $this->trademarkService->calculatorExtensionPeriod($dataAjax);
        $request['trademark_id'] = $id;
        $condition = array_merge($request->all(), $dataPayment);

        $saveDataExtensionPeriod = $this->trademarkService->saveDataExtensionPeriod($condition);

        switch ($saveDataExtensionPeriod['redirect_to']) {
            case SUBMIT:
                return redirect()->route('user.payment.index', ['s' => $saveDataExtensionPeriod['key_session']]);
            case QUOTE:
                return redirect()->route('user.quote', ['id' => $saveDataExtensionPeriod['payment_id']]);
            case ERROR_PAYMENT_TYPE_CREDIT:
                return redirect()->back()->with('error', __('messages.general.extend_U210_E002'));
            case ERROR_PAYMENT_TYPE_TRANSFER:
                return redirect()->back()->with('error', __('messages.general.extend_U210_E001'));
        }
    }

    /**
     * Ajax Extension Period
     *
     * @param  mixed $request
     * @return void
     */
    public function ajaxExtensionPeriod(Request $request)
    {
        return $this->trademarkService->calculatorExtensionPeriod($request->all());
    }
}
