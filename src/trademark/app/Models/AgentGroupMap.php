<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AgentGroupMap extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    const TYPE_NOMINATED = 1;
    const TYPE_NOT_NOMINATED = 2;

    /**
     * Relation of agent with agent group map.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Relation of agent with agent group map.
     */
    public function agentGroup()
    {
        return $this->belongsTo(AgentGroup::class);
    }
}
