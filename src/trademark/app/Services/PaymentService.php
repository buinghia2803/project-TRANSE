<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\AppTrademark;
use App\Models\MatchingResult;
use App\Models\MPriceList;
use App\Models\Payment;
use App\Models\PlanCorrespondence;
use App\Models\Precheck;
use App\Models\ReasonRefNumProd;
use App\Models\RegisterTrademark;
use App\Models\RegisterTrademarkProd;
use App\Models\SupportFirstTime;
use App\Models\TrademarkPlan;
use App\Services\GMO\GMOService;
use App\Services\GMO\GMOHelper;
use App\Services\Common\NoticeService as CommonNoticeService;
use App\Repositories\PaymentRepository;
use App\Repositories\PlanCorrespondenceRepository;
use App\Repositories\ReasonRefNumProdRepository;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\MPriceListRepository;
use App\Repositories\SettingRepository;
use App\Repositories\SupportFirstTimeRepository;
use App\Repositories\ComparisonTrademarkResultRepository;
use App\Repositories\PrecheckRepository;
use App\Repositories\PlanRepository;
use App\Repositories\RegisterTrademarkRepository;
use App\Repositories\TrademarkRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService extends BaseService
{
    /**
     * @param MPriceListRepository $mPriceListRepository
     * @param SettingRepository $settingRepository
     * @var   PaymentRepository $paymentRepository
     * @var   GMOService $gmoService
     */
    protected PaymentRepository                     $paymentRepository;
    protected TrademarkRepository                   $trademarkRepository;
    protected GMOPaymentService                     $gmoPaymentService;
    protected GMOService                            $gmoService;
    protected NoticeService                         $noticeService;
    protected MPriceListRepository                  $mPriceListRepository;
    protected SettingRepository                     $settingRepository;
    protected SupportFirstTimeRepository            $supportFirstTimeRepository;
    protected PrecheckRepository                    $precheckRepository;
    protected CommonNoticeService                   $commonNoticeService;
    protected PlanCorrespondenceRepository          $planCorrespondenceRepository;
    protected ComparisonTrademarkResultRepository   $comparisonTrademarkResultRepository;
    protected PlanRepository                        $planRepository;
    protected ReasonRefNumProdRepository            $reasonRefNumProdRepository;
    protected RegisterTrademarkRepository            $registerTrademarkRepository;

    /**
     * Initializing the instances and variables
     *
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(
        PaymentRepository                   $paymentRepository,
        GMOPaymentService                   $gmoPaymentService,
        NoticeService                       $noticeService,
        GMOService                          $gmoService,
        MPriceListRepository                $mPriceListRepository,
        SettingRepository                   $settingRepository,
        SupportFirstTimeRepository          $supportFirstTimeRepository,
        PlanCorrespondenceRepository        $planCorrespondenceRepository,
        ComparisonTrademarkResultRepository $comparisonTrademarkResultRepository,
        ReasonRefNumProdRepository          $reasonRefNumProdRepository,
        PrecheckRepository                  $precheckRepository,
        PlanRepository                      $planRepository,
        CommonNoticeService                 $commonNoticeService,
        RegisterTrademarkRepository         $registerTrademarkRepository,
        TrademarkRepository                 $trademarkRepository
    )
    {
        $this->gmoService = $gmoService;
        $this->noticeService = $noticeService;
        $this->repository = $paymentRepository;
        $this->gmoPaymentService = $gmoPaymentService;
        $this->mPriceListRepository = $mPriceListRepository;
        $this->settingRepository = $settingRepository;
        $this->supportFirstTimeRepository = $supportFirstTimeRepository;
        $this->reasonRefNumProdRepository = $reasonRefNumProdRepository;
        $this->planCorrespondenceRepository = $planCorrespondenceRepository;
        $this->comparisonTrademarkResultRepository = $comparisonTrademarkResultRepository;
        $this->precheckRepository = $precheckRepository;
        $this->planRepository = $planRepository;
        $this->commonNoticeService = $commonNoticeService;
        $this->trademarkRepository = $trademarkRepository;
        $this->registerTrademarkRepository = $registerTrademarkRepository;
    }

    /**
     * Handle payment.
     *
     * @param array $params
     * @return Model
     */
    public function payment(array $params): Model
    {
        try {
            DB::beginTransaction();
            $params['payment_date'] = now()->format('Y-m-d H:i:s');

            if (isset($params['is_confirm']) && $params['is_confirm'] == 'on') {
                $params['is_confirm'] = IS_CONFIRM;
            }

            if ($params['payment_type'] == Payment::BANK_TRANSFER) {
                $params['is_treatment '] = Payment::IS_TREATMENT_WAIT;
            } else {
                $params['is_treatment '] = Payment::IS_TREATMENT_DONE;
            }
            $params['payment_status'] = $params['payment_type'] == Payment::BANK_TRANSFER ? Payment::STATUS_WAITING_PAYMENT : Payment::STATUS_SAVE;

            //continue to another screen
            $payment = $this->repository->updateOrCreate(['id' => $params['payment_id']], $params);
            $payment->payment_amount = $payment->total_amount - $payment->tax_withholding;
            $payment->save();

            $trademark = $this->trademarkRepository->find($payment->trademark_id);
            if ($params['payment_type'] == Payment::BANK_TRANSFER) {
                if (isset($params['from_page']) && $params['from_page'] == U201_SIMPLE ||
                    isset($params['from_page']) && $params['from_page'] == U201_SELECT_01 ||
                    isset($params['from_page']) && $params['from_page'] == U201_SELECT_01_N
                ) {
                    $trademark->load('appTrademark', 'comparisonTrademarkResult.planCorrespondence');
                    $planCorrespondence = $trademark->comparisonTrademarkResult->planCorrespondence;
                    if ($planCorrespondence) {
                        $planCorrespondence->update(['register_date' => now()]);
                    }
                }

                $fromPage = $params['from_page'] ?? null;
                if (str_contains($fromPage, U302_402_5YR_KOUKI)) {
                    $fromPage = U302_402_5YR_KOUKI;
                }
                if (str_contains($fromPage, U302_402TSUINO_5YR_KOUKI)) {
                    $fromPage = U302_402TSUINO_5YR_KOUKI;
                }
                if (str_contains($fromPage, U402TSUINO)) {
                    $fromPage = U402TSUINO;
                } elseif (str_contains($fromPage, U402)) {
                    $fromPage = U402;
                }
                if (isset($params['from_page']) && $fromPage == U302_402_5YR_KOUKI
                    || isset($params['from_page']) && $fromPage == U302_402TSUINO_5YR_KOUKI
                    || isset($params['from_page']) && $fromPage == U402
                    || isset($params['from_page']) && $fromPage == U402TSUINO
                ) {
                    $registerTrademarkID = $payment->target_id;
                    $this->registerTrademarkRepository->updateById($registerTrademarkID, [
                        'is_register' => RegisterTrademark::IS_REGISTER,
                    ]);
                }

                $trademarkNumber = $this->generateTrademarkNumber();
                $trademarkNumber = $trademark->trademark_number;
                $regex = '/LA.+[0-9]/m';
                preg_match($regex, $trademarkNumber, $matches);
                if (isset($matches[0]) && $matches[0]) {
                    $trademarkNumber = $this->generateTrademarkNumber();
                    $trademark->update([
                        'trademark_number' => $trademarkNumber,
                    ]);
                }
                // if ($trademark->appTrademark) {
                //     $trademark->appTrademark->update([
                //         'status' => AppTrademark::STATUS_WAITING_FOR_ADMIN_CONFIRM,
                //     ]);
                // }
            }

            $params['payment_id'] = $payment->id;
            $this->updateServiceWithPage($payment);
            DB::commit();

            return $payment;
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollback();

            throw new \Exception($e);
        }
    }

    /**
     * Payment with GMO
     *
     * @param array $params
     *
     * @return boolean
     */
    public function paymentWithGMO(array $params): bool
    {
        try {
            if (!isset($params['payment_amount']) || !isset($params['tax'])) {
                throw new \Exception("Haven't payment_amount or tax in payment data.");
            }
            $orderID = GMOHelper::generateOrderID();
            // Create EntryTran before execute

            $entryTran = $this->gmoService->creditEntryTran([
                'OrderID' => $orderID,
                'Amount' => (int) $params['payment_amount'],
                'Tax' => (int) $params['tax'],
            ]);

            if (empty($entryTran)) {
                Log::error('Get EntryTran Response Data Fail');

                throw new \Exception('Get EntryTran Response Data Fail');
            }

            // Execute transition
            $execData = $this->gmoService->creditExecTran($entryTran, [
                // Test card
                'OrderID' => $orderID,
                'CardNo' => $params['card_no'] ?? '4111111111111111',
                'Expire' => $params['card_expire'] ?? '2512',
                'SecurityCode' => $params['cvc'] ?? '123',
            ]);

            $this->gmoPaymentService->create([
                'payment_id' => $params['payment_id'],
                'gmo_order_id' => $orderID,
                'job_cd' => GMO_JOB_CD_CAPTURE,
                'pay_type' => Payment::CREDIT_CARD,
                'access_id' => $entryTran['AccessID'] ?? '',
                'access_pass' => $entryTran['AccessPass'] ?? '',
                'forward' => $execData['Forward'] ?? '',
                'approve' => $execData['Approve'] ?? '',
                'tran_id' => $execData['TranID'] ?? 0,
                'tran_date' => $execData['TranDate'] ?? now()->format('Y-m-d H:i:s'),
                'status' => isset($execData['ErrInfo']) && $execData['ErrInfo'] ? PAYMENT_GMO_FAIL : PAYMENT_GMO_SUCCESS,
                'error_info' => isset($execData['ErrInfo']) && $execData['ErrInfo'] ? $execData['ErrInfo'] : '',
            ]);

            if (isset($execData['ErrCode']) && $execData['ErrCode']) {
                throw new \Exception($execData['ErrInfo']);
            }

            $payment = $this->find($params['payment_id'])->load('trademark.appTrademark');
            $appTrademark = $payment->trademark->appTrademark;
            $trademarkNumber = $this->generateTrademarkNumber();
            DB::beginTransaction();
            $payment->update([
                'payment_status' => Payment::STATUS_PAID,
                'is_treatment' => Payment::IS_TREATMENT_DONE,
            ]);
            $this->updateServiceWithPage($payment);
            // if ($appTrademark) {
            //     $appTrademark->update([
            //         'status' => AppTrademark::STATUS_WAITING_FOR_ADMIN_CONFIRM,
            //     ]);
            // }

            $fromPage = isset($payment) && $payment->from_page ? $payment->from_page : null;
            if (str_contains($fromPage, U302_402_5YR_KOUKI)) {
                $fromPage = U302_402_5YR_KOUKI;
            }
            if (str_contains($fromPage, U302_402TSUINO_5YR_KOUKI)) {
                $fromPage = U302_402TSUINO_5YR_KOUKI;
            }
            if (str_contains($fromPage, U402TSUINO)) {
                $fromPage = U402TSUINO;
            } elseif (str_contains($fromPage, U402)) {
                $fromPage = U402;
            }

            if (isset($payment) && $payment->from_page == U201_SIMPLE ||
                isset($payment) && $payment->from_page == U201_SELECT_01 ||
                isset($payment) && $payment->from_page == U201_SELECT_01_N
            ) {
                $planCorrespondence = $payment->trademark->comparisonTrademarkResult->planCorrespondence;
                if ($planCorrespondence) {
                    $planCorrespondence->update(['register_date' => now()]);
                }
            } elseif ($fromPage == U302_402_5YR_KOUKI
                || $fromPage == U302_402TSUINO_5YR_KOUKI
                || $fromPage == U402
                || $fromPage == U402TSUINO
            ) {
                $registerTrademarkID = $payment->target_id;
                $this->registerTrademarkRepository->updateById($registerTrademarkID, [
                    'is_register' => RegisterTrademark::IS_REGISTER,
                ]);
            } else {
                $trademarkNumber = $payment->trademark->trademark_number;
                $regex = '/LA.+[0-9]/m';
                preg_match($regex, $trademarkNumber, $matches);
                if (isset($matches[0]) && $matches[0]) {
                    $trademarkNumber = $this->generateTrademarkNumber();
                    $payment->trademark->update([
                        'trademark_number' => $trademarkNumber,
                    ]);
                }
            }
            // TODO: waiting documentation form customer.
            DB::commit();

            return true;
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            return false;
        }
    }

    /**
     * Update service with from page.
     */
    protected function updateServiceWithPage(?Payment $payment)
    {
        switch ($payment->from_page) {
            case U011:
                // Update s
                $sft = $this->supportFirstTimeRepository->find($payment->target_id);
                if ($sft) {
                    $sft->update(['status_register' => SupportFirstTime::IS_REGISTERED]);
                }
                break;
            default:
                # code...
                break;
        }
    }

    /**
     * Generate Trademark Number
     *
     * @return void
     */
    public function generateTrademarkNumber()
    {
        $maxTrademarkNumberGroup = 1000;
        $year = Carbon::now()->format('y');
        $trademarkNumberDefault = 'Q' . $year . 'A000' . '0' . 'JP';
        $prefixTrademarkNumber = 'Q' . $year;

        $month = Carbon::now()->format('m');
        $range = range('A', 'Z');
        $monthText = $range[$month - 1] ?? null;

        $query = DB::select(
            "SELECT trademark_number, id
            FROM trademarks
            WHERE SUBSTRING(`trademarks`.`trademark_number` , 1 , 3) = '$prefixTrademarkNumber'
            ORDER BY id DESC"
        );

        $lastTrademark = collect($query)->first();
        if ($lastTrademark) {
            $numberChar = Str::substr($lastTrademark->trademark_number, 3, 1);
            $number = (int) Str::substr($lastTrademark->trademark_number, 6, 1);
        } else {
            $numberChar = $trademarkNumberDefault;
            $number = 0;
        }
        $numberTrademark = null;
        $continue = true;
        while ($continue) {
            $number = $number + 1;
            if ($number == $maxTrademarkNumberGroup) {
                $numberChar = chr(ord($numberChar) + 1);
                $number = 1;
            }
            $numberTrademark = 'Q' . $year . $monthText . str_pad($number, 3, 0, STR_PAD_LEFT) . '0' . 'JP';
            $trademark = $this->trademarkRepository->findByCondition([
                'trademark_number' => $numberTrademark,
            ])->withTrashed()->first();

            if (empty($trademark)) {
                $continue = false;
            }
        }

        return $numberTrademark;
    }

    /**
     * Calculate subtotal from conditions.
     *
     * @param array $params
     */
    public function calculateSubtotal(array $params): int
    {
        return $this->repository->calculateSubtotal($params);
    }

    /**
     * Calculate total amount from conditions.
     *
     * @param array $params
     */
    public function calculateTotalAmount(array $params): int
    {
        return $this->repository->calculateTotalAmount($params);
    }

    /**
     * Calculate payment amount from conditions
     *
     * @param array $params
     */
    public function calculatePaymentAmount(array $params): int
    {
        return $this->repository->calculatePaymentAmount($params);
    }

    /**
     * Create payment for support the first time.
     *
     * @param array $condition .
     */
    public function createPaymentWithSFT(array $conditions): Model
    {
        return $this->repository->createPaymentWithSFT($conditions);
    }

    /**
     * Create payment for support the first time.
     *
     * @param array $condition .
     */
    public function createPaymentProds(array $conditions): void
    {
        $this->repository->createPaymentProds($conditions);
    }

    /**
     * Get info payment precheck
     *
     * @param array $inputs - package_type, is_mailing_register_cert, period_registration, total_distinction
     * @return mixed
     */
    public function ajaxGetCartInfoPayment(array $inputs)
    {
        return $this->repository->ajaxGetCartInfoPayment($inputs);
    }

    /**
     * Load data of Target ID for payments
     *
     * @param Collection $payments
     * @return Collection
     */
    public function loadDataTargetId(Collection $payments)
    {
        $paymentAppTrademarks = $payments->whereIn('type', [
            Payment::TYPE_TRADEMARK,
            Payment::TYPE_SUPPORT_FIRST_TIME_AMS,
            Payment::TYPE_PRECHECK_AMS,
            Payment::CHANG_ADDRESS,
            Payment::BEFORE_DUE_DATE,
        ]);
        $appTrademarks = AppTrademark::whereIn('id', $paymentAppTrademarks->pluck('target_id')->toArray())
            ->with(['trademark'])
            ->get();

        $paymentSFTs = $payments->whereIn('type', [
            Payment::TYPE_SUPPORT_FIRST_TIME,
        ]);
        $sfts = SupportFirstTime::whereIn('id', $paymentSFTs->pluck('target_id')->toArray())
            ->with(['trademark'])
            ->get();

        $paymentPrechecks = $payments->whereIn('type', [
            Payment::TYPE_PRECHECK,
        ]);
        $prechecks = Precheck::whereIn('id', $paymentPrechecks->pluck('target_id')->toArray())
            ->with(['trademark'])
            ->get();

        $paymentMatchingResults = $payments->whereIn('type', [
            Payment::TYPE_REASON_REFUSAL,
            Payment::TYPE_SELECT_POLICY,
        ]);
        $macingResults = MatchingResult::whereIn('id', $paymentMatchingResults->pluck('target_id')->toArray())
            ->with(['trademark', 'trademark.appTrademark'])
            ->get();

        $paymentRegisterTrademarks = $payments->whereIn('type', [
            Payment::TYPE_TRADEMARK_REGIS,
            Payment::TYPE_LATE_PAYMENT,
            Payment::RENEWAL_DEADLINE,
            Payment::CHANG_NAME,
        ]);
        $registerTrademarks = RegisterTrademark::whereIn('id', $paymentRegisterTrademarks->pluck('target_id')->toArray())
            ->with(['trademark'])
            ->get();
        $payments->map(function ($item) use ($appTrademarks, $sfts, $prechecks, $macingResults, $registerTrademarks) {
            switch ($item->type) {
                case Payment::TYPE_TRADEMARK:
                case Payment::TYPE_SUPPORT_FIRST_TIME_AMS:
                case Payment::TYPE_PRECHECK_AMS:
                case Payment::CHANG_ADDRESS:
                case Payment::BEFORE_DUE_DATE:
                    $appTrademark = $appTrademarks->where('id', $item->target_id)->first();

                    if (!empty($appTrademark)) {
                        // Get Pack Name
                        $appTrademark->pack_name = $appTrademark->getPackName();

                        // Get Pack Detail
                        $appTrademark->pack_detail = $appTrademark->getPackDetail();

                        $item->trademark = $appTrademark->trademark ?? null;
                        $item->app_trademark = $appTrademark;
                    }
                    break;

                case Payment::TYPE_SUPPORT_FIRST_TIME:
                    $supportFirstTime = $sfts->where('id', $item->target_id)->first();

                    $item->trademark = $supportFirstTime->trademark;
                    $item->support_first_time = $supportFirstTime;
                    break;

                case Payment::TYPE_PRECHECK:
                    $precheck = $prechecks->where('id', $item->target_id)->first();

                    $item->trademark = $precheck->trademark;
                    $item->precheck = $precheck;
                    break;

                case Payment::TYPE_REASON_REFUSAL:
                    $macingResult = $macingResults->where('id', $item->target_id)->first();
                    $trademark = $macingResult->trademark;

                    $item->trademark = $trademark;
                    $item->app_trademark = $trademark->appTrademark;
                    $item->matching_result = $macingResult;
                    break;
                case Payment::TYPE_SELECT_POLICY:
                    $trademarkPlan = TrademarkPlan::where('id', $item->target_id)
                        ->with([
                            'planCorrespondence',
                            'planCorrespondence.comparisonTrademarkResult',
                            'planCorrespondence.comparisonTrademarkResult.machingResult',
                        ])->first();
                    if ($trademarkPlan) {
                        $planCorrespondence = $trademarkPlan->planCorrespondence;
                        if ($planCorrespondence) {
                            $comparisonTrademarkResult = $planCorrespondence->comparisonTrademarkResult;
                            if ($comparisonTrademarkResult) {
                                $matchingResult = $comparisonTrademarkResult->machingResult;
                                $trademark = $matchingResult->trademark;
                                $item->trademark = $trademark;
                                $item->app_trademark = $trademark->appTrademark;
                                $item->matching_result = $matchingResult;
                            }
                        }
                    }

                    break;
                case Payment::TYPE_TRADEMARK_REGIS:
                case Payment::TYPE_LATE_PAYMENT:
                case Payment::RENEWAL_DEADLINE:
                case Payment::CHANG_NAME:
                    $registerTrademark = $registerTrademarks->where('id', $item->target_id)->first();

                    $item->trademark = $registerTrademark->trademark;
                    $item->register_trademark = $registerTrademark;
                    break;
            }
            return $item;
        });

        return $payments;
    }

    /**
     * Load content payments
     *
     * @param Collection $payments
     * @return Collection
     */
    public function loadContent(Collection $payments)
    {
        return $payments->map(function ($item) {
            $content = '';
            switch ($item->type) {
                case Payment::TYPE_TRADEMARK:
                case Payment::TYPE_SUPPORT_FIRST_TIME_AMS:
                case Payment::TYPE_PRECHECK_AMS:
                    $content = $item->app_trademark->pack_name ?? '';
                    $content .= '<br>';
                    $content .= __('labels.payment_table.cost_service.app_trademark.pack_info', [
                        'pack_detail' => $item->app_trademark->pack_detail ?? '',
                        'total_prod' => $item->total_prod ?? 0,
                    ]);
                    break;
                case Payment::TYPE_SUPPORT_FIRST_TIME:
                    $content = __('labels.payment_table.cost_service.support_first_time');
                    break;
                case Payment::TYPE_PRECHECK:
                    $content = __('labels.payment_table.cost_service.precheck.info', [
                        'precheck_type' => !empty($item->precheck) ? $item->precheck->getTextPrecheckType() : '',
                    ]);
                    $content .= '<br>';
                    $content .= __('labels.payment_table.cost_service.precheck.cost_service_add_prod', [
                        'cost_service_add_prod' => CommonHelper::formatPrice($item->cost_service_add_prod ?? 0, '円', 0),
                    ]);
                    break;
                case Payment::TYPE_REASON_REFUSAL:
                    $content .= __('labels.payment_table.cost_service.matching_result.info');
                    $content .= '<br>';
                    $content .= __('labels.payment_table.cost_service.matching_result.cost_service_add_prod', [
                        'total_prod' => $payment->total_prod ?? 0,
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod ?? 0, '円', 0),
                    ]);
                    break;
                case Payment::TYPE_SELECT_POLICY:
                    $content .= __('labels.payment_table.cost_service.matching_result.addition_fee', [
                        'total_prod' => $payment->total_prod ?? 0,
                    ]);
                    $content .= '<br>';
                    $content .= __('labels.payment_table.cost_service.matching_result.addition_fee_add_prod', [
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod ?? 0, '円', 0),
                    ]);
                    break;
                case Payment::TYPE_TRADEMARK_REGIS:
                    $content .= __('labels.payment_table.cost_service.regis_trademark.title');
                    $content .= '<br>';
                    $content .= __('labels.payment_table.cost_service.matching_result.addition_fee_add_prod', [
                        'total_prod_block' => $payment->total_prod_block ?? 0,
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod ?? 0, '円', 0),
                    ]);
                    break;
                case Payment::TYPE_LATE_PAYMENT:
                    $content .= __('labels.payment_table.cost_service.regis_trademark.late_payment');
                    $content .= '<br>';
                    $content .= __('labels.payment_table.cost_service.regis_trademark.late_payment_desc', [
                        'total_distinction_block' => $payment->total_distinction_block ?? 0,
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod ?? 0, '円', 0),
                    ]);
                    break;
                case Payment::RENEWAL_DEADLINE:
                    $content .= __('labels.payment_table.cost_service.renewal_deadline.title');
                    $content .= '<br>';
                    $content .= __('labels.payment_table.cost_service.renewal_deadline.desc', [
                        'total_prod_block' => $payment->total_prod_block ?? 0,
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod ?? 0, '円', 0),
                    ]);
                    break;
                case Payment::CHANG_ADDRESS:
                case Payment::CHANG_NAME:
                    $content = __('labels.payment_table.app_change_name');
                    break;
                case Payment::BEFORE_DUE_DATE:
                    $content = __('labels.payment_table.extension_of_period_before_expiry');
                    break;
            }
            $item->content = $content;

            return $item;
        });
    }

    /**
     * Get list payment all
     *
     * @param array $params
     * @return void
     */
    public function getListPaymentAll(array $params)
    {
        return $this->repository->getListPaymentAll($params);
    }

    /**
     * Query list payment all
     *
     * @param array $params
     * @return void
     */
    public function queryListPaymentAll(array $params)
    {
        return $this->repository->queryListPaymentAll($params);
    }

    /**
     * Update payment ajax
     *
     * @param int $paymentId
     * @return void
     */
    public function updatePaymentAjax(int $paymentId)
    {
        DB::beginTransaction();
        try {
            $payment = $this->repository->find($paymentId);
            if ($payment) {
                $updated = $payment->update([
                    'is_treatment' => 0,
                    'payment_status' => Payment::STATUS_WAITING_PAYMENT,
                ]);
                if ($updated) {
                    DB::commit();
                    return true;
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }

        return false;
    }

    /**
     * Get Payment Bank Transfer
     *
     * @return void
     */
    public function getPaymentBankTransfer()
    {
        return $this->repository->getPaymentBankTransfer();
    }

    /**
     * Get form payment
     *
     * @param array data
     * @param Model payment
     * @param string fromPage
     * @return string
     */
    public function getFormPayment(array $data, $payment, $fromPage)
    {
        $contents = [];
        if (str_contains($fromPage, U201SELECT02)) {
            $fromPage = U201SELECT02;
        }
        if (str_contains($fromPage, U201_SELECT_01_N)) {
            $fromPage = U201_SELECT_01_N;
        }

        if (str_contains($fromPage, U021N)) {
            $fromPage = U021N;
        }

        if (str_contains($fromPage, U203)) {
            $fromPage = U203;
        }

        if (str_contains($fromPage, U302_402_5YR_KOUKI)) {
            $fromPage = U302_402_5YR_KOUKI;
        }
        if (str_contains($fromPage, U302_402TSUINO_5YR_KOUKI)) {
            $fromPage = U302_402TSUINO_5YR_KOUKI;
        }
        if (str_contains($fromPage, U402TSUINO)) {
            $fromPage = U402TSUINO;
        } elseif (str_contains($fromPage, U402)) {
            $fromPage = U402;
        }

        $setting = $this->supportFirstTimeRepository->getSetting();
        $payerInfo = $payment->payerInfo;
        $totalProd = isset($data['productIds']) ? (count($data['productIds'] ?? $data['product_names']) ?? 0) : 0;

        $contents['cost_service_value'] = $payment->cost_service_base ?? 0;

        if ($data['payment_type'] == Payment::BANK_TRANSFER) {
            $contents['cost_bank_text'] = __('labels.user_common_payment.bank_transfer_fee');
            $contents['cost_bank_value'] = floor($payment->cost_bank_transfer) ?? 0;
        }
        if ($payerInfo->m_nation_id == NATION_JAPAN_ID) {
            $contents['commission_text'] = __('labels.box_cart.commission');
            $contents['commission_value'] = $payment->commission ?? 0;
            $contents['tax_text'] = __('labels.box_cart.tax_percentage') . '（' .
                $payment->tax_incidence . '%）　';
            $contents['tax_value'] = floor($payment->tax) ?? 0;
        }
        $contents['subtotal_text'] = __('labels.user_common_payment.sub_amount');
        $contents['subtotal_value'] = floor($payment->subtotal) ?? 0;

        $contents['total_text'] = __('labels.user_common_payment.total_amount');
        $contents['total_value'] = floor($payment->total_amount) ?? 0;

        $contents['tax_withholding_text'] = __('labels.user_common_payment.withholding_tax_amount');
        $contents['tax_withholding_value'] = round(floor($payment->tax_withholding * 100) / 100, 2) ?? 0;

        $contents['payment_amount_text'] = __('labels.payment_table.payment_amount');
        $contents['payment_amount_value'] = floor($payment->payment_amount) ?? 0;
        switch ($fromPage) {
            case U011:
                $contents['cost_service_text'] = __('labels.support_first_times.sft');
                break;
            case U000FREE:
                $contents['cost_service_text'] = __('labels.user_common_payment.free_histories');
                break;
            case U011B:
            case U011B_31:
            case U021:
            case U021N:
            case U021B:
            case U021B_31:
            case U000TOP:
            case U031_EDIT_WITH_NUMBER:
            case U031:
            case U031B:
            case U031C:
            case U031D:
            case U031EDIT:
                $appTrademark = null;
                $precheck = null;
                $totalDistinct = $data['sum_distintion'] ?? 0;
                $registType = MPriceList::REGISTRATION;
                $termChange = MPriceList::REGISTRATION_TERM_CHANGE;
                $registerTermChange = $this->getPeriodRegistrationRepository($registType, $termChange);

                if (isset($payment->trademark)
                    && isset($payment->trademark->appTrademark)
                    && $payment->trademark->appTrademark
                ) {
                    $appTrademark = $payment->trademark->appTrademark;
                    if ($appTrademark) {
                        if ($appTrademark->is_mailing_regis_cert == AppTrademark::IS_MAILING_REGIS_CERT_TRUE) {
                            $contents['mailing_regis_cert_el_text'] = __('labels.payment_table.cost_registration_certificate');
                            $contents['mailing_regis_cert_el'] = floor($payment->cost_registration_certificate) ?? 0;
                        }
                        if ($appTrademark->period_registration == AppTrademark::PERIOD_REGISTRATION_TRUE) {
                            $contents['change_5yrs_to_10yrs_text'] = __('labels.user_common_payment.change_5yrs_to_10yrs');
                            $contents['change_5yrs_to_10yrs'] = floor($registerTermChange->base_price + ($registerTermChange->base_price * $setting->value / 100)) ?? 0;
                            $contents['price_fee_submit_5_year'] = floor($payment->cost_10_year_one_distintion) ?? 0;
                            if ($appTrademark->pack != AppTrademark::PACK_A) {
                                $contents['fee_submit_register_year'] = floor($totalDistinct * ($payment->cost_10_year_one_distintion ?? 0));
                            }
                            $contents['stamp_fee_5yrs_text_v2'] = __('labels.support_first_times.cart.stamp_fee_10yrs_v2');
                        } else {
                            if (($payment->type == PAYMENT_TYPE_1)
                            || ($payment->type == PAYMENT_TYPE_3 && $fromPage == U011B)
                            || ($payment->type == PAYMENT_TYPE_5 && in_array($fromPage, [U021, U021N, U021B, U021B_31]))) {
                                $contents['stamp_fee_5yrs_text_v2'] = __('labels.support_first_times.cart.stamp_fee_5yrs_v2');
                                $contents['price_fee_submit_5_year_v2'] = '1区分' . CommonHelper::formatPrice(floor($payment->cost_5_year_one_distintion)) . 'ｘ' . $totalDistinct . '区分';
                            } else {
                                $contents['price_fee_submit_5_year'] = floor($payment->cost_5_year_one_distintion) ?? 0;
                            }
                            if ($appTrademark->pack != AppTrademark::PACK_A) {
                                $contents['fee_submit_register_year'] = floor($totalDistinct * ($payment->cost_5_year_one_distintion ?? 0));
                            }
                            $contents['stamp_fee_5yrs_text'] = __('labels.support_first_times.cart.stamp_fee_5yrs');
                        }
                    }
                }
                if ($fromPage == U021 || $fromPage == U021N) {
                    $contents['cost_service_add_prod_text'] = __('labels.box_cart.cost_service_add_prod');
                    $contents['cost_service_add_prod_value'] = floor($payment->cost_service_add_prod) ?? 0;
                    $precheck = $this->precheckRepository->find($payment->target_id);
                    $contents['cost_service_text'] = $precheck->type_precheck == Precheck::TYPE_CHECK_SIMPLE
                        ? __('labels.box_cart.precheck_simple')
                        : __('labels.box_cart.precheck_select');
                } else {
                    $contents['cost_service_text'] = $appTrademark ? $appTrademark->getPackName() : '';
                    //TODO: Waiting for test agent
                    // $contents['total_product'] = __('labels.user_common_payment.total_product', ['attr' => $totalProd]);
                    // $contents['breakdown'] = __('labels.user_common_payment.breakdown');
                    // $contents['basic_fee_up_to_3_prod'] = __('labels.user_common_payment.basic_fee_up_to_3_prod', [
                    //     'total' => ceil(($totalProd - 3) / 3),
                    //     'price' => CommonHelper::formatPrice($payment->cost_service_base)
                    // ]);
                    // $contents['addition_prod'] = __('labels.user_common_payment.addition_prod', [
                    //     'total' => $totalProd - 3,
                    //     'base_price' => CommonHelper::formatPrice($payment->cost_service_add_prod) ?? 0 ,
                    //     'price' => CommonHelper::formatPrice($payment->cost_service_add_prod * ceil(($totalProd - 3) / 3))
                    // ]);

                    // $contents['cost_service_value'] = (int)$payment->cost_service_base + $payment->cost_service_add_prod * ceil(($totalProd - 3) / 3);
                }

                $contents['product_selected_text'] = __('labels.support_first_times.addition');
                $contents['prod_name_3_prod_text'] = __('labels.support_first_times.cart.prod_name_3_prod');
                $contents['product_selected_count'] = $totalProd > 3 ? $totalProd - 3 : $totalProd;
                $contents['each_3_prod_pack'] = $payment->cost_service_add_prod ?? 0;
                $contents['price_product_add'] = $payment->cost_service_add_prod * ceil($payment->reduce_number_distitions / 3) ?? 0;

                $contents['cost_patent_office_text_1'] = __('labels.support_first_times.cart.expense_patent_3_category');
                $contents['sum_distinction'] = $totalDistinct;

                $contents['pof_1st_distinction_5yrs'] = $payment->cost_print_application_one_distintion;
                $contents['pof_2nd_distinction_5yrs'] = $payment->cost_print_application_add_distintion;
                $contents['fee_submit_register'] = ($totalDistinct > 0 ? $payment->cost_print_application_one_distintion : 0)
                    + ($totalDistinct - 1) * $payment->cost_print_application_add_distintion;

                // if ($fromPage == U031_EDIT_WITH_NUMBER || $fromPage == U031 || $fromPage == U031EDIT) {
                //     $contents['fee_submit_register'] = ($totalDistinct > 0 ? $payment->cost_print_application_one_distintion : 0)
                //         + ($totalDistinct) * $payment->cost_print_application_add_distintion;
                // }
                break;
            case U000LIST_CHANGE_ADDRESS_02:
                $contents['cost_change_address_text'] = __('labels.user_common_payment.change_address');
                $contents['cost_change_address_value'] = floor($payment->cost_change_address) ?? 0;

                $contents['cost_change_name_text'] = __('labels.user_common_payment.change_name_u000list_change_address_02');
                $contents['cost_change_name_value'] = floor($payment->cost_change_name) ?? 0;
                break;
            case U000LIST_CHANGE_ADDRESS_02_KENRISHA:
                $contents['cost_change_address_text'] = __('labels.user_common_payment.change_address');
                $contents['cost_change_address_value'] = floor($payment->cost_change_address) ?? 0;

                $contents['cost_change_name_text'] = __('labels.user_common_payment.change_name');
                $contents['cost_change_name_value'] = floor($payment->cost_change_name) ?? 0;

                $contents['cost_print_address_text'] = __('labels.user_common_payment.print_change_address');
                $contents['cost_print_address_value'] = floor($payment->cost_print_address) ?? 0;

                $contents['cost_print_name_text'] = __('labels.user_common_payment.print_change_name');
                $contents['cost_print_name_value'] = floor($payment->cost_print_name) ?? 0;
                break;
            case U201_SELECT_01:
            case U201_SIMPLE:
                if ($payment->from_page == U201_SELECT_01) {
                    $contents['cost_service_base_text'] = __('labels.plan.cart.title_select');
                } else {
                    $contents['cost_service_base_text'] = __('labels.user_common_payment.cost_service_base_u201simple01');
                }
                $contents['cost_service_base_value'] = floor($payment->cost_service_base) ?? 0;

                if ($payment->from_page == U201_SELECT_01 || $payment->from_page == U201_SIMPLE) {
                    $total = isset($payment->paymentProds) ? $payment->paymentProds->count() : 0;

                    if ($payment->from_page == U201_SELECT_01) {
                        $contents['cost_service_add_prod_text'] = __('labels.user_common_payment.cost_service_add_prod_u201select01n') . __('labels.plan.cart.content_prod_2', [
                            'total' => $total,
                            'price' => CommonHelper::formatPrice(floor($payment->cost_service_add_prod)),
                        ]);
                    } else {
                        $contents['cost_service_add_prod_text'] = __('labels.plan.cart.text_4') . __('labels.plan.cart.content_prod_2', [
                            'total' => $total,
                            'price' => CommonHelper::formatPrice(floor($payment->cost_service_add_prod)),
                        ]);
                    }

                    $contents['cost_service_add_prod_value'] = floor($payment->cost_service_add_prod) * $total ?? 0;
                } else {
                    $contents['cost_service_add_prod_text'] = __('labels.user_common_payment.cost_service_add_prod');
                    $contents['cost_service_add_prod_value'] = floor($payment->cost_service_add_prod) ?? 0;
                }

                $contents['extension_of_period_before_expiry_text'] = __('labels.user_common_payment.extension_of_period_before_expiry');
                $contents['extension_of_period_before_expiry_value'] = floor($payment->extension_of_period_before_expiry) ?? 0;

                $contents['application_discount_text'] = __('labels.user_common_payment.application_discount');
                $contents['application_discount_value'] = floor($payment->application_discount) ?? 0;

                $contents['print_fee_text'] = __('labels.user_common_payment.print_fee');
                $contents['print_fee_value'] = floor($payment->print_fee) ?? 0;
                break;
            case U201_SELECT_01_N:
                $data['count_product'] = count($data['productIds']);
                $contents['cost_service_add_prod_tax'] = isset($data['cost_service_add_prod']) ? $data['cost_service_add_prod'] : $payment->cost_service_add_prod;
                $contents['cost_service_add_prod_text'] = __('labels.user_common_payment.cost_service_add_prod_u201select01n', [
                    'product' => $data['count_product'],
                    'price' => isset($data['cost_service_add_prod']) ? $data['cost_service_add_prod'] : $payment->cost_service_add_prod
                ]);
                $contents['cost_service_add_prod_value'] = floor($payment->cost_service_add_prod * $data['count_product']) ?? 0;

                $contents['extension_of_period_before_expiry_text'] = __('labels.user_common_payment.extension_of_period_before_expiry');
                $contents['extension_of_period_before_expiry_value'] = floor($payment->extension_of_period_before_expiry) ?? 0;

                $contents['application_discount_text'] = __('labels.user_common_payment.application_discount');
                $contents['application_discount_value'] = floor($payment->application_discount) ?? 0;

                $contents['print_fee_text'] = __('labels.user_common_payment.print_fee');
                $contents['print_fee_value'] = floor($payment->print_fee) ?? 0;

                break;
            case U201SELECT02:
                $planCorrespondences = $this->planCorrespondenceRepository->find($payment->target_id);
                $selectPlanAPrice = $this->comparisonTrademarkResultRepository->getSelectPlanPrice(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_A_RATING);
                $selectPlanBCDEPrice = $this->comparisonTrademarkResultRepository->getSelectPlanPrice(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_B_C_D_E);
                $countARating = 0;
                $countOtherRating = 0;
                if ($planCorrespondences) {
                    $planCorrespondences->load('planCorrespondenceProds');
                    $planCorrespondenceProdIds = $planCorrespondences->planCorrespondenceProds->pluck('id')->toArray();
                    $reasonRefNumProd = $this->reasonRefNumProdRepository->findByCondition([
                        'plan_correspondence_prod_ids' => $planCorrespondenceProdIds,
                        'is_choice' => ReasonRefNumProd::IS_CHOICE
                    ])->get();

                    $countARating = $reasonRefNumProd->where('rank', 'like', 'A')->count();
                    $countOtherRating = $reasonRefNumProd->where('rank', '!=', 'A')->count();
                }

                $contents['select_A_price'] = floor($selectPlanAPrice['base_price'] + ($selectPlanAPrice['base_price'] * $setting->value / 100)) ?? 0;
                $contents['select_other_price'] = floor($selectPlanBCDEPrice['base_price'] + ($selectPlanBCDEPrice['base_price'] * $setting->value / 100)) ?? 0;

                $contents['count_a_rating'] = $countARating;
                $contents['count_other_rating'] = $countOtherRating;

                $contents['cost_service_a_rating_text'] = __('labels.plan_select02.A_rating');
                $contents['cost_service_a_rating_value'] = floor($payment->cost_service_base * $countARating) ?? 0;

                $contents['cost_service_other_rating_text'] = __('labels.plan_select02.B_rating');
                $contents['cost_service_other_rating_value'] = floor($payment->cost_service_add_prod * $countOtherRating) ?? 0;

                break;
            case U203:
            case U203N:
                $plans = $this->planRepository->findByCondition(['trademark_plan_id' => $data['trademark_plan_id']], ['planDetails.planDetailProducts'])->get();
                $productCount = count($data['productIds']) ?? 0;

                $costAdditional = MPriceList::where([
                    'service_type' => MPriceList::REASONS_REFUSAL,
                    'package_type' => MPriceList::ADD_OPTION_EACH_PROD,
                ])->first();

                $firstPlan = $plans->first();
                $totalDistinction = 0;
                if ($firstPlan) {
                    $firstPlanDetailChoice = $firstPlan->planDetails->where('is_choice', IS_CHOICE)->first()->load('planDetailProducts.planDetailDistinct');
                    $totalDistinction = count($this->planCorrespondenceRepository->getNumberProduct($firstPlanDetailChoice));
                }
                $contents['cost_service_base_text'] = __('labels.refusal_plans.u203b02.prod_addition_fee', [
                    'count_prod' => $productCount,
                    'base_price' => CommonHelper::formatPrice($payment->cost_service_add_prod ?? 0),
                ]);

                $contents['cost_service_base_value'] = floor(($payment->cost_service_add_prod ?? 0) * $totalDistinction);
                $appTrademark = $payment->trademark->appTrademark;
                $periodRegistrationFee = null;

                if ($appTrademark->period_registration == AppTrademark::PERIOD_REGISTRATION_TRUE) {
                    $periodRegistrationFee = $costAdditional->pof_2nd_distinction_10yrs;
                } else {
                    $periodRegistrationFee = $costAdditional->pof_2nd_distinction_5yrs;
                }
                $contents['expenses_to_PO_text'] = __('labels.refusal_plans.u203b02.expenses_to_PO', [
                    'distinction_count' => $totalDistinction,
                    'price_registration_yrs' => $periodRegistrationFee,
                ]);
                $contents['expenses_to_PO_value'] = floor($totalDistinction * $periodRegistrationFee);

                break;
            case U210_OVER_02:
            case U210_ALERT_02:
                if ($payment->extension_of_period_before_expiry != null) {
                    if ($fromPage == U210_OVER_02) {
                        $contents['cost_service_base_text'] = __('labels.user_common_payment.cost_base_service_alert');
                    } else {
                        $contents['cost_service_base_text'] = __('labels.user_common_payment.cost_base_service_over');
                    }
                    $contents['cost_service_base_value'] = floor($payment->extension_of_period_before_expiry) ?? 0;
                }
                $contents['cost_service_text'] = __('labels.user_common_payment.cost_base_service_over');
                $contents['print_fee_text'] = __('labels.user_common_payment.print_fee');
                $contents['print_fee_value'] = floor($payment->print_fee) ?? 0;
                break;
            case U302:
                $registerTrademark = $this->registerTrademarkRepository->find($payment->target_id);
                $appTrademark = $payment->trademark->appTrademark;

                $reduceNumberDistinctFee = $this->getSystemFee(MPriceList::REGISTRATION, MPriceList::REGISTRATION_REDUCTION_PROCEDURE);
                $contents['cost_service_base_text'] = __('labels.u302.est_regis_procedure_service');
                $contents['cost_service_base_value'] = floor($payment->cost_service_base) ?? 0;

                $productAddOnFee = (int) $payment->cost_service_add_prod * (int) ceil($data['total_product_each_add'] / 3);

                if ($registerTrademark->reg_period_change_fee > 0) {
                    $contents['regis_period_change_fee_text'] = __('labels.u302.regis_period_change_fee');
                    $contents['regis_period_change_fee'] = floor($registerTrademark->reg_period_change_fee) ?? 0;
                }
                if ($registerTrademark->mailing_register_cert_fee > 0) {
                    $contents['mailing_regis_cert_el_text'] = __('labels.u302.mailing_regis_cert');
                    $contents['mailing_regis_cert_el'] = floor($registerTrademark->mailing_register_cert_fee) ?? 0;
                }
                $contents['cost_service_add_prod_text'] = __('labels.u302.product_add_on', [
                    'total' => $data['total_product_each_add'],
                    'price' => CommonHelper::formatPrice($payment->cost_service_add_prod),
                ]);
                $contents['cost_service_add_prod_value'] = $productAddOnFee;
                if ($payment->reduce_distinctions > 0) {
                    $contents['reduce_number_distitions_text'] = __('labels.u302.reduce_number_distitions');
                    $contents['reduce_number_distitions_value'] = floor($reduceNumberDistinctFee['cost_service_base']) ?? 0;
                }

                $contents['cost_change_name_text'] = __('labels.u302.change_name');
                $contents['cost_change_name_value'] = floor($payment->cost_change_name) ?? 0;

                $contents['cost_change_address_text'] = __('labels.u302.change_address');
                $contents['cost_change_address_value'] = floor($payment->cost_change_address) ?? 0;
                $totalDistinct = $payment->reduce_number_distitions;
                $registerTermChange = $this->getPeriodRegistrationRepository(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
                if ($registerTrademark->period_registration == AppTrademark::PERIOD_REGISTRATION_TRUE) {
                    $contents['change_5yrs_to_10yrs_text'] = __('labels.user_common_payment.change_5yrs_to_10yrs');
                    $contents['change_5yrs_to_10yrs'] = floor($registerTermChange->base_price + ($registerTermChange->base_price * $setting->value / 100)) ?? 0;
                    $contents['price_fee_submit_5_year'] = floor($payment->cost_10_year_one_distintion) ?? 0;
                    $contents['fee_submit_register_year'] = floor($totalDistinct * ($payment->cost_10_year_one_distintion ?? 0));
                    $contents['stamp_fee_5yrs_text'] = __('labels.support_first_times.cart.stamp_fee_10yrs');
                    $contents['cost_patent_office_text'] = __('labels.u302.cost_patent_office') . __('labels.u302.10_year_registration');
                } else {
                    $contents['cost_patent_office_text'] = __('labels.u302.cost_patent_office') . __('labels.u302.5_year_registration');

                    $contents['price_fee_submit_5_year'] = floor($payment->cost_5_year_one_distintion) ?? 0;
                    $contents['fee_submit_register_year'] = floor($totalDistinct * ($payment->cost_5_year_one_distintion ?? 0));
                    $contents['stamp_fee_5yrs_text'] = __('labels.support_first_times.cart.stamp_fee_5yrs');
                }
                $price = $payment->cost_5_year_one_distintion;
                if ($registerTrademark->period_registration == AppTrademark::PERIOD_REGISTRATION_TRUE) {
                    $price = $payment->cost_10_year_one_distintion;
                }
                $contents['cost_patent_office_text_2'] = __('labels.u302.year_registration_2', [
                    'total' => $payment->reduce_number_distitions,
                    'price' => CommonHelper::formatPrice($price),
                ]);
                $contents['sum_distinction'] = $totalDistinct;
                break;
            case U302_402_5YR_KOUKI:
            case U302_402TSUINO_5YR_KOUKI:
                $paymentProds = $payment->paymentProds->load('product.mDistinction');
                $products = $paymentProds->groupBy('product.m_distinction_id');
                $totalDistinct = $products->count();

                $contents['cost_service_base_text'] = __('labels.u302_402_5yr_kouki.cart.price_service');
                $contents['cost_service_base_value'] = floor($payment->cost_service_base) ?? 0;

                $totalDistinctAddition = $totalDistinct - 3;
                $totalDistinctAddition = $totalDistinctAddition > 0 ? $totalDistinctAddition : 0;
                if ($totalDistinctAddition > 0) {
                    $contents['cost_service_add_prod_text'] = $totalDistinctAddition
                        . __('labels.u302_402_5yr_kouki.cart.price_service_add_prod')
                        . CommonHelper::formatPrice($payment->cost_service_add_prod, '円') . '）';
                    $contents['cost_service_add_prod_value'] = floor($totalDistinctAddition * $payment->cost_service_add_prod) ?? 0;
                }

                $contents['cost_change_name_text'] = __('labels.u302_402_5yr_kouki.cart.change_name_fee');
                $contents['cost_change_name_value'] = floor($payment->cost_change_name) ?? 0;

                $contents['cost_change_address_text'] = __('labels.u302_402_5yr_kouki.cart.change_address_fee');
                $contents['cost_change_address_value'] = floor($payment->cost_change_address) ?? 0;

                $contents['cost_5_year_one_distintion_text'] = __('labels.u302_402_5yr_kouki.cart.print_price_service_fee')
                    . '<br>'
                    . $totalDistinct
                    . '区分 1区分'
                    . CommonHelper::formatPrice($payment->cost_5_year_one_distintion, '円')
                    . 'x'
                    . $totalDistinct
                    . '区分';
                $contents['cost_5_year_one_distintion_value'] = floor($totalDistinct * $payment->cost_5_year_one_distintion) ?? 0;

                $contents['cost_print_name_text'] = __('labels.u302_402_5yr_kouki.cart.print_change_name_fee');
                $contents['cost_print_name_value'] = floor($payment->cost_print_name) ?? 0;

                $contents['cost_print_address_text'] = __('labels.u302_402_5yr_kouki.cart.print_change_address_fee');
                $contents['cost_print_address_value'] = floor($payment->cost_print_address) ?? 0;
                break;
            case U402:
            case U402TSUINO:
                $registerTrademark = $this->registerTrademarkRepository->find($payment->target_id);
                $registerTrademarkProd = $registerTrademark->registerTrademarkProds ?? collect([]);
                $registerTrademarkProd = $registerTrademarkProd->where('is_apply', RegisterTrademark::IS_APPLY_TRUE);
                $isApplyProdId = $registerTrademarkProd->pluck('m_product_id')->toArray();

                $paymentProds = $payment->paymentProds->load('product.mDistinction');
                $products = $paymentProds->whereIn('m_product_id', $isApplyProdId)->groupBy('product.m_distinction_id');
                $totalDistinct = $products->count();

                $contents['cost_service_base_text'] = __('labels.u402.cart.price_service');
                $contents['cost_service_base_value'] = floor($payment->cost_service_base) ?? 0;

                $totalDistinctAddition = $totalDistinct - 3;
                $totalDistinctAddition = $totalDistinctAddition > 0 ? $totalDistinctAddition : 0;
                if ($totalDistinctAddition > 0) {
                    $contents['cost_service_add_prod_text'] = __('labels.u402.cart.price_service_add_prod');
                    $contents['cost_service_add_prod_text'] = Str::replace(
                        '{distinction_addition}',
                        $totalDistinctAddition,
                        $contents['cost_service_add_prod_text']
                    );
                    $contents['cost_service_add_prod_text'] = Str::replace(
                        '{service_add_prod}',
                        CommonHelper::formatPrice($payment->cost_service_add_prod, '円'),
                        $contents['cost_service_add_prod_text']
                    );
                    $contents['cost_service_add_prod_value'] = floor($totalDistinctAddition * $payment->cost_service_add_prod) ?? 0;
                }

                $contents['reduce_number_distitions_text'] = __('labels.u402.cart.reduce_distinctions');
                $contents['reduce_number_distitions_value'] = floor($payment->reduce_distinctions) ?? 0;

                $contents['cost_change_name_text'] = __('labels.u302_402_5yr_kouki.cart.change_name_fee');
                $contents['cost_change_name_value'] = floor($payment->cost_change_name) ?? 0;

                $contents['cost_change_address_text'] = __('labels.u302_402_5yr_kouki.cart.change_address_fee');
                $contents['cost_change_address_value'] = floor($payment->cost_change_address) ?? 0;

                $text = __('labels.u402.cart.print_price_service_fee');
                $text = Str::replace('{total_distinction}', $totalDistinct, $text);
                $value = 0;
                if ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_5_YEAR) {
                    $text = Str::replace('{year}', YEAR_5, $text);
                    $text = Str::replace('{one_distinction_fee}', CommonHelper::formatPrice($payment->cost_5_year_one_distintion, '円'), $text);

                    $value = $totalDistinct * $payment->cost_5_year_one_distintion;
                } elseif ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_10_YEAR) {
                    $text = Str::replace('{year}', YEAR_10, $text);
                    $text = Str::replace('{one_distinction_fee}', CommonHelper::formatPrice($payment->cost_10_year_one_distintion, '円'), $text);

                    $value = $totalDistinct * $payment->cost_10_year_one_distintion;
                }
                $contents['cost_5_year_one_distintion_text'] = $text;
                $contents['cost_5_year_one_distintion_value'] = floor($value);

                $contents['cost_print_name_text'] = __('labels.u302_402_5yr_kouki.cart.print_change_name_fee');
                $contents['cost_print_name_value'] = floor($payment->cost_print_name) ?? 0;

                $contents['cost_print_address_text'] = __('labels.u302_402_5yr_kouki.cart.print_change_address_fee');
                $contents['cost_print_address_value'] = floor($payment->cost_print_address) ?? 0;
                break;
        }

        $contents['payment_amount_value'] = floor($payment->total_amount - $payment->tax_withholding);

        return $contents;
    }


    /**
     * Get Payment Draft
     *
     * @param  string $trademarkId
     * @param  int $type
     * @param  string $fromPage
     * @return Model|null
     */
    public function getPaymentDraft(string $trademarkId, int $type, string $fromPage): ?Model
    {
        $params = [
            'type' => $type,
            'trademark_id' => $trademarkId,
            // 'payment_status' => Payment::IS_TREATMENT_WAIT,
            // 'is_treatment' => Payment::STATUS_SAVE,
        ];
        switch ($fromPage) {
            case U203N:
                $params['from_page_n'] = U203 . '_';
                break;
            case U203:
                $params['from_page'] = U203;
                break;
            case U210_ALERT_02:
                $params['from_page'] = U210_ALERT_02;
                break;
            case U210_OVER_02:
                $params['from_page'] = U210_OVER_02;
                break;
            case U201_SELECT_01_N:
                $params['from_page_n'] = U201_SELECT_01_N;
                break;
        }

        return $this->findByCondition($params, ['payerInfo'])->orderBy('id', SORT_TYPE_DESC)->first();
    }
}
