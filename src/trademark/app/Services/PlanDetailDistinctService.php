<?php

namespace App\Services;

use App\Models\PlanDetailDistinct;
use App\Repositories\PlanDetailDistinctRepository;
use Illuminate\Database\Eloquent\Model;

class PlanDetailDistinctService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param PlanDetailDistinctRepository $planDetailDistinctRepository
     */
    public function __construct(PlanDetailDistinctRepository $planDetailDistinctRepository)
    {
        $this->repository = $planDetailDistinctRepository;
    }

    /**
     * Update Decision Data
     *
     * @param Model $planDetailDistinct
     * @return Model
     */
    public function updateDecision(Model $planDetailDistinct): Model
    {
        $dataUpdate = [];

        if ($planDetailDistinct->is_decision == PlanDetailDistinct::IS_DECISION_EDIT) {
            // m_distinction_id_edit
            $mDistinctionIdEdit = $planDetailDistinct->m_distinction_id_edit ?? null;
            if (!empty($mDistinctionIdEdit)) {
                $dataUpdate['m_distinction_id'] = $mDistinctionIdEdit;
            }

            // is_distinct_settlement_edit
            $isDistinctSettlementEdit = $planDetailDistinct->is_distinct_settlement_edit ?? 0;
            $dataUpdate['is_distinct_settlement'] = $isDistinctSettlementEdit;

            // is_leave_all_edit
            $isLeaveAllEdit = $planDetailDistinct->is_leave_all_edit ?? 0;
            $dataUpdate['is_leave_all'] = $isLeaveAllEdit;
        }

        $dataUpdate['m_distinction_id_edit'] = null;
        $dataUpdate['is_distinct_settlement_edit'] = false;
        $dataUpdate['is_leave_all_edit'] = false;
        $dataUpdate['is_decision'] = PlanDetailDistinct::IS_DECISION_NOT_CHOOSE;

        $planDetailDistinct->update($dataUpdate);

        return $planDetailDistinct;
    }
}
