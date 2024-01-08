<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanDocCmt extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_id',
        'from_send_doc',
        'content',
        'date_send',
        'type',
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
        'delete_at',
    ];

    public $selectable = [
        '*',
    ];

    const TYPE_U204 = 1;
    const TYPE_U204N = 2;
}
