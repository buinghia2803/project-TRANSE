<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\AgentRepository;

class AgentService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param     AgentRepository $agentRepository
     */
    public function __construct(AgentRepository $agentRepository)
    {
        $this->repository = $agentRepository;
    }

    /**
     * Get Identifier Code Nominated
     *
     * @param  mixed $id
     * @return void
     */
    public function getIdentifierCodeNominated($id)
    {
        return $this->repository->getIdentifierCodeNominated($id);
    }

    /**
     * Get Identifier Code Not Nominated
     *
     * @param  mixed $id
     * @return void
     */
    public function getIdentifierCodeNotNominated($id)
    {
        return $this->repository->getIdentifierCodeNotNominated($id);
    }
}
