<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanDetailDistinct extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_detail_id',
        'm_distinction_id',
        'is_distinct_settlement',
        'is_leave_all',
        'm_distinction_id_edit',
        'is_distinct_settlement_edit',
        'is_leave_all_edit',
        'is_add',
        'is_decision',
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

    // Is Decision
    const IS_DECISION_NOT_CHOOSE = 0;
    const IS_DECISION_DRAFT = 1;
    const IS_DECISION_EDIT = 2;

    // Is Addition: 追加が必要な区分 of a203c, a203 0: false, 1: true
    const IS_NOT_ADD = 0;
    const IS_ADD = 1;

    //is distinct settlement
    const IS_DISTINCT_SETTLEMENT_FALSE = 0;
    const IS_DISTINCT_SETTLEMENT_TRUE = 1;

    //is leave all
    const IS_LEAVE_ALL_FALSE = 0;
    const IS_LEAVE_ALL_TRUE = 1;

    //is distinct settlement edit

    const IS_DISTINCT_SETTLEMENT_EDIT_FALSE = 0;
    const IS_DISTINCT_SETTLEMENT_EDIT = 1;

    //is leave all edit
    const IS_LEAVE_ALL_EDIT_FALSE = 0;
    const IS_LEAVE_ALL_EDIT_TRUE = 1;

    //is add
    const IS_ADD_FALSE = 0;
    const IS_ADD_TRUE = 1;

    //is decision
    const IS_DECISION_FALSE = 0;
    const IS_DECISION_TRUE = 1;

    /**
     * Distincts
     *
     * @return BelongsToMany
     */
    public function planDetails(): BelongsToMany
    {
        return $this->belongsToMany(MDistinction::class);
    }

    /*
     * Relation m_distinction
     *
     * @return BelongsTo
     */
    public function mDistinction(): BelongsTo
    {
        return $this->belongsTo(MDistinction::class, 'm_distinction_id', 'id');
    }

    /**
     * Distincts
     *
     * @return HasMany
     */
    public function planDetailProducts(): HasMany
    {
        return $this->hasMany(PlanDetailProduct::class);
    }
}
