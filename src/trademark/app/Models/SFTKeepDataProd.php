<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SFTKeepDataProd extends Model
{
    use HasFactory;

//    use SoftDeletes;

    protected $table = 'sft_keep_data_prods';

    protected $fillable = [
        'sft_keep_data_id',
        'sft_suitable_product_id',
        'product_id',
        'product_name_edit',
        'type_product',
        'm_distinction_id',
        'is_decision',
        'is_block',
        'is_delete',
    ];

    /**
     * Const is_decision
     */
    const NOT_IS_DECISION = 0;
    const DRAFT_IS_DECISION = 1;
    const EDIT_IS_DECISION = 2;


    /**
     * Const type_product
     */
    const TYPE_ORIGINAL_CLEAN = 1;
    const TYPE_REGISTERED_CLEAN = 2;
    const TYPE_CREATIVE_CLEAN = 3;
    const TYPE_SEMI_CLEAN = 4;


    /**
     * MDistinction
     *
     * @return BelongsTo
     */
    public function mDistinction(): BelongsTo
    {
        return $this->belongsTo(MDistinction::class, 'm_distinction_id');
    }

    /**
     * SftKeepDataProdCodes
     *
     * @return HasMany
     */
    public function sftKeepDataProdCodes():HasMany
    {
        return $this->hasMany(SFTKeepDataProdCode::class, 'sft_keep_data_prod_id');
    }

    /**
     * SftSuitableProduct
     *
     * @return HasOne
     */
    public function sftSuitableProduct()
    {
        return $this->hasOne(SFTSuitableProduct::class, 'id', 'sft_suitable_product_id');
    }

    /**
     * M product
     *
     * @return BelongsTo
     */
    public function mProduct(): BelongsTo
    {
        return $this->belongsTo(MProduct::class, 'product_id');
    }

    /**
     * Sft Keep Data
     *
     * @return BelongsTo
     */
    public function sftKeepData(): BelongsTo
    {
        return $this->belongsTo(SFTKeepData::class, 'sft_keep_data_id');
    }
}
