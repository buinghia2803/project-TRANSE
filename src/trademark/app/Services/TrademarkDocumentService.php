<?php

namespace App\Services;

use App\Models\Notice;
use App\Models\TrademarkDocument;
use App\Repositories\TrademarkDocumentRepository;
use Illuminate\Support\Collection;

class TrademarkDocumentService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param TrademarkDocumentRepository $trademarkDocumentRepository
     */
    public function __construct(TrademarkDocumentRepository $trademarkDocumentRepository)
    {
        $this->repository = $trademarkDocumentRepository;
    }

    /**
     * Get data with condition
     *
     * @param array $condition
     * @return Collection
     */
    public function getByCondition(array $condition): Collection
    {
        $trademarkId = $condition['trademark_id'] ?? null;
        $type = $condition['type'] ?? null;
        $flow = $condition['flow'] ?? null;
        $step = $condition['step'] ?? null;

        $result = $this->findByCondition([
            'trademark_id' => $trademarkId,
        ]);

        if (!empty($type)) {
            $result = $result->where('type', $type);
        }

        $result = $result->get();

        if (!empty($flow) || !empty($step)) {
            $result = $result->load('noticeDetailBtn.noticeDetail.notice');

            if (!empty($flow)) {
                $result = $result->where('noticeDetailBtn.noticeDetail.notice.flow', $flow);
            }

            if (!empty($step)) {
                $result = $result->where('noticeDetailBtn.noticeDetail.notice.step', $step);
            }
        }

        return $result->map(function ($item) {
            $item->url = !empty($item->url) ? asset($item->url) : null;
            return $item;
        });
    }
}
