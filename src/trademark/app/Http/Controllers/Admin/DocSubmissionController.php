<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AgentGroup;
use App\Models\AgentGroupMap;
use App\Models\AppTrademark;
use App\Models\ComparisonTrademarkResult;
use App\Models\DocSubmission;
use App\Models\PlanDetailProduct;
use App\Models\DocSubmissionCmt;
use App\Models\MailTemplate;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Models\PlanCorrespondence;
use App\Models\Trademark;
use App\Services\Common\NoticeService;
use App\Services\Common\TrademarkTableService;
use App\Services\DocSubmissionAttachmentService;
use App\Services\DocSubmissionAttachPropertyService;
use App\Services\DocSubmissionCmtService;
use App\Services\DocSubmissionService;
use App\Services\MailTemplateService;
use App\Services\NoticeDetailBtnService;
use App\Services\NoticeDetailService;
use App\Services\PaymentService;
use App\Services\TrademarkPlanService;
use App\Services\TrademarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;
use phpseclib3\Math\PrimeField\Integer;

class DocSubmissionController extends BaseController
{
    private $request;
    private $trademarkPlanService;
    private $trademarkService;
    private $comparisonTrademarkResult;
    private $trademarkTableService;
    private $docSubmissionService;
    private $docSubmissionAttachPropertyService;
    private $docSubmissionCmtService;
    private $noticeDetailService;
    private $commonNoticeService;
    private $docSubmissionAttachmentService;
    private $paymentService;
    private $noticeDetailBtnService;
    private $mailTemplateService;

    public function __construct(
        Request $request,
        TrademarkPlanService $trademarkPlanService,
        TrademarkService $trademarkService,
        ComparisonTrademarkResult $comparisonTrademarkResult,
        TrademarkTableService $trademarkTableService,
        DocSubmissionService $docSubmissionService,
        DocSubmissionAttachPropertyService $docSubmissionAttachPropertyService,
        NoticeDetailService $noticeDetailService,
        NoticeService $commonNoticeService,
        DocSubmissionCmtService $docSubmissionCmtService,
        DocSubmissionAttachmentService $docSubmissionAttachmentService,
        PaymentService $paymentService,
        MailTemplateService $mailTemplateService,
        NoticeDetailBtnService $noticeDetailBtnService
    )
    {
        $this->request = $request;
        $this->trademarkPlanService = $trademarkPlanService;
        $this->trademarkService = $trademarkService;
        $this->comparisonTrademarkResult = $comparisonTrademarkResult;
        $this->trademarkTableService = $trademarkTableService;
        $this->docSubmissionService = $docSubmissionService;
        $this->docSubmissionAttachPropertyService = $docSubmissionAttachPropertyService;
        $this->noticeDetailService = $noticeDetailService;
        $this->commonNoticeService = $commonNoticeService;
        $this->docSubmissionCmtService = $docSubmissionCmtService;
        $this->docSubmissionAttachmentService = $docSubmissionAttachmentService;
        $this->paymentService = $paymentService;
        $this->mailTemplateService = $mailTemplateService;
        $this->noticeDetailBtnService = $noticeDetailBtnService;

        // Check permission
        if (!$this->request->type || $this->request->type != 'view') {
            $this->middleware('permission:refusal_documents.storeA205')->only(['storeA205']);
            $this->middleware('permission:refusal_documents.showA205Hiki')->only(['showA205Hiki']);
            $this->middleware('permission:refusal_documents.showA205Sashi')->only(['showA205Sashi']);
            $this->middleware('permission:refusal_documents.showA205Kakunin')->only(['showA205Kakunin']);
            $this->middleware('permission:refusal_documents.postA205Kakunin')->only(['postA205Kakunin']);
        }
        // Permission A205shu
        $this->middleware('permission:refusal_documents.showA205shu')->only(['showA205shu']);
        $this->middleware('permission:refusal_documents.saveA205Shu')->only(['saveA205Shu']);
    }

    /**
     * Get Common A205 Hosei01 Window
     *
     * @param integer $tradeMarkPlanId - trademark_plan_id
     * @return View
     */
    public function getCommonA205Hosei01Window($tradeMarkPlanId): View
    {
        $dataCommonA205Hosei01 = $this->trademarkPlanService->getCommonA205Hosei01Window($tradeMarkPlanId);

        return view('admin.modules.doc-submissions.a205.common.a205hosei01window', compact('dataCommonA205Hosei01'));
    }

    /**
     * Get Common A205 Iken02 Window
     *
     * @param integer $trademarkPlanId - trademark_plan_id
     * @return View
     */
    public function getCommonA205Iken02Window(int $docSubmissionId): View
    {
        $dataCommonA205Iken02 = $this->docSubmissionService->getDataDocSubmission($docSubmissionId);

        return view('admin.modules.doc-submissions.a205.common.a205iken02window', compact('dataCommonA205Iken02'));
    }

    /**
     * Get common a205shu02 window
     *
     * @param Request $request
     * @param $trademarkPlanId
     *
     * @return View
     */
    public function getCommonA205Shu02Window(Request $request, $trademarkPlanId): View
    {
        $inputs = $request->all();
        $docSubmissionId = $inputs['doc_submission_id'] ?? 0;
        //query common
        $oldDataCommonShu02Window = $this->docSubmissionService->getDataDocSubmission($docSubmissionId);
        $dataProductCommonA205Shu02 = $this->docSubmissionService->getDataProductCommonA205Shu02($trademarkPlanId);

        $data = [
            'oldDataCommonShu02Window' => $oldDataCommonShu02Window,
            'dataProductCommonA205Shu02' => $dataProductCommonA205Shu02,
        ];
        return view('admin.modules.doc-submissions.a205.common.a205shu02window', $data);
    }

