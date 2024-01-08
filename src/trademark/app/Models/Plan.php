<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Plan extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'trademark_plan_id',
        'plan_no',
        'is_completed',
        'is_completed_edit',
        'description_documents_miss',
        'is_confirm',
        'flag_role',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'created_at',
        'update_at',
    ];

    public $selectable = [
        '*',
    ];

    const IS_COMPLETED_FALSE = 0;
    const IS_COMPLETED_TRUE = 1;

    /**
     * Trademark plan
     *
     * @return BelongsTo
     */
    public function tradeMarkPlan(): BelongsTo
    {
        return $this->belongsTo(TrademarkPlan::class, 'trademark_plane_id', 'id');
    }

    /**
     * Reasons
     *
     * @return BelongsToMany
     */
    public function reasons(): BelongsToMany
    {
        return $this->belongsToMany(Reason::class, 'plan_reasons', 'plan_id', 'reason_id');
    }

    /**
     * Plans Details
     *
     * @return HasMany
     */
    public function planDetails(): HasMany
    {
        return $this->hasMany(PlanDetail::class, 'plan_id', 'id');
    }

    /**
     * Plan Reasons
     *
     * @return HasMany
     */
    public function planReasons(): HasMany
    {
        return $this->hasMany(PlanReason::class, 'plan_id', 'id');
    }

    /**
     * Get reason name
     *
     * @return string
     */
    public function getReasonNames(): string
    {
        if (isset($this->planReasons)) {
            return $this->planReasons->pluck('reason.reason_name')->implode(',');
        }

        return '';
    }

    /**
     * Reasons
     *
     * @return BelongsToMany
     */
    public function reason():BelongsToMany
    {
        return $this->belongsToMany(Reason::class, 'plan_reasons', 'plan_id', 'reason_id')->where('deleted_at', null);
    }

    /**
     * Plans Details is choice
     *
     * @return HasMany
     */
    public function planDetailsIsChoice(): HasMany
    {
        return $this->hasMany(PlanDetail::class, 'plan_id', 'id')->where('is_choice', 1);
    }
    /**
     * Plans Details is choice
     *
     * @return HasMany
     */
    public function planDetailsNotChoice(): HasMany
    {
        return $this->hasMany(PlanDetail::class, 'plan_id', 'id')->where('is_choice', 0);
    }

    /**
     * Plan Doc Cmts
     *
     * @return HasMany
     */
    public function planDocCmts(): HasMany
    {
        return $this->hasMany(PlanDocCmt::class, 'plan_id', 'id');
    }

    /**
     * Required document plans
     *
     * @return HasMany
     */
    public function requiredDocumentPlans(): HasMany
    {
        return $this->hasMany(RequiredDocumentPlan::class);
    }

    /**
     * Required documents
     *
     * @return BelongsToMany
     */
    public function requiredDocuments(): belongsToMany
    {
        return $this->belongsToMany(RequiredDocument::class, 'required_document_plans', 'plan_id', 'required_document_id');
    }

    /**
     * Required documents
     *
     * @return HasMany
     */
    public function requiredDocumentsMiss(): HasMany
    {
        return $this->hasMany(RequiredDocumentMiss::class);
    }
}
