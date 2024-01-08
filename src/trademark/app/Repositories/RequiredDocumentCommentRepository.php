<?php

namespace App\Repositories;

use App\Models\RequiredDocument;
use App\Models\RequiredDocumentComment;
use Illuminate\Database\Eloquent\Builder;

class RequiredDocumentCommentRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   RequiredDocumentComment $requiredDocumentComment
     * @return  void
     */
    public function __construct(RequiredDocumentComment $requiredDocumentComment)
    {
        $this->model = $requiredDocumentComment;
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
            case 'type_comment_step':
            case 'from_send_doc':
            case 'required_document_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
