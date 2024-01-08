<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RegisterTrademark extends BaseModel
{
    use HasFactory;

    /**
     * Register Trademark Prods
     *
     * @var array
     */
    protected $fillable = [
        'trademark_id',
        'trademark_info_id',
        'id_register_trademark_choice',
        'user_response_deadline',
        'admin_id',
        'period_registration',
        'period_registration_fee',
        'period_change_fee',
        'reg_period_change_fee',
        'mailing_register_cert_fee',
        'regist_cert_nation_id',
        'regist_cert_postal_code',
        'regist_cert_address',
        'regist_cert_payer_name',
        'trademark_info_change_status',
        'trademark_info_change_fee',
        'info_type_acc',
        'trademark_info_nation_id',
        'trademark_info_address_first',
        'trademark_info_address_second',
        'trademark_info_address_three',
        'is_change_address_free',
        'trademark_info_name',
        'option',
        'is_payment',
        'is_cancel',
        'is_send_mail',
        'is_register',
        'is_confirm',
        'is_send',
        'is_submit_at',
        'agent_group_id',
        'display_info_status',
        'date_register',
        'register_number',
        'ams_comment',
        'extension_status',
        'representative_name',
        'is_register_change_info',
        'deadline_update',
        'is_update_info_register',
        'type',
        'type_page',
        'type_notices',
        'is_updated',
        'filing_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Const period_registration
     */
    const PERIOD_REGISTRATION_5_YEAR = 1;
    const PERIOD_REGISTRATION_10_YEAR = 2;

    const TYPE_ACC_COMPANY = 1;
    const TYPE_ACC_SINGLE = 2;

    /**
     * Const is_confirm
     */
    const IS_NOT_CONFIRM = 0;
    const IS_CONFIRM = 1;

    const IS_REGISTER_CHANGE_INFO = 1;
    const IS_NOT_REGISTER_CHANGE_INFO = 0;

    /**
     * Const is_register
     */
    const IS_REGISTER = 1;
    const IS_NOT_REGISTER = 0;

    /**
     * Const is_send
     */
    const IS_NOT_SEND = 0;
    const IS_SEND = 1;

    /**
     * Const is_payment
     */
    const IS_PAYMENT = 1;
    const IS_NOT_PAYMENT = 0;

    /**
     * Const is_cancel
     */
    const IS_CANCEL = 1;
    const IS_NOT_CANCEL = 0;

    const IS_APPLY_TRUE = 1;
    const IS_APPLY_FALSE = 0;

    /**
     * Const trademark_info_change_status
     */
    const STATUS_APPLICANT_NAME = 1;
    const STATUS_ADDRESS = 2;
    const STATUS_NAME_AND_ADDRESS = 3;

    const TYPE_PAGE_1 = 1;
    const TYPE_PAGE_2 = 2;
    const TYPE_PAGE_3 = 3;

    const TYPE_NOTICE_1 = 1;
    const TYPE_NOTICE_2 = 2;
    const TYPE_NOTICE_3 = 3;
    const TYPE_NOTICE_4 = 4;
    const TYPE_NOTICE_5 = 5;
    const TYPE_NOTICE_6 = 6;

    /**
     * Payer Information
     *
     * @return BelongsTo
     */
    public function trademark(): BelongsTo
    {
        return $this->belongsTo(Trademark::class);
    }

    /**
     * Trademark info
     *
     * @return BelongsTo
     */
    public function trademarkInfo(): BelongsTo
    {
        return $this->belongsTo(TrademarkInfo::class);
    }

    /**
     * Product trademark information
     *
     * @return BelongsTo
     */
    public function mProduct(): BelongsTo
    {
        return $this->belongsTo(MProduct::class);
    }

    /**
     * Trademark prod register CSC
     *
     * @return HasMany
     */
    public function registerTrademarkProds(): HasMany
    {
        return $this->hasMany(RegisterTrademarkProd::class);
    }

    /**
     * Trademark prod register CSC
     *
     * @return HasMany
     */
    public function registerTrademarkRenewals(): HasMany
    {
        return $this->hasMany(RegisterTrademarkRenewal::class);
    }

    /**
     * Get Period Registration
     *
     * @return string
     */
    public function getPeriodRegistration(): string
    {
        return ($this->period_registration == self::PERIOD_REGISTRATION_10_YEAR) ? '10年' : '5年';
    }

    /**
     * Relationship Prefecture
     *
     * @return void
     */
    public function prefecture()
    {
        return $this->hasOne(MPrefecture::class, 'id', 'trademark_info_address_first');
    }

    /**
     * M Nation
     *
     * @return BelongsTo
     */
    public function mNation(): BelongsTo
    {
        return $this->belongsTo(MNation::class, 'regist_cert_nation_id');
    }

    /**
     * Get Option Period Registration
     *
     * @return array
     */
    public function getOptionPeriodRegistration(): array
    {
        return [
            self::PERIOD_REGISTRATION_5_YEAR => __('labels.five_years'),
            self::PERIOD_REGISTRATION_10_YEAR => __('labels.ten_years'),
        ];
    }

    /**
     * M Nation trademark info
     *
     * @return BelongsTo
     */
    public function mNationTrademarkInfo(): BelongsTo
    {
        return $this->belongsTo(MNation::class, 'trademark_info_nation_id');
    }

    /**
     * Get type acc
     *
     * @return string|null
     */
    public function getTypeAcc(): ?string
    {
        switch ($this->info_type_acc) {
            case self::TYPE_ACC_COMPANY:
                $typeAcc = __('labels.common.info_type_acc_company');
                break;
            case self::TYPE_ACC_SINGLE:
                $typeAcc = __('labels.common.info_type_acc_single');
                break;
            default:
                $typeAcc = null;
        }
        return $typeAcc;
    }

    /**
     * Get type change
     *
     * @return int|null
     */
    public function getTypeChange(): ?int
    {
        $typeChangeName = TrademarkInfo::TYPE_CHANGE_NAME;
        $typeChangeAddress = TrademarkInfo::TYPE_CHANGE_ADDRESS;
        $typeChangeDouble = TrademarkInfo::TYPE_CHANGE_NAME_AND_ADDRESS;

        $trademarkInfoName = $this->trademark_info_name ?? null;
        $trademarkInfoNationId = $this->trademark_info_nation_id ?? null;

        $typeChange = null;
        if (!empty($trademarkInfoName) && empty($trademarkInfoNationId)) {
            $typeChange = $typeChangeName;
        } elseif (empty($trademarkInfoName) && !empty($trademarkInfoNationId)) {
            $typeChange = $typeChangeAddress;
        } elseif (!empty($trademarkInfoName) && !empty($trademarkInfoNationId)) {
            $typeChange = $typeChangeDouble;
        }

        return $typeChange;
    }

    /**
     * ShowInfoAddress
     *
     * @return string
     */
    public function showInfoAddress()
    {
        return $this->prefecture ? $this->prefecture->name : '' . $this->trademark_info_address_second . $this->trademark_info_address_three;
    }

    /**
     * Get Deadline Update
     *
     * @param int $addDay
     * @param string $format
     * @return string|null
     */
    public function deadlineUpdate(int $addDay = 0, string $format = 'Y年m月d日'): ?string
    {
        $deadlineUpdate = $this->deadline_update ?? null;
        if (empty($deadlineUpdate)) {
            return null;
        }

        return Carbon::parse($deadlineUpdate)->addDays($addDay)->format($format);
    }

    /**
     * ShowTitlePageByType
     *
     * @param string $typePage
     * @return string
     */
    public function showTitlePageByType(string $typePage)
    {
        $title = '';
        if ($typePage == A302_402_5YR_KOUKI) {
            if (Str::startsWith($this->type, U302_402TSUINO.'_')) {
                //U302_402tsuino
                $title = __('labels.a402hosoku01.type_page_a302_402_5yr_kouki_1');
            } elseif (Str::startsWith($this->type, U302_402.'_')) {
                //U302_402_{round}
                $title = __('labels.a402hosoku01.type_page_a302_402_5yr_kouki_2');
            }
        } elseif ($typePage == A402) {
            if (Str::startsWith($this->type, U402TSUINO.'_')) {
                $title = __('labels.a402.title_page_1');
            } elseif (Str::startsWith($this->type, U402.'_')) {
                $title = __('labels.a402.title_page_2');
            }
        }

        return $title;
    }

    /**
     * Has Submit At
     *
     * @param $hasSubmitScreen
     * @return boolean
     */
    public function hasSubmitAt($hasSubmitScreen): bool
    {
        if (!empty($this->is_submit_at)) {
            $isSubmitAt = json_decode($this->is_submit_at, true);
        } else {
            $isSubmitAt = [];
        }

        return in_array($hasSubmitScreen, $isSubmitAt);
    }

    /**
     * Update Is Submit At
     *
     * @param $submitScreen
     * @return void
     */
    public function updateIsSubmitAt($submitScreen)
    {
        if (!empty($this->is_submit_at)) {
            $isSubmitAt = json_decode($this->is_submit_at, true);
        } else {
            $isSubmitAt = [];
        }

        $isSubmitAt[] = $submitScreen;

        $this->update(['is_submit_at' => json_encode($isSubmitAt)]);
    }

    /**
     * Is Change Distinct/Product, Trademark Info
     *
     * @return bool
     */
    public function isChangeStatus(): bool
    {
        $isChangeStatus = false;

        $registerTrademarkProds = $this->registerTrademarkProds ?? collect();
        $registerTrademarkProdNotApply = $registerTrademarkProds->where('is_apply', RegisterTrademarkProd::IS_NOT_APPLY);

        if ($registerTrademarkProdNotApply->count() > 0 || !empty($this->trademark_info_change_status)) {
            $isChangeStatus = true;
        }

        return $isChangeStatus;
    }
}
