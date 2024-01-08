<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FreeHistory;
use App\Models\MPriceList;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\PayerInfo;
use App\Models\Payment;
use App\Models\User;
use App\Services\Common\NoticeService;
use App\Services\Common\TrademarkTableService;
use App\Services\FreeHistoryService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\MPriceListService;
use App\Services\NoticeDetailService;
use App\Services\PayerInfoService;
use App\Services\PaymentService;
use App\Services\TrademarkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FreeHistoryController extends Controller
{
    protected FreeHistoryService        $freeHistoryService;
    protected TrademarkService          $trademarkService;
    protected TrademarkTableService     $trademarkTableService;
    protected NoticeService             $noticeService;
    protected NoticeDetailService       $noticeDetailService;
    protected MNationService            $mNationService;
    protected MPrefectureService        $mPrefectureService;
    protected PaymentService            $paymentService;
    protected PayerInfoService          $payerInfoService;
    protected MPriceListService         $mPriceListService;

    public function __construct(
        FreeHistoryService $freeHistoryService,
        TrademarkService $trademarkService,
        TrademarkTableService $trademarkTableService,
        NoticeService $noticeService,
        NoticeDetailService $noticeDetailService,
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        PaymentService $paymentService,
        PayerInfoService $payerInfoService,
        MPriceListService $mPriceListService
    )
    {
        $this->freeHistoryService = $freeHistoryService;
        $this->trademarkService = $trademarkService;
        $this->trademarkTableService = $trademarkTableService;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
        $this->paymentService = $paymentService;
        $this->payerInfoService = $payerInfoService;
        $this->mPriceListService = $mPriceListService;
    }

    /**
     * Get list free history
     *
     * @param int $id
     * @return View
     */
    public function index($id): View
    {
        $freeHistory = $this->freeHistoryService->find($id);

        if (!$freeHistory) {
            abort(404);
        }
        $freeHistory->attachment = json_decode($freeHistory->attachment);
        $trademark = $this->trademarkService->find($freeHistory->trademark_id);
        if (!$trademark) {
            abort(404);
        }
        $trademark = $trademark->load('appTrademark');
        if ($trademark->user_id != Auth::user()->id) {
            abort(404);
        }

        if ($freeHistory->type == FreeHistory::TYPE_1 || $freeHistory->type == FreeHistory::TYPE_2) {
            abort(404);
        }

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $trademark->id, [
            U000_FREE => true,
        ]);

        $payerInfo = $this->payerInfoService->findByCondition([
            'type' => TYPE_FREE_HISTORY,
            'target_id' => $freeHistory->id ?? 0,
        ])->first();

        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        // Price Base
        $setting = $this->mPriceListService->getSetting();

        $priceService = $freeHistory->amount ?? 0;
        $priceServiceFee = $priceService + floor($priceService * $setting->value / 100);

        $priceBankTransfer = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $priceBankTransferFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $paymentFee = $this->freeHistoryService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);

        $priceData = [
            'priceService' => $priceService,
            'priceServiceFee' => $priceServiceFee,
            'priceBankTransfer' => $priceBankTransfer,
            'priceBankTransferFee' => $priceBankTransferFee,
            'paymentFee' => $paymentFee,
        ];

        return view('user.modules.free-history.create', compact([
            'freeHistory',
            'trademarkTable',
            'nations',
            'prefectures',
            'payerInfo',
            'priceData',
            'setting',
        ]));
    }

    /**
     * Create free history
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function create(Request $request):RedirectResponse
    {
        $params = $request->all();

        $freeHistory = $this->freeHistoryService->findByCondition(['id' => $params['free_history_id']])->with('trademark')->first();
        if (empty($freeHistory)) {
            abort(CODE_ERROR_404);
        }

        try {
            DB::beginTransaction();
            $trademark = $freeHistory->trademark;

            $this->freeHistoryService->update($freeHistory, [
                'content_answer' => $request->content_answer,
            ]);

            if ($freeHistory->is_check_amount == IS_CHECK_AMOUNT) {
                // Create payer information
                $payerInfo = $this->payerInfoService->updateOrCreate([
                    'target_id' => $freeHistory->id,
                    'type' => TYPE_FREE_HISTORY,
                ], [
                    'target_id' => $freeHistory->id,
                    'payment_type' => $params['payment_type'] ?? null,
                    'payer_type' => $params['payer_type'] ?? 0,
                    'm_nation_id' => $params['m_nation_id'] ?? null,
                    'payer_name' => $params['payer_name'] ?? '',
                    'payer_name_furigana' => $params['payer_name_furigana'] ?? '',
                    'postal_code' => $params['postal_code'] ?? null,
                    'm_prefecture_id' => $params['m_prefecture_id'] ?? null,
                    'address_second' => $params['address_second'] ?? '',
                    'address_three' => $params['address_three'] ?? '',
                    'type' => TYPE_FREE_HISTORY,
                ]);

                $quoteNumber = $this->paymentService->generateQIR($trademark->trademark_number, 'quote');
                $invoiceNumber = $this->paymentService->generateQIR('', 'invoice');
                $receiptNumber = $this->paymentService->generateQIR('', 'receipt');

                // Create payment with payment status is
                $dataPayment = $params['payment'] ?? [];
                $dataPayment['trademark_id'] = $trademark->id;
                $dataPayment['target_id'] = $freeHistory->id;
                $dataPayment['payer_info_id'] = $payerInfo->id;
                $dataPayment['quote_number'] = $quoteNumber;
                $dataPayment['invoice_number'] = $invoiceNumber;
                $dataPayment['receipt_number'] = $receiptNumber;
                $dataPayment['tax_withholding'] = 0;
                $dataPayment['payment_amount'] = 0;
                $dataPayment['type'] = TYPE_FREE_HISTORY;
                $dataPayment['from_page'] = U000FREE;
                $dataPayment['payment_status'] = $params['payment_status'] ?? Payment::STATUS_SAVE;

                $paymentFee = $this->freeHistoryService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
                if ($params['payment_type'] == Payment::BANK_TRANSFER) {
                    $dataPayment['cost_bank_transfer'] = $paymentFee['cost_service_base'] ?? 0;
                }

                $payment = $this->paymentService->createPaymentWithSFT($dataPayment);
            }

            DB::commit();

            $submitType = $params['submit_type'] ?? null;
            switch ($submitType) {
                case REDIRECT_TO_COMMON_PAYMENT:
                    $dataPayment['freeHistory'] = $freeHistory;
                    $dataPayment['trademark'] = $trademark;
                    $dataPayment['payment_id'] = $payment->id;

                    $dataPayment = array_merge($params, $dataPayment);

                    $secretKey = Str::random(11);
                    Session::put($secretKey, $dataPayment);

                    return redirect()->route('user.payment.index', ['s' => $secretKey]);
                    break;
                case REDIRECT_TO_COMMON_QUOTE:
                    return redirect()->route('user.quote', ['id' => $payment->id]);
                    break;
                case REDIRECT_TO_ANKEN_TOP:
                    return redirect()->route('user.application-detail.index', ['id' => $trademark->id]);
                    break;
                default:
                    return redirect()->route('user.top')->with('message', __('messages.general.Common_E047'));
                    break;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get tax withholding
     *
     * @param string $payerType
     * @param string $subTotal
     * @return string
     */
    public function getTaxWithHolding(PayerInfo $payerInfo, string $subTotal): string
    {
        $taxWithholdingPercent = 0;
        if ($payerInfo->payer_type == User::INFO_TYPE_ACC_GROUP && $payerInfo->m_nation_id == 1) {
            if ($subTotal > Payment::WITH_HOLDING_TAX_NUM) {
                $taxWithholdingPercent = Payment::WITH_HOLDING_TAX_MAX;
            } else {
                $taxWithholdingPercent = Payment::WITH_HOLDING_TAX_MIN;
            }
        }

        return $subTotal * $taxWithholdingPercent / 100;
    }

    /**
     * Get free history cancel
     *
     * @param int $id
     * @return View
     */
    public function showCancel($id)
    {
        $freeHistory = $this->freeHistoryService->find($id);

        if (!$freeHistory) {
            abort(404);
        }

        $trademark = $this->trademarkService->find($freeHistory->trademark_id);

        if (!$trademark) {
            abort(404);
        }

        if ($trademark->user_id != Auth::user()->id) {
            abort(404);
        }


        if ($freeHistory->type == FreeHistory::TYPE_1 || $freeHistory->type == FreeHistory::TYPE_2) {
            abort(404);
        }
        return view('user.modules.free-history.cancel', compact('freeHistory', 'trademark'));
    }

    /**
     * Cancel free history
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancel(Request $request): RedirectResponse
    {
        $freeHistory = $this->freeHistoryService->findByCondition(['id' => $request->free_history_id])->first();
        if (!$freeHistory) {
            abort(404);
        }
        $targetPage = route('admin.free-history.edit', $freeHistory->id);
        $this->freeHistoryService->update($freeHistory, ['is_cancel' => FreeHistory::IS_CANCEL]);

        $trademark = $this->trademarkService->findByCondition(['id' => $request->trademark_id])->first();
        if ($trademark) {
             // Update Notice at a000free_s (No 7: H I)
            $targetPage = route('admin.free-history.edit', [
                'id' => $freeHistory->id,
            ]);
            $targetPage = str_replace(request()->root(), '', $targetPage);

            $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            ])->with('notice')->get()
                ->where('notice.trademark_id', $trademark->id)
                ->where('notice.user_id', $trademark->user_id)
                ->where('notice.flow', Notice::FLOW_FREE_HISTORY);
            $stepBeforeNotice->map(function ($item) {
                $item->update([
                    'is_answer' => NoticeDetail::IS_ANSWER,
                ]);
            });

            $this->noticeService->sendNotice([
                'notices' => [
                    'flow' => Notice::FLOW_FREE_HISTORY,
                    'user_id' => Auth::user()->id,
                    'trademark_id' => $trademark->id,
                ],
                'notice_details' => [
                    // Send Notice jimu
                    [
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'content' => '(書類名)対応不要指示受領',
                        'target_page' => route('user.free-history.show-cancel', $freeHistory->id),
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'attribute' => 'お客様から',
                        'completion_date' => Carbon::now(),
                    ],
                    // Send Notice seki
                    [
                        'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                        'content' => '(書類名)対応不要指示受領',
                        'target_page' => route('user.free-history.show-cancel', $freeHistory->id),
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'attribute' => 'お客様から',
                    ],
                    // Send Notice user
                    [
                        'target_id' => Auth::user()->id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'content' => '(書類名)対応中止',
                        'target_page' => route('user.free-history.show-cancel', $freeHistory->id),
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    ],
                    [
                        'target_id' => Auth::user()->id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'content' => '(書類名)対応中止',
                        'target_page' => route('user.free-history.show-cancel', $freeHistory->id),
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    ],
                ],
            ]);
        }

        return redirect()->route('user.application-detail.index', $freeHistory->trademark_id)->with('message', __('messages.common.successes.Freerireki_S001'))->withInput();
    }
}
