<?php

namespace App\Services;

use App\Models\AppTrademark;
use App\Models\MProduct;
use App\Models\Payment;
use App\Models\PlanDetail;
use App\Models\PlanDetailDistinct;
use App\Models\PlanDetailProduct;
use App\Models\Setting;
use App\Repositories\MatchingResultRepository;
use App\Repositories\PayerInfoRepository;
use App\Repositories\PaymentProductRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PlanCorrespondenceRepository;
use App\Repositories\PlanDetailRepository;
use App\Repositories\SettingRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PlanCorrespondenceService extends BaseService
{
    protected $planDetailRepository;
    protected $settingRepository;
    protected $payerInfoRepository;
    protected $paymentRepository;
    protected $paymentProductRepository;
    protected $matchingResultRepository;
    /**
     * Initializing the instances and variables
     *
     * @param PlanCorrespondenceRepository $planCorrespondenceRepository
     */
    public function __construct(
        PlanCorrespondenceRepository $planCorrespondenceRepository,
        SettingRepository $settingRepository,
        PayerInfoRepository $payerInfoRepository,
        PlanDetailRepository $planDetailRepository,
        PaymentRepository $paymentRepository,
        MatchingResultRepository $matchingResultRepository,
        PaymentProductRepository $paymentProductRepository
    )
    {
        $this->repository = $planCorrespondenceRepository;
        $this->settingRepository = $settingRepository;
        $this->payerInfoRepository = $payerInfoRepository;
        $this->planDetailRepository = $planDetailRepository;
        $this->paymentRepository = $paymentRepository;
        $this->matchingResultRepository = $matchingResultRepository;
        $this->paymentProductRepository = $paymentProductRepository;
    }

    /**
     * Get MProduct Id By Plan Detail
     *
     * @param  mixed $planCorrespondence
     * @return array
     */
    public function getMProductIdByPlanDetail($trademarkPlan): array
    {
        $mProductId = [];
        $numberPlanDetailProducts = [];
        foreach ($trademarkPlan->plans as $plan) {
            foreach ($plan->planDetails as $planDetail) {
                if ($planDetail->planDetailProducts->count()) {
                    array_push($mProductId, $planDetail->planDetailProducts->pluck('m_product_id')->toArray());
                    array_push($numberPlanDetailProducts, $planDetail->planDetailProducts->whereIn('role_add', [ROLE_MANAGER, ROLE_SUPERVISOR])->count());
                }
            }
        }
        $data = [
            'mProductId' => collect($mProductId)->flatten()->unique()->toArray(),
            'number_plan_detail_product' => array_sum($numberPlanDetailProducts),
        ];

        return $data;
    }

    /**
     * Get Number Plan Detail Product
     *
     * @param  mixed $trademarkPlan
     * @return int
     */
    public function getNumberPlanDetailProduct($trademarkPlan): int
    {
        $numberPlanDetailProducts = [];
        foreach ($trademarkPlan->plans as $plan) {
            foreach ($plan->planDetails as $planDetail) {
                $planDetail->load('planDetailProducts.planDetailDistinct');
                if ($planDetail->planDetailProducts->count()) {
                    $count = $planDetail->planDetailProducts
                        ->where('planDetailDistinct.is_distinct_settlement', PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_TRUE)
                        ->whereIn('role_add', [ROLE_MANAGER, ROLE_SUPERVISOR])
                        ->count();
                    if ($count) {
                        array_push($numberPlanDetailProducts, $count);
                    }
                }
            }
        }

        return count($numberPlanDetailProducts);
    }

    /**
     * Get Plan Correspondence
     *
     * @param  mixed $id
     * @param  mixed $flag
     * @return Model|null
     */
    public function getPlanCorrespondence($id, $flag = null)
    {
        $conditionIsChoice = isset($flag) && $flag == PlanDetailProduct::IS_CHOICE ? PlanDetailProduct::IS_CHOICE : PlanDetailProduct::IS_NOT_CHOICE;

        return $this->repository->findByCondition(['comparison_trademark_result_id' => $id])->with([
            'comparisonTrademarkResult.trademark.trademarkDocuments',
            'trademarkPlans.plans.planDetails.planDetailProducts' => function ($query) use ($conditionIsChoice) {
                return $query->where('is_choice', $conditionIsChoice)->with([
                    'mProducts',
                ]);
            },
            'trademarkPlans.plans.planReasons.reasons' => function ($query) {
                return $query->with(['reasonNo', 'mLawsRegulation']);
            },
            'trademarkPlans.plans.planDetails.mTypePlan.mTypePlanDocs',
        ])->whereHas('comparisonTrademarkResult.trademark', function ($query) {
            return $query->where('user_id', Auth::user()->id);
        })->orderBy('id', 'DESC')->first();
    }

    /**
     * Calculator Choose Plan
     *
     * @param  mixed $request
     * @return array
     */
    public function calculatorChoosePlan($request): array
    {
        $planDetail = null;
        if (isset($request['plan_detail_id'])) {
            $planDetail = $this->planDetailRepository->find($request['plan_detail_id']);
        }
        $numberPlanDetailProducts = count($this->getNumberProduct($planDetail));

        $setting = $this->settingRepository->getSetting();
        $numberDistinct = 0;
        if (isset($request['number_distinct'])) {
            $numberDistinct = $request['number_distinct'];
        }


        $prodAdd = $request['cost_additional']['base_price'] + (($request['cost_additional']['base_price'] * $setting->value) / 100);

        $feeDistintion = 0;
        if ($request['period_registration'] == AppTrademark::PERIOD_REGISTRATION_5_YEAR) {
            $feeDistintion = $request['cost_additional']['pof_2nd_distinction_5yrs'];
            $patentCosts = $request['cost_additional']['pof_2nd_distinction_5yrs'];
        } else {
            $feeDistintion = $request['cost_additional']['pof_2nd_distinction_10yrs'];
            $patentCosts = $request['cost_additional']['pof_2nd_distinction_10yrs'];
        }

        $pofDistinction = $numberDistinct * $feeDistintion;
        $costAddProdName = $numberPlanDetailProducts * $prodAdd;
        $total = $pofDistinction + $costAddProdName;
        $subTotal = $request['cost_bank_transfer'] + $costAddProdName;
        $commission = $subTotal / (1 + (($setting->value) / 100));
        $tax = $subTotal - $commission;
        $totalAmount = $subTotal + $pofDistinction;

        $data = [
            'pof_distinction' => $pofDistinction,
            'number_distinct' => $request['number_distinct'] ?? 0,
            'number_plan_detail_product' => $numberPlanDetailProducts,
            'cost_add_prod_name' => floor($costAddProdName),
            'prod_add' => $prodAdd,
            'total' => floor($total),
            'cost_bank_transfer' => floor($request['cost_bank_transfer']),
            'sub_total' => floor($subTotal),
            'commission' => floor((float) (string) $commission),
            'tax' => floor($tax * 100) / 100,
            'percent_tax' => $setting->value,
            'patent_cost' => floor($patentCosts),
            'cost_prod_add' => floor($costAddProdName),
            'total_amount' => floor($totalAmount)
        ];

        return $data;
    }

    /**
     * Get Number Product
     *
     * @param  mixed $planDetail
     * @return array
     */
    public function getNumberProduct($planDetail): array
    {
        return $this->repository->getNumberProduct($planDetail);
    }

    /**
     * Save Data Choose Plan
     *
     * @param  mixed $params
     * @return array
     */
    public function saveDataChoosePlan($params): array
    {
        try {
            DB::beginTransaction();

            switch ($params['submit_type']) {
                case U201B_CANCEL:
                    return ['redirect_to' => U201B_CANCEL];
            }
            $redirectTo = null;
            $key = Str::random(11);
            $planDetails = $this->planDetailRepository->findByCondition(['plan_ids' => $params['plan_ids']])->get();
            $payment = $this->paymentRepository->findByCondition([
                'from_page_n' => U203,
                'trademark_id' => $params['trademark_id'],
            ])
                ->whereIn('is_treatment', [Payment::IS_TREATMENT_WAIT, Payment::IS_TREATMENT_DONE])
                ->whereIn('payment_status', [Payment::STATUS_WAITING_PAYMENT, Payment::STATUS_PAID])
                ->get();

            if (isset($params['redirect_to']) && $params['redirect_to'] == U203N) {
                $fromPage = U203 . '_' . ($payment->count() + 1);
            } else {
                $fromPage = U203;
            }

            foreach ($planDetails as $planDetail) {
                if (isset($params['is_choice']) && ($params['is_choice'])) {
                    if (in_array($planDetail->id, $params['is_choice'])) {
                        $planDetail->update(['is_choice' => PlanDetail::IS_CHOICE]);
                    } else {
                        $planDetail->update(['is_choice' => PlanDetail::IS_NOT_CHOICE]);
                    }
                } else {
                    $planDetail->update(['is_choice' => PlanDetail::IS_NOT_CHOICE]);
                }
            }

            if (isset($params['total']) && $params['total'] == 0
                // && isset($params['is_choice']) && ($params['is_choice'])
            ) {
                $params['has_fee'] = 0;
                $params['from_page'] = U203;
                Session::put($key, $params);

                DB::commit();

                switch ($params['submit_type']) {
                    case U203C:
                        $redirectTo = U203C;
                        break;
                    case U203C_N:
                        $redirectTo = U203C_N;
                        break;
                    case U203B02:
                        $redirectTo = U203B02;
                        break;
                    case _QUOTES:
                        $redirectTo = _QUOTES;
                        break;
                    case U000ANKEN_TOP:
                        $redirectTo = U000ANKEN_TOP;
                        break;
                }

                return [
                    'redirect_to' => $redirectTo,
                    'key_session' => $key,
                    'trademark_id' => $params['trademark_id'],
                ];
            }

            $dataPayerInfo = [
                'target_id' => $params['trademark_plan_id'],
                'payment_type' => $params['payment_type'] ?? null,
                'payer_type' => $params['payer_type'] ?? 0,
                'm_nation_id' => $params['m_nation_id'] ?? 0,
                'payer_name' => $params['payer_name'] ?? '',
                'payer_name_furigana' => $params['payer_name_furigana'] ?? '',
                'postal_code' => $params['postal_code'] ?? null,
                'm_prefecture_id' => $params['m_prefecture_id'] ?? null,
                'address_second' => $params['address_second'] ?? '',
                'address_three' => $params['address_three'] ?? '',
                'type' => TYPE_MATCHING_RESULT_SELECTION
            ];
            if (isset($params['payer_info_id']) && $params['payer_info_id']) {
                $payerInfoDraft = $this->payerInfoRepository->find($params['payer_info_id']);
                $payerInfo = $this->payerInfoRepository->update($payerInfoDraft, $dataPayerInfo);
            } else {
                $payerInfo = $this->payerInfoRepository->create($dataPayerInfo);
            }

            $taxWithHolding = $this->getTaxWithHolding($payerInfo, $params['total_amount']);
            $paymentAmount = $params['total_amount'] - $taxWithHolding;
            $dataPayment = [
                'target_id' => $params['trademark_plan_id'],
                'trademark_id' => $params['trademark_id'],
                'payer_info_id' => $payerInfo->id,
                'payment_status' => Payment::IS_TREATMENT_WAIT,
                'is_treatment' => Payment::STATUS_SAVE,
                'payment_date' => now(),
                'cost_service_base' => 0,
                'cost_bank_transfer' => floor($params['cost_bank_transfer']),
                'cost_service_add_prod' => floor($params['cost_prod_add']),
                'cost_5_year_one_distintion' => floor($params['cost_one_distintion']),
                'cost_10_year_one_distintion' => floor($params['cost_one_distintion']),
                'subtotal' => floor($params['subtotal']),
                'commission' => floor($params['commission']),
                'tax' => floor($params['tax']),
                'total_amount' => floor($params['total_amount']),
                'tax_withholding' => floor($taxWithHolding),
                'payment_amount' => floor($paymentAmount),
                'from_page' => isset($params['from_page']) && $params['from_page'] ? $params['from_page'] : $fromPage,
                'type' => Payment::TYPE_SELECT_POLICY,
            ];

            $conditionUpdatePayment = [
                'payment_status' => Payment::IS_TREATMENT_WAIT,
                'is_treatment' => Payment::STATUS_SAVE,
                'trademark_id' => $params['trademark_id'],
                'type' => Payment::TYPE_SELECT_POLICY,
                'target_id' => $params['trademark_plan_id'],
            ];

            if (isset($params['from_page']) && $params['from_page']) {
                $conditionUpdatePayment['from_page'] = $params['from_page'];
            } else {
                $conditionUpdatePayment['from_page'] = $fromPage;
            }

            $payment = $this->paymentRepository->updateOrCreate($conditionUpdatePayment, $dataPayment);

            $dataUpdatePayment = [];
            if (!$payment->quote_number) {
                $dataUpdatePayment['quote_number'] = $this->generateQIR($params['trademark_number'], 'quote');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['invoice_number'] = $this->generateQIR('', 'invoice');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['receipt_number'] = $this->generateQIR('', 'receipt');
            }
            $payment->update($dataUpdatePayment);
            //6.Create or update payment_prods
            $dataPaymentPro['payment_id'] = $payment->id;
            $dataPaymentPro['productIds'] = $params['productIds'];

            $this->paymentRepository->createPaymentProds($dataPaymentPro);

            $dataPayment['payment_id'] = $payment->id;
            $dataPayment['payment_type'] = $params['payment_type'];
            $dataPayment['productIds'] = isset($params['productIds']) ? $params['productIds'] : [];
            $dataPayment['has_fee'] = 1;
            $dataPayment['from_page'] = $params['from_page'];
            $dataPayment['comparison_trademark_result_id'] = $params['comparison_trademark_result_id'];
            $dataPayment['trademark_plan_id'] = $params['trademark_plan_id'];
            $dataPayment['redirect_to'] = $params['redirect_to'];
            if ($params['redirect_to'] == U203N) {
                $dataPayment['from_page'] = U203N;
            }
            if ($params['redirect_to'] == U203) {
                $dataPayment['from_page'] = U203;
            }
            Session::put($key, $dataPayment);

            switch ($params['submit_type']) {
                case U203C:
                    $redirectTo = U203C;
                    break;
                case U203C_N:
                    $redirectTo = U203C_N;
                    break;
                case U203B02:
                    $redirectTo = U203B02;
                    break;
                case _QUOTES:
                    $redirectTo = _QUOTES;
                    break;
                case U000ANKEN_TOP:
                    $redirectTo = U000ANKEN_TOP;
                    break;
            }
            DB::commit();

            return [
                'redirect_to' => $redirectTo,
                'payment_id' => $payment->id ?? '',
                'key_session' => $key,
                'trademark_id' => $params['trademark_id'],
            ];
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            throw new \Exception($e->getMessage());
        }
    }
}
