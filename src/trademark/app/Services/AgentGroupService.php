<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\AgentGroupRepository;

class AgentGroupService extends BaseService
{
  /**
   * Initializing the instances and variables
   *
   * @param     AgentGroupRepository $agentGroupRepository
   */
    public function __construct(AgentGroupRepository $agentGroupRepository)
    {
        $this->repository = $agentGroupRepository;
    }
}
