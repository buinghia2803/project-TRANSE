<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\MNationRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class MNationService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param MNationRepository $mNationRepository
     */

    public function __construct(MNationRepository $mNationRepository)
    {
        $this->repository = $mNationRepository;
    }

    /**
     * List Nation Options
     */
    public function listNationOptions()
    {
        return $this->repository->listNationOptions();
    }
}
