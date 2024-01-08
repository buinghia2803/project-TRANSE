<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanComment extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'trademark_id',
        'trademark_plan_id',
        'target_id',
        'content',
        'type',
        'type_comment_step',
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

    // Type
    const TYPE_1 = 1;
    const TYPE_2 = 2;

    // Step
    const STEP_1 = 1;
    const STEP_2 = 2;
    const STEP_3 = 3;
    const STEP_4 = 4;
    const STEP_5 = 5;
    const STEP_6 = 6;
    const STEP_7 = 7;
    const STEP_8 = 8;

    /**
     *  Const type
     */
    const TYPE_INTERNAL = 1;
    const TYPE_AMS = 2;

    /**
     * Relation trademark plan
     *
     * @return BelongsTo
     */
    public function trademarkPlan(): BelongsTo
    {
        return $this->belongsTo(TrademarkPlan::class);
    }
}
