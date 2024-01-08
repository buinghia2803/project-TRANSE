<?php

namespace App\Services;

use App\Repositories\MTypePlanDocRepository;

class MTypePlanDocService extends BaseService
{

    /**
     * Initializing the instances and variables
     *
     * @param MTypePlanDocRepository $mTypePlanDocRepository
     */
    public function __construct(
        MTypePlanDocRepository $mTypePlanDocRepository
    )
    {
        $this->repository = $mTypePlanDocRepository;
    }

    /**
     * Get all type plan
     */
    public function getAllTypePlanDoc()
    {
        return $this->repository->getAllTypePlanDoc();
    }
}
