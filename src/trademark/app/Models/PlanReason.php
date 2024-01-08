<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanReason extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_id',
        'reason_id',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * Reasons
     *
     * @return HasMany
     */
    public function reasons(): HasMany
    {
        return $this->hasMany(Reason::class, 'id', 'reason_id');
    }
    /**
     * Reason
     *
     * @return BelongsTo
     */
    public function reason(): BelongsTo
    {
        return $this->belongsTo(Reason::class, 'reason_id');
    }
}
