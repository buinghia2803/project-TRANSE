<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeInfoRegister extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trademark_id',
        'trademark_info_id',
        'register_trademark_id',
        'payment_id',
        'name',
        'm_nation_id',
        'm_prefectures_id',
        'address_second',
        'address_three',
        'type',
        'is_send',
        'is_change_address_free',
        'representative_name',
        'is_updated',
    ];

    const IS_CHANGE_ADDRESS_FREE = 1;
    const IS_CHANGE_ADDRESS_NOT_FREE = 0;

    const IS_UPDATED_TRUE = 1;
    const IS_UPDATED_FALSE = 0;
    /**
     * Trademark
     *
     * @return BelongsTo
     */
    public function trademark(): BelongsTo
    {
        return $this->belongsTo(Trademark::class);
    }

    /**
     * Payment
     *
     * @return void
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'id', 'payment_id');
    }

    /**
     * Relationship Prefecture
     *
     * @return void
     */
    public function prefecture()
    {
        return $this->hasOne(MPrefecture::class, 'id', 'm_prefectures_id');
    }

    /**
     * Relationship Prefecture
     *
     * @return void
     */
    public function nation()
    {
        return $this->hasOne(MNation::class, 'id', 'm_nation_id');
    }

    /**
     * Trademark Info
     *
     * @return BelongsTo
     */
    public function trademarkInfo(): BelongsTo
    {
        return $this->belongsTo(TrademarkInfo::class, 'trademark_info_id', 'id');
    }

    /**
     * Trademark Info
     *
     * @return BelongsTo
     */
    public function registerTrademark(): BelongsTo
    {
        return $this->belongsTo(RegisterTrademark::class, 'register_trademark_id', 'id');
    }
}
