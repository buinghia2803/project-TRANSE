<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AgentGroup;
use App\Models\AgentGroupMap;
use App\Models\AppTrademark;
use App\Models\MProduct;
use App\Models\Payment;
use App\Models\RegisterTrademark;
use App\Models\RegisterTrademarkProd;
use App\Services\Common\TrademarkTableService;
use App\Services\MProductService;
use App\Services\PaymentService;
use App\Services\RegisterTrademarkService;
use App\Services\TrademarkService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterTrademarkController extends Controller
{
    protected $registerTrademarkService;
    protected $paymentService;
    protected $mProductService;
    protected $trademarkTableService;
    protected TrademarkService $trademarkService;

    /**
     * Constructor
     *
     * @param   RegisterTrademarkService $registerTrademarkService
     * @param   PaymentService $paymentService
     * @param   MProductService $mProductService
     * @param   TrademarkTableService $trademarkTableService
     * @param   TrademarkService $trademarkService
     * @return  void
     */
    public function __construct(
        RegisterTrademarkService $registerTrademarkService,
        PaymentService $paymentService,
        MProductService $mProductService,
        TrademarkTableService $trademarkTableService,
        TrademarkService $trademarkService
    )
    {
        $this->registerTrademarkService = $registerTrademarkService;
        $this->paymentService = $paymentService;
        $this->mProductService = $mProductService;
        $this->trademarkTableService = $trademarkTableService;
        $this->trademarkService = $trademarkService;

        $this->middleware('permission:register_trademark.updateDocumentModifyProd')->only(['updateDocumentModifyProd']);
        $this->middleware('permission:register_trademark.postRegisProcedureLatterPeriodDocument')->only(['postRegisProcedureLatterPeriodDocument']);
        $this->middleware('permission:register_trademark.getDocumentModifyProd')->only(['getDocumentModifyProd']);
        $this->middleware('permission:register_trademark.postDocumentModifyProd')->only(['postDocumentModifyProd']);
        $this->middleware('permission:register_trademark.skipDocumentModifyProd')->only(['skipDocumentModifyProd']);
        $this->middleware('permission:register_trademark.updateProcedureDocumentPost')->only(['updateProcedureDocumentPost']);
    }

    /**
     * Get Regis Procedure Latter Period Document Submit - a402for_submit
     *
     * @param $id
     * @return mixed
     */
    public function getRegisProcedureLatterPeriodDocumentSubmit($id)
    {
        $registerTrademark = $this->registerTrademarkService->findByCondition(['id' => $id], [
            'trademark.appTrademark',
        ])->first();
        if (!$registerTrademark) {
            abort(404);
        }
        if (!$registerTrademark->trademark) {
            abort(404);
        }
        $appTrademark = $registerTrademark->trademark->appTrademark;
        if (!$appTrademark) {
            abort(404);
        }

        //round
        $round = $this->registerTrademarkService->findByCondition([
            'trademark_id' => $registerTrademark->trademark_id,
            'is_register' => RegisterTrademark::IS_REGISTER
        ])->where('id', '<=', $id)->where('type', '!=', U302)->count();
        $totalAmount = $this->registerTrademarkService->getTotalAmount($registerTrademark, Payment::RENEWAL_DEADLINE, $round);

        //agent
        $agent = $appTrademark->getAgent();

        $data = [
            'registerTrademark' => $registerTrademark,
            'agent' => $agent,
            'totalAmount' => $totalAmount
        ];

        return view('admin.modules.register-trademark.a402for_submit', $data);
    }

    /**
     * UpdateDocumentModifyProdWindow - a402hosoku01window
     *
     * @return mixed
     */
    public function updateDocumentModifyProdWindow($id)
    {
        $registerTrademark = $this->registerTrademarkService->findByCondition(['id' => $id], ['trademark.appTrademark'])->first();

        if (!$registerTrademark) {
            abort(404);
        }
        $products = $this->mProductService->getInfoProdByRegisterTrademark($registerTrademark->id);

        return view('admin.modules.register-trademark.partials.a402hosoku01window', compact('products', 'id'));
    }

    /**
     * UpdateDocumentModifyProd - a402hosoku01
     *
     * @param int $id
     * @return mixed
     */
    public function updateDocumentModifyProd(int $id)
    {
        $registerTrademark = $this->registerTrademarkService->findByCondition(['id' => $id], ['trademark.appTrademark'])->first();

        if (!$registerTrademark) {
            abort(404);
        }
        $expirationYear = null;
        if ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_5_YEAR) {
            $expirationYear = LABEL_FIVE_YEAR;
        } elseif ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_10_YEAR) {
            $expirationYear = LABEL_TEN_YEAR;
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $registerTrademark->trademark_id);
        //route redirect click - default: a402hosoku02skip
        $routeRedirect = null;

        $products = $this->mProductService->getInfoProdByRegisterTrademarkQuery($registerTrademark->id)->get();
        $flugCheckAll = $products->every(function ($item, $index) {
            return $item->is_apply == RegisterTrademarkProd::IS_APPLY;
        });

        if ($flugCheckAll) {
            //a402hosoku02skip
            $routeRedirect = route('admin.update.document.modification.product.skip', $id);
        } elseif ($products->contains('is_apply', RegisterTrademarkProd::IS_NOT_APPLY)) {
            //a402hosoku02
            $routeRedirect = route('admin.update.document.modification.product.document', $id);
        }

        $admin = auth()->guard()->user();
        //disabled button page role seki
        $isAuthIsJimu = $admin->role == ROLE_OFFICE_MANAGER ? true : false;

        $data = [
            'id' => $id,
            'expirationYear' => $expirationYear,
            'trademarkTable' => $trademarkTable,
            'routeRedirect' => $routeRedirect,
            'isAuthIsJimu' => $isAuthIsJimu,
        ];

        return view('admin.modules.register-trademark.a402hosoku01', $data);
    }

    /**
     * GetRegisProcedureLatterPeriodDocument - a302_402_5yr_kouki
     *
     * @param $id - register_trademark_id
     * @return View
     */
    public function getRegisProcedureLatterPeriodDocument($id): View
    {
        $registerTrademark = $this->registerTrademarkService->findByCondition(['id' => $id], [
            'trademark.appTrademark'
        ])->first();
        if (!$registerTrademark) {
            abort(404);
        }
        if (!$registerTrademark->trademark) {
            abort(404);
        }
        $appTrademark = $registerTrademark->trademark->appTrademark;
        if (!$appTrademark) {
            abort(404);
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $registerTrademark->trademark_id);
        $agent = $appTrademark->getAgent();

        //round
        $round = $this->registerTrademarkService->findByCondition([
            'trademark_id' => $registerTrademark->trademark_id,
            'is_register' => RegisterTrademark::IS_REGISTER
        ])->where('id', '<=', $id)
            ->where('type', '!=', U302)
            ->count();
        //total amount
        $totalAmount = $this->registerTrademarkService->getTotalAmount($registerTrademark, Payment::TYPE_LATE_PAYMENT, $round);

        $totalDistinctions = $this->mProductService->getInfoProdByRegisterTrademark($registerTrademark->id)->count();
        $isRegisChangeInfo = RegisterTrademark::IS_REGISTER_CHANGE_INFO;
        $depositTypeAdvence = Agent::DEPOSIT_TYPE_ADVENCE;
        $depositTypePayment = Agent::DEPOSIT_TYPE_CREDIT;
        $isSendTrue = RegisterTrademark::IS_SEND;

        $data = [
            'trademarkTable' => $trademarkTable,
            'registerTrademark' => $registerTrademark,
            'trademark' => $registerTrademark->trademark,
            'agent' => $agent,
            'totalAmount' => $totalAmount,
            'totalDistinctions' => $totalDistinctions,
            'isRegisChangeInfo' => $isRegisChangeInfo,
            'depositTypeAdvence' => $depositTypeAdvence,
            'depositTypePayment' => $depositTypePayment,
            'isSendTrue' => $isSendTrue
        ];

        return view('admin.modules.register-trademark.a302_402_5yr_kouki', $data);
    }

    /**
     * PostRegisProcedureLatterPeriodDocument
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRegisProcedureLatterPeriodDocument(Request $request, $id)
    {
        $inputs = $request->all();
        $registerTrademark = $this->registerTrademarkService->findByCondition(['id' => $id], [
            'trademark.appTrademark'
        ])->first();
        if (!$registerTrademark) {
            abort(404);
        }
        if (!$registerTrademark->trademark) {
            abort(404);
        }
        $appTrademark = $registerTrademark->trademark->appTrademark;
        if (!$appTrademark) {
            abort(404);
        }
        $inputs['registerTrademark'] = $registerTrademark;
        $result = $this->registerTrademarkService->postRegisProcedureLatterPeriodDocument($inputs);
        if ($result) {
            return redirect()->route('admin.application-detail.index', $registerTrademark->trademark_id)->with('message', __('messages.update_success'));
        }

        return redirect()->back()->with('error', __('messages.update_fail'));
    }

    /**
     * Get u402hosoku02
     *
     * @param $id
     * return View
     */
    public function getDocumentModifyProd($id)
    {
        if (!isset($id)) {
            abort(CODE_ERROR_404);
        }

        $registerTrademark = $this->registerTrademarkService->findByCondition(['id' => $id])->with([
            'trademark.appTrademark.agentGroup' => function ($query) {
                $query->where('status_choice', AgentGroup::STATUS_CHOICE_TRUE);
            },
            'trademark.appTrademark.agentGroup.agentGroupMaps' => function ($query) {
                $query->where('type', AgentGroupMap::TYPE_NOMINATED);
            },
            'trademark.appTrademark.agentGroup.agentGroupMaps.agent'
        ])->first();
        if (!$registerTrademark) {
            abort(CODE_ERROR_404);
        }

        if (empty($registerTrademark->trademark)) {
            abort(CODE_ERROR_404);
        }

        $trademark = $registerTrademark->trademark;

        if (empty($trademark->appTrademark)) {
            abort(CODE_ERROR_404);
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_4, $registerTrademark->trademark_id);

        $appTrademark = $trademark->appTrademark;

        $agentGroup = $appTrademark->agentGroup;
        if (!$agentGroup) {
            $agentGroupMaps = [];
        } else {
            $agentGroupMaps = $agentGroup->agentGroupMaps;
        }

        return view('admin.modules.register-trademark.a402hosoku02', compact([
            'trademarkTable',
            'appTrademark',
            'registerTrademark',
            'trademark',
            'agentGroupMaps',
        ]));
    }

    /**
     * Post u402hosoku02
     *
     * @param $id
     * @param $request
     * return RedirectResponse
     */
    public function postDocumentModifyProd(Request $request, $id)
    {
        if (isset($request->submit)) {
            if ($request->submit == BACK_URL) {
                return redirect()->route('admin.update.document.modification.product.detail', ['id' => $id]);
            } else {
                $registerTrademark = $this->registerTrademarkService->find($id);
                if (!$registerTrademark) {
                    abort(CODE_ERROR_404);
                }
                $registerTrademark->update(['is_confirm' => RegisterTrademark::IS_CONFIRM]);
                $this->registerTrademarkService->sendNoticeOfA402hosoku02($registerTrademark);
                return redirect()->route('admin.application-detail.index', ['id' => $registerTrademark->trademark_id]);
            }
        }
    }

    /**
     * Skip Document Modify Prod
     *
     * @param int $id
     * @return View
     */
    public function skipDocumentModifyProd(int $id): View
    {
        $registerTrademark = $this->registerTrademarkService->findByCondition(['id' => $id], [
            'trademark.appTrademark'
        ])->first();
        if (!$registerTrademark) {
            abort(404);
        }

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $registerTrademark->trademark_id);
        //disabled button page role seki
        $isAuthIsJimu = auth()->user()->role == ROLE_OFFICE_MANAGER ? true : false;
        $data = [
            'id' => $id,
            'trademarkTable' => $trademarkTable,
            'isAuthIsJimu' => $isAuthIsJimu
        ];

        return view('admin.modules.register-trademark.a402hosoku02skip', $data);
    }

    /**
     * Update Procedure Document a402: id - register_trademark_id
     *
     * @param int $id
     * @return void
     */
    public function updateProcedureDocument(int $id)
    {
        $registerTrademark = $this->registerTrademarkService->findByCondition(['id' => $id], [
            'trademark.appTrademark'
        ])->first();
        if (!$registerTrademark) {
            abort(404);
        }
        if (!$registerTrademark->trademark) {
            abort(404);
        }
        $appTrademark = $registerTrademark->trademark->appTrademark;
        if (!$appTrademark) {
            abort(404);
        }
        $registerTrademark->load('registerTrademarkProds');

        $agent = $appTrademark->getAgent();
        //round
        $round = $this->registerTrademarkService->findByCondition([
            'trademark_id' => $registerTrademark->trademark_id,
            'is_register' => RegisterTrademark::IS_REGISTER
        ])->where('id', '<=', $id)->where('type', '!=', U302)->count();

        $totalAmount = $this->registerTrademarkService->getTotalAmount($registerTrademark, Payment::RENEWAL_DEADLINE, $round);


        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $registerTrademark->trademark_id);
        $distinctions = $this->mProductService->getInfoProdByRegisterTrademark($registerTrademark->id, ['is_apply' => RegisterTrademarkProd::IS_APPLY]);
        $depositTypeAdvence = Agent::DEPOSIT_TYPE_ADVENCE;
        $depositTypePayment = Agent::DEPOSIT_TYPE_CREDIT;
        $isRegisChangeInfo = RegisterTrademark::IS_REGISTER_CHANGE_INFO;
        $isSendTrue = RegisterTrademark::IS_SEND;

        //all flag
        //1.check has document
        $flagHasDocumentEdit = $registerTrademark->registerTrademarkProds->every(function ($value, $key) {
            return $value->is_apply == RegisterTrademarkProd::IS_APPLY;
        });

        //2.check change form 10yr to 5yr
        $registerTrademarkPreview = $this->registerTrademarkService->findByCondition([
            'trademark_id' => $registerTrademark->trademark->id,
        ])->where('id', '<', $id)->get()->last();
        $flagChangeTenYearToFiveYear = false;

        if ($registerTrademarkPreview) {
            if (($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_5_YEAR)
                && ($registerTrademarkPreview->period_registration == RegisterTrademark::PERIOD_REGISTRATION_10_YEAR)) {
                $flagChangeTenYearToFiveYear = true;
            }
        }

        $data = [
            'id' => $id,
            'trademarkTable' => $trademarkTable,
            'agent' => $agent,
            'registerTrademark' => $registerTrademark,
            'distinctions' => $distinctions,
            'depositTypeAdvence' => $depositTypeAdvence,
            'depositTypePayment' => $depositTypePayment,
            'isRegisChangeInfo' => $isRegisChangeInfo,
            'flagHasDocumentEdit' => $flagHasDocumentEdit,
            'flagChangeTenYearToFiveYear' => $flagChangeTenYearToFiveYear,
            'totalAmount' => $totalAmount,
            'isSendTrue' => $isSendTrue
        ];

        return view('admin.modules.register-trademark.a402', $data);
    }

    /**
     * UpdateProcedureDocumentPost a402 post: id - register_trademark_id
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function updateProcedureDocumentPost(Request $request, int $id)
    {
        $inputs = $request->all();
        $registerTrademark = $this->registerTrademarkService->findByCondition(['id' => $id], [
            'trademark.appTrademark'
        ])->first();

        if (!$registerTrademark) {
            abort(404);
        }
        if (!$registerTrademark->trademark) {
            abort(404);
        }
        $appTrademark = $registerTrademark->trademark->appTrademark;
        if (!$appTrademark) {
            abort(404);
        }
        $inputs['registerTrademark'] = $registerTrademark;
        $result = $this->registerTrademarkService->updateProcedureDocumentPost($inputs);
        if ($result) {
            return redirect()->route('admin.application-detail.index', $registerTrademark->trademark_id)->with('message', __('messages.update_success'));
        }

        return redirect()->back()->with('error', __('messages.update_fail'));
    }
}
