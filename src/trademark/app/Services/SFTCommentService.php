<?php

namespace App\Services;

use App\Models\SFTComment;
use App\Repositories\SFTCommentRepository;

class SFTCommentService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param SFTCommentRepository $sFTCommentRepository
     */
    public function __construct(SFTCommentRepository $sFTCommentRepository)
    {
        $this->repository = $sFTCommentRepository;
    }

    /**
     * Create data comment a011
     *
     * @param array $data.
     * @param int   $supportFirstTimeId
     * @return bool
     */
    public function createData(array $data, int $supportFirstTimeId): bool
    {
        foreach ($data as $value) {
            if ($value['content']) {
                $value['admin_id'] = auth()->guard('admin')->user()->id;
                $value['support_first_time_id'] = $supportFirstTimeId;
                $this->updateOrCreate([
                    'type' => $value['type'],
                    'support_first_time_id' => $supportFirstTimeId,
                ], $value);
            }
        }

        return true;
    }
}
