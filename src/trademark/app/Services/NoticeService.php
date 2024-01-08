<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\Admin;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\NoticeRepository;
use Illuminate\Support\Collection;

class NoticeService extends BaseService
{
    /**
     * @var   NoticeRepository $noticeRepository
     */
    protected $noticeRepository;

    /**
     * Initializing the instances and variables
     *
     * @param   NoticeRepository $noticeRepository
     */
    public function __construct(NoticeRepository $noticeRepository)
    {
        $this->repository = $noticeRepository;
    }

    /**
     * Get List Notice
     *
     * @param  mixed $trademarkId
     * @return void
     */
    public function getListNotice($request, $trademarkId)
    {
        return $this->repository->getListNotice($request, $trademarkId);
    }
}
