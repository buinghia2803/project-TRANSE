<?php

namespace App\Services;

use App\Repositories\SFTCommentRepository;
use App\Repositories\SFTKeepDataProdRepository;

class SFTKeepDataProdService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param SFTKeepDataProdRepository $sftKeepDataProdRepository
     */
    public function __construct(SFTKeepDataProdRepository $sftKeepDataProdRepository)
    {
        $this->repository = $sftKeepDataProdRepository;
    }
}
