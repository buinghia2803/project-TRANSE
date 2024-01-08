<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlanCorrespondence extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comparison_trademark_result_id',
        'type',
        'is_ext_period',
        'is_ext_period_2',
        'register_date',
        'updated_at',
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

    // const
    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;

    const TYPE_SELECT = 2;

    /**
     * Reason Question
     *
     * @return HasOne
     */
    public function reasonQuestion(): HasOne
    {
        return $this->HasOne(ReasonQuestion::class, 'plan_correspondence_id', 'id');
    }

    /**
     * Reason Question
     *
     * @return HasOne
     */
    public function reasonQuestions(): HasMany
    {
        return $this->hasMany(ReasonQuestion::class, 'plan_correspondence_id', 'id');
    }

    /**
     * Reason Comment
     *
     * @return HasMany
     */
    public function reasonComment(): HasOne
    {
        return $this->HasOne(ReasonComment::class, 'plan_correspondence_id', 'id');
    }

    const TYPE_SIMPLE = 1;
    const TYPE_SELECTION = 2;
    const TYPE_PACK_C = 3;

    /**
     * Reason Comments
     *
     * @return HasMany
     */
    public function reasonComments(): HasMany
    {
        return $this->hasMany(ReasonComment::class);
    }

    /**
     * Plan Correspondence Prods
     *
     * @return HasMany
     */
    public function planCorrespondenceProds(): HasMany
    {
        return $this->hasMany(PlanCorrespondenceProd::class);
    }

    /**
     * Reason No
     *
     * @return HasOne
     */
    public function reasonNo(): HasOne
    {
        return $this->hasOne(ReasonNo::class);
    }

    /**
     * Reason Nos
     *
     * @return HasMany
     */
    public function reasonNos(): HasMany
    {
        return $this->hasMany(ReasonNo::class, 'plan_correspondence_id', 'id');
    }

    /**
     * Trademark Plans
     *
     * @return HasMany
     */
    public function trademarkPlans(): HasMany
    {
        return $this->hasMany(TrademarkPlan::class);
    }

    /**
     * Comparison Trademark Result
     *
     * @return BelongsTo
     */
    public function comparisonTrademarkResult(): BelongsTo
    {
        return $this->belongsTo(ComparisonTrademarkResult::class, 'comparison_trademark_result_id', 'id');
    }
}
