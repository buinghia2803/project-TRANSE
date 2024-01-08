<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrecheckKeepData extends Model
{
//    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'precheck_id',
        'comment_from_ams_identification',
        'comment_from_ams_similar',
        'comment_internal',
        'step',
    ];

    /**
     * The name table
     */
    protected $table = 'precheck_keep_datas';

    /**
     * PrecheckKeepDataProd
     *
     * @return HasMany
     */
    public function precheckKeepDataProd(): HasMany
    {
        return $this->hasMany(PrecheckKeepDataProd::class, 'precheck_keep_data_id');
    }
}
