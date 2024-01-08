<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SFTComment;
use App\Models\SFTContentProduct;
use App\Models\SFTKeepDataProd;
use App\Models\SupportFirstTime;
use App\Services\Common\TrademarkTableService;
use App\Models\MProduct;
use App\Services\MDistinctionService;
use App\Services\MProductService;
use App\Services\SupportFirstTimeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;
use App\Services\SFTSuitableProductService;
use App\Services\TrademarkService;

class SupportFirstTimeController extends BaseController
{
    /**
     * @var SupportFirstTimeService $supportFirstTimeService
     * @var SFTSuitableProductService $SFTSuitableProductService
     * @var MDistinctionService $mDistinctionService
     */
    public SupportFirstTimeService $supportFirstTimeService;
    public SFTSuitableProductService $SFTSuitableProductService;
    public MDistinctionService $mDistinctionService;
    private TrademarkTableService $trademarkTableService;
    private TrademarkService $trademarkService;
    public MProductService $mProductService;

    /**
     * Constructor
     *
     * @param SupportFirstTimeService $supportFirstTimeService
     * @param SFTSuitableProductService $SFTSuitableProductService
     * @param MDistinctionService $mDistinctionService
     * @param TrademarkService $trademarkService
     * @param TrademarkTableService $trademarkTableService
     * @param MProductService $mProductService
     */
    public function __construct(
        SupportFirstTimeService   $supportFirstTimeService,
        SFTSuitableProductService $SFTSuitableProductService,
        MDistinctionService       $mDistinctionService,
        TrademarkService          $trademarkService,
        TrademarkTableService     $trademarkTableService,
        MProductService           $mProductService,
        Request                   $request
    )
    {
        parent::__construct();

        $this->supportFirstTimeService = $supportFirstTimeService;
        $this->SFTSuitableProductService = $SFTSuitableProductService;
        $this->mDistinctionService = $mDistinctionService;
        $this->trademarkService = $trademarkService;
        $this->trademarkTableService = $trademarkTableService;
        $this->mProductService = $mProductService;

        $this->middleware('permission:sft.store')->only(['store']);
        $this->middleware('permission:sft.editPost')->only(['editPost']);
    }

    /**
     * Show screen index.
     *
     * @param Request $request
     * @param int $id - trademark_id
     * @return View
     */
    public function index(Request $request, int $id): View
    {
        $input = $request->all();
        $input['trademark_id'] = $id;
        $sft = $this->supportFirstTimeService->getList($input);
        if (!$sft) {
            abort(404);
        }

        $trademark = $this->trademarkService->showTrademark($sft->trademark->id);
        $sftSuitableProduct = $this->SFTSuitableProductService->getList(['support_first_time_id' => $sft->id]);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id);
        $typeCmtInsider = SFTComment::TYPE_COMMENT_INSIDER;
        $typeCmtCustom = SFTComment::TYPE_COMMENT_CUSTOMER;
        $isConfirmTrue = SupportFirstTime::IS_CONFIRM;

