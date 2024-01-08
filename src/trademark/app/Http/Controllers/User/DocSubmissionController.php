<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\TrademarkDocument;
use App\Services\Common\TrademarkTableService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\DocSubmissionService;
use App\Services\TrademarkDocumentService;
use App\Services\TrademarkPlanService;
use App\Services\TrademarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocSubmissionController extends Controller
{
    protected $comparisonTrademarkResultService;
    protected $trademarkService;
    protected $trademarkPlanService;
    protected $docSubmissionService;
    protected $trademarkTableService;
    protected $trademarkDocumentService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        ComparisonTrademarkResultService $comparisonTrademarkResultService,
        TrademarkService $trademarkService,
        TrademarkPlanService $trademarkPlanService,
        DocSubmissionService $docSubmissionService,
        TrademarkTableService $trademarkTableService,
        TrademarkDocumentService $trademarkDocumentService
    )
    {
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
        $this->trademarkService = $trademarkService;
        $this->trademarkPlanService = $trademarkPlanService;
        $this->docSubmissionService = $docSubmissionService;
        $this->trademarkTableService = $trademarkTableService;
        $this->trademarkDocumentService = $trademarkDocumentService;
    }

    /**
     * Show u205
     *
     * @param  mixed $request
     * @param  mixed $id - comparison_trademark_result_id?
     * @return void
     */
    public function index(Request $request, $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $comparisonTrademarkResult->load('trademark', 'planCorrespondence');

        $trademark = $this->trademarkService->findByCondition([
            'id' => $comparisonTrademarkResult->trademark_id,
            'user_id' => Auth::user()->id,
        ])->first();
        if (!$trademark) {
            abort(404);
        }
        $trademarkInfo = $this->trademarkService->getTrademarkInfo($trademark->id);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        if (!$trademarkPlan) {
            abort(404);
        }
        $dataCommon = $this->trademarkPlanService->getCommonA205Hosei01Window($trademarkPlan->id);
        $docSubmission = $this->docSubmissionService->findByCondition([
            'id' => $request->doc_submission_id,
            'trademark_plan_id' => $trademarkPlan->id,
        ])->first();
        if (!$docSubmission) {
            abort(404);
        }
        $docSubmission->load('docSubmissionAttachProperties');
        $docSubmissionAttachProperties = $docSubmission->docSubmissionAttachProperties;
        $docSubmissionAttachProperties->load([
            'docSubmissionAttachments' => function ($query) {
                return $query->orderBy('file_no', SORT_TYPE_ASC)->orderBy('id', SORT_TYPE_ASC);
            },
        ]);
        $docSubmissionAttachProperties = $docSubmissionAttachProperties->map(function ($item) {
            $docSubmissionAttachments = $item->docSubmissionAttachments ?? collect([]);
            $docSubmissionAttachments = $docSubmissionAttachments->sortBy('file_no');

            $fileNo = $docSubmissionAttachments->first()->file_no;
            $fileNo = (int) mb_convert_kana($fileNo, 'n');

            $item->file_no = $fileNo ?? 9999;

            return $item;
        })->sortBy('file_no')->values();

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $trademark->id, [
            U205 => true,
        ]);

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        return view('user.modules.doc-submission.u205', compact(
            'comparisonTrademarkResult',
            'trademarkInfo',
            'docSubmission',
            'dataCommon',
            'docSubmissionAttachProperties',
            'trademark',
            'trademarkTable',
            'trademarkDocuments'
        ));
    }
}
