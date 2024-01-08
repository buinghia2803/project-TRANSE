<?php

namespace App\Services;

use App\Repositories\PrecheckResultRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrecheckResultService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param PrecheckResultRepository $precheckResultRepository
     */
    public function __construct(PrecheckResultRepository $precheckResultRepository)
    {
        $this->repository = $precheckResultRepository;
    }

    /**
     * Get data precheck results
     *
     * @param array $ids
     * @return array
     */
    public function getPrecheckResult($ids)
    {
        return $this->repository->getPrecheckResult($ids);
    }

    /**
     * Get data precheck results
     *
     * @param array $ids
     * @return array
     */
    public function getPrecheckResultByPrecheckId($ids)
    {
        return $this->repository->getPrecheckResultByPrecheckId($ids);
    }

    /**
     * Get data precheck results
     *
     * @param array $ids
     * @return array
     */
    public function getPrecheckResultSimilar($ids)
    {
        return $this->repository->getPrecheckResultSimilar($ids);
    }
}
