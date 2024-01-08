<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PlanDetail;
use App\Repositories\PaymentRepository;
use App\Repositories\PlanDetailRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PlanDetailService extends BaseService
{
    protected $paymentRepository;

    /**
     * Initializing the instances and variables
     *
     * @param PlanDetailRepository $planDetailRepository
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(
        PlanDetailRepository $planDetailRepository,
        PaymentRepository $paymentRepository
    )
    {
        $this->repository = $planDetailRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Update Plan Details
     *
     * @param  mixed $params
     * @return array
     */
    public function updatePlanDetails(array $params): array
    {
        try {
            DB::beginTransaction();

            $planDetails = $this->findByCondition(['plan_ids' => $params['plan_ids']])->get();

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

            $redirectTo = null;
            $key = Str::random(11);

            // Update Payment
            $paymentDraft = $this->paymentRepository->findByCondition([
                'type' => TYPE_MATCHING_RESULT_SELECTION,
                'target_id' => $params['trademark_plan_id'] ?? null,
                'from_page_n' => U203,
            ])->get()->last();

            if (!empty($paymentDraft)) {
                $payerInfo = $paymentDraft->payerInfo;

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
                    'type' => Payment::TYPE_SELECT_POLICY,
                ];

                $paymentDraft->update($dataPayment);
            }

            $sessionData = Session::get($params['s']);
            $params = array_merge($sessionData, $params);

            if (isset($params['total']) && $params['total'] == 0) {
                $params['has_fee'] = 0;
            }

            switch ($params['submit_type']) {
                case U203:
                    $redirectTo = U203;
                    break;
                case U203N:
                    $redirectTo = U203N;
                    break;
                case U203B02:
                    Session::put($key, $params);
                    $redirectTo = U203B02;
                    break;
            }

            DB::commit();

            return [
                'redirect_to' => $redirectTo,
                'key_session' => $key,
            ];
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update Decision Data
     *
     * @param Model $planDetail
     * @return Model
     */
    public function updateDecision(Model $planDetail): Model
    {
        $dataUpdate = [];

        if ($planDetail->is_decision == PlanDetail::IS_DECISION_EDIT) {
            // type_plan_id_edit
            $typePlanIdEdit = $planDetail->type_plan_id_edit ?? null;
            if (!empty($typePlanIdEdit)) {
                $dataUpdate['type_plan_id'] = $typePlanIdEdit;
            }

            // plan_description_edit
            $planDescriptionEdit = $planDetail->plan_description_edit ?? null;
            if (!empty($planDescriptionEdit)) {
                $dataUpdate['plan_description'] = $planDescriptionEdit;
            }

            // possibility_resolution_edit
            $possibilityResolutionEdit = $planDetail->possibility_resolution_edit ?? null;
            if (!empty($possibilityResolutionEdit)) {
                $dataUpdate['possibility_resolution'] = $possibilityResolutionEdit;
            }
        }

        $dataUpdate['type_plan_id_edit'] = null;
        $dataUpdate['plan_description_edit'] = null;
        $dataUpdate['possibility_resolution_edit'] = null;
        $dataUpdate['is_confirm'] = false;
        $dataUpdate['is_decision'] = false;

        $planDetail->update($dataUpdate);

        return $planDetail;
    }
}
