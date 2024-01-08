<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PrecheckProduct extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'precheck_id',
        'm_product_id',
        'is_register_product',
        'is_apply',
        'admin_id',
    ];

    /**
     * Const is_register_product
     */
    const IS_PRECHECK_PRODUCT = 1;
    const IS_NOT_PRECHECK_PRODUCT = 0;

    /**
     * Const is apply
     */
    const IS_APPLY_UN_CHECK = 0;
    const IS_APPLY_CHECKED = 1;

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'admin_id',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * MProduct
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(MProduct::class, 'm_product_id', 'id');
    }

    /**
     * Precheck
     *
     * @return BelongsTo
     */
    public function precheck(): BelongsTo
    {
        return $this->belongsTo(Precheck::class, 'precheck_id');
    }

    /**
     * PrecheckResult
     *
     * @return HasMany
     */
    public function precheckResult(): HasMany
    {
        return $this->hasMany(PrecheckResult::class, 'precheck_product_id');
    }


    //    public function getProductGroupBy()
    //    {
    //
    //        dd('dád');
    //        $resultArray = [];
    //
    //        $paymentProds = $this->paymentProds;
    //        foreach ($paymentProds as $paymentProd) {
    //            $product = $paymentProd->product;
    //            $distinction = $product->mDistinction;
    //            $resultArray[$distinction->id]['distinction_name'] = $distinction->name;
    //            $resultArray[$distinction->id]['products'][] = $product;
    //        }
    //
    //        return $resultArray;
    //    }
}
