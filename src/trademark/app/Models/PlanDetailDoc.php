<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlanDetailDoc extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_detail_id',
        'attachment_user',
        'attachment_ams',
        'm_type_plan_doc_id',
        'doc_requirement_des',
        'm_type_plan_doc_id_edit',
        'doc_requirement_des_edit',
        'is_completed',
        'is_completed_edit',
    ];

    protected $table = 'plan_detail_docs';

    const IS_COMPLETED_FALSE = 0;
    const IS_COMPLETED_TRUE = 1;
    /**
     * Type Plan doc
     *
     * @return BelongsTo
     */
    public function MTypePlanDoc(): BelongsTo
    {
        return $this->belongsTo(MTypePlanDoc::class, 'm_type_plan_doc_id');
    }

    /**
     * Type Plan doc edit
     *
     * @return BelongsTo
     */
    public function MTypePlanDocEdit(): BelongsTo
    {
        return $this->belongsTo(MTypePlanDoc::class, 'm_type_plan_doc_id_edit');
    }

    /**
     * Plan detail doc with required document detail
     *
     * @return BelongsTo
     */
    public function requiredDocumentDetail(): HasOne
    {
        return $this->hasOne(RequiredDocumentDetail::class);
    }

    /**
     * Plan detail doc with required document detail
     *
     * @return HasMany
     */
    public function requiredDocumentDetails(): HasMany
    {
        return $this->hasMany(RequiredDocumentDetail::class);
    }
}
