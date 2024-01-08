<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegisterTrademarkProd extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'register_trademark_id',
        'app_trademark_prod_id',
        'is_apply',
        'm_product_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Const is_apply
     */
    const IS_NOT_APPLY = 0;
    const IS_APPLY = 1;

    /**
     * Register trademark information
     *
     * @return BelongsTo
     */
    public function registerTrademark(): BelongsTo
    {
        return $this->belongsTo(RegisterTrademark::class);
    }

    /**
     * Trademark prod register transe
     *
     * @return BelongsTo
     */
    public function appTrademarkProd(): BelongsTo
    {
        return $this->belongsTo(AppTrademarkProd::class);
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
}
