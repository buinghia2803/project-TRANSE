<?php

namespace App\Repositories;

use App\Models\Notice;
use Illuminate\Database\Eloquent\Builder;

class NotifyRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   Notice $notice
     * @return  void
     */
    public function __construct(Notice $notices)
    {
        $this->model = $notices;
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
            case 'target_id':
            case 'type_acc':
            case 'content':
            case 'page':
            case 'app_trademark_id':
            case 'ams_response_deadline':
            case 'patent_response_deadline':
            case 'document':
            case 'is_completed':
            case 'type_notify':
            case 'attribute':
                return $query->where($column, $data);
            default:
                return $query;
        }
    }
}
