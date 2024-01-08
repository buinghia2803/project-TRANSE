<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReasonQuestionNo extends Model
{
    use SoftDeletes;

    protected $table = 'reason_question_no';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reason_question_id',
        'is_confirm',
        'user_response_deadline',
        'admin_id',
        'flag_role',
        'question_status',
    ];

    /**
     * Const is_confirm
     */
    const IS_NOT_CONFIRM = 0;
    const IS_CONFIRM = 1;

    /**
     * Const question_status const
     */
    const QUESTION_STATUS_DONT = 1;
    const QUESTION_STATUS_NECESSARY = 2;

    /**
     * Reason question details
     *
     * @return HasMany
     */
    public function reasonQuestionDetails(): HasMany
    {
        return $this->hasMany(ReasonQuestionDetail::class);
    }
}
