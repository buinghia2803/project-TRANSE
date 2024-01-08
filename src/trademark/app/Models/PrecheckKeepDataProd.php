<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrecheckKeepDataProd extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'precheck_keep_data_id',
        'm_product_id',
    ];

    /**
     * The name table
     */
    protected $table = 'precheck_keep_data_prods';

    /**
     * PrecheckKeepData
     *
     * @return BelongsTo
     */
    public function precheckKeepData(): BelongsTo
    {
        return $this->belongsTo(PrecheckKeepData::class, 'precheck_keep_data_id');
    }

    /**
     * PrecheckKeepDataProdResult
     *
     * @return HasMany
     */
    public function precheckKeepDataProdResult(): HasMany
    {
        return $this->hasMany(PrecheckKeepDataProdResult::class, 'precheck_keep_data_prod_id');
    }
}
