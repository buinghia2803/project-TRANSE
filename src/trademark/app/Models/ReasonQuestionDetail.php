<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ReasonQuestionDetail extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reason_question_id',
        'reason_question_no_id',
        'question',
        'question_edit',
        'question_decision',
        'answer',
        'is_answer',
        'is_confirm',
        'attachment',
        'question_status',
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

    /**
     * Const is_answer
     */
    const IS_NOT_ANSWER = 0;
    const IS_ANSWER = 1;

    /**
     * Const question_status
     */
    const QUESTION_STATUS_DONT = 1;
    const QUESTION_STATUS_NECESSARY = 2;

    /**
     * Is confirm
     */
    const IS_CONFIRM_FALSE = 0;
    const IS_CONFIRM_TRUE = 1;


    /**
     * Get path folder u202
     *
     * @return string
     */
    public static function getPathFolderU202()
    {
        return '/uploads/refusal/pre-question/'.auth()->user()->id;
    }
}
