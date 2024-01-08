<?php

namespace App\Repositories;

use App\Models\DocSubmission;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DocSubmissionRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   DocSubmission $docSubmission
     * @return  void
     */
    public function __construct(DocSubmission $docSubmission)
    {
        $this->model = $docSubmission;
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
            case 'trademark_plan_id':
            case 'is_written_opinion':
            case 'is_confirm':
            case 'is_reject':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
