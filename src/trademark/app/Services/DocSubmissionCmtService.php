<?php

namespace App\Services;

use App\Models\DocSubmissionCmt;
use App\Services\BaseService;
use App\Repositories\DocSubmissionCmtRepository;

class DocSubmissionCmtService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param DocSubmissionCmtRepository $docSubmissionCmtRepository
     */
    public function __construct(DocSubmissionCmtRepository $docSubmissionCmtRepository)
    {
        $this->repository = $docSubmissionCmtRepository;
    }

    /**
     * Get Doc Submission Cmt Draft
     *
     * @param  mixed $docSubmissionId
     * @param  mixed $step
     * @return Model|null
     */
    public function getDocSubmissionCmtDraft($docSubmissionId, $step)
    {
        $docSubmissionCmt = $this->findByCondition([
            'doc_submission_id' => $docSubmissionId,
            'type_comment_of_step' => $step,
            'type' => DocSubmissionCmt::TYPE_INTERNAL_COMMENT,
        ])->first();
        if (!$docSubmissionCmt) {
            return null;
        }

        return $docSubmissionCmt;
    }
}
