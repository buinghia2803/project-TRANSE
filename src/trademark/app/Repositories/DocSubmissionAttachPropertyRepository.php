<?php

namespace App\Repositories;

use App\Models\DocSubmissionAttachProperty;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DocSubmissionAttachPropertyRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   DocSubmissionAttachProperty $docSubmissionAttachProperty
     * @return  void
     */
    public function __construct(DocSubmissionAttachProperty $docSubmissionAttachProperty)
    {
        $this->model = $docSubmissionAttachProperty;
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
            case 'doc_submission_attach_property_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
