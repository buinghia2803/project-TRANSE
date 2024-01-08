<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class MProductCode extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'm_product_id',
        'm_code_id',
    ];

    /**
     * MCode
     *
     * @return HasOne
     */
    public function mCode(): HasOne
    {
        return $this->HasOne(MCode::class, 'id', 'm_code_id');
    }

    /**
     * Code.
     *
     * @return HasOne
     */
    public function code(): HasOne
    {
        return $this->hasOne(MCode::class, 'id', 'm_code_id');
    }

    /**
     * MProduct
     *
     * @return HasMany
     */
    public function product(): HasMany
    {
        return $this->hasMany(MProduct::class);
    }
}
