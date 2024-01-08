<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MTypePlanDoc extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'm_type_plan_id',
        'name',
        'description',
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
}
