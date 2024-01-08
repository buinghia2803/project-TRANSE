<?php

namespace App\Services;

use App\Models\PrecheckComment;
use App\Repositories\PrecheckCommentRepository;

class PrecheckCommentService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   PrecheckCommentRepository $precheckCommentRepository
     */
    public function __construct(PrecheckCommentRepository $precheckCommentRepository)
    {
        $this->repository = $precheckCommentRepository;
    }
    
    /**
     * GetCommentByConfirmPrecheck
     *
     * @param  array $precheckIds
     * @return array
     */
    public function getCommentByConfirmPrecheck(array $precheckIds): array
    {
        $commentAMS = $this->list([
            'precheck_ids' => $precheckIds,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
            'input_of_pages' => [PrecheckComment::INPUT_OF_PAGE_RUI_AND_SHIKI_SCREEN, PrecheckComment::INPUT_OF_PAGE_KAN_SCREEN],
        ]);
        $commentInternal = $this->list([
            'precheck_ids' => $precheckIds,
            'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
        ]);

        return [
            'commentAMS' => $commentAMS,
            'commentInternal' => $commentInternal,
        ];
    }
}
