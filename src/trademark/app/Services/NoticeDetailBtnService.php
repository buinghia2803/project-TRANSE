<?php

namespace App\Services;

use App\Models\Notice;
use App\Models\NoticeDetailBtn;
use App\Repositories\NoticeDetailBtnRepository;
use Illuminate\Database\Eloquent\Model;

class NoticeDetailBtnService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param NoticeDetailBtnRepository $noticeDetailBtnRepository
     */
    public function __construct(NoticeDetailBtnRepository $noticeDetailBtnRepository)
    {
        $this->repository = $noticeDetailBtnRepository;
    }

    /**
     * Get Notice Detail Btn A205s
     *
     * @param  model $trademark
     * @return model
     */
    public function getNoticeDetailBtnA205s($trademark): ?Model
    {
        $targetPage = route('admin.refusal.create-request.alert', [
            'id' => $trademark->id,
        ]);
        $targetPage = str_replace(request()->root(), '', $targetPage);
        $noticeDetailBtn = $this->findByCondition([
            'btn_type' => NoticeDetailBtn::BTN_XML_UPLOAD,
        ])->whereHas('noticeDetail', function ($query) use ($targetPage, $trademark) {
            return $query->where('target_page', $targetPage)->whereHas('notice', function ($q) use ($trademark) {
                return $q->where('trademark_id', $trademark->id)
                    ->where('user_id', $trademark->user_id)
                    ->where('flow', Notice::FLOW_RENEWAL_BEFORE_DEADLINE);
            });
        })->first();

        return $noticeDetailBtn;
    }
}
