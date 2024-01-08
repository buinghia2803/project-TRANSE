<?php

namespace App\Services;

use App\Repositories\PlanDetailDocRepository;
use Illuminate\Database\Eloquent\Model;

class PlanDetailDocService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param PlanDetailDocRepository $planDetailRepository
     */
    public function __construct(PlanDetailDocRepository $planDetailDocRepository)
    {
        $this->repository = $planDetailDocRepository;
    }

    /**
     * Update Decision Data
     *
     * @param Model $planDetailDoc
     * @return Model
     */
    public function updateDecision(Model $planDetailDoc): Model
    {
        $dataUpdate = [];

        // m_type_plan_doc_id_edit
        $mTypePlanDocIdEdit = $planDetailDoc->m_type_plan_doc_id_edit ?? null;
        if (!empty($mTypePlanDocIdEdit)) {
            $dataUpdate['m_type_plan_doc_id'] = $mTypePlanDocIdEdit;
        }

        // doc_requirement_des_edit
        $docRequirementDesEdit = $planDetailDoc->doc_requirement_des_edit ?? null;
        if (!empty($docRequirementDesEdit)) {
            $dataUpdate['doc_requirement_des'] = $docRequirementDesEdit;
        }

        $dataUpdate['m_type_plan_doc_id_edit'] = null;
        $dataUpdate['doc_requirement_des_edit'] = null;

        $planDetailDoc->update($dataUpdate);

        return $planDetailDoc;
    }
}
