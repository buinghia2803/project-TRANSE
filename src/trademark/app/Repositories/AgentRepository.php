<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Models\AgentGroup;
use App\Models\AgentGroupMap;
use App\Models\Trademark;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class AgentRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   Agent $agent
     * @return  void
     */
    public function __construct(Agent $agent)
    {
        $this->model = $agent;
    }


    /**
     * @param   Builder $query
     * @param   string  $column
     * @param   mixed   $data
     *
     * @return  Builder
     */
    public function mergeQuery(Builder $query, string $column, $data): Builder
    {
        switch ($column) {
            case 'ids':
                return $query->whereIn('id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get Identifier Code Nominated
     *
     * @param  mixed $id
     * @return void
     */
    public function getIdentifierCodeNominated($id)
    {
        $type = AgentGroupMap::TYPE_NOMINATED;

        return $this->getAgent($id, $type);
    }

    /**
     * Get Identifier Code Not Nominated
     *
     * @param  mixed $id
     * @return void
     */
    public function getIdentifierCodeNotNominated($id)
    {
        $type = AgentGroupMap::TYPE_NOT_NOMINATED;

        return $this->getAgent($id, $type);
    }

    /**
     * Get Agent
     *
     * @param  mixed $id
     * @param  mixed $typeAgentGroupMap
     * @return void
     */
    public function getAgent($id, $typeAgentGroupMap)
    {
        $trademark = Trademark::where('id', $id)->first();

        return $this->model->with([
            'agentGroupMap' => function ($query) use ($typeAgentGroupMap) {
                return $query->where('type', $typeAgentGroupMap);
            },
            'agentGroupMap.agentGroup' => function ($query) {
                return $query->where('status_choice', AgentGroup::STATUS_CHOICE_TRUE);
            },
            'agentGroupMap.agentGroup.appTrademark.trademark'
        ])
            ->whereHas('agentGroupMap', function ($query) use ($typeAgentGroupMap) {
                return $query->where('type', $typeAgentGroupMap);
            })
            ->whereHas('agentGroupMap.agentGroup', function ($query) {
                return $query->where('status_choice', AgentGroup::STATUS_CHOICE_TRUE);
            })
            ->first();
    }
}
