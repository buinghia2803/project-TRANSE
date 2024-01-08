<?php

namespace App\Services;

use App\Repositories\SFTCommentRepository;
use App\Repositories\SFTKeepDataProdCodeRepository;

class SFTKeepDataProdCodeService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param SFTKeepDataProdCodeRepository $sftKeepDataProdCodeRepository
     */
    public function __construct(SFTKeepDataProdCodeRepository $sftKeepDataProdCodeRepository)
    {
        $this->repository = $sftKeepDataProdCodeRepository;
    }
}
