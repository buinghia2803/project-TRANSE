<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanDetail extends BaseModel
{

    // possibility resolution value
    const  VALUE_VERY_EXPENSIVE = 1;
    const  VALUE_HIGH = 2;
    const  VALUE_LOW = 3;
    const  VALUE_EXTREMELY_DIFFICULT = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'plan_id',
        'type_plan_id',
        'plan_description',
        'plan_content',
        'possibility_resolution',
        'type_plan_id_edit',
        'plan_description_edit',
        'plan_content_edit',
        'possibility_resolution_edit',
        'is_decision',
        'is_confirm',
        'is_choice',
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

    // Choice plan detail
    const IS_NOT_CHOICE = 0;
    const IS_CHOICE = 1;

    // Possibility Resolution
    const RESOLUTION_1 = 1;
    const RESOLUTION_2 = 2;
    const RESOLUTION_3 = 3;
    const RESOLUTION_4 = 4;

    //Is confirm
    const IS_CONFIRM_FALSE = 0;
    const IS_CONFIRM_TRUE = 1;

    //Is decision
    const IS_DECISION_NOT_CHOSSE = 0;
    const IS_DECISION_DRAFT = 1;
    const IS_DECISION_EDIT = 2;

    //Is choice pass
    const IS_CHOICE_PAST_FALSE = 0;
    const IS_CHOICE_PAST_TRUE = 1;
    /**
     * M Products
     *
     * @return BelongsToMany
     */
    public function mProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            MProduct::class,
            'plan_detail_products',
            'plan_detail_id',
            'm_product_id'
        );
    }

    /**
     *  M Type Plan
     *
     * @return BelongsTo
     */
    public function mTypePlan(): BelongsTo
    {
        return $this->belongsTo(MTypePlan::class, 'type_plan_id', 'id');
    }

    /**
     *  M Type Plan edit
     *
     * @return BelongsTo
     */
    public function mTypePlanEdit(): BelongsTo
    {
        return $this->belongsTo(MTypePlan::class, 'type_plan_id_edit', 'id');
    }

    /**
     * List possibility resolution
     *
     * @return string[]
     */
    public static function listPossibilityResolution()
    {
        return [
            self::RESOLUTION_1 => '◎',
            self::RESOLUTION_2 => '○',
            self::RESOLUTION_3 => '△',
            self::RESOLUTION_4 => '×',
        ];
    }

    /**
     * Plan Detail Distincts
     *
     * @return HasMany
     */
    public function planDetailDistincts(): HasMany
    {
        return $this->hasMany(PlanDetailDistinct::class, 'plan_detail_id', 'id');
    }

    /**
     * Plan Detail Products
     *
     * @return HasMany
     */
    public function planDetailProducts(): HasMany
    {
        return $this->hasMany(PlanDetailProduct::class, 'plan_detail_id', 'id');
    }

    /**
     * Plan
     *
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Plan Detail Products
     *
     * @return HasMany
     */
    public function planDetailDocs(): HasMany
    {
        return $this->hasMany(PlanDetailDoc::class, 'plan_detail_id', 'id');
    }


    /**
     * Get Text Revolution
     *
     * @return string
     */
    public function getTextRevolution(): string
    {
        $str = '';
        switch ($this->possibility_resolution) {
            case self::RESOLUTION_1:
                $str = '◎';
                break;
            case self::RESOLUTION_2:
                $str = '○';
                break;
            case self::RESOLUTION_3:
                $str = '△';
                break;
            case self::RESOLUTION_4:
                $str = '×';
                break;
        }

        return $str;
    }

    /**
     * Get Text Revolution
     *
     * @return string
     */
    public function getTextRevolutionV2(): string
    {
        $str = '';
        switch ($this->possibility_resolution) {
            case self::RESOLUTION_1:
                $str = 'かなり高い';
                break;
            case self::RESOLUTION_2:
                $str = '高い';
                break;
            case self::RESOLUTION_3:
                $str = '低い';
                break;
            case self::RESOLUTION_4:
                $str = '極めて困難';
                break;
        }

        return $str;
    }

    /**
     * Get Text Revolution
     *
     * @return string
     */
    public function getPossibilityResolution($max, $checkRoleAdd): string
    {
        $str = '';
        if ($checkRoleAdd != '') {
            $str = $checkRoleAdd;
        } else {
            switch ($max) {
                case self::RESOLUTION_1:
                    $str = '◎';
                    break;
                case self::RESOLUTION_2:
                    $str = '○';
                    break;
                case self::RESOLUTION_3:
                    $str = '△';
                    break;
                case self::RESOLUTION_4:
                    $str = '×';
                    break;
            }
        }

        return $str;
    }

    /**
     * Get Text Revolution
     *
     * @return string
     */
    public function getStrRevolution(): string
    {
        $str = '';
        switch ($this->possibility_resolution) {
            case self::RESOLUTION_1:
                $str = __('labels.refusal_plans.u203.content_26');
                break;
            case self::RESOLUTION_2:
                $str = __('labels.refusal_plans.u203.content_27');
                break;
            case self::RESOLUTION_3:
                $str = __('labels.refusal_plans.u203.content_28');
                break;
            case self::RESOLUTION_4:
                $str = __('labels.refusal_plans.u203.content_29');
                break;
        }

        return $str;
    }

    /**
     * Define revolution types
     *
     * @return array
     */
    public static function getRevolutionTypes()
    {
        return [
            1 => __('labels.refusal_plans.u203.content_26') . '◎',
            2 => __('labels.refusal_plans.u203.content_27') . '○',
            3 => __('labels.refusal_plans.u203.content_28') . '△',
            4 => __('labels.refusal_plans.u203.content_29') . '×',
        ];
    }

    /**
     * Get distinction name.
     *
     * @return string
     */
    public function getMDistinctionName(): string
    {
        $data = $this->planDetailDistincts->where('is_add', IS_ADD_TRUE);
        if ($data->count()) {
            return $data->unique('m_distinction_id')->pluck('mDistinction.name')->implode(',');
        }

        return '';
    }

    /**
     * Get type plan
     *
     * @return string
     */
    public function getTypePlanName(): string
    {
        if (!in_array($this->type_plan_id, [2, 4, 5, 7, 8])) {
            return __('labels.a203c_rui.unnecessary');
        }

        //if route a203s || a203sashi
        if (\Route::is([
            'admin.refusal.response-plan.supervisor', // a203s
            'admin.refusal.response-plan.supervisor-reject', // a203sashi
        ])) {
            $html = '';
            foreach ($this->mTypePlan->mTypePlanDocs as $mTypePlanDoc) {
                $html .= "<span class='white-space-pre-line'>$mTypePlanDoc->name</span><br>";
            }

            return $html;
        }

        return __('labels.a203c_rui.requirement');
    }

    /**
     * Is Type Plan Required
     *
     * @return boolean
     */
    public function isRequiredTypePlan(): bool
    {
        if (!in_array($this->type_plan_id, [2, 4, 5, 7, 8])) {
            return false;
        }

        return true;
    }

    /**
     * Get Distincts Is Add
     *
     * @return BelongsToMany
     */
    public function distinctsIsAdd(): BelongsToMany
    {
        return $this->belongsToMany(MDistinction::class, 'plan_detail_distincts', 'plan_detail_id', 'm_distinction_id')
           ->where('plan_detail_distincts.deleted_at', null)->where('is_add', 1);
    }


    /**
     * Get Distincts is Distinct Settement
     *
     * @return BelongsToMany
     */
    public function distinctsIsDistinctSettement(): BelongsToMany
    {
        return $this->belongsToMany(MDistinction::class, 'plan_detail_distincts', 'plan_detail_id', 'm_distinction_id')
            ->where('plan_detail_distincts.deleted_at')->where('is_distinct_settlement', 1)->where('is_add', 1);
    }


    /**
     * Get Distincts is Distinct Settement edit
     *
     * @return BelongsToMany
     */
    public function isDistinctSettmentEdit()
    {
        return $this->belongsToMany(MDistinction::class, 'plan_detail_distincts', 'plan_detail_id', 'm_distinction_id')
            ->where('plan_detail_distincts.deleted_at')->where('is_distinct_settlement_edit', 1)->where('is_add', 1);
    }
}
