<?php

namespace App\Repositories;

use App\Models\PrecheckComment;
use App\Models\PrecheckResultComment;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PrecheckCommentRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PrecheckComment $precheckComment
     * @return  void
     */
    public function __construct(PrecheckComment $precheckComment)
    {
        $this->model = $precheckComment;
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
            case 'precheck_id':
            case 'admin_id':
            case 'content':
            case 'type':
            case 'input_of_page':
                return $query->where($column, $data);
            case 'precheck_ids':
                return $query->whereIn('precheck_id', $data);
            case 'input_of_pages':
                return $query->whereIn('input_of_page', $data);
            default:
                return $query;
        }
    }
}
