<?php

namespace App\Repositories;

use App\Models\AgentGroup;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class AgentGroupRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   AgentGroup $agent
     * @return  void
     */
    public function __construct(AgentGroup $agentGroup)
    {
        $this->model = $agentGroup;
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
            case 'id':
            case 'status_choice':
                return $query->where($column, $data);
            case 'ids':
                return $query->whereIn('id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
