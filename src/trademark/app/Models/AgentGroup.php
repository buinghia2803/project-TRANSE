<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AgentGroup extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'name',
        'status_choice',
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

    const STATUS_NOT_CHOICE_TRUE = 0;
    const STATUS_CHOICE_TRUE = 1;

    /**
     * Relation of agent group with agent.
     */
    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, 'agent_group_maps', 'agent_group_id', 'agent_id');
    }

    /**
     * AgentGroupMaps
     *
     * @return HasMany
     */
    public function agentGroupMaps(): HasMany
    {
        return $this->hasMany(AgentGroupMap::class, 'agent_group_id');
    }

    /**
     * Relation of agent group with agent.
     */
    public function collectAgent()
    {
        return $this->hasMany(AgentGroupMap::class);
    }

    /**
     * Relation of app trademark.
     */
    public function appTrademark()
    {
        return $this->hasOne(AppTrademark::class, 'agent_group_id', 'id');
    }
}
