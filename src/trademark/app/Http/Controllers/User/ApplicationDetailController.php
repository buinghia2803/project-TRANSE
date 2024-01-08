<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Common\TrademarkTableService;
use App\Services\NoticeService;
use App\Services\TrademarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ApplicationDetailController extends Controller
{
    protected NoticeService $noticeService;
    protected TrademarkTableService $trademarkTableService;
    protected TrademarkService $trademarkService;

    /**
     * Constructor
     *
     * @param   NoticeService $trademarkService
     * @return  void
     */
    public function __construct(
        NoticeService $noticeService,
        TrademarkTableService $trademarkTableService,
        TrademarkService $trademarkService
    )
    {
        $this->noticeService = $noticeService;
        $this->trademarkTableService = $trademarkTableService;
        $this->trademarkService = $trademarkService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $trademarkId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $trademarkId)
    {
        $trademark = $this->trademarkService->findByCondition(['id' => $trademarkId, 'user_id' => auth()->user()->id])->first();
        if (!$trademark) {
            abort(404);
        }

        $listNotice = $this->noticeService->getListNotice($request->all(), $trademarkId);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_1, $trademarkId);
        $trademarkNotice = $this->trademarkService->getTrademarkNotice($trademarkId);

        return view('user.modules.application_detail.application_detail', compact('trademarkTable', 'listNotice', 'trademarkNotice', 'trademarkId'));
    }
}
