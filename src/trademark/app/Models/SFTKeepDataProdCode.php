<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SFTKeepDataProdCode extends Model
{
    use HasFactory;

//    use SoftDeletes;

    protected $table = 'sft_keep_data_prod_codes';

    protected $fillable = [
        'sft_keep_data_prod_id',
        'code',
        'm_code_id',
    ];

    /**
     * M code
     *
     * @return BelongsTo
     */
    public function mCode(): BelongsTo
    {
        return $this->belongsTo(MCode::class, 'm_code_id');
    }
}
