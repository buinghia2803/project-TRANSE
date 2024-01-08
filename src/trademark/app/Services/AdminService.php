<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\AdminRepository;

class AdminService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   AdminRepository $adminRepository
     */
    public function __construct(AdminRepository $adminRepository)
    {
        $this->repository = $adminRepository;
    }
}
