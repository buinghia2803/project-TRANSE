<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PrecheckComment extends Model
{
    use SoftDeletes;

    const TYPE_COMMENT_INTERNAL = 1; //社内用コメント
    const TYPE_COMMENT_SEND_CUSTOMERS = 2; //AMSからお客様へのコメント
    const INPUT_OF_PAGE_RUI_AND_SHIKI_SCREEN = 1; //社内用コメント
    const INPUT_OF_PAGE_KAN_SCREEN = 2; //AMSからお客様へのコメント

    const INPUT_OF_PAGE_RUI_AND_KAN = 1; //a021rui and a021kan
    const INPUT_OF_PAGE_SHIKI = 2; //a021shiki

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'precheck_id',
        'type',
        'content',
        'admin_id',
        'input_of_page',
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
