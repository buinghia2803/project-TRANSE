<?php

namespace App\Repositories;

use App\Models\NoticeDetail;
use Illuminate\Database\Eloquent\Builder;

class NoticeDetailRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param NoticeDetail $noticeDetail
     * @return  void
     */
    public function __construct(NoticeDetail $noticeDetail)
    {
        $this->model = $noticeDetail;
    }

    /**
     * @param Builder $query
     * @param string $column
     * @param mixed $data
     *
     * @return  Builder
     */
    public function mergeQuery(Builder $query, string $column, $data): Builder
    {
        switch ($column) {
            case 'id':
            case 'notice_id':
            case 'target_id':
            case 'type_acc':
            case 'type_notify':
            case 'type_page':
            case 'response_deadline':
            case 'target_page':
            case 'redirect_page':
            case 'is_action':
            case 'is_answer':
            case 'content':
            case 'attribute':
                return $query->where($column, $data);
            case 'target_pages':
                return $query->whereIn('target_page', $data);
            case 'type_pages':
                return $query->where('type_page', $data);
            case 'completion_date':
                return $query->where('completion_date', $data);
            case 'target_ids':
                return $query->whereIn('target_id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
