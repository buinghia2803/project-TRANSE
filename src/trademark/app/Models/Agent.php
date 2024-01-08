<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use SoftDeletes;

// 1: 予納 | 2: 指定立替納付（クレジット）
    const ADVANCE_PAYMENT = 1;
    const DESIGNATED_ADVANCE_PAYMENT = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'identification_number',
        'name',
        'deposit_account_number',
        'deposit_type',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'admin_id',
        'name',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * Const deposit_type
     */
    const DEPOSIT_TYPE_ADVENCE = 1;
    const DEPOSIT_TYPE_CREDIT = 2;

    /**
     * Relation Agent Group Map
     *
     * @return HasMany
     */
    public function agentGroupMap(): HasMany
    {
        return $this->hasMany(AgentGroupMap::class, 'agent_id', 'id');
    }
}
