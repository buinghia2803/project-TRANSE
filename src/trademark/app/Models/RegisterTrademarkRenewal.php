<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegisterTrademarkRenewal extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'register_trademark_id',
        'type',
        'trademark_id',
        'registration_period',
        'is_send_mail',
        'start_date',
        'end_date',
        'status',
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
    // const TYPE
    const TYPE_EXTENSION_PERIOD_BEFORE_DEADLINE = 1;
    const TYPE_EXTENSION_OUTSIDE_PERIOD = 2;
    // const status
    const SAVE_DRAFT = 1;
    const ADMIN_CONFIRM = 2;
    const COMPLEDTED = 3;
}
