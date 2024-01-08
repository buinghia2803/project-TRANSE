<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class QuestionAnswer extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'admin_id',
        'question_content',
        'question_attaching_file',
        'question_type',
        'question_content_edit',
        'question_content_decision',
        'answer_content_edit',
        'answer_content_decision',
        'answer_content',
        'answer_attaching_file',
        'question_date',
        'flag_role',
        'answer_date',
        'response_deadline_user',
        'response_deadline_admin',
        'office_comments',
        'is_confirm',
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


    const QUESTION_ANSWERS_NEWEST = 5;
    const QUESTION_ANSWERS_NEWEST_SIX_QA = 6;
    const QUESTION_FROM_CUSTOMERS = 1;
    const IS_NOT_CONFIRM = 0;
    const IS_CONFIRM = 1;
    const QUESTION_FROM_AMS = 2;
    const FLAG_ROLE_TANTOU = 1;
    const FLAG_ROLE_SEKI = 2;
    /**
     * Relationship User
     *
     * @return \Illuminate\Http\Response
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Relationship Admin
     *
     * @return \Illuminate\Http\Response
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }

    /**
     * Get Response Deadline User
     *
     * @return null|string
     */
    public function getResponseDeadlineUser(): ?string
    {
        if (!empty($this->response_deadline_user)) {
            return Carbon::parse($this->response_deadline_user)->format('Y-m-d');
        }

        return null;
    }

    /**
     * Get Response Deadline Admin
     *
     * @return null|string
     */
    public function getResponseDeadlineAdmin(): ?string
    {
        if (!empty($this->response_deadline_admin)) {
            return Carbon::parse($this->response_deadline_admin)->format('Y-m-d');
        }

        return null;
    }
}
