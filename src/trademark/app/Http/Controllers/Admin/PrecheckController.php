<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Precheck\createPrecheckResultRequest;
use App\Models\MailTemplate;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\Precheck;
use App\Models\PrecheckComment;
use App\Models\PrecheckProduct;
use App\Services\Common\NoticeService;
use App\Services\Common\TrademarkTableService;
use App\Services\MailTemplateService;
use App\Services\NoticeDetailService;
use App\Services\PrecheckKeepDataProdResultService;
use App\Services\PrecheckKeepDataProdService;
use App\Services\PrecheckKeepDataService;
use App\Services\PrecheckProductService;
use App\Services\PrecheckCommentService;
use App\Services\PrecheckResultService;
use App\Services\PrecheckService;
use App\Services\TrademarkService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PrecheckController extends Controller
{
    protected $precheckService;
    protected $precheckProductService;
    protected $precheckResultService;
    protected $precheckCommentService;
    protected $precheckKeepDataService;
    protected $precheckKeepDataProdService;
    protected $precheckKeepDataProdResultService;
    protected $trademarkTableService;
    protected $trademarkService;
    protected $mailTemplateService;

    /**
     * Constructor
     *
     * @param PrecheckService $precheckService
     * @param PrecheckProductService $precheckProductService
     * @param PrecheckResultService $precheckResultService
     * @param PrecheckCommentService $precheckResultCommentService
     * @param PrecheckKeepDataService $precheckKeepDataService
     * @param PrecheckKeepDataProdService $precheckKeepDataProdService
     * @param PrecheckKeepDataProdResultService $precheckKeepDataProdResultService
     * @param TrademarkService $trademarkService
     * @param MailTemplateService $mailTemplateService
     * @return  void
     */
    public function __construct(
        PrecheckProductService            $precheckProductService,
        PrecheckResultService             $precheckResultService,
        PrecheckCommentService            $precheckCommentService,
        PrecheckService                   $precheckService,
        TrademarkTableService             $trademarkTableService,
        PrecheckKeepDataService           $precheckKeepDataService,
        PrecheckKeepDataProdService       $precheckKeepDataProdService,
        PrecheckKeepDataProdResultService $precheckKeepDataProdResultService,
        TrademarkService                  $trademarkService,
        NoticeDetailService               $noticeDetailService,
        NoticeService                     $noticeService,
        MailTemplateService               $mailTemplateService
    )
    {
        $this->precheckService = $precheckService;
        $this->precheckProductService = $precheckProductService;
        $this->precheckResultService = $precheckResultService;
        $this->precheckCommentService = $precheckCommentService;
        $this->trademarkTableService = $trademarkTableService;
        $this->precheckKeepDataService = $precheckKeepDataService;
        $this->precheckKeepDataProdService = $precheckKeepDataProdService;
        $this->precheckKeepDataProdResultService = $precheckKeepDataProdResultService;
        $this->trademarkService = $trademarkService;
        $this->noticeDetailService = $noticeDetailService;
        $this->noticeService = $noticeService;
        $this->mailTemplateService = $mailTemplateService;

        $this->middleware('permission:prechecks.createPrecheckResult')->only(['createPrecheckResult']);
        $this->middleware('permission:prechecks.createPrecheckResultUnique')->only(['createPrecheckResultUnique']);
        $this->middleware('permission:prechecks.createPrecheckResultSimilar')->only(['createPrecheckResultSimilar']);
        $this->middleware('permission:prechecks.updateRolePrecheck')->only(['updateRolePrecheck']);
        $this->middleware('permission:prechecks.EditPrecheckUnique')->only(['EditPrecheckUnique']);
        $this->middleware('permission:prechecks.EditPrecheckSimilar')->only(['EditPrecheckSimilar']);
    }

    /**
     * View precheck simple (admin tantou)
     *
     * @param mixed $id
     * @return View
     */
    public function viewPrecheckSimple($id, Request $request): View
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }
        $precheckIdPresent = $request->precheck_id;

        if (!isset($request->precheck_id) || empty($precheckIdPresent) || !is_numeric($precheckIdPresent)) {
            abort(404);
        }
        $precheckPresent = $this->precheckService->findByCondition(['id' => $precheckIdPresent])->first();
        if (!$precheckPresent
            || $precheckPresent->trademark_id != $trademark->id
            || $precheckPresent->type_precheck != Precheck::TYPE_PRECHECK_SIMPLE_REPORT) {
            abort(CODE_ERROR_404);
        }
        $prechecks = $this->precheckService->findByCondition(['trademark_id' => $id])
            ->where('id', '<=', $request->precheck_id)
            ->where('type_precheck', Precheck::TYPE_PRECHECK_SIMPLE_REPORT)
            ->orderBy('id', 'DESC')->get();

        $precheckIdsPerious = $prechecks->pluck('id')->toArray();

        if (!$precheckIdsPerious) {
            abort(404);
        }

        $type = TYPE_CHECK_SIMPLE_OR_SIMILAR;
        $precheckProduct = $this->precheckProductService->getPrecheckProduct($precheckIdsPerious);
        $datas = $this->precheckProductService->getDataCheckSimple($precheckProduct['precheckProduct'], $precheckPresent->id, $type);

        $precheckProductRegister = $this->precheckProductService->getPrecheckProductRegister($trademark->id);
        $dataNotRegister = $this->precheckProductService
            ->getDataNotRegister($precheckProductRegister['productNotRegister'], $precheckProductRegister['precheckProductRegister'])->groupBy('code_name');

        if (Session::has(SESSION_PRECHECK_FROM_A021KAN)) {
            $dataSession = Session::get(SESSION_PRECHECK_FROM_A021KAN);
            foreach ($datas as $data) {
                foreach ($data['product'] as &$item) {
                    foreach ($dataSession['m_product_ids'] as $key => $value) {
                        if ($item->id == $value && $item->m_code_id == $dataSession['m_code_ids'][$key]) {
                            $item['result_confirm'] = $dataSession['result_similar_simple'][$key];
                        }
                    }
                }
            }
        }
        $dataRegister = $this->precheckProductService->getPrecheckProductIsRegister($trademark->id);

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_3, $id);

        $precheckCommentInternal = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            'admin_id' => Auth::user()->id,
        ])->orderBy('created_at', 'desc')->first();

        $precheckCommentCustomer = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            'admin_id' => Auth::user()->id,
        ])->orderBy('created_at', 'desc')->first();

        return view('admin.modules.precheck.precheck-simple', compact(
            'datas',
            'dataNotRegister',
            'dataRegister',
            'precheckPresent',
            'precheckCommentInternal',
            'precheckCommentCustomer',
            'trademarkTable',
            'id'
        ));
    }

    /**
     * View precheck simple (admin tantou)
     *
     * @param mixed $id
     * @return View
     */
    public function viewPrecheckSimpleConfirm($id, Request $request): View
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }
        if (!Session::has('go_to_simple_confirm')) {
            abort(404);
        }
        $precheckIdPresent = $request->precheck_id;

        if (!isset($request->precheck_id) || empty($precheckIdPresent) || !is_numeric($precheckIdPresent)) {
            abort(404);
        }
        $precheckPresent = $this->precheckService->findByCondition(['id' => $precheckIdPresent])->first();
        if (!$precheckPresent || $precheckPresent->trademark_id != $trademark->id) {
            abort(CODE_ERROR_404);
        }
        $dataCheckSimpleConfirm = Session::get('go_to_simple_confirm')['request'];
        $prechecks = $this->precheckService->findByCondition(['trademark_id' => $dataCheckSimpleConfirm['trademark_id']])
            ->where('id', '<=', $precheckIdPresent)
            ->where('type_precheck', Precheck::TYPE_PRECHECK_SIMPLE_REPORT)
            ->orderBy('id', 'DESC')->get();

        $precheckIdsPerious = $prechecks->pluck('id')->toArray();

        Session::put(SESSION_PRECHECK_FROM_A021KAN, [
            'm_product_ids' => $dataCheckSimpleConfirm['m_product_id'],
            'm_code_ids' => $dataCheckSimpleConfirm['m_code_id'],
            'result_similar_simple' => $dataCheckSimpleConfirm['result_similar_simple'],
        ]);

        $type = TYPE_CHECK_SIMPLE_OR_SIMILAR;
        $trademarkId = $dataCheckSimpleConfirm['trademark_id'];
        $precheckPresent = $prechecks[0]->id;
        $precheckProduct = $this->precheckProductService->getPrecheckProduct($precheckIdsPerious);
        $datas = $this->precheckProductService->getDataCheckSimple($precheckProduct['precheckProduct'], $precheckPresent, $type);
        foreach ($datas as $data) {
            foreach ($data['product'] as &$item) {
                foreach ($dataCheckSimpleConfirm['m_product_id'] as $key => $value) {
                    if ($item->id == $value && $item->m_code_id == $dataCheckSimpleConfirm['m_code_id'][$key]) {
                        $item['result_confirm'] = $dataCheckSimpleConfirm['result_similar_simple'][$key];
                    }
                }
            }
        }
        $precheckProductRegister = $this->precheckProductService->getPrecheckProductRegister($trademark->id);
        $dataNotRegister = $this->precheckProductService
            ->getDataNotRegister($precheckProductRegister['productNotRegister'], $precheckProductRegister['precheckProductRegister'])->groupBy('code_name');
        $dataRegister = $this->precheckProductService->getPrecheckProductIsRegister($trademark->id);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_3, $dataCheckSimpleConfirm['trademark_id']);
        $precheckCommentInternal = $dataCheckSimpleConfirm['content1'];

        $precheckCommentCustomer = $dataCheckSimpleConfirm['content2'];

        return view('admin.modules.precheck.precheck-simple-confirm', compact(
            'datas',
            'dataNotRegister',
            'dataRegister',
            'precheckPresent',
            'precheckCommentInternal',
            'precheckCommentCustomer',
            'trademarkTable',
            'trademarkId',
            'id',
            'precheckIdPresent'
        ));
    }

    /**
     * Create precheck simple (admin tantou)
     *
     * @param $request
     *
     * @return RedirectResponse
     */
    public function createPrecheckResult(createPrecheckResultRequest $request): RedirectResponse
    {
        Session::forget(SESSION_PRECHECK_FROM_A021KAN);
        $countPrecheckProductIds = count($request->precheck_product_id);
        $precheck = $this->precheckService->find($request->precheck_id)->first()->load('trademark');

        if ($request->submit == CONFIRM) {
            for ($i = 0; $i < $countPrecheckProductIds; $i++) {
                if ($request->is_register_product[$i] == PrecheckProduct::IS_PRECHECK_PRODUCT) {
                    if (empty($request->m_code_id[$i])) {
                        return redirect()->back()->with('error', __('messages.precheck.errors.U021_E002'));
                    }
                    if (empty($request->result_similar_simple[$i])) {
                        return redirect()->back()->with('error', __('messages.precheck.errors.U021_E004'));
                    }
                }
            }
        }

        $this->precheckProductService->insertDataPrechcheckSimple($request);

        if ($request->submit == CONFIRM) {
            Session::put('go_to_simple_confirm', [
                'request' => $request->all(),
            ]);

            return Redirect::route('admin.precheck.show-simple-confirm', [
                'id' => $request->trademark_id,
                'precheck_id' => $request->precheck_id,
            ])->with(['request' => $request->all()]);
        } elseif ($request->submit == CREATE) {
            return redirect()->back()->with('message', __('messages.precheck.success'))->withInput();
        } elseif ($request->submit == SEND_TO_USER) {
            $precheck = $this->precheckService->findByCondition(['id' => $request->precheck_id])->first();
            $precheck->update(['flag_role' => Precheck::FLAG_ROLE_2]);
            $trademark = $this->trademarkService->findByCondition(['id' => $request->trademark_id], ['user'])->first();
            $this->noticeService->updateComment(
                Notice::FLOW_PRECHECK,
                $request->content2 ?? '',
                $trademark->id
            );

            if ($trademark) {
                $this->precheckProductService->sendNoticeOfCheckSimple($trademark, $precheck);
                // send mail submit a021kan
                $dataMail = [
                    'from_page' => A021KAN,
                    'user' => $trademark->user
                ];
                $this->mailTemplateService->sendMailRequest($dataMail, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);
            }

            return redirect()->route('admin.home')->with('message', __('messages.precheck.send_message_precheck_to_user'))->withInput();
        }
    }

    /**
     * View page check unique of precheck select (admin tantou)
     *
     * @param mixed $id
     * @return View
     */
    public function viewPrecheckSelectUnique($id, Request $request): View
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }
        $precheckPresentId = $request->precheck_id;
        if (!isset($request->precheck_id) || empty($precheckPresentId) || !is_numeric($precheckPresentId)) {
            abort(404);
        }
        $precheckPresent = $this->precheckService->findByCondition(['id' => $precheckPresentId])->first();
        if (!$precheckPresent || $precheckPresent->trademark_id != $trademark->id) {
            abort(CODE_ERROR_404);
        }
        $precheckPresent = $precheckPresent->id;
        $prechecks = $this->precheckService->findByCondition(['trademark_id' => $id])
            ->where('id', '<=', $precheckPresentId)
            ->where('type_precheck', Precheck::TYPE_PRECHECK_DETAILED_REPORT)
            ->orderBy('id', 'DESC')->get();

        if ($prechecks->count() == 0) {
            abort(404);
        }

        $precheckIdsPerious = $prechecks->pluck('id')->toArray();

        $precheck = Precheck::where('id', $precheckIdsPerious['0'])->first();
        if ($precheck->type_precheck != Precheck::TYPE_PRECHECK_DETAILED_REPORT) {
            abort(404);
        }

        $precheckProduct = $this->precheckProductService->getPrecheckProductUnique($precheckIdsPerious);
        $datas = $this->precheckProductService->getDataCheckUnique($precheckProduct['precheckProduct'], $precheckPresent);
        $precheckProductRegister = $this->precheckProductService->getPrecheckProductRegister($trademark->id);
        $dataNotRegister = $this->precheckProductService->getDataNotRegister($precheckProductRegister['productNotRegister'], $precheckProductRegister['precheckProductRegister'])->groupBy('code_name');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $id);
        $precheckCommentCheckSimilar = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
            'admin_id' => Auth::user()->id,
        ])->first();
        $precheckCommentCheckUnique = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_SHIKI,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
            'admin_id' => Auth::user()->id,
        ])->first();
        $precheckCommentInternal = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent,
            'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
            'admin_id' => Auth::user()->id,
        ])->first();

        return view('admin.modules.precheck.precheck-select.check-unique', compact(
            'datas',
            'id',
            'dataNotRegister',
            'precheckCommentCheckSimilar',
            'precheckCommentCheckUnique',
            'precheckCommentInternal',
            'precheckPresent',
            'trademarkTable',
            'precheck'
        ));
    }

    /**
     * Create precheck select check unique (admin tantou)
     *
     * @param $request
     * @return RedirectResponse
     */
    public function createPrecheckResultUnique(createPrecheckResultRequest $request): RedirectResponse
    {
        $submitType = $request->submit;
        if ($submitType == SAVE) {
            $this->precheckProductService->insertDataPrecheckUnique($request);
        } elseif ($submitType == CONFIRM) {
            $m_code_id = $request->m_code_id;
            $m_code_id = array_keys($m_code_id);

            $countPrecheckProductIds = count($request->precheck_product_id);
            if ($countPrecheckProductIds !== count($m_code_id)) {
                return redirect()->back()->with('error', __('messages.precheck.errors.U021_E002'));
            }
            for ($i = 0; $i < $countPrecheckProductIds; $i++) {
                foreach ($request->m_code_id[$request->precheck_product_id[$i]] as $valueCode) {
                    if (empty($valueCode) && $request->is_register_product[$i] == 1) {
                        return redirect()->back()->with('error', __('messages.precheck.errors.U021_E002'));
                    }
                    if ($request->is_register_product[$i] == 1 && empty($request->result_identification_detail[$i])) {
                        return redirect()->back()->with('error', __('messages.precheck.errors.U021_E003'));
                    }
                }
            }

            $this->precheckProductService->insertDataPrecheckUnique($request);

            return redirect()->route('admin.precheck.check-similar', [
                'id' => $request->trademark_id,
                'precheck_id' => $request->precheck_id,
            ])->withInput();
        }

        return redirect()->back()->with([
            'message' => __('messages.common.successes.Common_S008'),
        ]);
    }

    /**
     * View page check similar of precheck select
     *
     * @param mixed $id
     *
     * @return View
     */
    public function viewPrecheckSelectSimilar($id, Request $request): View
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }
        $precheckPresentId = $request->precheck_id;
        if (!isset($request->precheck_id) || empty($precheckPresentId) || !is_numeric($precheckPresentId)) {
            abort(404);
        }
        $precheckSelectId = $this->precheckService->findByCondition(['trademark_id' => $id])
            ->where('type_precheck', Precheck::TYPE_PRECHECK_DETAILED_REPORT)
            ->where('id', '<=', $precheckPresentId)
            ->orderBy('id', 'DESC')->pluck('id')->toArray();
        $precheckSimpleId = $this->precheckService->findByCondition(['trademark_id' => $id])
            ->where('type_precheck', Precheck::TYPE_PRECHECK_SIMPLE_REPORT)
            ->where('id', '<=', $precheckPresentId)
            ->orderBy('id', 'DESC')->pluck('id')->toArray();

        $precheckId = array_merge($precheckSelectId, $precheckSimpleId);
        if (count($precheckId) <= 0) {
            abort(404);
        }

        $precheckProduct = $this->precheckProductService->getPrecheckProduct($precheckId);
        $datas = $this->precheckProductService->getDataCheckSimilar($precheckProduct['precheckProduct'], $precheckPresentId, TYPE_CHECK_SIMPLE_OR_SIMILAR);

        $precheckProductRegister = $this->precheckProductService->getPrecheckProductRegister($trademark->id);
        $dataNotRegister = $this->precheckProductService
            ->getDataNotRegister($precheckProductRegister['productNotRegister'], $precheckProductRegister['precheckProductRegister'])->groupBy('code_name');
        $dataRegister = $this->precheckProductService->getPrecheckProductIsRegister($trademark->id);

        $precheckProductSelect = $this->precheckProductService->getPrecheckProductUnique($precheckId);
        $dataPrecheckProductSelects = $this->precheckProductService->getDataCheckUnique($precheckProductSelect['precheckProduct'], $precheckPresentId);

        $precheckCommentUnique = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresentId,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_SHIKI,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
            'admin_id' => Auth::user()->id,
        ])->orderBy('created_at', 'DESC')->first();

        $precheckCommentSimilar = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresentId,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
            'admin_id' => Auth::user()->id,
        ])->orderBy('created_at', 'DESC')->first();

        $precheckCommentInternal = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresentId,
            'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            'admin_id' => Auth::user()->id,
        ])->orderBy('created_at', 'desc')->first();
        $precheckPresent = $this->precheckService->findByCondition(['id' => $precheckPresentId])->first();
        if (!$precheckPresent || $precheckPresent->trademark_id != $trademark->id) {
            abort(CODE_ERROR_404);
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        return view(
            'admin.modules.precheck.precheck-select.check-similar',
            compact(
                'datas',
                'dataPrecheckProductSelects',
                'dataNotRegister',
                'dataRegister',
                'precheckPresent',
                'precheckPresentId',
                'precheckCommentUnique',
                'precheckCommentSimilar',
                'precheckCommentInternal',
                'id',
                'trademarkTable'
            )
        );
    }

    /**
     * Create precheck result select check unique (admin tantou)
     *
     * @param $request
     * @return RedirectResponse
     */
    public function createPrecheckResultSimilar(createPrecheckResultRequest $request): RedirectResponse
    {
        if ($request->submit == CONFIRM) {
            $results = $request->result;
            $resultCodeNameArray = collect($results)->where('is_register_product', 1)->pluck('code_name')->unique()->toArray();
            $resultSimilarDetails = $request->result_similar_detail;

            foreach ($resultCodeNameArray as $codeNameArray) {
                $resultSimilarDetail = $resultSimilarDetails[$codeNameArray] ?? null;

                if (empty($resultSimilarDetail)) {
                    return redirect()->back()->with('error', __('messages.precheck.errors.U021_E003'));
                }
            }

            $precheck = $this->precheckService->findOrFail($request->precheck_id)->load('trademark');
            $precheck->update(['flag_role' => 2]);
            $trademark = $precheck->trademark;
            $this->precheckProductService->insertDataPrecheckSimilar($request);
            $this->noticeService->updateComment(
                Notice::FLOW_PRECHECK,
                $request->content2 ?? '',
                $trademark->id
            );
            if ($trademark) {
                $this->precheckProductService->sendNoticeOfCheckSimilar($trademark, $precheck);
            }
            return redirect()->route('admin.home')->with('message', __('messages.precheck.success_a011'))->withInput();
        }

        $this->precheckProductService->insertDataPrecheckSimilar($request);
        return redirect()->back()->with('message', __('messages.create_success'))->withInput();
    }


    /**
     * View page confirm approve check unique of precheck select (admin seki)
     *
     * @param mixed $id
     *
     * @return View
     */
    public function viewEditPrecheckUnique($id, Request $request): View
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }
        $precheckPresentId = $request->precheck_id;
        if (!isset($request->precheck_id) || empty($precheckPresentId) || !is_numeric($precheckPresentId)) {
            abort(404);
        }
        $precheckPresent = $this->precheckService->find($request->precheck_id);
        if (!$precheckPresent || $precheckPresent->trademark_id != $trademark->id) {
            abort(CODE_ERROR_404);
        }
        $prechecks = $this->precheckService->findByCondition(['trademark_id' => $id])
            ->where('type_precheck', Precheck::TYPE_PRECHECK_DETAILED_REPORT)
            ->where('id', '<=', $precheckPresentId)
            ->orderBy('id', 'DESC')->get();
        if (count($prechecks) <= 0) {
            abort('404');
        }
        $countPrecheck = count($prechecks);
        for ($i = 0; $i < $countPrecheck; $i++) {
            $precheckIdsPerious[] = $prechecks[$i]->id;
        }
        $precheckProduct = $this->precheckProductService->getPrecheckProductUnique($precheckIdsPerious);
        $datas = $this->precheckProductService->getDataCheckUnique($precheckProduct['precheckProduct'], $precheckPresent->id);
        $precheckKeepData = $this->precheckKeepDataService->findByCondition(['precheck_id' => $precheckIdsPerious[0]])->first();
        if ($precheckKeepData) {
            $precheckKeepData->updated_at_convert = CommonHelper::formatTime($precheckKeepData->updated_at, 'Y/m/d');
            $resultKeepData = $this->precheckKeepDataProdService->getPrecheckKeepDataProduct($precheckKeepData->id)->get()->groupBy('mDistinction.name');
            if (count($resultKeepData) > 0) {
                foreach ($resultKeepData as $key => $value) {
                    $dataResultKeepDatas[] = [
                        'codeDistriction' => $key,
                        'product' => $value
                    ];
                }
                foreach ($datas as $key => $data) {
                    foreach ($data['product'] ?? [] as $keyProduct => &$product) {
                        foreach ($dataResultKeepDatas ?? [] as $dataResultKeepData) {
                            foreach ($dataResultKeepData['product'] ?? [] as $dataResultKeepDataProduct) {
                                $precheckKeepDataProd = $dataResultKeepDataProduct->precheckKeepDataProd->last() ?? null;
                                foreach ($precheckKeepDataProd->precheckKeepDataProdResult ?? [] as $value) {
                                    if ($product->id == $dataResultKeepDataProduct->id) {
                                        $product->result_identification_detail_edit = $value->result_identification_detail_edit;
                                        $product->result_identification_detail_final = $value->result_identification_detail_final;
                                        $product->is_decision_draft = $value->is_decision_draft;
                                        $product->is_decision_edit = $value->is_decision_edit;
                                        $product->is_block_identification = $value->is_block_identification;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $precheckKeepData = '';
        }

        $precheckProductRegister = $this->precheckProductService->getPrecheckProductRegister($trademark->id);
        $dataNotRegister = $this->precheckProductService
            ->getDataNotRegister($precheckProductRegister['productNotRegister'], $precheckProductRegister['precheckProductRegister'])->groupBy('code_name');
        $precheckCommentUnique = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_SHIKI,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
        ])->where('admin_id', '!=', Auth::user()->id)->orderBy('created_at', 'DESC')->first();

        $precheckCommentSimilar = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
        ])->where('admin_id', '!=', Auth::user()->id)->orderBy('created_at', 'DESC')->first();

        $precheckCommentInternal = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
        ])->where('admin_id', '!=', Auth::user()->id)->orderBy('created_at', 'desc')->first();
        $precheckCommentInternalOfTantou = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
        ])->where('admin_id', '!=', Auth::user()->id)->orderBy('created_at', 'desc')->first();
        if ($precheckCommentInternalOfTantou) {
            $precheckCommentInternalOfTantou->updated_at_convert = CommonHelper::formatTime($precheckCommentInternalOfTantou->updated_at, 'Y/m/d');
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_3, $id);

        $iconPrecheck = [
            NO_REGISTED => '',
            LIKELY_TO_BE_REGISTERED => '○',
            LOOK_FORWARD_TO_REGISTERING => '△',
            LESS_LIKELY_TO_BE_REGISTERED => '▲',
            DIFFICULT_TO_REGISTER => '×'
        ];
        return view('admin.modules.precheck.precheck-select.edit-check-unique', compact([
            'datas',
            'dataNotRegister',
            'precheckPresent',
            'precheckCommentUnique',
            'precheckCommentSimilar',
            'precheckCommentInternal',
            'precheckCommentInternalOfTantou',
            'id',
            'trademarkTable',
            'precheckKeepData',
            'iconPrecheck',
        ]));
    }

    /**
     * Update precheck select check unique (admin seki)
     *
     * @param $request
     * @return RedirectResponse
     */
    public function EditPrecheckUnique(createPrecheckResultRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            if ($request->submit == FROM_A021SHIKISHU_TO_A021S) {
                return redirect()->route('admin.precheck.view-precheck-confirm', [
                    'id' => $request->trademark_id,
                    'precheck_id' => $request->precheck_id,
                ])->withInput();
            }
            $id = $request->trademark_id;
            $request->all();
            if ($request->submit == CONFIRM) {
                $countProductIds = count($request->m_product_id);
                for ($i = 0; $i < $countProductIds; $i++) {
                    if (!empty($request->result_edit[$i]) && $request->is_decision_draft[$i] == 0 && $request->is_decision_edit[$i] == 0) {
                        return redirect()->back()->with('error', __('messages.precheck.errors.U021_E005'));
                    }
                    if (empty($request->result_edit[$i]) && $request->is_register_product[$i] == 1) {
                        return redirect()->back()->with('error', __('messages.precheck.errors.U021_E005'));
                    }
                    if (!isset($request->check_lock)) {
                        if ($request->is_register_product[$i] == 1) {
                            return redirect()->back()->with('error', __('messages.precheck.errors.U021_E007'));
                        }
                    }
                }
                $precheck = $this->precheckService->findOrFail($request->precheck_id);
                $precheck->update(['flag_role' => 2]);
                $this->precheckProductService->insertDataEditPrecheckUnique($request);
                DB::commit();

                return redirect()->route('admin.precheck_select.view-edit-precheck-similar', [
                    'id' => $request->trademark_id,
                    'precheck_id' => $request->precheck_id,
                ]);
            }
            $this->precheckProductService->insertDataEditPrecheckUnique($request);
            DB::commit();
            return redirect()->back()
                ->with('message', __('messages.create_success'))->withInput();
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([], CODE_ERROR_500);
        }
    }

    /**
     * ShowConfirmPreCheck
     *
     * @param mixed $id
     * @return void
     */
    public function showConfirmPreCheck($id, Request $request)
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }
        $trademarkId = $trademark->id;

        $precheckPresentId = $request->precheck_id;
        if (!isset($request->precheck_id) || empty($precheckPresentId) || !is_numeric($precheckPresentId)) {
            abort(404);
        }

        $precheckPresent = $this->precheckService->find($request->precheck_id);
        if (!$precheckPresent
            || $precheckPresent->trademark_id != $trademark->id
            || $precheckPresent->type_precheck != Precheck::TYPE_PRECHECK_DETAILED_REPORT) {
            abort(CODE_ERROR_404);
        }

        $precheckSelectId = $this->precheckService->findByCondition(['trademark_id' => $id])
            ->where('type_precheck', Precheck::TYPE_PRECHECK_DETAILED_REPORT)
            ->where('id', '<=', $precheckPresentId)
            ->orderBy('id', 'DESC')->pluck('id')->toArray();
        $precheckSimpleId = $this->precheckService->findByCondition(['trademark_id' => $id])
            ->where('type_precheck', Precheck::TYPE_PRECHECK_SIMPLE_REPORT)
            ->where('id', '<=', $precheckPresentId)
            ->orderBy('id', 'DESC')->pluck('id')->toArray();

        $precheckId = array_merge($precheckSelectId, $precheckSimpleId);
        if (count($precheckId) <= 0) {
            abort(404);
        }

        $type = 1;
        $precheckUnique = $this->precheckProductService->getPrecheckProductUnique($precheckSelectId);
        $datasUnique = $this->precheckProductService->getDataCheckUnique($precheckUnique['precheckProduct'], $precheckPresent->id);
        $dataConfirm = $this->precheckProductService->getPrecheckProduct($precheckId);
        $datas = $this->precheckProductService->getDataCheckSimilar($dataConfirm['precheckProduct'], $precheckPresent->id, $type);
        $commentAMSShiki = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_SHIKI,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS
        ])->orderBy('created_at', 'DESC')->first();

        $commentAMSRui = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS
        ])->orderBy('created_at', 'DESC')->first();
        $precheckCommentInternal = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
        ])->orderBy('created_at', 'desc')->get();

        $flagDisabled = false;
        if ($precheckPresent->is_confirm == Precheck::IS_CONFIRM_TRUE) {
            $flagDisabled = true;
        }
        $comment = $this->precheckCommentService->getCommentByConfirmPrecheck($precheckId);

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_3, $id);


        return View('admin.modules.precheck.confirm-precheck', compact(
            'datasUnique',
            'datas',
            'precheckPresent',
            'commentAMSShiki',
            'commentAMSRui',
            'precheckCommentInternal',
            'trademarkId',
            'trademarkTable',
            'flagDisabled'
        ));
    }

    /**
     * Update role precheck of page confirm
     *
     * @param mixed $request
     * @return RedirectResponse
     */
    public function updateRolePrecheck(Request $request): RedirectResponse
    {
        $precheck = $this->precheckService->findByCondition(['id' => $request->precheck_id])->first()->load('trademark', 'trademark.user');
        if ($precheck) {
            $precheck->update([
                'flag_role' => 2,
                'is_confirm' => 1,
            ]);
        }
        $trademark = $precheck->trademark;
        if ($trademark) {
            $this->precheckProductService->sendNoticeOfPrecheckConfirm($trademark, $precheck);
            $dataMail = [
                'from_page' => A021S,
                'user' => $trademark->user
            ];
            $this->mailTemplateService->sendMailRequest($dataMail, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);
        }
        return redirect()->route('admin.home')
            ->with('message', __('messages.general.Common_E030'))->withInput();
    }

    /**
     * View modal precheck
     *
     * @param $request
     *
     * @return View
     */
    public function detailPrecheckModal(Request $request): View
    {
        $trademark = $this->trademarkService->find($request->id);
        if (!$trademark) {
            abort(404);
        }
        if (!isset($request->precheck_id)) {
            $precheckSimple = $this->precheckService->findByCondition([
                'trademark_id' => $request->id,
                'type_precheck' => Precheck::TYPE_CHECK_SIMPLE,
            ])->orderBy('id', 'DESC')->pluck('id');
            $precheckSelect = $this->precheckService->findByCondition([
                'trademark_id' => $request->id,
                'type_precheck' => Precheck::TYPE_CHECK_SELECT,
            ])->orderBy('id', 'DESC')->pluck('id');
        } else {
            $precheckSimple = $this->precheckService->findByCondition([
                'trademark_id' => $request->id,
                'type_precheck' => Precheck::TYPE_CHECK_SIMPLE,
            ])->where('id', '<=', $request->precheck_id)->orderBy('id', 'DESC')->pluck('id');

            $precheckSelect = $this->precheckService->findByCondition([
                'trademark_id' => $request->id,
                'type_precheck' => Precheck::TYPE_CHECK_SELECT,
            ])->where('id', '<=', $request->precheck_id)->orderBy('id', 'DESC')->pluck('id');
        }

        if (count($precheckSelect) > 0) {
            $precheckPresent = $precheckSelect[0];
        } else {
            $precheckPresent = 0;
        }

        $precheckProduct = $this->precheckProductService->getPrecheckProductModal($precheckSimple, $precheckSelect);
        $dataResult = $this->precheckProductService->getDataCheckUnique($precheckProduct[0], $precheckPresent);
        foreach ($dataResult as &$data) {
            $data['product'] = $data['product']->toArray();
        }
        $datas = $request->data;
        if (isset($datas) && count($datas) > 0) {
            foreach ($dataResult as $keyData => &$data) {
                foreach ($data['product'] as $keyProduct => &$product) {
                    $finalByCode = [];
                    foreach ($product['code'] as &$code) {
                        if (isset($datas[$code['id'] . '-' . $product['id']])) {
                            $finalByCode[] = $datas[$code['id'] . '-' . $product['id']];
                        }
                    }
                    if (count($finalByCode) > 0) {
                        $product['detailPresent'][0]['result_similar_detail'] = max($finalByCode);
                    } else {
                        $product['detailPresent'][0]['result_similar_detail'] = 0;
                    }
                    $final = [];
                }
            }
        }
        foreach ($dataResult as $keyData => &$data) {
            foreach ($data['product'] as $keyProduct => &$product) {
                if (!empty($product['detailPresent'])) {
                    $detailPresent = $product['detailPresent'][0];
                    if (empty($detailPresent['result_similar_detail']) || empty($detailPresent['result_identification_detail'])) {
                        $detailPresent['result_final'] = '-';
                    } else {
                        if ($detailPresent['result_similar_detail'] < $detailPresent['result_identification_detail']) {
                            $detailPresent['result_final'] = $detailPresent['result_identification_detail'];
                        } elseif ($detailPresent['result_similar_detail'] == $detailPresent['result_identification_detail']) {
                            $detailPresent['result_final'] = $detailPresent['result_identification_detail'];
                        } elseif ($detailPresent['result_similar_detail'] > $detailPresent['result_identification_detail']) {
                            $detailPresent['result_final'] = $detailPresent['result_similar_detail'];
                        }
                    }
                }
            }
        }
        $dataResultPerious = $this->precheckProductService->getDataCheckUnique($precheckProduct[1], $precheckPresent);

        return view('admin.modules.precheck.partials.item', compact(['dataResult', 'dataResultPerious']));
    }

    /**
     * View page check similar of precheck select
     *
     * @param mixed $id
     *
     * @return View
     */
    public function viewEditPrecheckSimilar($id, Request $request): View
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }
        $precheckPresentId = $request->precheck_id;
        if (!isset($request->precheck_id) || empty($precheckPresentId) || !is_numeric($precheckPresentId)) {
            abort(CODE_ERROR_404);
        }
        $precheckPresent = $this->precheckService->find($request->precheck_id);

        if (!$precheckPresent || $precheckPresent->trademark_id != $trademark->id) {
            abort(CODE_ERROR_404);
        }
        $precheckSelectId = $this->precheckService->findByCondition(['trademark_id' => $id])
            ->where('type_precheck', Precheck::TYPE_PRECHECK_DETAILED_REPORT)
            ->where('id', '<=', $precheckPresentId)
            ->orderBy('id', 'DESC')->pluck('id')->toArray();
        $precheckSimpleId = $this->precheckService->findByCondition(['trademark_id' => $id])
            ->where('type_precheck', Precheck::TYPE_PRECHECK_SIMPLE_REPORT)
            ->where('id', '<=', $precheckPresentId)
            ->orderBy('id', 'DESC')->pluck('id')->toArray();
        $precheckId = array_merge($precheckSelectId, $precheckSimpleId);
        if (count($precheckId) <= 0) {
            abort(404);
        }

        $type = 1;
        $precheckProduct = $this->precheckProductService->getPrecheckProduct($precheckId);
        $datas = $this->precheckProductService->getDataCheckSimilar($precheckProduct['precheckProduct'], $precheckPresent->id, $type);
        $precheckKeepData = $this->precheckKeepDataService->findByCondition(['precheck_id' => $precheckPresent->id])->first();
        if (!$precheckKeepData) {
            abort(404);
        }
        if ($precheckKeepData) {
            $precheckKeepData->updated_at_convert = CommonHelper::formatTime($precheckKeepData->updated_at, 'Y/m/d');
            $resultKeepData = $this->precheckKeepDataProdService->getPrecheckKeepDataProduct2($precheckKeepData->id)->get()->groupBy('code_name');
            collect($datas)->map(function ($item) use ($resultKeepData) {
                foreach ($resultKeepData as $keyResultKeepData => $valueResultKeepData) {
                    if ($keyResultKeepData == $item['codeName']) {
                        $keepDatas = $resultKeepData[$item['codeName']];

                        $keepDataProdResults = [];
                        foreach ($keepDatas as $keepData) {
                            $precheckKeepDataProds = $keepData->precheckKeepDataProd;
                            foreach ($precheckKeepDataProds as $precheckKeepDataProd) {
                                $precheckKeepDataProdResults = $precheckKeepDataProd->precheckKeepDataProdResult;
                                foreach ($precheckKeepDataProdResults as $precheckKeepDataProdResult) {
                                    $keepDataProdResults[] = $precheckKeepDataProdResult;
                                }
                            }
                        }

                        $keepDataProdResults = collect($keepDataProdResults);

                        $products = $item['product'] ?? collect();

                        foreach ($products as &$product) {
                            $prodResult = $keepDataProdResults->where('m_code_id', $product->m_code_id)->where('m_product_id', $product->id)->first();
                            $product->keepDataProdResults = $prodResult;
                        }

                        return $item;
                    }
                }
            });
        } else {
            $precheckKeepData = '';
        }

        $precheckProductSelect = $this->precheckProductService->getPrecheckProductUnique($precheckId);
        $dataPrecheckProductSelects = $this->precheckProductService->getDataCheckUnique($precheckProductSelect['precheckProduct'], $precheckPresent->id);
        $precheckProductRegister = $this->precheckProductService->getPrecheckProductRegister($trademark->id);
        $dataNotRegister = $this->precheckProductService
            ->getDataNotRegister($precheckProductRegister['productNotRegister'], $precheckProductRegister['precheckProductRegister'])->groupBy('code_name');
        $precheckCommentUnique = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_SHIKI,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
        ])->where('admin_id', '!=', Auth::user()->id)->orderBy('created_at', 'DESC')->first();

        $precheckCommentSimilar = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
        ])->where('admin_id', '!=', Auth::user()->id)->orderBy('created_at', 'DESC')->first();

        $precheckCommentInternal = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
        ])->where('admin_id', '!=', Auth::user()->id)->orderBy('created_at', 'desc')->first();

        $precheckCommentInternalOfTantou = $this->precheckCommentService->findByCondition([
            'precheck_id' => $precheckPresent->id,
            'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
            'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
        ])->where('admin_id', '!=', Auth::user()->id)->orderBy('created_at', 'desc')->first();
        if ($precheckCommentInternalOfTantou) {
            $precheckCommentInternalOfTantou->updated_at_convert = CommonHelper::formatTime($precheckCommentInternalOfTantou->updated_at, 'Y/m/d');
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_3, $id);

        $iconPrecheck = [
            NO_REGISTED => '',
            LIKELY_TO_BE_REGISTERED => '○',
            LOOK_FORWARD_TO_REGISTERING => '△',
            LESS_LIKELY_TO_BE_REGISTERED => '▲',
            DIFFICULT_TO_REGISTER => '×'
        ];

        return view(
            'admin.modules.precheck.precheck-select.edit-check-similar',
            compact(
                'datas',
                'dataNotRegister',
                'precheckPresent',
                'dataPrecheckProductSelects',
                'precheckCommentUnique',
                'id',
                'trademarkTable',
                'precheckCommentSimilar',
                'precheckCommentInternal',
                'precheckCommentInternalOfTantou',
                'id',
                'precheckKeepData',
                'iconPrecheck'
            )
        );
    }

    /**
     * Update precheck select check similar (admin seki)
     *
     * @param $request
     * @return RedirectResponse
     */
    public function EditPrecheckSimilar(createPrecheckResultRequest $request): RedirectResponse
    {
        $precheck = $this->precheckService->findOrFail($request->precheck_id)->load('trademark');

        if ($request->submit == CREATE) {
            $this->precheckProductService->inserDataPrecheckKeepData($request);
            return redirect()->back()->with('message', __('messages.precheck.success'))->withInput();
        } else {
            $results = $request->result ?? [];
            $resultCodeNameArray = collect($results)->where('is_register_product', 1)->pluck('code_name')->unique()->toArray();

            $resultData = $request->result_data ?? [];

            foreach ($resultCodeNameArray as $codeNameArray) {
                $result = $resultData[$codeNameArray] ?? null;

                if (empty($result)) {
                    return redirect()->back()->with('error', __('messages.precheck.errors.U021_E005'));
                } elseif (empty($result['check_lock'])) {
                    return redirect()->back()->with('error', __('messages.precheck.errors.U021_E007'));
                }
            }

            $this->precheckProductService->inserDataPrecheckKeepData($request);
            $this->precheckProductService->insertDataToEditPrecheckResult($request, $precheck);

            $precheck->update([
                'is_confirm' => 1,
            ]);

            $trademark = $precheck->trademark->load('user');

            $this->noticeService->updateComment(
                Notice::FLOW_PRECHECK,
                $request->content2 ?? '',
                $trademark->id
            );
            if ($trademark) {
                $this->precheckProductService->sendNoticeOfEditPrecheckSimilar($trademark, $precheck);
                // send mail submit a21rui_shu
                $dataMail = [
                    'from_page' => A021RUI_SHU,
                    'user' => $trademark->user
                ];
                $this->mailTemplateService->sendMailRequest($dataMail, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);
            }
            return redirect()->route('admin.home')->with('message', __('messages.precheck.send_message_precheck_to_user'))->withInput();
        }
    }
}
