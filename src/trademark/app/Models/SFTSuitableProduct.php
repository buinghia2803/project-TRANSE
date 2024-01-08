<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SFTSuitableProduct extends BaseModel
{
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $table = 'sft_suitable_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'support_first_time_id',
        'm_product_id',
        'is_block',
        'is_choice_user',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'admin_id',
        'name',
        'is_block',
        'created_at',
        'updated_at',
    ];

    public $selectable = [
        '*',
    ];

    const IS_BLOCK = 1;
    const NOT_IS_BLOCK = 0;

    const IS_CHOICE_USER_TRUE = 1;
    const IS_CHOICE_USER_FALSE = 0;

    /**
     * MDistinction
     *
     * @return \Znck\Eloquent\Relations\BelongsToThrough
     */
    public function mDistinction()
    {
        return $this->belongsToThrough(MDistinction::class, MProduct::class);
    }

    /**
     * MProduct.
     *
     * @return BelongsTo
     */
    public function mProduct(): BelongsTo
    {
        return $this->belongsTo(MProduct::class, 'm_product_id');
    }

    /**
     * Sft Keep Data Prod
     *
     * @return HasOne
     */
    public function sftKeepDataProd(): HasOne
    {
        return $this->hasOne(SFTKeepDataProd::class, 'sft_suitable_product_id');
    }
}
