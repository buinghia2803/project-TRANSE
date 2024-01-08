<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Notice extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trademark_id',
        'user_id',
        'trademark_info_id',
        'flow',
        'step',
        'created_at',
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

    const NOTICE_PAGE_QUESTION_FROM_CUSTOMER = 'a000qa02';
    const NOTICE_TYPE_ACC_QUESTION_FROM_CUSTOMER = 3;
    const NOTICE_TYPE_NOTIFY_QUESTION_FROM_CUSTOMER = 1;
    const IS_COMPLETED_DEFAULT = 0;
    const ATTR_FROM_CUSTOMER = 'お客様から';
    const CONTENT_ANSWERS_ADMIN = '責任者 Q&A：回答確認';
    const TYPE_ACC_ANSWERS_ADMIN = '4';
    const PAGES_ANSWERS_ADMIN = 'a000qa02s';
    const TYPE_NOTIFY_ANSWERS_ADMIN = '1';
    const ATTR_ANSWERS_ADMIN = '所内処理';

    // Role Seki
    const CONTENT_NOTIFY_ADMIN_ROLE_SEKI = 'AMSからの回答';
    const TYPE_ACC_NOTIFY_ADMIN_ROLE_SEKI = '1';
    const PAGE_QUESTION_ANSWERS_ADMIN_ROLE_SEKI = 'u000aq03kaito_list';
    const TYPE_NOTIFY_ANSWERS_ADMIN_ROLE_SEKI = '2';

    // Const Create Question Admin
    const CONTENT_CREATE_QUESTION = '責任者　Q&A：AMSからの質問';
    const PAGE_CREATE_QUESTION = 'a000qa_from_ams_s';

    // Const Create Question Admin Role Seki
    const PAGE_CREATE_QUESTION_SEKI = 'u000qa02kaito';

    // Const Answer The Question
    const CONTENT_ANSWER_THE_QUESTION = 'お客様からののご回答';
    const TYPE_ACC_ANSWER_THE_QUESTION = 1;
    const PAGE_ANSWER_THE_QUESTION = 'a000qa03kaito_list';
    const TYPE_NOTIFY_ANSWER_THE_QUESTION = 1;

    // Flow
    const FLOW_SFT = 1;
    const FLOW_PRECHECK = 2;
    const FLOW_FREE_HISTORY = 3;
    const FLOW_QA = 4;
    const FLOW_APP_TRADEMARK = 5;
    const FLOW_RESPONSE_REASON = 6;
    const FLOW_REGISTER_TRADEMARK = 7;
    const FLOW_RENEWAL = 8;
    const FLOW_RENEWAL_BEFORE_DEADLINE = 9;
    const FLOW_CHANGE_INFO = 10;
    const FLOW_RESPONSE_NOTICE_REASON_REFUSAL = 11;
    const FLOW_REGISTRATION_5_YEARS = 12;

    // Step
    const STEP_1 = 1;
    const STEP_2 = 2;
    const STEP_3 = 3;
    const STEP_4 = 4;
    const STEP_5 = 5;

    /**
     * All Notice Details
     *
     * @return HasMany
     */
    public function noticeDetails(): HasMany
    {
        return $this->hasMany(NoticeDetail::class);
    }

    /**
     * Trademark
     *
     * @return HasOne
     */
    public function trademark(): HasOne
    {
        return $this->hasOne(Trademark::class, 'id', 'trademark_id');
    }

    /**
     * User
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Trademark Info
     *
     * @return HasOne
     */
    public function trademarkInfo(): HasOne
    {
        return $this->hasOne(TrademarkInfo::class, 'id', 'trademark_info_id');
    }

    /**
     * Get Created At
     *
     * @return void
     */
    public function getCreatedAt()
    {
        return Carbon::parse($this->created_at)->format('Y/m/d');
    }
}
