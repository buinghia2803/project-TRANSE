<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AppTrademark;
use App\Models\ComparisonTrademarkResult;
use App\Models\MailTemplate;
use App\Services\Common\NoticeService;
use App\Services\MailTemplateService;
use App\Services\ComparisonTrademarkResultService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CancelController extends Controller
{
    protected MailTemplateService $mailTemplateService;
    protected ComparisonTrademarkResultService $comparisonTrademarkResultService;
    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        MailTemplateService $mailTemplateService,
        ComparisonTrademarkResultService $comparisonTrademarkResultService
    ) {
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
        $this->mailTemplateService = $mailTemplateService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewNoticationCancel($id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->findOrFail($id)->load('trademark');
        if (!$comparisonTrademarkResult || $comparisonTrademarkResult->trademark->user_id != Auth::user()->id) {
            abort(404);
        } else {
            return view('user.modules.refuse-cancel.cancel', compact('comparisonTrademarkResult'));
        }
    }

    /**
     * Update Notification Cancel
     *
     * @param  mixed $id
     * @return void
     */
    public function updateNotificationCancel($id)
    {
        try {
            $comparisonTrademarkResult = $this->comparisonTrademarkResultService->findOrFail($id);
            $comparisonTrademarkResult->load(['trademark.appTrademark', 'trademark.user']);
            $user = $comparisonTrademarkResult->trademark->user ?? null;
            $appTrademark = $comparisonTrademarkResult->trademark->appTrademark ?? null;
            $comparisonTrademarkResult->update(['is_cancel' => IS_CANCEL_TRUE]);

            $route = route('user.refusal.notification.cancel', $id);

            // Only apply for pack C
            if ($appTrademark && $appTrademark->pack == AppTrademark::PACK_C) {
                $dataSendMail = [
                    'user' => $user,
                    'from_page' => U201B_CANCEL
                ];
                // Send mail 拒絶理由通知書：対応不要
                $this->mailTemplateService->sendMailRequest($dataSendMail, MailTemplate::TYPE_OTHER);
            }

            if ($comparisonTrademarkResult) {
                $this->comparisonTrademarkResultService->sendNoticeComparison($id, $route);
            }

            return redirect()->back()->with('message', OPEN_MODAL);
        } catch (\Exception $e) {
            Log::error($e);

            return redirect()->back()->with('error', __('messages.import_xml.system_error'));
        }
    }
}
