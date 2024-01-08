<?php

namespace App\Repositories;

use App\Models\NoticeDetailBtn;
use Illuminate\Database\Eloquent\Builder;

class NoticeDetailBtnRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param NoticeDetailBtn $noticeDetailBtn
     */
    public function __construct(NoticeDetailBtn $noticeDetailBtn)
    {
        $this->model = $noticeDetailBtn;
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
            case 'notice_detail_id':
            case 'btn_type':
            case 'url':
            case 'date_click':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
