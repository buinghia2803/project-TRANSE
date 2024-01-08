<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reason extends Model
{
    public $timestamps = false;

    /**
     * No reason
     */
    const NO_REASON = '理由無';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_correspondence_id',
        'admin_id',
        'reason_no_id',
        'm_laws_regulation_id',
        'reference_number',
        'question_status',
        'flag_role',
        'reason_name',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'created_at',
        'updated_at',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * M Laws Regulation
     *
     * @return HasOne
     */
    public function mLawsRegulation(): HasOne
    {
        return $this->hasOne(MLawsRegulation::class, 'id', 'm_laws_regulation_id');
    }

    /**
     * Reason No
     *
     * @return BelongsTo
     */
    public function reasonNo(): BelongsTo
    {
        return $this->belongsTo(ReasonNo::class, 'reason_no_id', 'id');
    }
}
