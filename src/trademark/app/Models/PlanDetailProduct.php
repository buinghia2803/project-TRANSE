<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanDetailProduct extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_detail_id',
        'm_product_id',
        'plan_correspondence_product_id',
        'plan_detail_distinct_id',
        'm_distinction_id_edit',
        'm_distinction_id_decision',
        'm_product_id',
        'product_name_edit',
        'product_name_decision',
        'leave_status',
        'leave_status_other',
        'leave_status_edit',
        'leave_status_other_edit',
        'leave_status_decision',
        'leave_status_other_decision',
        'code_name_edit',
        'code_name_decision',
        'role_add',
        'is_deleted',
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

    /**
     * Const leave status
     */
    const LEAVE_STATUS_TYPES_1 = 1; //残す
    const LEAVE_STATUS_TYPES_2 = 2; // 削除
    const LEAVE_STATUS_TYPES_3 = 3; //※
    const LEAVE_STATUS_TYPES_4 = 4; //-
    const LEAVE_STATUS_TYPES_5 = 5; //NG
    const LEAVE_STATUS_TYPES_6 = 6; //追加
    const LEAVE_STATUS_TYPES_7 = 7; //追加せず
    const LEAVE_STATUS_TYPES_8 = 8; //※（追加）
    const LEAVE_STATUS_TYPES_9 = 9; //※（追加せず）

    /**
     * Role add const
     */
    const ROLE_ADD_OTHER = 1;
    const ROLE_ADD_PERSON_CHARGE = 2;
    const ROLE_ADD_RESPONSIBLE_PERSON = 3;

    /**
     *  Const is_choice
     */
    const IS_NOT_CHOICE = 0;
    const IS_CHOICE = 1;

    // Roll Add
    const ROLL_ADD_USER = 1;
    const ROLL_ADD_MANAGER = 2;
    const ROLL_ADD_SUPERVISOR = 3;

    // Leave Status
    // 1: 残す | 2: 削除 | 3: ※ | 4: - | 5: NG | 6: 追加 | 7: 追加せず | 8: ※（追加） | 9: ※（追加せず）
    const LEAVE_STATUS_1 = 1;
    const LEAVE_STATUS_2 = 2;
    const LEAVE_STATUS_3 = 3;
    const LEAVE_STATUS_4 = 4;
    const LEAVE_STATUS_5 = 5;
    const LEAVE_STATUS_6 = 6;
    const LEAVE_STATUS_7 = 7;
    const LEAVE_STATUS_8 = 8;
    const LEAVE_STATUS_9 = 9;

    /**
     * M product
     *
     * @return BelongsTo
     */
    public function mProduct(): BelongsTo
    {
        return $this->belongsTo(MProduct::class, 'm_product_id');
    }

    /**
     * Get Leave Status Type Text
     *
     * @return string
     */
    public function getLeaveStatusTypeText()
    {
        return self::getListLeaveStatus()[$this->leave_status];
    }

    /**
     * Get List Leave Status
     *
     * @return string[]
     */
    public static function getListLeaveStatus()
    {
        return [
            self::LEAVE_STATUS_TYPES_1 => '残す',
            self::LEAVE_STATUS_TYPES_2 => '削除',
            self::LEAVE_STATUS_TYPES_3 => '※',
            self::LEAVE_STATUS_TYPES_4 => '-',
            self::LEAVE_STATUS_TYPES_5 => 'NG',
            self::LEAVE_STATUS_TYPES_6 => '追加',
            self::LEAVE_STATUS_TYPES_7 => '追加せず',
            self::LEAVE_STATUS_TYPES_8 => '※（追加）',
            self::LEAVE_STATUS_TYPES_9 => '※（追加せず）',
        ];
    }

    /**
     * Get Background By LeaveStatus
     *
     * @param integer $leaveStatus
     * @return string
     */
    public static function getBackgroundByLeaveStatus(int $leaveStatus)
    {
        if ($leaveStatus == self::LEAVE_STATUS_TYPES_2) {
            $background = '#f1b5d0';
        } elseif ($leaveStatus == self::LEAVE_STATUS_TYPES_3) {
            $background = '#E4EABF';
        } else {
            $background = '';
        }

        return $background;
    }

    /**
     * Option Default a203c
     *
     * @return array
     */
    public function optionProduct(): array
    {
        return [
            self::LEAVE_STATUS_1 => '残す',
            self::LEAVE_STATUS_2 => '削除',
            self::LEAVE_STATUS_3 => '※',
        ];
    }

    /**
     * Option Required a203c
     *
     * @return array
     */
    public function optionRequired(): array
    {
        return [
            self::LEAVE_STATUS_6 => '追加',
            self::LEAVE_STATUS_7 => '追加せず',
            self::LEAVE_STATUS_3 => '※',
        ];
    }

    /**
     * Option Default a203c
     *
     * @return array
     */
    public function optionDefault(): array
    {
        return [
            self::LEAVE_STATUS_4 => '-',
            self::LEAVE_STATUS_5 => 'NG',
            self::LEAVE_STATUS_3 => '※',
        ];
    }

    /**
     * Plan Detail Distinct
     *
     * @return BelongsTo
     */
    public function planDetailDistinct(): BelongsTo
    {
        return $this->belongsTo(PlanDetailDistinct::class);
    }

    /**
     * Products
     *
     * @return HasMany
     */
    public function mProducts(): HasMany
    {
        return $this->hasMany(MProduct::class, 'id', 'm_product_id');
    }

    /**
     * Relation of plan detail product with plan detail product code
     * id - plan_detail_product_id
     *
     * @return HasMany
     */
    public function planDetailProductCodes(): HasMany
    {
        return $this->hasMany(PlanDetailProductCode::class);
    }

    /**
     * Plan_Detail
     *
     * @return BelongsTo
     */
    public function planDetail(): BelongsTo
    {
        return $this->belongsTo(PlanDetail::class, 'plan_detail_id', 'id');
    }


    /**
     * Is Role Add User
     *
     * @return boolean
     */
    public function isRoleAddUser(): bool
    {
        return $this->role_add == self::ROLL_ADD_USER;
    }

    /**
     * Is Role Add Supervisor
     *
     * @return boolean
     */
    public function isRoleAddSupervisor(): bool
    {
        return $this->role_add == self::ROLL_ADD_SUPERVISOR;
    }

    /**
     * Get class color by leave status.
     *
     * @return string
     */
    public function getClassColor(): string
    {
        if ($this->leave_status == self::LEAVE_STATUS_TYPES_2) {
            return 'bg_pink';
        } elseif ($this->leave_status == self::LEAVE_STATUS_TYPES_3) {
            return 'bg_green';
        }

        return '';
    }

    /**
     * Get Leave Status Prod
     *
     * @return string
     */
    public function getLeaveStatusProd(): string
    {
        $str = '';
        if ($this->role_add == ROLE_OFFICE_MANAGER && $this->leave_status == self::LEAVE_STATUS_2) {
            $str = DELETE;
        } elseif ($this->role_add == ROLE_OFFICE_MANAGER && $this->leave_status != self::LEAVE_STATUS_2) {
            $str = TEXT_REVOLUTION;
        } elseif (in_array($this->role_add, [ROLE_MANAGER, ROLE_SUPERVISOR]) && in_array($this->leave_status, [7, 9])) {
            $str = $this->leave_status;
        }

        return $str;
    }

    /**
     * Get leave status and resolution.
     *
     * @return string
     */
    public function getLeaveStsResolutionEvaluation()
    {
        if ($this->role_add == ROLE_OFFICE_MANAGER) {
            // Case 1: plan_detail_products.role_add = 1 và plan_detail_products.leave_status = 2
            if ($this->leave_status == LEAVE_STATUS_2) {
                return LEAVE_STATUS_TYPES[LEAVE_STATUS_2];
            }
            // Case 2: plan_detail_products.role_add = 1 thì  và plan_detail_products.leave_status != 2
            if ($this->leave_status != LEAVE_STATUS_2) {
                return $this->planDetail->getTextRevolution();
            }
        } else {
            return '－';
        }
    }

    /**
     * Get Leave Status Prod
     *
     * @return string
     */
    public function getLeaveStatusProdPossility(): string
    {
        if ($this->role_add != ROLE_OFFICE_MANAGER) {
            $str = '－';
        } elseif ($this->role_add == ROLE_OFFICE_MANAGER) {
            $str = '';
        }

        return $str;
    }

    /**
     * Get leave status text
     *
     * @return string
     */
    public function getLeaveStatusText($planDetails)
    {
        $this->leave_status = $leaveStatus ?? $this->leave_status;
        if ($this->is_choice) {
            if ($this->leave_status != null) {
                return LEAVE_STATUS_TYPES[$this->leave_status];
            } elseif (($this->leave_status == null && $this->leave_status_other == '[]') || ($this->leave_status == null && $this->leave_status_other == null)) {
                return __('labels.a203c_rui.leave_all');
            } elseif ($this->leave_status == null && $this->leave_status_other != '[]') {
                $arr = [];
                $leaveStatusOthers = json_decode($this->leave_status_other);
                if (is_array($leaveStatusOthers)) {
                    foreach ($leaveStatusOthers as $key => $item) {
                        $firstPlanDetailData = collect($planDetails)->where('id', $item->plan_product_detail_id)->first();
                        $firstPlanDetailData = $firstPlanDetailData['plan_detail_product'];

                        $leaveStatusText = '';
                        switch ($item->value) {
                            case LEAVE_STATUS_4:
                                if ($firstPlanDetailData['leave_status'] == LEAVE_STATUS_6) {
                                    $leaveStatusText = LEAVE_STATUS_TYPES[LEAVE_STATUS_6];
                                } else if ($firstPlanDetailData['leave_status'] == LEAVE_STATUS_7) {
                                    $leaveStatusText = LEAVE_STATUS_TYPES[LEAVE_STATUS_7];
                                } else if ($firstPlanDetailData['leave_status'] == LEAVE_STATUS_3) {
                                    $leaveStatusText = LEAVE_STATUS_TYPES[LEAVE_STATUS_3];
                                }
                                break;
                            case LEAVE_STATUS_3:
                                if ($firstPlanDetailData['leave_status'] == LEAVE_STATUS_3) {
                                    $leaveStatusText = LEAVE_STATUS_TYPES[LEAVE_STATUS_6] . LEAVE_STATUS_TYPES[LEAVE_STATUS_3];
                                } else {
                                    $leaveStatusText = LEAVE_STATUS_TYPES[$firstPlanDetailData['leave_status']] . LEAVE_STATUS_TYPES[LEAVE_STATUS_3];
                                }
                                break;
                            default:
                                $leaveStatusText = LEAVE_STATUS_TYPES[$item->value];
                                break;
                        }

                        $arr[] = $leaveStatusText;
                    }
                }

                return implode('、', $arr);
            }

            return __('labels.a203c_rui.leave_all');
        } else {
            return '-';
        }
    }
}
