<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanDetailProductCode extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_detail_product_id',
        'm_code_id',
        'code_name_edit',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'created_at',
        'update_at',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * MCode
     *
     * @return BelongsTo
     */
    public function mCode(): BelongsTo
    {
        return $this->belongsTo(MCode::class);
    }
}
