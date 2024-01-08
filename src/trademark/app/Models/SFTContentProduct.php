<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SFTContentProduct extends Model
{
    use SoftDeletes;

    protected $table = 'sft_content_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'support_first_time_id',
        'name',
        'is_choice_admin',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'support_first_time_id',
        'name',
        'created_at',
        'updated_at',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * Const is choice admin
     */
    const IS_CHOICE_ADMIN_FAlSE = 0;
    const IS_CHOICE_ADMIN_TRUE = 1;
}
