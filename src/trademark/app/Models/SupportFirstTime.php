<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupportFirstTime extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'trademark_id',
        'pack',
        'is_mailing_register_cert',
        'period_registration',
        'is_cancel',
        'flag_role',
        'is_confirm',
        'status_register',
    ];

    const SENT_SESSION_TO_QUOTE = 'QUOTE'; //quote.html
    const SENT_SESSION_TO_ANKEN_TOP = 'ANKEN_TOP'; //u020b
    const TYPE_SUBMIT_SENT_SESSION = 'U020B'; //u020b
    const SAVE_DATA_NO_SENT_SESSION = 'DRAFT';
    const SENT_SESSION_TO_U021 = 'GTU021'; // U021
    const SENT_SESSION_TO_U021C = 'GTU021C'; // U021C
    const SENTS_SESION_TO_COMMON_PAYMENT = 'GTCP'; // Go to common payment
    // Const Redirect
    const REDIRECT_TO_QUOTE = 'QUOTE';
    const REDIRECT_TO_ANKEN_TOP = 'ANKEN_TOP';
    const REDIRECT_TO_U021 = 'GTU021';
    const REDIRECT_TO_U021C = 'GTU021C';
    const REDIRECT_TO_SUGGEST_AI = 'U020B';
    const REDIRECT_TO_COMMON_PAYMENT = 'GTCP';
    const REDIRECT_TO_APPLY_TRADEMARK_WITH_NUMBER = 'はい';
    const REDIRECT_TO_REGISTER_PRECHECK = 'プレチェックサービス申込みへ';
    const REDIRECT_TO_U031_PASS = 'U031pass';
    // Const Input Submit Search Ai Send Session
    const SEND_SESSION_TO_REGISTER_TRADEMARK = '出願申込へ進む';
    const CHECK_TYPE_REGISTER = 'プレチェックレポートが必要な方';

    // Const value button send session
    const SEND_SESSION_TO_APPLY_TRADEMARK_WITH_NUMBER = 'はい';

    /**
     * Const is_cancel
     */
    const IS_CANCEL_FALSE = 0;
    const IS_CANCEL_TRUE = 1;

    /**
     * Const flag_role
     */
    const FLAG_ROLE_TANTO = 1;
    const FLAG_ROLE_SEKI = 2;

    /**
     * Const is_confirm
     */
    const NOT_IS_CONFIRM = 0;
    const IS_CONFIRM = 1;

    /**
     * Const status register
     */
    const NOT_REGISTER = 1;
    const IS_REGISTERED = 2;

    /**
     * Const is_mailing_register_cert
     */
    const IS_MAILING_REGISTER_CERT_FALSE = 0;
    const IS_MAILING_REGISTER_CERT = 1;

    /**
     * SftContent Product.
     *
     * @return HasMany
     */
    public function StfContentProduct(): HasMany
    {
        return $this->hasMany(SFTContentProduct::class);
    }

    /**
     * Admin.
     *
     * @return BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Payer Information.
     *
     * @return BelongsTo
     */
    public function trademark(): BelongsTo
    {
        return $this->belongsTo(Trademark::class);
    }

    /**
     * Sft Comment.
     *
     * @return HasMany
     */
    public function stfComment(): HasMany
    {
        return $this->hasMany(SFTComment::class);
    }

    /**
     * Sft Suitable Product.
     *
     * @return HasMany
     */
    public function StfSuitableProduct(): HasMany
    {
        return $this->hasMany(SFTSuitableProduct::class);
    }


    /**
     * Sft Keep Data
     *
     * @return HasOne
     */
    public function sftKeepData(): HasOne
    {
        return $this->hasOne(SFTKeepData::class, 'support_first_time_id');
    }
}
