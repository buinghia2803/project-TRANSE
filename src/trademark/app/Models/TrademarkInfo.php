<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrademarkInfo extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'target_id',
        'type_acc',
        'name',
        'm_nation_id',
        'm_prefecture_id',
        'address_second',
        'address_three',
        'type',
        'from_page',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_updated',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id'
    ];

    public $selectable = [
        '*'
    ];

    /**
     * Const
     */
    const TYPE_TRADEMARK = 1;
    const TYPE_ACC_COMPANY = 1;
    const TYPE_ACC_SINGLE = 2;

    /**
     * Type change trademark info
     */
    const TYPE_CHANGE_NAME = 1;
    const TYPE_CHANGE_ADDRESS = 2;
    const TYPE_CHANGE_NAME_AND_ADDRESS = 3;

    /**
     * Get type acc
     *
     * @return string|null
     */
    public function getTypeAcc(): ?string
    {
        switch ($this->type_acc) {
            case self::TYPE_ACC_COMPANY:
                $typeAcc = __('labels.common.info_type_acc_company');
                break;
            case self::TYPE_ACC_SINGLE:
                $typeAcc = __('labels.common.info_type_acc_single');
                break;
            default:
                $typeAcc = null;
        }
        return $typeAcc;
    }

    /**
     * M Nation
     *
     * @return BelongsTo
     */
    public function mNation(): BelongsTo
    {
        return $this->belongsTo(MNation::class, 'm_nation_id');
    }

    /**
     * M Prefecture
     *
     * @return BelongsTo
     */
    public function mPrefecture(): BelongsTo
    {
        return $this->belongsTo(MPrefecture::class, 'm_prefecture_id');
    }
}
