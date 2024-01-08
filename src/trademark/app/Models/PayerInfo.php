<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayerInfo extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "target_id",
        "payment_type",
        "payer_type",
        "m_nation_id",
        "payer_name",
        "payer_name_furigana",
        "postal_code",
        "m_prefecture_id",
        "address_second",
        "address_three",
        "from_page",
        "type",
    ];

    /**
     * Const type payment
     */
    const PAYMENT_CREATE_CARD = 1;
    const PAYMENT_BANK_TRANSFER = 2;

    /**
     * Nation
     *
     * @return BelongsTo
     */
    public function nation(): BelongsTo
    {
        return $this->belongsTo(MNation::class, 'm_nation_id', 'id');
    }

    /**
     * Nation
     *
     * @return BelongsTo
     */
    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(MPrefecture::class, 'm_prefecture_id', 'id');
    }

    /**
     * Check payment credit card
     *
     * @return boolean
     */
    public function isPaymentCreditCard(): bool
    {
        return $this->payment_type == PayerInfo::PAYMENT_CREATE_CARD;
    }

    /**
     * Check payment bank transfer
     *
     * @return boolean
     */
    public function isPaymentBankTransfer(): bool
    {
        return $this->payment_type == PayerInfo::PAYMENT_BANK_TRANSFER;
    }

    /**
     * Check address in Japan
     *
     * @return boolean
     */
    public function isNationJapan(): bool
    {
        return $this->m_nation_id == NATION_JAPAN_ID;
    }

    /**
     * Get Payment Type Text
     *
     * @return string
     */
    public function getPaymentType(): string
    {
        switch ($this->payment_type) {
            case self::PAYMENT_CREATE_CARD:
                $paymentType = __('labels.payer_info.payment_type_credit');
                break;
            case self::PAYMENT_BANK_TRANSFER:
                $paymentType = __('labels.payer_info.payment_type_bank_transfer');
                break;
            default:
                $paymentType = '';
        }
        return $paymentType;
    }
}
