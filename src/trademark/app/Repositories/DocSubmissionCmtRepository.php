<?php

namespace App\Repositories;

use App\Models\DocSubmissionCmt;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DocSubmissionCmtRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   DocSubmissionCmt $docSubmissionCmt
     * @return  void
     */
    public function __construct(DocSubmissionCmt $docSubmissionCmt)
    {
        $this->model = $docSubmissionCmt;
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
            case 'doc_submission_id':
            case 'type_comment_of_step':
            case 'type':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
