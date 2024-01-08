<?php

namespace App\Services;

use App\Repositories\DocSubmissionAttachmentRepository;
use App\Services\BaseService;

class DocSubmissionAttachmentService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param     DocSubmissionAttachmentRepository $docSubmissionAttachmentRepository
     */
    public function __construct(DocSubmissionAttachmentRepository $docSubmissionAttachmentRepository)
    {
        $this->repository = $docSubmissionAttachmentRepository;
    }
}
