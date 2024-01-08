<?php

namespace App\Services;

use App\Models\History;
use App\Services\BaseService;
use App\Repositories\HistoryRepository;

class HistoryService extends BaseService
{
  /**
   * Initializing the instances and variables
   *
   * @param     HistoryRepository $historyRepository
   */
    public function __construct(HistoryRepository $historyRepository)
    {
        $this->repository = $historyRepository;
    }
}
