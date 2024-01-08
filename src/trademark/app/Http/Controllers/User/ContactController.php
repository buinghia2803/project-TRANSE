<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AppTrademarkProd;
use App\Models\Notice;
use App\Models\Trademark;
use App\Models\TrademarkDocument;
use App\Services\Common\TrademarkTableService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\MProductService;
use App\Services\PlanCorrespondenceService;
use App\Services\RegisterTrademarkService;
use App\Services\TrademarkDocumentService;
use App\Services\TrademarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    private TrademarkService $trademarkService;
    private TrademarkTableService $trademarkTableService;
    private MProductService $mProductService;
    private ComparisonTrademarkResultService $comparisonTrademarkResultService;
    private PlanCorrespondenceService $planCorrespondenceService;
    private RegisterTrademarkService $registerTrademarkService;
    private TrademarkDocumentService $trademarkDocumentService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        TrademarkService $trademarkService,
        TrademarkTableService $trademarkTableService,
        MProductService $mProductService,
        ComparisonTrademarkResultService $comparisonTrademarkResultService,
        PlanCorrespondenceService $planCorrespondenceService,
        RegisterTrademarkService $registerTrademarkService,
        TrademarkDocumentService $trademarkDocumentService
    )
    {
        $this->trademarkService = $trademarkService;
        $this->trademarkTableService = $trademarkTableService;
        $this->mProductService = $mProductService;
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
        $this->planCorrespondenceService = $planCorrespondenceService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->trademarkDocumentService = $trademarkDocumentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return  void
     */
    public function index(Request $request, $trademarkId)
    {
        $trademark = $this->trademarkService->findByCondition([
            'id' => $trademarkId,
            'user_id' => Auth::user()->id,
        ])->first();
        if (!$trademark) {
            abort(404);
        }

        if ($trademark->is_refusal != Trademark::IS_REFUSAL_CONFIRM) {
            abort(404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->findByCondition([
            'id' => $request->comparison_trademark_result_id,
            'trademark_id' => $trademark->id,
        ])->first();
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $planCorrespondence = $this->planCorrespondenceService->findByCondition([
            'id' => $request->plan_correspondence_id,
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
        ])->first();
        if (!$planCorrespondence) {
            abort(404);
        }
        $planCorrespondence->load([
            'planCorrespondenceProds' => function ($query) {
                return $query->with('reasonRefNumProd', 'appTrademarkProd')->whereHas('reasonRefNumProd', function ($q) {
                    return $q->where('rank', 'A');
                })->whereHas('appTrademarkProd', function ($q) {
                    return $q->where('is_apply', AppTrademarkProd::IS_APPLY);
                });
            },
        ]);

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $trademark->id, [
            U207Kyo => true,
        ]);
        $mProductIds = $planCorrespondence->planCorrespondenceProds->pluck('appTrademarkProd')->flatten()->pluck('m_product_id')->toArray();
        $mDistinct = $this->mProductService->getDataMproduct($mProductIds);

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        return view('user.modules.kyo.u207kyo', compact(
            'trademark',
            'trademarkTable',
            'mDistinct',
            'trademarkDocuments',
            'planCorrespondence',
            'comparisonTrademarkResult'
        ));
    }

    /**
     * Redirect Page
     *
     * @param  mixed $request
     * @return void
     */
    public function redirectPage(Request $request)
    {
        $trademark = $this->trademarkService->find($request->trademark_id);
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->findByCondition([
            'id' => $request->comparison_trademark_result_id,
            'trademark_id' => $trademark->id,
        ])->first();

        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $planCorrespondence = $this->planCorrespondenceService->findByCondition([
            'id' => $request->plan_correspondence_id,
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
        ])->first();
        if (!$planCorrespondence) {
            abort(404);
        }
        $planCorrespondence->load([
            'planCorrespondenceProds' => function ($query) {
                return $query->with('reasonRefNumProd', 'appTrademarkProd')->whereHas('reasonRefNumProd', function ($q) {
                    return $q->where('rank', 'A');
                })->whereHas('appTrademarkProd', function ($q) {
                    return $q->where('is_apply', AppTrademarkProd::IS_APPLY);
                });
            },
        ]);

        $mProductIds = $planCorrespondence->planCorrespondenceProds->pluck('appTrademarkProd')->flatten()->pluck('m_product_id')->toArray();
        $mDistinct = $this->mProductService->getDataMproduct($mProductIds);
        $params['trademark_id'] = $trademark->id;
        $params['products'] = $mDistinct;
        $params['from_page'] = U207Kyo;
        $key = Str::random(11);
        $request->session()->put($key, $params);

        return redirect()->route('user.apply-trademark-with-product-copied', ['s' => $key]);
    }
}
