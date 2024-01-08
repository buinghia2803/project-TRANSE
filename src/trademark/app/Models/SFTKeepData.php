<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SFTKeepData extends Model
{
    use HasFactory;

//    use SoftDeletes;

    protected $table = 'sft_keep_datas';

    protected $fillable = [
        'support_first_time_id',
        'comment_from_ams',
        'comment_internal',
        'content_product',
    ];

    /**
     * Sft Keep Data Prods
     *
     * @return HasMany
     */
    public function sftKeepDataProds(): HasMany
    {
        return $this->hasMany(SFTKeepDataProd::class, 'sft_keep_data_id');
    }
}
