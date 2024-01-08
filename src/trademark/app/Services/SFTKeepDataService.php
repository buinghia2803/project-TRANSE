<?php

namespace App\Services;

use App\Repositories\SFTCommentRepository;
use App\Repositories\SFTKeepDataRepository;

class SFTKeepDataService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param SFTKeepDataRepository $sftKeepDataRepository
     */
    public function __construct(SFTKeepDataRepository $sftKeepDataRepository)
    {
        $this->repository = $sftKeepDataRepository;
    }
}