    /**
     * Delete property data
     *
     * @param Request $request
     * @return void
     */
    public function deletePropertyData(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->all();
            if ($input['doc_submission_attach_property_id']) {
                $res = $this->docSubmissionAttachPropertyService->deletePropertyData($input['doc_submission_attach_property_id']);

                return response()->json(['status' => $res]);
            }
        }
        return response()->json(['status' => false]);
    }

    /**
     * Delete submission attachment
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteSubmissionAttachment(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->all();
            if ($input['doc_submission_attachment_id']) {
                $res = $this->docSubmissionAttachPropertyService->deleteSubmissionAttachment($input['doc_submission_attachment_id']);

                return response()->json(['status' => $res]);
            }
        }
        return response()->json(['status' => false]);
    }

    /**
     * Show A205
     *
     * @param  integer $id {comparison_trademark_result_id}?trademark_plan_id={trademark_plans.id}?doc_submission_id={doc_submission_id}
     * @param  Request $request
     * @return void
     */
    public function showA205(Request $request, int $id)
    {
        $inputs = $request->all();

        //check params comparison_trademark_result_id in query string
        $comparisonTrademarkResult = $this->comparisonTrademarkResult->find($id);
        if (empty($inputs['trademark_plan_id']) || !$comparisonTrademarkResult) {
            abort(404);
        }
        $docSubmissionId = null;
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $inputs['trademark_plan_id'],
            'plan_correspondence_id' => $comparisonTrademarkResult->planCorrespondence ? $comparisonTrademarkResult->planCorrespondence->id : 0,
        ])->first();

        //check params trademark_plan_id in query string
        if (!$trademarkPlan) {
            abort(404);
        }

        $trademarkPlanId = $trademarkPlan->id;

        //doc_submission_id not required on params
        if (!empty($inputs['doc_submission_id'])) {
            $docSubmission = $this->docSubmissionService->getDocSubmission([
                'id' => $inputs['doc_submission_id'],
                'trademark_plan_id' => $trademarkPlanId,
            ], DocSubmissionCmt::TYPE_COMMENT_OF_STEP_1);
            if (!$docSubmission) {
                //if params doc_submission_id false
                abort(404);
            }
        } else {
            //get docSubmission first
            $docSubmission = $this->docSubmissionService->getDocSubmission([
                'trademark_plan_id' => $trademarkPlanId,
                'flag_role' => DocSubmission::FLAG_ROLE_1,
                'is_reject' => DocSubmission::IS_REJECT_FALSE
            ], DocSubmissionCmt::TYPE_COMMENT_OF_STEP_1);
        }

        $trademakInfo = $this->trademarkService->getTrademarkInfo($comparisonTrademarkResult->trademark->id);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $comparisonTrademarkResult->trademark->id);

        //query common a205shu02-window
        $oldDataCommonShu02Window = null;
        $planDetailDescription = '';
        if ($docSubmission) {
            $docSubmissionId = $docSubmission->id;
            if ($docSubmission->is_reject == DocSubmission::IS_REJECT_FALSE) {
                $oldDataCommonShu02Window = $this->docSubmissionService->getDataDocSubmission($docSubmission->id);
            }
        } else {
            $planDetailDescription = $this->trademarkPlanService->getDescriptionPlanDetail($inputs['trademark_plan_id']);
        }

        $dataProductCommonA205Shu02 = $this->docSubmissionService->getDataProductCommonA205Shu02($trademarkPlanId);

        //common a205 housei01
        $dataCommonA205Hosei01 = $this->trademarkPlanService->getCommonA205Hosei01Window($trademarkPlanId);

        //internal comments old: doc_submission_cmts table
        $commentInternalComment = $this->docSubmissionCmtService->findByCondition([
            'doc_submission_id' => $docSubmissionId,
            'type' => DocSubmissionCmt::TYPE_INTERNAL_COMMENT,
            'type_comment_of_step' => DocSubmissionCmt::TYPE_COMMENT_OF_STEP_1,
        ])->first();

        //const
        $flagRole2 = DocSubmission::FLAG_ROLE_2;
        $isRejectFalse = DocSubmission::IS_REJECT_FALSE;
        $isReject = DocSubmission::IS_REJECT_TRUE;
        $isConfirmTrue = DocSubmission::IS_CONFIRM;

        //role show button submit:
        $isRoleTanto = false;
        $isRoleSeki = false;
        if (auth('admin')->user()->role == Admin::ROLE_ADMIN_TANTO) {
            $isRoleTanto = true;
        }
        if (auth('admin')->user()->role == Admin::ROLE_ADMIN_SEKI) {
            $isRoleSeki = true;
        }

        $data = [
            'trademarkPlanId' => $inputs['trademark_plan_id'],
            'docSubmissionId' => $docSubmissionId,
            'comparisonTrademarkResult' => $comparisonTrademarkResult,
            'docSubmission' => $docSubmission,
            'trademakInfo' => $trademakInfo,
            'trademarkTable' => $trademarkTable,
            'oldDataCommonShu02Window' => $oldDataCommonShu02Window,
            'dataProductCommonA205Shu02' => $dataProductCommonA205Shu02,
            'dataCommonA205Hosei01' => $dataCommonA205Hosei01,
            'commentInternalComment' => $commentInternalComment,
            'flagRole2' => $flagRole2,
            'isRejectFalse' => $isRejectFalse,
            'isConfirmTrue' => $isConfirmTrue,
            'isRoleTanto' => $isRoleTanto,
            'isRoleSeki' => $isRoleSeki,
            'isReject' => $isReject,
            'trademarkPlan' => $trademarkPlan,
            'planDetailDescription' => $planDetailDescription
        ];

        return view('admin.modules.doc-submissions.a205.a205', $data);
    }

    /**
     * Store A205
     *
     * @param  mixed $id {comparison_trademark_result_id}?trademark_plan_id={trademark_plans.id}
     * @param  Request $request
     * @return void
     */
    public function storeA205(Request $request, $id)
    {
        $inputs = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResult->find($id);
        if (empty($inputs['trademark_plan_id']) || !$comparisonTrademarkResult) {
            abort(404);
        }

        $inputs['comparison_trademark_result_id'] = $id;

        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $inputs['trademark_plan_id'],
            'plan_correspondence_id' => $comparisonTrademarkResult->planCorrespondence ? $comparisonTrademarkResult->planCorrespondence->id : 0,
        ])->first();

        //check params trademark_plan_id in query string
        if (!$trademarkPlan) {
            abort(404);
        }

        //check doc_submission_id not required on params
        if (!empty($inputs['doc_submission_id'])) {
            $docSubmission = $this->docSubmissionService->getDocSubmission([
                'id' => $inputs['doc_submission_id'],
                'trademark_plan_id' => $inputs['trademark_plan_id'],
            ]);
            if (!$docSubmission) {
                //if params doc_submission_id false
                abort(404);
            }
        } else {
            //get docSubmission first
            $docSubmission = $this->docSubmissionService->getDocSubmission([
                'trademark_plan_id' => $inputs['trademark_plan_id'],
                'flag_role' => DocSubmission::FLAG_ROLE_1,
                'is_reject' => DocSubmission::IS_REJECT_FALSE
            ]);
        }
        $inputs['doc_submission_id'] = $docSubmission ? $docSubmission->id : 0;
        $result = $this->docSubmissionService->storeDataA205($inputs);

        if ($result) {
            if ($inputs['code'] == SAVE_SUBMIT && !empty($result['params_redirect'])) {
                //redirect to a205kakunin
                return redirect()->route('admin.refusal.documents.confirm', $result['params_redirect']);
            } else {
                return redirect()->back()->with('message', __('messages.create_success'))->withInput();
            }
        }

        return redirect()->back()->with('error', __('messages.error'))->withInput();
    }

    /**
     * Show A205shu
     *
     * @param  mixed $id
     * @return void
     */
    public function showA205shu(Request $request, $id)
    {
        $params = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResult->find($id);
        if (!isset($params['trademark_plan_id']) || !isset($params['doc_submission_id']) || !$comparisonTrademarkResult) {
            abort(404);
        }

        $docSubmission = $this->docSubmissionService->getDocSubmission([
            'id' => $params['doc_submission_id'],
            'trademark_plan_id' => $params['trademark_plan_id'],
        ], DocSubmissionCmt::TYPE_COMMENT_OF_STEP_4);

        if (!$docSubmission) {
            abort(404);
        }

        //session open modal
        if ($docSubmission->is_confirm) {
            $request->session()->put('message_confirm', [
                'url' => route('admin.home'),
                'content' => __('messages.general.Common_E035'),
                'btn' => __('labels.back'),
            ]);
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $comparisonTrademarkResult->trademark->id);

        //query common
        $docSubmissionId = $params['doc_submission_id'];
        $trademarkPlanId = $params['trademark_plan_id'];
        $oldDataCommonShu02Window = null;
        if ($docSubmission && $docSubmission->is_reject == DocSubmission::IS_REJECT_FALSE) {
            $oldDataCommonShu02Window = $this->docSubmissionService->getDataDocSubmission($docSubmissionId);
        }
        //common a205 housei01
        $dataCommonA205Hosei01 = $this->trademarkPlanService->getCommonA205Hosei01Window($trademarkPlanId);

        $dataProductCommonA205Shu02 = $this->docSubmissionService->getDataProductCommonA205Shu02($trademarkPlanId);

        //internal comments old: doc_submission_cmts table
        $commentInternalComment = $this->docSubmissionCmtService->findByCondition([
            'doc_submission_id' => $docSubmissionId,
            'type' => DocSubmissionCmt::TYPE_INTERNAL_COMMENT,
            'type_comment_of_step' => DocSubmissionCmt::TYPE_COMMENT_OF_STEP_4,
        ])->first();

        return view('admin.modules.doc-submissions.a205.a205_shu', compact(
            'comparisonTrademarkResult',
            'docSubmission',
            'trademarkTable',
            'dataCommonA205Hosei01',
            'commentInternalComment',
            'dataProductCommonA205Shu02',
            'oldDataCommonShu02Window'
        ));
    }

    /**
     * Save data of a205shu
     *
     * @param Request $request
     * @param int $id
     */
    public function saveA205Shu(Request $request, int $id)
    {
        $docSubmission = null;
        $params = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResult->find($id);

        if (!isset($params['trademark_plan_id']) || !isset($params['doc_submission_id']) || !$comparisonTrademarkResult) {
            abort(404);
        }

        $params['comparison_trademark_result_id'] = $id;
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $params['trademark_plan_id'],
            'plan_correspondence_id' => $comparisonTrademarkResult->planCorrespondence ? $comparisonTrademarkResult->planCorrespondence->id : 0,
        ])->first();

        //check params trademark_plan_id in query string
        if (!$trademarkPlan) {
            abort(404);
        }

        //check doc_submission_id not required on params
        if (isset($params['doc_submission_id']) && $params['doc_submission_id']) {
            $docSubmission = $this->docSubmissionService->getDocSubmission([
                'id' => $params['doc_submission_id'],
                'trademark_plan_id' => $params['trademark_plan_id'],
            ]);
        }

        if (!$docSubmission) {
            //if params doc_submission_id false
            abort(404);
        }

        $result = $this->docSubmissionService->saveDataA205Shu($params);

        if ($result) {
            if ($params['code'] == SAVE_SUBMIT && !empty($result['params_redirect'])) {
                //redirect to a205kakunin
                return redirect()->route('admin.refusal.documents.confirm', $result['params_redirect']);
            } else {
                return redirect()->back()->with('message', __('messages.create_success'))->withInput();
            }
        }

        return redirect()->back()->with('error', __('messages.error'))->withInput();
    }

    /**
     * Show A205 Kakunin: {comparison_trademark_result_id}?trademark_plan_id={trademark_plans.id}&doc_submission_id={doc_submissions.id}
     *
     * @param  Request $request
     * @param integer $id
     * @return View
     */
    public function showA205Kakunin(Request $request, int $id): View
    {
        $inputs = $request->all();

        //check params comparison_trademark_result_id in query string
        $comparisonTrademarkResult = $this->comparisonTrademarkResult->find($id);
        if (empty($inputs['trademark_plan_id']) || empty($inputs['doc_submission_id']) || empty($inputs['s']) || !$comparisonTrademarkResult) {
            abort(404);
        }
        //check session key: from page data (A205, A205hiki, A205shu)
        $dataSession = Session::get($inputs['s']);
        if (!$dataSession || !in_array($dataSession['from_page'], [A205, A205_HIKI, A205_SHU])) {
            abort(404);
        }

        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $inputs['trademark_plan_id'],
            'plan_correspondence_id' => $comparisonTrademarkResult->planCorrespondence ? $comparisonTrademarkResult->planCorrespondence->id : 0,
        ])->first();

        //check params trademark_plan_id in query string
        if (!$trademarkPlan) {
            abort(404);
        }
        $trademarkPlanId = $trademarkPlan->id;

        //check params doc_submission_id in query string
        $dataCheck = [
            'id' => $request->doc_submission_id,
            'trademark_plan_id' => $trademarkPlanId,
        ];
        $docSubmission = $this->docSubmissionService->getDocSubmission($dataCheck);
        if (!$docSubmission) {
            abort(404);
        }
        $docSubmissionId = $docSubmission->id;
        $trademakInfo = $this->trademarkService->getTrademarkInfo($comparisonTrademarkResult->trademark->id);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $comparisonTrademarkResult->trademark->id);

        //common a205 housei01
        $dataCommonA205Hosei01 = $this->trademarkPlanService->getCommonA205Hosei01Window($trademarkPlanId);

        //query common a205shu02-window
        $oldDataCommonShu02Window = null;
        if ($docSubmission->is_reject == DocSubmission::IS_REJECT_FALSE) {
            $oldDataCommonShu02Window = $this->docSubmissionService->getDataDocSubmission($docSubmissionId);
        }

        $dataProductCommonA205Shu02 = $this->docSubmissionService->getDataProductCommonA205Shu02($trademarkPlanId);

        //role show button submit:
        $btnSubmitTanTo = false;
        $btnSubmitSeki = false;
        if (auth('admin')->user()->role == Admin::ROLE_ADMIN_TANTO) {
            $btnSubmitTanTo = true;
        }

        if (auth('admin')->user()->role == Admin::ROLE_ADMIN_SEKI) {
            $btnSubmitSeki = true;
        }

        //const
        $flagRole2 = DocSubmission::FLAG_ROLE_2;
        $isConfirmTrue = DocSubmission::IS_CONFIRM;

        //data
        $data = [
            'comparisonTrademarkResultId' => $id,
            'trademarkPlanId' => $trademarkPlanId,
            'docSubmissionId' => $docSubmissionId,
            'fromPage' => $dataSession['from_page'],
            'comparisonTrademarkResult' => $comparisonTrademarkResult,
            'docSubmission' => $docSubmission,
            'trademakInfo' => $trademakInfo,
            'trademarkTable' => $trademarkTable,
            'dataCommonA205Hosei01' => $dataCommonA205Hosei01,
            'dataProductCommonA205Shu02' => $dataProductCommonA205Shu02,
            'oldDataCommonShu02Window' => $oldDataCommonShu02Window,
            'btnSubmitTanTo' => $btnSubmitTanTo,
            'btnSubmitSeki' => $btnSubmitSeki,
            'flagRole2' => $flagRole2,
            'isConfirmTrue' => $isConfirmTrue,
            'trademarkPlanId' => $request->trademark_plan_id
        ];

        return view('admin.modules.doc-submissions.a205.a205kakunin', $data);
    }

    /**
     * Post A205 Kakunin: {comparison_trademark_result_id}?trademark_plan_id={trademark_plans.id}&doc_submission_id={doc_submissions.id}
     *
     * @param  mixed $request
     * @param  integer $id
     * @return void
     */
    public function postA205Kakunin(Request $request, $id)
    {
        $inputs = $request->all();
        //check params comparison_trademark_result_id in query string
        $comparisonTrademarkResult = $this->comparisonTrademarkResult->find($id);
        if (empty($inputs['trademark_plan_id']) || empty($inputs['doc_submission_id']) || empty($inputs['s']) || !$comparisonTrademarkResult) {
            abort(404);
        }
        $inputs['user_id'] = $comparisonTrademarkResult->trademark->user_id;
        $inputs['comparison_trademark_result_id'] = $id;

        //check session key: from page data (A205, A205hiki, A205shu)
        $dataSession = Session::get($inputs['s']);
        if (!$dataSession || !in_array($dataSession['from_page'], [A205, A205_HIKI, A205_SHU])) {
            abort(404);
        }

        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $inputs['trademark_plan_id'],
            'plan_correspondence_id' => $comparisonTrademarkResult->planCorrespondence ? $comparisonTrademarkResult->planCorrespondence->id : 0,
        ])->first();

        $inputs['trademark_id'] = $comparisonTrademarkResult->trademark_id;

        //check params trademark_plan_id in query string
        if (!$trademarkPlan) {
            abort(404);
        }
        $trademarkPlanId = $trademarkPlan->id;

        //check params doc_submission_id in query string
        $dataCheck = [
            'id' => $inputs['doc_submission_id'] ?? 0,
            'trademark_plan_id' => $trademarkPlanId,
        ];
        $docSubmission = $this->docSubmissionService->getDocSubmission($dataCheck);
        if (!$docSubmission) {
            abort(404);
        }

        $inputs['content'] = $dataSession['content'] ?? null;

        $result = $this->docSubmissionService->postA205Kakunin($inputs);
        $comparisonTrademarkResult->load(['trademark.appTrademark', 'trademark.user']);
        $appTrademark = $comparisonTrademarkResult->trademark->appTrademark;
        $user = $comparisonTrademarkResult->trademark->user ?? null;

        if ($appTrademark && $appTrademark->pack == AppTrademark::PACK_C) {
            $dataSendMail = [
                'user' => $user,
                'from_page' => A205_KAKUNIN,
            ];
            // Send mail 拒絶理由通知書：対応不要
            $this->mailTemplateService->sendMailRequest($dataSendMail, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);
        }

        if ($result) {
            if ($inputs['from_page'] == A205) {
                return redirect()->route('admin.home')->with('message', __('messages.general.Common_E034'));
            } elseif (in_array($inputs['from_page'], [A205_HIKI, A205_SHU])) {
                return redirect()->route('admin.home')->with('message', __('messages.is_confirm_a011s'));
            }
        }

        return redirect()->back()->with('error', __('messages.error'));
    }

    /**
     * Show A205 Hiki
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function showA205Hiki(Request $request, $id)
    {
        // a205hiki not working
        abort(404);

        $comparisonTrademarkResult = $this->comparisonTrademarkResult->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $docSubmission = [];
        $oldDataCommonShu02Window = null;
        $comparisonTrademarkResult->load('planCorrespondence');
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        $trademarkPlanId = null;
        if ($trademarkPlan) {
            $trademarkPlanId = $trademarkPlan->id;
        }
        $docSubmissionId = $request->doc_submission_id;
        $docSubmissionCmtDraft = null;
        if ($request->doc_submission_id && $trademarkPlanId) {
            $data = [
                'id' => $docSubmissionId,
                'trademark_plan_id' => $trademarkPlanId,
            ];
            $docSubmission = $this->docSubmissionService->getDocSubmission($data, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_3);
            if ($docSubmission) {
                if ($docSubmission->flag_role == DocSubmission::FLAG_ROLE_1) {
                    abort(404);
                }
                $docSubmissionCmtDraft = $this->docSubmissionCmtService->getDocSubmissionCmtDraft($docSubmission->id, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_3);
                $oldDataCommonShu02Window = $this->docSubmissionService->getDataDocSubmission($docSubmissionId);
            }
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $comparisonTrademarkResult->trademark->id);
        $dataCommonA205Hosei01 = $this->trademarkPlanService->getCommonA205Hosei01Window($trademarkPlanId);
        $dataProductCommonA205Shu02 = $this->docSubmissionService->getDataProductCommonA205Shu02($trademarkPlanId);
        $routeAdmin = route('admin.home');

        return view('admin.modules.doc-submissions.a205.a205hiki', compact(
            'comparisonTrademarkResult',
            'trademarkTable',
            'docSubmission',
            'dataCommonA205Hosei01',
            'oldDataCommonShu02Window',
            'dataProductCommonA205Shu02',
            'docSubmissionCmtDraft',
            'routeAdmin'
        ));
    }

    /**
     * Show A205sashi
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function showA205Sashi(Request $request, $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResult->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load('planCorrespondence');
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondences->last();
        $docSubmission = [];
        $docSubmissionCmt = null;
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        if (!$trademarkPlan) {
            abort(404);
        }
        if ($request->doc_submission_id) {
            $data = [
                'id' => $request->doc_submission_id,
                'trademark_plan_id' => $trademarkPlan->id,
            ];
            $docSubmission = $this->docSubmissionService->getDocSubmission($data, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_6);
        }
        $oldDataCommonShu02Window = null;
        if ($docSubmission) {
            $docSubmissionCmt = $this->docSubmissionCmtService->getDocSubmissionCmtDraft($docSubmission->id, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_6);
            $oldDataCommonShu02Window = $this->docSubmissionService->getDataDocSubmission($docSubmission->id);
        }
        $dataProductCommonA205Shu02 = $this->docSubmissionService->getDataProductCommonA205Shu02($trademarkPlan->id);
        $dataCommonA205Hosei01 = $this->trademarkPlanService->getCommonA205Hosei01Window($trademarkPlan->id);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $comparisonTrademarkResult->trademark->id);
        $routeAdmin = route('admin.home');

        return view('admin.modules.doc-submissions.a205.a205shashi', compact(
            'comparisonTrademarkResult',
            'docSubmission',
            'trademarkTable',
            'docSubmissionCmt',
            'routeAdmin',
            'dataProductCommonA205Shu02',
            'oldDataCommonShu02Window',
            'dataCommonA205Hosei01'
        ));
    }

    /**
     * Show A205s
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function showA205s(Request $request, $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResult->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load('planCorrespondence', 'trademark.payment');
        $trademark = $comparisonTrademarkResult->trademark;
        $payment = $this->paymentService->findByCondition([
            'trademark_id' => $trademark->id,
            'from_page_array' => [
                U210_ALERT_02,
                U201_SELECT_01,
                U201_SIMPLE,
            ],
        ])->get()
            ->filter(function ($item) {
                return $item->from_page == U210_ALERT_02
                    || in_array($item->from_page, [U201_SELECT_01, U201_SIMPLE]) && $item->extension_of_period_before_expiry != null;
            })
            ->sortByDesc('id')
            ->first();
        $noticeDetailBtn = $this->noticeDetailBtnService->getNoticeDetailBtnA205s($trademark);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $docSubmission = [];
        $docSubmissionBefore = null;
        $docSubmissionId = $request->doc_submission_id;
        $docSubmissionCmt = null;
        $dataCommonA205Hosei01 = null;
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();

        if (!$trademarkPlan) {
            abort(404);
        }
        if ($docSubmissionId) {
            $data = [
                'id' => $docSubmissionId,
                'trademark_plan_id' => $trademarkPlan->id,
            ];
            $docSubmission = $this->docSubmissionService->getDocSubmission($data, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_2);
        }
        $oldDataCommonShu02Window = null;

        if ($docSubmission) {
            $docSubmissionCmt = $this->docSubmissionCmtService->getDocSubmissionCmtDraft($docSubmission->id, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_2);
            $oldDataCommonShu02Window = $this->docSubmissionService->getDataDocSubmission($docSubmission->id);

            $docSubmissionBefore = $this->docSubmissionService->findByCondition([])
                ->where('trademark_plan_id', $trademarkPlan->id)
                ->where('id', '<', $docSubmission->id)
                ->get()->last();
        }
        $dataProductCommonA205Shu02 = $this->docSubmissionService->getDataProductCommonA205Shu02($trademarkPlan->id);
        $dataCommonA205Hosei01 = $this->trademarkPlanService->getCommonA205Hosei01Window($trademarkPlan->id);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $comparisonTrademarkResult->trademark->id);
        $routeAdmin = route('admin.home');

        return view('admin.modules.doc-submissions.a205.a205s', compact(
            'comparisonTrademarkResult',
            'docSubmission',
            'docSubmissionBefore',
            'trademarkTable',
            'docSubmissionCmt',
            'dataProductCommonA205Shu02',
            'dataCommonA205Hosei01',
            'routeAdmin',
            'oldDataCommonShu02Window',
            'payment',
            'noticeDetailBtn'
        ));
    }

    /**
     * Redirect Page
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return RedirectResponse
     */
    public function redirectPage(Request $request, $id)
    {
        $params = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResult->find($id);
        $comparisonTrademarkResult->load('trademark');
        $docSubmission = $this->docSubmissionService->find($request['doc_submission_id']);

        if (!$docSubmission) {
            return redirect()->back();
        }

        switch ($request->submit_type) {
            case A205_SHU:
                return redirect()->route('admin.refusal.documents.edit.supervisor', [
                    'id' => $id,
                    'trademark_plan_id' => $request['trademark_plan_id'],
                    'doc_submission_id' => $request['doc_submission_id'],
                ]);
            case A205_HIKI:
                return redirect()->route('admin.refusal.documents.increase', [
                    'id' => $id,
                    'trademark_plan_id' => $request['trademark_plan_id'],
                    'doc_submission_id' => $request['doc_submission_id'],
                ]);
            case A205_SASHI:
                return redirect()->route('admin.refusal.documents.reject.supervisor', [
                    'comparison_trademark_result_id' => $id,
                    'trademark_plan_id' => $request['trademark_plan_id'],
                    'doc_submission_id' => $request['doc_submission_id'],
                ]);
            case A205S:
                return redirect()->route('admin.refusal.documents.supervisor', [
                    'comparison_trademark_result_id' => $id,
                    'trademark_plan_id' => $request['trademark_plan_id'],
                    'doc_submission_id' => $request['doc_submission_id'],
                ]);
            case CREATE_A205S:
                $docSubmission->update([
                    'flag_role' => ROLE_MANAGER,
                    'is_confirm' => true,
                ]);
                $this->createDataDocSubmissionCmt($params, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_2);
                $this->createOrUpdateNoticeA205s($comparisonTrademarkResult, $params);

                return redirect()->route('admin.home')->with('message', __('messages.general.update_success'));
            case DRAFT:
                $this->createDataDocSubmissionCmt($params, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_6);

                return redirect()->back()->with('message', __('messages.general.Common_E047'));
            case CREATE_A205SHASHI:
                $docSubmission->update([
                    'is_reject' => true,
                ]);
                $this->createDataDocSubmissionCmt($params, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_5);

                $docSubmission = $docSubmission->load([
                    'docSubmissionAttachProperties.docSubmissionAttachments'
                ]);

                // Create new docsubmission
                $newDocSubmission = $this->docSubmissionService->create([
                    'trademark_plan_id' => $params['trademark_plan_id'],
                    'admin_id' => auth('admin')->user()->id,
                    'is_reject' => DocSubmission::IS_REJECT_FALSE,
                    'flag_role' => DocSubmission::FLAG_ROLE_1,
                    'is_written_opinion' => $docSubmission->is_written_opinion ?? 0,
                    'description_written_opinion' => $docSubmission->description_written_opinion ?? null,
                ]);

                $params['doc_submission_id'] = $newDocSubmission->id;
                $this->createDataDocSubmissionCmt($params, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_1);

                // Duplicate DocSubmission Data
                $docSubmissionAttachProperties = $docSubmission->docSubmissionAttachProperties;
                foreach ($docSubmissionAttachProperties as $docSubmissionAttachProperty) {
                    $newDocSubmissionAttachProperty = $docSubmissionAttachProperty->replicate();
                    $newDocSubmissionAttachProperty->doc_submission_id = $newDocSubmission->id;
                    $newDocSubmissionAttachProperty->created_at = now();
                    $newDocSubmissionAttachProperty->updated_at = now();
                    $newDocSubmissionAttachProperty->save();

                    $docSubmissionAttachments = $docSubmissionAttachProperty->docSubmissionAttachments;
                    foreach ($docSubmissionAttachments as $docSubmissionAttachment) {
                        $newDocSubmissionAttachment = $docSubmissionAttachment->replicate();
                        $newDocSubmissionAttachment->doc_submission_attach_property_id = $newDocSubmissionAttachProperty->id;
                        $newDocSubmissionAttachment->created_at = now();
                        $newDocSubmissionAttachment->updated_at = now();
                        $newDocSubmissionAttachment->save();
                    }
                }

                $this->createOrUpdateNoticeA205shashi($comparisonTrademarkResult, $params, $newDocSubmission);

                return redirect()->route('admin.home')->with('message', __('messages.general.send_to_tantou'));
            case SAVE_DRAFT_A205Hiki:
                $dataSubMissionDocCmts = [
                    'doc_submission_id' => $params['doc_submission_id'],
                    'content' => $params['content'],
                ];
                $docSubmission->update([
                    'is_written_opinion' => $params['data-submission']['is_written_opinion'] ?? 0,
                    'description_written_opinion' => $params['data-submission']['description_written_opinion'] ?? '',
                ]);
                $this->createDataDocSubmissionCmt($dataSubMissionDocCmts, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_3);
                if (isset($params['data-properties']) && $params['data-properties']) {
                    $this->docSubmissionService->saveDataSubmissionAttachProperties($params['data-properties'], $params['doc_submission_id']);
                }

                return redirect()->back()->with('message', __('messages.general.Common_E047'));
            case SUBMIT_A205Hiki:
                $dataSubMissionDocCmts = [
                    'doc_submission_id' => $params['doc_submission_id'],
                    'content' => $params['content'],
                ];
                $docSubmission->update([
                    'is_written_opinion' => $params['data-submission']['is_written_opinion'] ?? 0,
                    'description_written_opinion' => $params['data-submission']['description_written_opinion'] ?? '',
                ]);
                $this->createDataDocSubmissionCmt($params, DocSubmissionCmt::TYPE_COMMENT_OF_STEP_3);
                if (isset($params['data-properties']) && $params['data-properties']) {
                    $this->docSubmissionService->saveDataSubmissionAttachProperties($params['data-properties'], $params['doc_submission_id']);
                }
                $params['from_page'] = A205_HIKI;
                $session = $this->docSubmissionService->createSessionRedirectA205Kakunin($params);

                return redirect()->route('admin.refusal.documents.confirm', [
                    'id' => $id,
                    'trademark_plan_id' => $request['trademark_plan_id'],
                    'doc_submission_id' => $request['doc_submission_id'],
                    's' => $session
                ]);
        }
    }

    /**
     * Create Data Doc Submission Cmt
     *
     * @param  mixed $request
     * @param  mixed $step
     * @return void
     */
    public function createDataDocSubmissionCmt($request, $step)
    {
        if (isset($request['content']) && $request['content']) {
            $this->docSubmissionCmtService->updateOrCreate([
                'admin_id' => Auth::user()->id,
                'doc_submission_id' => $request['doc_submission_id'],
                'type' => DocSubmissionCmt::TYPE_INTERNAL_COMMENT,
                'type_comment_of_step' => $step,
            ], [
                'content' => $request['content'],
            ]);
        }
    }

    /**
     * Create Or Update Notice A205 shashi
     *
     * @param  mixed $comparisonTrademarkResult
     * @param  mixed $request
     * @param  mixed $newDocSubmission
     * @return void
     */
    public function createOrUpdateNoticeA205shashi($comparisonTrademarkResult, $request, $newDocSubmission)
    {
        $trademark = $comparisonTrademarkResult->trademark;
        $machingResult = $comparisonTrademarkResult->machingResult;

        //update comment notices
        $this->commonNoticeService->updateComment(
            Notice::FLOW_RESPONSE_REASON,
            $request['content'] ?? null,
            $comparisonTrademarkResult->trademark_id
        );

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '!=', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON
                    && in_array($item->notice->step, [Notice::STEP_3, Notice::STEP_4, Notice::STEP_5])) {
                    return true;
                } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                    return true;
                }
                return false;
            });

        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => now(),
            ]);
        });
        if ($comparisonTrademarkResult->planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-36);
        } else {
            $responseDeadline = $machingResult->calculateResponseDeadline(-31);
        }

        $this->commonNoticeService->sendNotice([
            'notices' => [
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'user_id' => $trademark->user_id,
                'trademark_id' => $trademark->id,
                'step' => Notice::STEP_5,
            ],
            'notice_details' => [
                // A-000top
                [
                    'type_acc' => NoticeDetail::TYPE_MANAGER,
                    'target_page' => route('admin.refusal.documents.reject.supervisor', [
                        'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $request['trademark_plan_id'],
                        'doc_submission_id' => $request['doc_submission_id'],
                    ]),
                    'redirect_page' => route('admin.refusal.documents.create', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $request['trademark_plan_id'],
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '拒絶理由通知対応：提出書類作成',
                    'response_deadline' => $responseDeadline,
                ],
                // A000anken_top
                [
                    'type_acc' => NoticeDetail::TYPE_MANAGER,
                    'target_page' => route('admin.refusal.documents.reject.supervisor', [
                        'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $request['trademark_plan_id'],
                        'doc_submission_id' => $request['doc_submission_id'],
                    ]),
                    'redirect_page' => route('admin.refusal.documents.create', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $request['trademark_plan_id'],
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '担当者　拒絶理由通知対応：提出書類作成',
                    'attribute' => '所内処理',
                    'response_deadline' => $responseDeadline,
                ],
            ],
        ]);
    }

    /**
     * Create Or Update Notice A205 shashi
     *
     * @param  mixed $comparisonTrademarkResult
     * @param  mixed $request
     * @return void
     */
    public function createOrUpdateNoticeA205s($comparisonTrademarkResult, $request)
    {
        $trademark = $comparisonTrademarkResult->trademark;
        $machingResult = $comparisonTrademarkResult->machingResult;

        //update comment notices
        $this->commonNoticeService->updateComment(
            Notice::FLOW_RESPONSE_REASON,
            $request['content'] ?? null,
            $comparisonTrademarkResult->trademark_id
        );

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '!=', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_5) {
                    return true;
                } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                    return true;
                }
                return false;
            });
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => now(),
            ]);
        });

        if ($comparisonTrademarkResult->planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-36);
            $responseDeadlineA000top = $machingResult->calculateResponseDeadline(-36);

            $responseDeadlineUser = $machingResult->calculateResponseDeadline(-37);
        } else {
            $responseDeadline = $machingResult->calculateResponseDeadline(-31);
            $responseDeadlineA000top = $machingResult->calculateResponseDeadline(-31);

            $responseDeadlineUser = $machingResult->calculateResponseDeadline(-32);
        }
        $targetPage = route('admin.refusal.documents.supervisor', [
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $request['trademark_plan_id'],
            'doc_submission_id' => $request['doc_submission_id']
        ]);
        $redirectPage = route('user.refusal_documents_confirm', [
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $request['trademark_plan_id'],
            'doc_submission_id' => $request['doc_submission_id'],
        ]);

        $this->commonNoticeService->sendNotice([
            'notices' => [
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'user_id' => $trademark->user_id,
                'trademark_id' => $trademark->id,
                'step' => Notice::STEP_5,
            ],
            'notice_details' => [
               //A-000top
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'redirect_page' => route('admin.application-detail.index', $comparisonTrademarkResult->trademark_id),
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'content' => '事務担当　拒絶理由通知対応：提出書類提出作業中',
                    'attribute' => '特許庁へ',
                    'response_deadline' => $responseDeadlineA000top,
                ],
                // A-000anken_top
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '事務担当　拒絶理由通知対応：提出書類提出作業中',
                    'attribute' => '特許庁へ',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => null,
                    'buttons' => [
                        [
                            'btn_type' => NoticeDetailBtn::BTN_CREATE_HTML,
                            'url' => route('admin.refusal.documents.supervisor', [
                                'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                                'trademark_plan_id' => $request['trademark_plan_id'],
                                'doc_submission_id' => $request['doc_submission_id'],
                                'type' => 'view'
                            ]),
                            'from_page' => A205S,
                        ],
                    ]
                ],
                // u000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '拒絶理由通知対応：提出書類確認',
                    'attribute' => null,
                    'response_deadline' => $responseDeadlineUser,
                ],
                // u000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '拒絶理由通知対応：提出書類確認',
                    'attribute' => null,
                    'response_deadline' => $responseDeadlineUser,
                ],
            ],
        ]);
    }
}
