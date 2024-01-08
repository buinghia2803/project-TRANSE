<?php

namespace App\Services;

use App\Models\MCode;
use App\Repositories\MCodeRepository;
use App\Repositories\MProductRepository;
use Illuminate\Database\Eloquent\Model;

class MCodeService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   MCodeRepository $mCodeRepository
     */
    public function __construct(MCodeRepository $mCodeRepository)
    {
        $this->repository = $mCodeRepository;
    }

    /**
     * Initializing the instances and variables
     *
     * @param string $name
     * @param int|null $type
     * @return Model
     */
    public function createOrUpdateName(string $name, int $type = null): Model
    {
        $isExist = $this->repository->findByCondition(['name' => $name])->first();
        if (!empty($isExist)) {
            $mCode = $isExist;
        } else {
            $mCode = $this->repository->create([
                'admin_id' => auth()->guard('admin')->id(),
                'name' => $name,
                'type' => $type ?? MCode::TYPE_CREATIVE_CLEAN,
            ]);
        }

        return $mCode;
    }
}
