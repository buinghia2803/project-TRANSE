<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppTrademarkProd extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_trademark_id',
        'm_product_id',
        'is_apply',
        'is_remove',
        'is_new_prod',
        'is_block',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // const
    const IS_APPLY = 1;
    const IS_NOT_APPLY = 0;

    /**
     * Register trademark prod
     *
     * @return BelongsTo
     */
    public function registerTrademarkProd(): BelongsTo
    {
        return $this->belongsTo('App\Models\RegisterTrademarkProd', 'id', 'app_trademark_prod_id');
    }

    /**
     * Payer Information
     *
     * @return BelongsTo
     */
    public function appTrademark(): BelongsTo
    {
        return $this->belongsTo(Trademark::class);
    }

    /**
     * Payer Information
     *
     * @return BelongsTo
     */
    public function mProduct(): BelongsTo
    {
        return $this->belongsTo(MProduct::class);
    }

    /**
     * Plan Correspondence Prod
     *
     * @return BelongsTo
     */
    public function planCorrespondenceProd(): BelongsTo
    {
        return $this->belongsTo(PlanCorrespondenceProd::class, 'id', 'app_trademark_prod_id');
    }
}
