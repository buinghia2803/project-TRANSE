<?php

namespace App\Repositories;

use App\Models\Notice;
use App\Models\NoticeDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NoticeRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   Notice $notice
     * @return  void
     */
    public function __construct(Notice $notice)
    {
        $this->model = $notice;
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
            case 'trademark_id':
            case 'flow':
            case 'user_id':
                return $query->where($column, $data);
            case 'flows':
                return $query->whereIn('flow', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get List Notice
     *
     * @param  mixed $trademarkId
     * @return void
     */
    public function getListNotice($request, $trademarkId)
    {
        $noticeList = NoticeDetail::where([
            'type_acc' => NoticeDetail::TYPE_USER,
            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
        ])
        ->with(['noticeDetailBtns', 'noticeDetailBtns.trademarkDocuments', 'notice.trademark'])
        ->whereHas('notice.trademark', function ($query) use ($trademarkId) {
            return $query->where('trademark_id', $trademarkId);
        });

        switch ($request['orderType'] ?? '') {
            case SORT_ASC_ANKEN:
                return $noticeList->orderBy('id', 'asc')->get();
            case SORT_DESC_ANKEN:
                return $noticeList->orderBy('id', 'desc')->get();
            default:
                return $noticeList->orderBy('id', 'desc')->get();
        }
    }
}
