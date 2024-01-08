<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AppTrademarkProdCmt extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_trademark_id',
        'm_distinction_id',
        'internal_remark',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'app_trademark_id',
        'internal_remark',
        'created_at',
        'updated_at',
    ];

    public $selectable = [
        '*',
    ];
}
