<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FreeHistory extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trademark_id',
        'maching_result_id',
        'admin_id_create',
        'admin_id_confirm',
        'XML_delivery_date',
        'type',
        'user_response_deadline',
        'property',
        'status_name',
        'patent_response_deadline',
        'attachment',
        'amount_type',
        'amount',
        'internal_remark',
        'comment',
        'is_check_amount',
        'content_answer',
        'is_cancel',
        'flag_role',
        'is_confirm',
        'comment_free02',
        'is_completed',
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

    // Type
    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_4 = 4;

    // Property
    const PROPERTY_1 = 1;
    const PROPERTY_2 = 2;
    const PROPERTY_3 = 3;
    const PROPERTY_4 = 4;
    const PROPERTY_5 = 5;

    // Amount type
    const AMOUNT_TYPE_NO_FREE = 1;
    const AMOUNT_TYPE_CUSTOM = 2;
    const AMOUNT_TYPE_FREE = 3;

    // Flag Role
    const FLAG_ROLE_1 = 1;
    const FLAG_ROLE_2 = 2;

    const IS_NOT_CANCEL = 0;
    const IS_CANCEL = 1;

    const IS_ANSWER_FALSE = 0;
    const IS_ANSWER_TRUE = 1;

    /**
     * Admin Create
     *
     * @return BelongsTo
     */
    public function adminCreate(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id_create', 'id');
    }

    /**
     * Admin Confirm
     *
     * @return BelongsTo
     */
    public function adminConfirm(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id_confirm', 'id');
    }

    /**
     * Trademark
     *
     * @return BelongsTo
     */
    public function trademark(): BelongsTo
    {
        return $this->belongsTo(Trademark::class);
    }
}
