<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrademarkDocument extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notice_detail_btn_id',
        'trademark_id',
        'type',
        'name',
        'url',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
    ];

    public $selectable = [
        '*',
    ];

    // const
    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_4 = 4;
    const TYPE_5 = 5;
    const TYPE_6 = 6;
    const TYPE_7 = 7;
    const TYPE_8 = 8;
    const TYPE_9 = 9;
    const TYPE_10 = 10;

    /**
     * Notice Detail
     *
     * @return BelongsTo
     */
    public function noticeDetailBtn(): BelongsTo
    {
        return $this->belongsTo(NoticeDetailBtn::class);
    }
}
