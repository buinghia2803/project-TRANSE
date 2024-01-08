<?php

namespace App\Services;

use App\Helpers\FileHelper;
use App\Repositories\MDistinctionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MDistinctionService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   MDistinctionRepository $mDistinctionRepository
     */
    public function __construct(MDistinctionRepository $mDistinctionRepository)
    {
        $this->repository = $mDistinctionRepository;
    }

    /**
     * List disctinction option
     *
     * @return mixed
     */
    public function listDistinctionOptions()
    {
        return $this->repository->findByCondition([])->pluck('name', 'id');
    }
}
