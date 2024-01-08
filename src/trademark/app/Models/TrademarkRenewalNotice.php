<?php

namespace App\Models;

class TrademarkRenewalNotice extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trademark_id',
        'notice_detail_id',
        'pattern',
        'type',
        'is_send_notice',
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

    // Pattern constant
    const PATTERN_NEVER_RENEWED = 1;
    const PATTERN_EARLY_EXTENSION = 2;
    const PATTERN_EXTENSION_AFTER_TERM = 3;

    // Type due date constant
    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_4 = 4;

    const IS_SEND_NOTICE_TRUE = 1;
}
