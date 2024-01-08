<?php

namespace App\Repositories;

use App\Models\RequiredDocumentPlan;
use Illuminate\Database\Eloquent\Builder;

class RequiredDocumentPlanRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   RequiredDocumentPlan $requiredDocumentPlan
     * @return  void
     */
    public function __construct(RequiredDocumentPlan $requiredDocumentPlan)
    {
        $this->model = $requiredDocumentPlan;
    }

    /**
     * @param   Builder $query
     * @param   string  $column
     * @param   mixed   $data
     *
     * @return  Builder
     */
    public function mergeQuery(Builder $query, string $column, $data): Builder
    {
        switch ($column) {
            case 'id':
                return $query->where('id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