        return View('admin.modules.support_first_time.index', compact(
            'sft',
            'sftSuitableProduct',
            'trademark',
            'trademarkTable',
            'typeCmtCustom',
            'typeCmtInsider',
            'isConfirmTrue'
        ));
    }

    /**
     * Update role sft
     *
     * @param int $id - support_first_time_id
     * @return void
     */
    public function updateRoleSft(Request $request, int $id)
    {
        $inputs = $request->all();
        $res = $this->supportFirstTimeService->updateRoleSft($inputs, $id);
        if ($res) {
            return redirect()->route('admin.home')->with('message', __('messages.general.Common_E035'));
        }
        return redirect()->back();
    }

    /**
     * Show form create support first time
     *
     * @param Request $request
     * @param int $id - trademark_id
     * @return View
     */
    public function create(Request $request, int $id)
    {
        $input = $request->all();
        $input['trademark_id'] = $id;
        $sft = $this->supportFirstTimeService->getList($input);
        if (!$sft) {
            abort(404);
        }
        $params = [
            'sort' => 'id',
            'sortType' => SORT_TYPE_ASC,
        ];
        $distinction = $this->mDistinctionService->list($params);
        $sftSuitableProduct = $this->SFTSuitableProductService->getListDataSuitableProductOld(['support_first_time_id' => $sft->id]);
        $trademark = $this->trademarkService->showTrademark($sft->trademark->id);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id);
        $isChoiceAdminTrue = SFTContentProduct::IS_CHOICE_ADMIN_TRUE;
        $flagRoleSeki = SupportFirstTime::FLAG_ROLE_SEKI;
        //comment Tanto
        $commentInsider = $this->supportFirstTimeService->getCommentTanTou($sft, SFTComment::TYPE_COMMENT_INSIDER);
        $commentAMS = $this->supportFirstTimeService->getCommentTanTou($sft, SFTComment::TYPE_COMMENT_CUSTOMER);

        $data = [
            'sft' => $sft,
            'distinction' => $distinction,
            'trademark' => $trademark,
            'trademarkTable' => $trademarkTable,
            'isChoiceAdminTrue' => $isChoiceAdminTrue,
            'sftSuitableProduct' => $sftSuitableProduct,
            'flagRoleSeki' => $flagRoleSeki,
            'commentInsider' => $commentInsider,
            'commentAMS' => $commentAMS
        ];

        return View('admin.modules.support_first_time.create', $data);
    }

    /**
     * Store
     *
     * @param Request $request
     * @param int $id - trademark_id
     * @return mixed
     */
    public function store(Request $request, int $id)
    {
        $input = $request->all();
        $input['trademark_id'] = $id;
        $input['support_first_time_id'] = $this->supportFirstTimeService->findByCondition(['trademark_id' => $id])
            ->first()
            ->id;
        $res = $this->supportFirstTimeService->createSupportFirstTimeAdmin($input);
        if ($res) {
            if (isset($input['code']) && $input['code'] == NOTICE_CODE) {
                return redirect()->route('admin.home')->with('message', __('messages.precheck.success_a011'));
            }
            return redirect()->back()->with('message', __('messages.general.Common_E047'));
        }

        return redirect()->back();
    }

    /**
     * SearchRecommend
     *
     * @param Request $request
     * @return Response
     */
    public function searchRecommend(Request $request): Response
    {
        if ($request->ajax()) {
            $input = $request->all();
            $product = $this->supportFirstTimeService->searchRecommend($input);
            $html = '<div class="search-suggest" id="suggest_search">
            <div class="search-suggest__list">';

            if ($product->count() > 0) {
                foreach ($product as $value) {
                    $html = $html . '
                    <div class="item" data-id="' . $value->id . '" prod_value="' . $value->name . '" key_item="' . $request->prod . '" key_type="' . $value->type . '" >'
                        . $value->name .
                        '</div>
                ';
                }
            } else {
                $html = $html . '<div class="no_item">no item</div>';
            }

            $html = $html . '</div></div>';

            return response()->json([
                'status' => true,
                'prod_count' => $product->count(),
                'data' => [
                    'html' => $html,
                    'prod' => $request->prod,
                ],
            ], 200);
        }
        return response()->json([
            'status' => false,
            'prod_count' => 0,
        ], 200);
    }

    /**
     * SearchRecommendGetItem
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchRecommendGetItem(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->all();
            $product = $this->supportFirstTimeService->searchRecommendGetItem($input);

            return response()->json([
                'status' => true,
                'data' => $product,
            ], 200);
        }
        return response()->json([
            'status' => false,
        ], 200);
    }

    /**
     * Support first time admin edit - a011shu
     *
     * @param int $id - trademark_id
     * @return View
     */
    public function edit(int $id)
    {
        $sft = $this->supportFirstTimeService->findByCondition(['trademark_id' => $id])->first();
        if (!$sft) {
            abort(404);
        }

        $flugSftKeepData = false;
        //if exists row support_first_time_id in sft_keep_datas
        $sftKeepData = $sft->sftKeepData;
        if ($sftKeepData) {
            $flugSftKeepData = true;
        }
        $supFirstTimeData = $this->supportFirstTimeService->getInfoSupportFirstTimeEdit($sft->id);
        $distinctions = $this->mDistinctionService->listDistinctionOptions();
        $mProducts = $this->mProductService->getMProductCommonOptions();
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_3, $id);
        $isConfirmTrue = SupportFirstTime::IS_CONFIRM;
        $typeProductCommon = [
            MProduct::TYPE_ORIGINAL_CLEAN,
            MProduct::TYPE_REGISTERED_CLEAN,
        ];
        $typeProductCommonDiff = [
            MProduct::TYPE_CREATIVE_CLEAN,
            MProduct::TYPE_SEMI_CLEAN,
        ];

        //const comment
        $cmtTypeInsider = SFTComment::TYPE_COMMENT_INSIDER;
        $cmtTypeCustomer = SFTComment::TYPE_COMMENT_CUSTOMER;

        //comment Tanto
        $commentInsider = $this->supportFirstTimeService->getCommentTanTou($sft, SFTComment::TYPE_COMMENT_INSIDER);
        $commentAMS = $this->supportFirstTimeService->getCommentTanTou($sft, SFTComment::TYPE_COMMENT_CUSTOMER);

        $data = [
            'supFirstTimeData' => $supFirstTimeData,
            'distinctions' => $distinctions,
            'mProducts' => $mProducts,
            'trademarkTable' => $trademarkTable,
            'sftKeepData' => $sftKeepData,
            'typeProductCommon' => $typeProductCommon,
            'typeProductCommonDiff' => $typeProductCommonDiff,
            'flugSftKeepData' => $flugSftKeepData,
            'isConfirmTrue' => $isConfirmTrue,
            'sft' => $sft,
            'commentInsider' => $commentInsider,
            'commentAMS' => $commentAMS,
            'cmtTypeInsider' => $cmtTypeInsider,
            'cmtTypeCustomer' => $cmtTypeCustomer
        ];

        return view('admin.modules.support_first_time.edit', $data);
    }

    /**
     * Support first time admin edit
     *
     * @param int $id - trademark_id
     * @param Request $request
     *
     * @return mixed
     */
    public function editPost(Request $request, $id)
    {
        $params = $request->all();
        $params['trademark_id'] = $id;
        $sft = $this->supportFirstTimeService->findByCondition(['trademark_id' => $id])->first();
        if (!$sft) {
            abort(404);
        }

        //Validate request
        if (!empty($params['code']) && $params['code'] == NOTICE_CODE && !empty($params['data'])) {
            foreach ($params['data'] as $item) {
                //validate when submit to user: not select all is_block
                if (!isset($item['delete_item']) && !isset($item['is_block'])) {
                    return redirect()->back()->with('error', __('messages.precheck.error_not_select_suitable_u021n'));
                }
                //validate submit send user has data not is decision
                if (!isset($item['delete_item']) && !in_array($item['is_decision'], [SFTKeepDataProd::DRAFT_IS_DECISION, SFTKeepDataProd::EDIT_IS_DECISION])) {
                    return redirect()->back()->with('error', __('messages.error_not_is_decision_all_data'));
                }
            }
        }
        //save data
        $res = $this->supportFirstTimeService->editSFT($params, $sft->id);
        if ($res) {
            return redirect()->back()->with('message', __('messages.precheck.success'));
        }

        return redirect()->back()->withInput();
    }
}
