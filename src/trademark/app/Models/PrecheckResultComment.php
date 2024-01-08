<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PrecheckResultComment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'precheck_result_id',
        'type',
        'content',
        'admin_id',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'admin_id',
        'user_id',
        'question_type',
        'answer_date',
        'question_date',
        'is_confirm',
        'created_at',
        'updated_at',
    ];

    public $selectable = [
        '*',
    ];
}
