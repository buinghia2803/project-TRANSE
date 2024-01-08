<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\MPrefectureRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MPrefectureService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param MPrefectureRepository $mPrefectureRepository
     */
    public function __construct(MPrefectureRepository $mPrefectureRepository)
    {
        $this->repository = $mPrefectureRepository;
    }

    /**
     * List prefecture options
     *
     * @return mixed
     */
    public function listPrefectureOptions(): Collection
    {
        return $this->repository->listPrefectureOptions();
    }
}
