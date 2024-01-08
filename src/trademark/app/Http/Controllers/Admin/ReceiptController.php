<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MPriceList;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Trademark;
use App\Services\AppTrademarkService;
use App\Services\MatchingResultService;
use App\Services\PaymentService;
use App\Services\PrecheckService;
use App\Services\RegisterTrademarkService;
use App\Services\SupportFirstTimeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public PaymentService $paymentService;
    public AppTrademarkService $appTrademarkService;
    public SupportFirstTimeService $supportFirstTimeService;
    public PrecheckService $precheckService;
    public MatchingResultService $matchingResultService;
    public RegisterTrademarkService $registerTrademarkService;

    /**
     * Constructor
     *
     * @param PaymentService           $paymentService
     * @param AppTrademarkService      $appTrademarkService
     * @param SupportFirstTimeService  $supportFirstTimeService
     * @param PrecheckService          $precheckService
     * @param MatchingResultService     $matchingResultService
     * @param RegisterTrademarkService $registerTrademarkService
     * @return  void
     */
    public function __construct(
        PaymentService $paymentService,
        AppTrademarkService $appTrademarkService,
        SupportFirstTimeService $supportFirstTimeService,
        PrecheckService $precheckService,
        MatchingResultService $matchingResultService,
        RegisterTrademarkService $registerTrademarkService
    )
    {
        $this->paymentService = $paymentService;
        $this->appTrademarkService = $appTrademarkService;
        $this->supportFirstTimeService = $supportFirstTimeService;
        $this->precheckService = $precheckService;
        $this->matchingResultService = $matchingResultService;
        $this->registerTrademarkService = $registerTrademarkService;
    }

    /**
     * Show receipt of payment
     *
     * @param   integer $id
     * @return  View
     */
    public function receipt(int $id): View
    {
        $payment = $this->paymentService->findByCondition(['id' => $id])->first();
        if (!$payment) {
            abort(CODE_ERROR_404);
        }
        $payment = $payment->load(['payerInfo', 'trademark'])->loadDataTargetId();
        if (in_array($payment->payment_status, [Payment::STATUS_SAVE, Payment::STATUS_WAITING_PAYMENT])) {
            abort(CODE_ERROR_404);
        }
        $userLogin = auth()->user();
        $trademark = $payment->trademark;

        $payerInfo = $payment->payerInfo->load(['nation', 'prefecture']);

        $trademark = $payment->trademark;
        $setting = Setting::get();

        return view('user.modules.receipts.index', compact(
            'payment',
            'payerInfo',
            'trademark',
            'setting'
        ));
    }

    /**
     * Show invoice for payment
     *
     * @param   integer $id
     * @return  View
     */
    public function invoice(int $id, Request $request): View
    {
        $payment = $this->paymentService->findByCondition(['id' => $id])->first();
        if (!$payment) {
            abort(CODE_ERROR_404);
        }
        $payment = $payment->load([
            'trademark.comparisonTrademarkResults',
            'payerInfo',
            'paymentProds',
            'paymentProds.product',
            'paymentProds.product.mDistinction',
        ])->withPaymentInfo()->loadDataTargetId();

        $userLogin = auth()->user();
        $trademark = $payment->trademark;

        $sumDistinction = $payment->paymentProds->pluck('product.mDistinction.id')->unique()->values()->count();

        $payerInfo = $payment->payerInfo->load(['nation', 'prefecture']);

        $trademark = $payment->trademark;

        $productGroup = $payment->getProductInvoice();

        $data = [
            'productIds' => $payment->paymentProds->pluck('m_product_id') ?? [],
            'payment_type' => $payerInfo->payment_type,
            'from_page' => $payment->from_page,
            'sum_distintion' => $sumDistinction,
        ];
        switch ($data['from_page']) {
            case U203:
            case U203N:
                $data['trademark_plan_id'] = $request->trademark_plan_id;
                break;
            case U302:
                $productAddOnFee = $this->matchingResultService->getSystemFee(MPriceList::REGISTRATION, MPriceList::REGISTRATION_EACH_3_PRODS);
                $data['total_product_each_add'] = $payment->reduce_number_distitions;
                $data['product_each_add_fee'] = $productAddOnFee['cost_service_base'] * $payment->reduce_number_distitions;
                break;
            case U201_SELECT_01:
            case U201_SIMPLE:
                $comparisonTrademarkResult = $trademark->comparisonTrademarkResults->last();
                $planCorrespondence = $comparisonTrademarkResult->planCorrespondences->last();
                $data['planCorrespondence'] = $planCorrespondence;
                break;
            default:
                break;
        }


        $contentPay = '';
        if (isset($data['from_page']) && $data['from_page']) {
            $contentPay = $this->paymentService->getFormPayment($data, $payment, $data['from_page']);
        }

        $invoiceDeadline = $trademark->created_at->addDay(3);
        if (in_array($payment->type, [
            Payment::TYPE_TRADEMARK,
            Payment::TYPE_SUPPORT_FIRST_TIME,
            Payment::TYPE_SUPPORT_FIRST_TIME_AMS,
            Payment::TYPE_PRECHECK,
            Payment::TYPE_PRECHECK_AMS,
        ])) {
            $invoiceDeadline = $trademark->created_at->addDay(7);
        }
        $setting = Setting::get();
        return view('user.modules.receipts.invoice', compact(
            'payment',
            'payerInfo',
            'trademark',
            'invoiceDeadline',
            'productGroup',
            'contentPay',
            'data',
            'setting'
        ));
    }

    /**
     * Show quote of payment
     *
     * @param integer $id
     * @return View
     */
    public function quote(int $id, Request $request): View
    {
        $payment = $this->paymentService->findByCondition(['id' => $id])->first();
        if (!$payment) {
            abort(CODE_ERROR_404);
        }
        $payment = $payment->load([
            'trademark.comparisonTrademarkResults',
            'trademark.comparisonTrademarkResults.planCorrespondences',
            'payerInfo',
            'paymentProds',
            'paymentProds.product',
            'paymentProds.product.mDistinction',
        ])->withPaymentInfo()->loadDataTargetId();
        $trademark = $payment->trademark;
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }

        $userLogin = auth()->user();
        if ($userLogin->id != $trademark->user_id) {
            abort(CODE_ERROR_404);
        }

        $sumDistinction = $payment->paymentProds->pluck('product.mDistinction.id')->unique()->values()->count();

        $payerInfo = $payment->payerInfo->load(['nation', 'prefecture']);

        $productGroup = $payment->getProductInvoice();

        $quoteDeadline = $trademark->created_at->addMonth(1);
        $data = [
            'productIds' => $payment->paymentProds->pluck('m_product_id') ?? [],
            'payment_type' => $payerInfo->payment_type,
            'from_page' => $payment->from_page,
            'sum_distintion' => $sumDistinction,
        ];


        switch ($data['from_page']) {
            case U203:
            case U203N:
                $data['trademark_plan_id'] = $request->trademark_plan_id;
                break;
            case U302:
                $productAddOnFee = $this->matchingResultService->getSystemFee(MPriceList::REGISTRATION, MPriceList::REGISTRATION_EACH_3_PRODS);
                $data['total_product_each_add'] = $payment->reduce_number_distitions;
                $data['product_each_add_fee'] = $productAddOnFee['cost_service_base'] * $payment->reduce_number_distitions;
                break;
            case U201_SELECT_01:
            case U201_SIMPLE:
                $comparisonTrademarkResult = $trademark->comparisonTrademarkResults->last();
                $planCorrespondence = $comparisonTrademarkResult->planCorrespondences->last();
                $data['planCorrespondence'] = $planCorrespondence;
                break;
            default:
                break;
        }
        $contentPay = '';
        if (isset($data['from_page']) && $data['from_page']) {
            $contentPay = $this->paymentService->getFormPayment($data, $payment, $data['from_page']);
        }
        $setting = Setting::get();
        return view('user.modules.receipts.quote', compact(
            'payment',
            'payerInfo',
            'trademark',
            'quoteDeadline',
            'productGroup',
            'contentPay',
            'data',
            'setting'
        ));
    }
}
