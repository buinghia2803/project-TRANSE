<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrecheckKeepDataProdResult extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'precheck_keep_data_id',
        'precheck_keep_data_prod_id',
        'm_product_id',
        'm_code_id',
        'result_identification_detail_edit',
        'result_identification_detail_final',
        'result_similar_detail_edit',
        'result_similar_detail_final',
        'is_decision_draft',
        'is_decision_edit',
        'is_decision_similar_draft',
        'is_decision_similar_edit',
        'is_block_identification',
        'is_block_similar',
    ];

    /**
     * The name table
     */
    protected $table = 'precheck_keep_data_prod_results';
}
