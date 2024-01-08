<?php

namespace App\Services;

use App\Repositories\FreeHistoryRepository;

class FreeHistoryService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param FreeHistoryRepository $freeHistoryRepository
     */
    public function __construct(FreeHistoryRepository $freeHistoryRepository)
    {
        $this->repository = $freeHistoryRepository;
    }

    /**
     * Get type option
     *
     * @return array
     */
    public function types()
    {
        return $this->repository->types();
    }

    /**
     * Get properties option
     *
     * @return array
     */
    public function properties()
    {
        return $this->repository->properties();
    }
}
