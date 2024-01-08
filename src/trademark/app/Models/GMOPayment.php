<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class GMOPayment extends Model
{
    use SoftDeletes;

    const UPDATED_AT = null;
    protected $table = 'gmo_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id',
        'gmo_order_id',
        'job_cd',
        'pay_type',
        'access_id',
        'access_pass',
        'forward',
        'approve',
        'tran_id',
        'tran_date',
        'status',
        'error_info',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'gmo_order_id',
        'pay_type',
        'access_id',
        'forward',
        'approve',
        'tran_id',
        'status',
        'created_at',
    ];

    public $selectable = [
        '*',
    ];
}
