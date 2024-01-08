<?php

namespace App\Services;

use App\Repositories\AuthenticationRepository;

class AuthenticationService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   AuthenticationRepository $authenticationRepository
     */
    public function __construct(AuthenticationRepository $authenticationRepository)
    {
        $this->repository = $authenticationRepository;
    }
}
