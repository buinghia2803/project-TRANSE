<?php

namespace App\Repositories;

use App\Models\DocSubmissionAttachment;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DocSubmissionAttachmentRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   DocSubmissionAttachment $docSubmissionAttachment
     * @return  void
     */
    public function __construct(DocSubmissionAttachment $docSubmissionAttachment)
    {
        $this->model = $docSubmissionAttachment;
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
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
