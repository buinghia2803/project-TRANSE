<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Services\AppTrademarkService;
use App\Services\MatchingResultService;
use App\Services\NoticeDetailService;
use App\Services\NoticeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends BaseController
{
    private NoticeService $noticeService;
    private MatchingResultService $matchingResultService;
    private NoticeDetailService $noticeDetailService;
    private AppTrademarkService $appTrademarkService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        NoticeService $noticeService,
        NoticeDetailService $noticeDetailService,
        MatchingResultService $matchingResultService,
        AppTrademarkService $appTrademarkService
    )
    {
        parent::__construct();

        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->matchingResultService = $matchingResultService;
        $this->appTrademarkService = $appTrademarkService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param   Request $request
     * @return  View
     */
    public function index(Request $request): View
    {
        // Todo List
        $todoLists = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::getTypeAcc('admin'),
            'target_id' => Auth::guard('admin')->id(),
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
            'completion_date' => null,
        ])->limit(100)->orderBy('created_at', SORT_BY_DESC)->get();
        $todoLists = $this->noticeDetailService->formatData($todoLists, false);

        // User List
        $noticeUserList = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::getTypeAcc('web'),
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
            'completion_date' => null,
            'is_action' => true,
            'is_answer' => false,
        ])->get();
        $noticeUserList = $this->noticeDetailService->formatData($noticeUserList, false);

        $dataQA = $noticeUserList->where('notice.flow', Notice::FLOW_QA)
            ->sortByDesc('created_at')
            ->unique('notice.user_id');
        $dataFreeHistories = $noticeUserList->where('notice.flow', Notice::FLOW_FREE_HISTORY)
            ->sortByDesc('created_at')
            ->unique('notice.trademark_id');
        $data = $noticeUserList->whereNotIn('notice.flow', [ Notice::FLOW_QA, Notice::FLOW_FREE_HISTORY ])
            ->sortByDesc('created_at')
            ->unique('notice.trademark_id');
        $noticeUserList = $data->merge($dataQA)->merge($dataFreeHistories)->sortByDesc('created_at');

        // Get List Import History
        $startDate = Carbon::now()->subDay(30);
        $endDate = Carbon::now();
        $importHistory = $this->matchingResultService->findByCondition([
            'start_created_at' => $startDate,
            'end_created_at' => $endDate,
        ])->with(['trademark'])->get();

        return view('admin.modules.top', compact(
            'todoLists',
            'noticeUserList',
            'importHistory'
        ));
    }

    /**
     * Display a listing of the resource.
     *
     * @param   Request $request
     * @return  View
     */
    public function search(Request $request): View
    {
        $filter = $request->filter ?? null;
        $sessionSearchTop = [];
        if (!empty($filter) && \Session::has(SESSION_SEARCH_TOP)) {
            $sessionSearchTop = session()->get(SESSION_SEARCH_TOP);
        }

        $appTrademarks = $this->appTrademarkService->filterAppList($sessionSearchTop);

        $orderField = $request->orderField ?? 'notice_updated_at';
        $orderType = $request->orderType ?? SORT_TYPE_DESC;
        if ($orderType == SORT_TYPE_DESC) {
            $appTrademarks = $appTrademarks->sortByDesc($orderField);
        } else {
            $appTrademarks = $appTrademarks->sortBy($orderField);
        }

        return view('admin.modules.search', compact(
            'appTrademarks'
        ));
    }
}
