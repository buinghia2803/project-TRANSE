<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReasonNo extends BaseModel
{
    use HasFactory;

    protected $table = 'reason_no';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_correspondence_id',
        'reason_number',
        'reason_branch_number',
        'response_deadline',
        'round',
        'flag_role',
        'is_confirm',
    ];

    // Flag
    const FLAG_1 = 1;
    const FLAG_2 = 2;
    const IS_CONFIRM_0 = 0;
    const IS_CONFIRM_TRUE = 1;

    /**
     * Reason
     *
     * @return HasMany
     */
    public function reasons(): HasMany
    {
        return $this->hasMany(Reason::class);
    }

    /**
     * Reason
     *
     * @return HasMany
     */
    public function reasonRefNumProds(): HasMany
    {
        return $this->hasMany(ReasonRefNumProd::class);
    }
}
