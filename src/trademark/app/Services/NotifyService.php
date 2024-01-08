<?php

namespace App\Services;

use App\Repositories\NotifyRepository;

class NotifyService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   NotifyRepository $notifyRepository
     */
    public function __construct(NotifyRepository $notifyRepository)
    {
        $this->repository = $notifyRepository;
    }
}
