<?php

namespace App\Services;

use App\Models\AgentGroup;
use App\Models\AgentGroupMap;
use App\Models\AppTrademark;
use App\Models\DocSubmission;
use App\Models\MProduct;
use App\Models\Payment;
use App\Models\PlanDetail;
use App\Models\PlanDetailDistinct;
use App\Models\PlanDetailProduct;
use App\Models\Trademark;
use App\Models\TrademarkPlan;
use App\Repositories\DocSubmissionAttachPropertyRepository;
use App\Repositories\DocSubmissionRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\TrademarkPlanRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TrademarkPlanService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param TrademarkPlanRepository $trademarkPlanRepository
     * @param PaymentRepository $paymentRepository
     * @param DocSubmissionRepository $docSubmissionRepository
     * @param DocSubmissionAttachPropertyRepository $docSubmissionAttachPropertyRepository
     */
    public function __construct(
        TrademarkPlanRepository $trademarkPlanRepository,
        PaymentRepository $paymentRepository,
        DocSubmissionRepository $docSubmissionRepository,
        DocSubmissionAttachPropertyRepository $docSubmissionAttachPropertyRepository
    )
    {
        $this->repository = $trademarkPlanRepository;
        $this->paymentRepository = $paymentRepository;
        $this->docSubmissionRepository = $docSubmissionRepository;
        $this->docSubmissionAttachPropertyRepository = $docSubmissionAttachPropertyRepository;
    }

    /**
     * Get Common A205 Hosei01 Window
     *
     * @param integer $tradeMarkPlanId - trademark_plan_id
     * @return array
     */
    public function getCommonA205Hosei01Window($tradeMarkPlanId): array
    {
        $depositType = null;
        $depositAccountNumber = null;
        $totalAmount = 0;
        $flagShowTotalAmount = false;

        $dataProducts = DB::table('m_products')
            ->leftJoin('plan_detail_products', 'm_products.id', 'plan_detail_products.m_product_id')
            ->leftJoin('plan_details', 'plan_detail_products.plan_detail_id', 'plan_details.id')
            ->leftJoin('plans', 'plan_details.plan_id', 'plans.id')
            ->leftJoin('trademark_plans', 'plans.trademark_plan_id', 'trademark_plans.id')
            ->leftJoin('m_distinctions', 'm_products.m_distinction_id', 'm_distinctions.id')
            ->where('trademark_plans.id', $tradeMarkPlanId)
            ->where('plan_detail_products.is_choice', PlanDetailProduct::IS_CHOICE)
            ->where('plan_detail_products.is_deleted', false)
            ->whereNull('plan_detail_products.deleted_at')
            ->distinct()
            ->select('m_products.*', 'plans.trademark_plan_id', 'm_distinctions.name as m_distinction_name')
            ->get()
            ->groupBy('m_distinction_name')
            ->sortKeys();

        $trademark = DB::table('trademarks')
            ->leftJoin('app_trademarks', 'trademarks.id', 'app_trademarks.trademark_id')
            ->leftJoin('comparison_trademark_results', 'trademarks.id', 'comparison_trademark_results.trademark_id')
            ->leftJoin('plan_correspondences', 'comparison_trademark_results.id', 'plan_correspondences.comparison_trademark_result_id')
            ->leftJoin('trademark_plans', 'plan_correspondences.id', 'trademark_plans.plan_correspondence_id')
            ->where('trademark_plans.id', $tradeMarkPlanId)
            ->select('trademarks.*', 'app_trademarks.period_registration', 'app_trademarks.id as app_trademark_id')
            ->first();

        if ($trademark) {
            $agent = DB::table('agents')
                ->leftJoin('agent_group_maps', 'agents.id', 'agent_group_maps.agent_id')
                ->leftJoin('agent_groups', 'agent_group_maps.agent_group_id', 'agent_groups.id')
                ->leftJoin('app_trademarks', 'agent_groups.id', 'app_trademarks.agent_group_id')
                ->where('agent_groups.status_choice', AgentGroup::STATUS_CHOICE_TRUE)
                ->where('agent_group_maps.type', AgentGroupMap::TYPE_NOMINATED)
                ->first();

            if ($agent) {
                $depositType = $agent->deposit_type;
                $depositAccountNumber = $agent->deposit_account_number;
            }

            $dataPayments = DB::table('payments')
                ->leftJoin('payment_prods', 'payments.id', 'payment_prods.payment_id')
                ->leftJoin('m_products', 'payment_prods.m_product_id', 'm_products.id')
                ->leftJoin('m_distinctions', 'm_products.m_distinction_id', 'm_distinctions.id')
                ->where('payments.trademark_id', $trademark->id)
                ->where('payments.type', Payment::TYPE_SELECT_POLICY)
                ->whereIn('payments.from_page', [
                    U203,
                    U203C,
                    U203B02,
                    U203N,
                    U203C_N,
                ])
                ->select('payments.id', 'payments.cost_5_year_one_distintion', 'payments.cost_10_year_one_distintion', 'm_distinctions.id as m_distinction_id')
                ->get()
                ->groupBy('id');

            foreach ($dataPayments as $idPayment => $payment) {
                $mDistinctionIds = [];
                $costYearOneDistintion = 0;
                foreach ($payment as $item) {
                    if ($item->m_distinction_id) {
                        $mDistinctionIds[] = $item->m_distinction_id;
                    }
                    $mDistinctionIds = array_unique($mDistinctionIds);
                    //check 5 year or 10 year
                    if ($trademark->period_registration == AppTrademark::PERIOD_REGISTRATION_FALSE) {
                        $costYearOneDistintion = $item->cost_5_year_one_distintion ?? 0;
                    } else {
                        $costYearOneDistintion = $item->cost_10_year_one_distintion ?? 0;
                    }
                }
                $totalAmount += $costYearOneDistintion * count($mDistinctionIds);
            }

            if ($dataPayments->count() > 0 && $totalAmount > 0) {
                $flagShowTotalAmount = true;
            }
        }

        return [
            'slag_show_total_amount' => $flagShowTotalAmount,
            'data_products' => $dataProducts,
            'deposit_type' => $depositType,
            'deposit_account_number' => $depositAccountNumber,
            'total_amount' => $totalAmount
        ];
    }

    /**
     * Duplicate Trademark Plan with relation data
     *
     * @param Model $trademarkPlan
     * @return Model
     */
    public function duplicate(Model $trademarkPlan): Model
    {
        $trademarkPlan = $trademarkPlan->load([
            'plans.planReasons',
            'plans.planDetails.planDetailProducts.planDetailDistinct',
            'plans.planDetails.planDetailProducts.planDetailProductCodes',
            'plans.planDetails.planDetailDocs',
        ]);

        // Duplicate Trademark Plan
        $newTrademarkPlan = $trademarkPlan->replicate();
        $newTrademarkPlan->flag_role = TrademarkPlan::FLAG_ROLE_2;
        $newTrademarkPlan->is_reject = false;
        $newTrademarkPlan->reason_cancel = null;
        $newTrademarkPlan->is_cancel = false;
        $newTrademarkPlan->is_decision = false;
        $newTrademarkPlan->is_confirm = false;
        $newTrademarkPlan->save();

        // Duplicate Plans
        $plans = $trademarkPlan->plans ?? collect([]);
        foreach ($plans as $plan) {
            $newPlan = $plan->replicate();
            $newPlan->trademark_plan_id = $newTrademarkPlan->id;
            $newPlan->save();

            // Duplicate Plan Reasons
            $planReasons = $plan->planReasons ?? collect([]);
            foreach ($planReasons as $planReason) {
                $newPlanReason = $planReason->replicate();
                $newPlanReason->plan_id = $newPlan->id;
                $newPlanReason->save();
            }

            // Duplicate Plan Details
            $planDetails = $plan->planDetails ?? collect([]);
            foreach ($planDetails as $planDetail) {
                $newPlanDetail = $planDetail->replicate();
                $newPlanDetail->plan_id = $newPlan->id;
                $newPlanDetail->is_choice = false;
                $newPlanDetail->is_confirm = false;
                $newPlanDetail->is_choice_past = false;
                $newPlanDetail->save();

                // Duplicate Plan Detail Docs
                $planDetailDocs = $planDetail->planDetailDocs ?? collect([]);
                foreach ($planDetailDocs as $planDetailDoc) {
                    $newPlanDetailDoc = $planDetailDoc->replicate();
                    $newPlanDetailDoc->plan_detail_id = $newPlanDetail->id;
                    $newPlanDetailDoc->save();
                }

                // Duplicate Plan Detail [Distinct | Product | ProductCode]
                $planDetailProducts = $planDetail->planDetailProducts ?? collect([]);
                foreach ($planDetailProducts as $planDetailProduct) {
                    // Duplicate Plan Detail Distinct
                    $planDetailDistinct = $planDetailProduct->planDetailDistinct;
                    $newPlanDetailDistinct = $planDetailDistinct->replicate();
                    $newPlanDetailDistinct->plan_detail_id = $newPlanDetail->id;
                    $newPlanDetailDistinct->is_decision = PlanDetailDistinct::IS_DECISION_NOT_CHOOSE;
                    $newPlanDetailDistinct->save();

                    // Duplicate Plan Detail Product
                    $newPlanDetailProduct = $planDetailProduct->replicate();
                    $newPlanDetailProduct->plan_detail_id = $newPlanDetail->id;
                    $newPlanDetailProduct->plan_detail_distinct_id = $newPlanDetailDistinct->id;
                    $newPlanDetailProduct->save();

                    // Duplicate Plan Detail ProductCode
                    $planDetailProductCodes = $planDetailProduct->planDetailProductCodes ?? collect([]);
                    foreach ($planDetailProductCodes as $planDetailProductCode) {
                        $newPlanDetailProductCode = $planDetailProductCode->replicate();
                        $newPlanDetailProductCode->plan_detail_product_id = $newPlanDetailProduct->id;
                        $newPlanDetailProductCode->save();
                    }
                }
            }
        }

        return $newTrademarkPlan;
    }

    /**
     * Get Description Plan Detail
     *
     * @param mixed $id
     * @return mixed
     */
    public function getDescriptionPlanDetail($id)
    {
        $planDetailDescription = '';
        $trademarkPlan = $this->find($id, [
            'plans' => function ($subQuery) {
                return $subQuery->join('plan_details', 'plans.id', '=', 'plan_details.plan_id')->where('plan_details.is_choice', PlanDetail::IS_CHOICE);
            },
        ]);

        if ($trademarkPlan && count($trademarkPlan->plans)) {
            foreach ($trademarkPlan->plans as $value) {
                $planDetailDescription .= $value->plan_content . PHP_EOL . PHP_EOL;
            }
        }

        return $planDetailDescription;
    }
}
