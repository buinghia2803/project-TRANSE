<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class TrademarkPlan extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_correspondence_id',
        'is_cancel',
        'is_reject',
        'is_register',
        'reason_cancel',
        'response_deadline',
        'sending_docs_deadline',
        'flag_role',
        'is_confirm',
        'is_redirect',
        'is_confirm_docs',
        'is_edit_plan',
        'is_decision',
        'is_confirm_docs',
        'from_send_doc',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'created_at',
        'updated_at',
    ];

    public $selectable = [
        '*',
    ];

    // Flag Role
    const FLAG_ROLE_1 = 1;
    const FLAG_ROLE_2 = 2;

    // Is Decision
    const DECISION_NOT_CH0OSE = 0;
    const DECISION_DRAFT = 1;
    const DECISION_EDIT = 2;

    /**
     * Const is_reject
     */
    const IS_REJECT_FALSE = 0;
    const IS_REJECT_TRUE = 1;

    /**
     * Is confirm
     */
    const IS_CONFIRM_FALSE = 0;
    const IS_CONFIRM_TRUE = 1;

    /**
     * Is confirm docs
     */
    const IS_CONFIRM_DOCS_FALSE = 0;
    const IS_CONFIRM_DOCS_TRUE = 1;

    /**
     * Parse Response Deadline
     *
     * @return string
     */
    public function parseResponseDeadline(): string
    {
        return Carbon::parse($this->response_deadline)->format('Y年m月d日');
    }

    /**
     * Plans
     *
     * @return HasMany
     */
    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class, 'trademark_plan_id', 'id');
    }

    /**
     * Plan comments
     *
     * @return HasMany
     */
    public function planComments(): HasMany
    {
        return $this->hasMany(PlanComment::class, 'trademark_plan_id', 'id');
    }

    /**
     * Doc Submissions.
     *
     * @return HasMany
     */
    public function docSubmissions(): HasMany
    {
        return $this->hasMany(DocSubmission::class);
    }

    /**
     * Relation of trademark plan - Plan correspondence.
     *
     * @return BelongsTo
     */
    public function planCorrespondence(): BelongsTo
    {
        return $this->belongsTo(PlanCorrespondence::class);
    }


    /**
     * Get all data Plan
     *
     * @return Collection
     */
    public function getPlans(): Collection
    {
        $plans = $this->plans;

        $plans->map(function ($item) {
            $planDetails = $item->planDetails;

            $planDetails->map(function ($planDetail) {
                $planDetail->text_revolution = $planDetail->getTextRevolution();
                $planDetail->text_revolution_v2 = $planDetail->getTextRevolutionV2();

                $planDetailProducts = $planDetail->planDetailProducts ?? collect();
                $distinct = collect();
                foreach ($planDetailProducts as $planDetailProduct) {
                    $planDetailDistinct = $planDetailProduct->planDetailDistinct;
                    $distinct->push($planDetailDistinct);
                }
                $planDetail->plan_detail_distincts = $distinct;

                $distinctIsAdd = $distinct->where('is_add', true)->unique('mDistinction.id');
                $planDetail->distinct_is_add = $distinctIsAdd;

                $distinctIsAddText = $distinctIsAdd->pluck('mDistinction.name')->toArray();
                $planDetail->distinct_is_add_text = implode(',', $distinctIsAddText);

                return $planDetail;
            });

            // Get Plan Reason
            $planReasons = $item->planReasons ?? collect();
            $reasons = collect();
            foreach ($planReasons as $planReason) {
                $reason = $planReason->reason;
                $reasons->push($reason);
            }
            $item->reasons = $reasons;

            $reasonNameArray = $reasons->pluck('reason_name')->toArray();
            $reasonStr = count($reasonNameArray) > 0 ? implode(', ', $reasonNameArray) : '';
            $item->reason_name = $reasonStr;

            return $item;
        });

        return $plans;
    }

    /**
     * Get all product of Trademark Plan
     *
     * @return Collection
     */
    public function getProducts(): Collection
    {
        $plans = $this->plans;

        // Get Rank
        $planCorrespondenceProds = PlanCorrespondenceProd::where([
            'plan_correspondence_id' => $this->plan_correspondence_id,
        ])->with(['appTrademarkProd.mProduct', 'reasonRefNumProd'])->get()->map(function ($item) {
            $item->m_product_id = $item->appTrademarkProd->m_product_id ?? null;
            $item->rank = $item->reasonRefNumProd->rank ?? null;

            return $item;
        });

        // Get Product
        $detailProducts = collect();

        $plans->map(function ($item) use (&$detailProducts) {
            $planDetails = $item->planDetails;

            $planDetails->map(function ($planDetail) use (&$detailProducts) {
                $planDetailProducts = $planDetail->planDetailProducts ?? collect();

                foreach ($planDetailProducts as $planDetailProduct) {
                    $planDetailProduct->plan_detail = $planDetail;
                    $detailProducts->push($planDetailProduct);
                }
            });
        });

        $products = [];

        foreach ($detailProducts as $key => $item) {
            $planDetailDistinct = $item->planDetailDistinct;

            $planDetailProductCodes = $item->planDetailProductCodes;
            $mCode = collect();
            foreach ($planDetailProductCodes as $planDetailProductCode) {
                $mCode->push($planDetailProductCode->mCode);
            }

            $products[$item->m_product_id]['plan_detail_product'] = $item;
            $products[$item->m_product_id]['product'] = $item->mProduct;
            $products[$item->m_product_id]['plan_detail_distinction'] = $planDetailDistinct;
            $products[$item->m_product_id]['distinction'] = $planDetailDistinct->mDistinction ?? null;
            $products[$item->m_product_id]['codes'] = $mCode;
            $products[$item->m_product_id]['reasonRefNumProd'] = $planCorrespondenceProds->where('m_product_id', $item->m_product_id)->first();
        }

        foreach ($detailProducts as $key => $item) {
            $item->plan_detail->distinction_prod = $item->planDetailDistinct;
            $item->plan_detail->plan_detail_product = $item;
            $products[$item->m_product_id]['plan_details'][] = $item->plan_detail->toArray();
        }

        return collect($products);
    }

    /**
     * Get all product with m_product_id of Trademark Plan
     *
     * @return Collection
     */
    public function getProductsV2(): Collection
    {
        $plans = $this->plans;

        // Get Product
        $detailProducts = collect();

        $plans->map(function ($item) use (&$detailProducts) {
            $planDetails = $item->planDetails->where('is_choice', 1);
            $planDetails->map(function ($planDetail) use (&$detailProducts) {
                $planDetailProducts = $planDetail->planDetailProducts->where('is_choice', 1) ?? collect();

                foreach ($planDetailProducts as $planDetailProduct) {
                    $planDetailProduct->plan_detail = $planDetail;
                    $detailProducts->push($planDetailProduct);
                }
            });
        });

        $products = [];
        foreach ($detailProducts as $key => $item) {
            $mProducts = $item->mProduct;

            $products[$item->m_product_id]['product'] = $item->mProduct;
            $products[$item->m_product_id]['distinction'] = $mProducts->mDistinction ?? null;
        }

        return collect($products);
    }

    /**
     * Get all product rank A of Trademark Plan
     *
     * @return Collection
     */
    public function getProductsRankA()
    {
        // Get product rank A
        $planCorrespondenceProds = PlanCorrespondenceProd::where([
            'plan_correspondence_id' => $this->plan_correspondence_id,
        ])
        ->with(['appTrademarkProd.mProduct.mDistinction', 'reasonRefNumProd'])
        ->get()
        ->map(function ($item) {
            $item->mProduct = $item->appTrademarkProd->mProduct ?? null;
            $item->mDistinction = $item->mProduct->mDistinction ?? null;
            $item->rank = $item->reasonRefNumProd->rank ?? null;

            return $item;
        });

        return $planCorrespondenceProds->where('rank', 'A');
    }


    /**
     * Get round
     *
     * @return string
     */
    public function getRound(): string
    {
        $fromSendDoc = $this->from_send_doc ?? '';
        $explodeFromSendDoc = explode('_', $fromSendDoc);

        return $explodeFromSendDoc[1] ?? 1;
    }

    /**
     * Duplicate A203 Group
     *
     * @return void
     */
    public function duplicateA203Group()
    {
        $trademarkPlan = $this->load([
            'planComments',
            'plans.planReasons',
            'plans.planDetails.planDetailProducts.planDetailDistinct',
            'plans.planDetails.planDetailProducts.planDetailProductCodes',
            'plans.planDetails.planDetailDocs',
        ]);

        // Duplicate Trademark Plan
        $newTrademarkPlan = $trademarkPlan->replicate();
        $newTrademarkPlan->is_cancel = false;
        $newTrademarkPlan->is_reject = false;
        $newTrademarkPlan->is_register = false;
        $newTrademarkPlan->is_confirm = false;
        $newTrademarkPlan->is_redirect = false;
        $newTrademarkPlan->flag_role = TrademarkPlan::FLAG_ROLE_1;
        $newTrademarkPlan->is_edit_plan = false;
        $newTrademarkPlan->is_decision = false;
        $newTrademarkPlan->save();

        // Duplicate Plans
        $plans = $trademarkPlan->plans ?? collect([]);
        $newPlanDetailProductArray = [];
        $mappingPlanDetailProductID = [];
        foreach ($plans as $plan) {
            $newPlan = $plan->replicate();
            $newPlan->trademark_plan_id = $newTrademarkPlan->id;
            $newPlan->is_confirm = false;
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
                $newPlanDetail->is_confirm = false;
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
                    $newPlanDetailProduct->product_name_edit = null;
                    $newPlanDetailProduct->leave_status_edit = null;
                    $newPlanDetailProduct->leave_status_other_edit = null;
                    $newPlanDetailProduct->product_name_decision = null;
                    $newPlanDetailProduct->leave_status_decision = null;
                    $newPlanDetailProduct->leave_status_other_decision = null;
                    $newPlanDetailProduct->save();

                    $mappingPlanDetailProductID[] = [
                        'old_plan_detail_id' => $planDetailProduct->plan_detail_id,
                        'new_plan_detail_id' => $newPlanDetailProduct->plan_detail_id,
                    ];
                    $newPlanDetailProductArray[] = $newPlanDetailProduct;

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

        // Update leave_status_other of new PlanDetailProduct with role_add != 1
        $mappingPlanDetailProductData = collect($mappingPlanDetailProductID)->unique()->toArray();
        foreach ($newPlanDetailProductArray as $newPlanDetailProductValue) {
            if ($newPlanDetailProductValue->role_add != PlanDetailProduct::ROLL_ADD_USER && $newPlanDetailProductValue->leave_status == null) {
                $newLeaveStatusOther = $newPlanDetailProductValue->leave_status_other;

                foreach ($mappingPlanDetailProductData as $mappingPlanDetailProductValue) {
                    $oldPlanDetailID = $mappingPlanDetailProductValue['old_plan_detail_id'] ?? null;
                    $newPlanDetailID = $mappingPlanDetailProductValue['new_plan_detail_id'] ?? null;
                    $newLeaveStatusOther = str_replace($oldPlanDetailID, $newPlanDetailID, $newLeaveStatusOther);
                }

                $newPlanDetailProductValue->update([
                    'leave_status_other' => $newLeaveStatusOther,
                ]);
            }
        }
    }
}
