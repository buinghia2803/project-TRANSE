<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReasonQuestion extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_correspondence_id',
        'updated_at',
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

    // const
    const QUESTION_STATUS_NOT_REQUIRED = 1;
    const QUESTION_STATUS_REQUIRED = 2;

    const FLAG_ROLE_1 = 1;
    const FLAG_ROLE_2 = 2;

    //question_status const
    const QUESTION_STATUS_DONT = 1;
    const QUESTION_STATUS_NECESSARY = 2;

    //is_confirm const
    const IS_CONFIRM_FALSE = 0;
    const IS_CONFIRM_TRUE = 1;

    /**
     * Reason question nos.
     *
     * @return HasMany
     */
    public function reasonQuestionNos(): HasMany
    {
        return $this->hasMany(ReasonQuestionNo::class);
    }

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
