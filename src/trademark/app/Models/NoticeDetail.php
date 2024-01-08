<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use DateTime;

class NoticeDetail extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notice_id',
        'target_id',
        'type_acc',
        'response_deadline',
        'response_deadline_ams',
        'content',
        'comment',
        'target_page',
        'redirect_page',
        'attribute',
        'type_notify',
        'type_page',
        'is_open',
        'is_action',
        'is_answer',
        'completion_date',
        'created_at',
        'payment_id',
        'payment_status',
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

    // Type Acc
    const TYPE_USER = 1;
    const TYPE_OFFICE_MANAGER = 2;
    const TYPE_MANAGER = 3;
    const TYPE_SUPERVISOR = 4;

    // Type notify
    const TYPE_NOTIFY_TODO = 1;
    const TYPE_NOTIFY_DEFAULT = 2;
    const TYPE_NOTIFY_AMS = 3;

    // Type page
    const TYPE_PAGE_TOP = 1;
    const TYPE_PAGE_ANKEN_TOP = 2;

    // is open
    const TYPE_OPEN = 1;

    //is_action
    const IS_ACTION_TRUE = 1;

    //const is_answer
    const IS_NOT_ANSWER = 0;
    const IS_ANSWER = 1;

    //const response_deadline

    /**
     * Notice
     *
     * @return HasOne
     */
    public function notice(): HasOne
    {
        return $this->hasOne(Notice::class, 'id', 'notice_id');
    }

    /**
     * Notice Detail Btns
     *
     * @return HasMany
     */
    public function noticeDetailBtns(): HasMany
    {
        return $this->hasMany(NoticeDetailBtn::class);
    }

    /**
     * The attributes that can be order by.
     *
     * @param string $guard
     * @return int
     */
    public static function getTypeAcc(string $guard)
    {
        // If user
        if ($guard == 'web') {
            return self::TYPE_USER;
        }

        // If admin
        $admin = Auth::guard($guard)->user();
        switch ($admin->role) {
            case ROLE_OFFICE_MANAGER:
                return self::TYPE_OFFICE_MANAGER;
            case ROLE_MANAGER:
                return self::TYPE_MANAGER;
            case ROLE_SUPERVISOR:
                return self::TYPE_SUPERVISOR;
        }

        return null;
    }

    /**
     * Is open notice
     *
     * @return boolean
     */
    public function isOpen()
    {
        return $this->is_open == true;
    }

    /**
     * Payment
     *
     * @return BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Is Admin Owner
     *
     * @return boolean
     */
    public function isAdminOwner(): bool
    {
        $adminRole = self::getTypeAcc(ADMIN_ROLE);

        if ($adminRole == $this->type_acc) {
            return true;
        }

        return false;
    }

    /**
     * Check Notice Detail Expired
     *
     * @return boolean
     */
    public function checkNoticeDetailExpired(): bool
    {
        $now = now();

        $responseDeadline = null;
        if ($this->response_deadline) {
            $responseDeadline = new DateTime($this->response_deadline);
        }

        if (!empty($responseDeadline)
            && $this->is_answer == self::IS_NOT_ANSWER
            && $now <= $responseDeadline
            && $now->diff($responseDeadline)->days < 10
            && $now->diff($responseDeadline)->days > 3
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get Class Color Top
     *
     * @return string
     */
    public function getClassColorTop(): string
    {
        $class = '';
        $bgGreen = 'bg_green';
        $bgPink = 'bg_pink';

        $notice = $this->notice ?? null;

        $responseDeadline = null;
        if (!empty($this->response_deadline)) {
            $responseDeadline = Carbon::parse($this->response_deadline)->endOfDay();
        }

        if (!empty($notice) && !empty($responseDeadline) && $this->redirect_page != null) {
            $now = now();
            $daysDifference = $now->diff($responseDeadline)->days;

            if ($responseDeadline < $now) {
                $daysDifference = -$daysDifference;
            }

            switch ($notice->flow) {
                case Notice::FLOW_RESPONSE_REASON:
                case Notice::FLOW_RENEWAL_BEFORE_DEADLINE:
                case Notice::FLOW_REGISTER_TRADEMARK:
                case Notice::FLOW_REGISTRATION_5_YEARS:
                    if ($daysDifference > 3 && $daysDifference < 10) {
                        $class = $bgGreen;
                    } elseif ($daysDifference <= 3) {
                        $class = $bgPink;
                    }
                    break;
                case Notice::FLOW_RENEWAL:
                    if ($daysDifference > 1 && $daysDifference < 10) {
                        $class = $bgGreen;
                    } elseif ($daysDifference <= 1) {
                        $class = $bgPink;
                    }
                    break;
            }
        }

        return $class;
    }
}
