<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Helpers\FileHelper;
use App\Models\Admin;
use App\Models\AppTrademark;
use App\Models\DocSubmission;
use App\Models\FreeHistory;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Models\PlanCorrespondence;
use App\Models\RegisterTrademark;
use App\Models\RegisterTrademarkRenewal;
use App\Models\TrademarkDocument;
use App\Models\MailTemplate;
use App\Models\MatchingResult;
use App\Services\Common\ExportService;
use App\Services\Common\NoticeService;
use App\Services\DocSubmissionService;
use App\Services\NoticeDetailBtnService;
use App\Services\NoticeDetailService;
use App\Services\RegisterTrademarkService;
use App\Services\TrademarkService;
use App\Services\TrademarkDocumentService;
use App\Services\MailTemplateService;
use App\Services\ImportService;
use App\Services\MatchingResultService;
use App\Services\XMLProcedures\Procedure;
use App\Services\XMLProcedures\ProcedureInfomation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class NoticeDetailBtnController extends BaseController
{
    private NoticeDetailBtnService $noticeDetailBtnService;
    private TrademarkDocumentService $trademarkDocumentService;
    private NoticeService $noticeService;
    private NoticeDetailService $noticeDetailService;
    private DocSubmissionService $docSubmissionService;
    private MailTemplateService $mailTemplateService;
    private RegisterTrademarkService $registerTrademarkService;
    private TrademarkService $trademarkService;
    private ExportService $exportService;
    private ImportService $importService;
    private MatchingResultService $matchingResultService;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(
        NoticeDetailBtnService   $noticeDetailBtnService,
        TrademarkDocumentService $trademarkDocumentService,
        NoticeService            $noticeService,
        DocSubmissionService     $docSubmissionService,
        NoticeDetailService      $noticeDetailService,
        MailTemplateService      $mailTemplateService,
        RegisterTrademarkService $registerTrademarkService,
        ExportService            $exportService,
        ImportService            $importService,
        TrademarkService         $trademarkService,
        MatchingResultService    $matchingResultService
    )
    {
        parent::__construct();

        $this->noticeDetailBtnService = $noticeDetailBtnService;
        $this->trademarkDocumentService = $trademarkDocumentService;
        $this->noticeService = $noticeService;
        $this->docSubmissionService = $docSubmissionService;
        $this->noticeDetailService = $noticeDetailService;
        $this->mailTemplateService = $mailTemplateService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->trademarkService = $trademarkService;
        $this->exportService = $exportService;
        $this->importService = $importService;
        $this->matchingResultService = $matchingResultService;
    }

    /**
     * Constructor
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function createHtml(Request $request, int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $noticeDetailBtn = $this->noticeDetailBtnService->find($id);
            if (empty($noticeDetailBtn)) {
                return response()->json([], CODE_ERROR_500);
            }

            // Update date_click
            $noticeDetailBtn->update(['date_click' => Carbon::now()]);
            // Handle for each from_page
            switch ($noticeDetailBtn->from_page) {
                case A000FREE_S:
                case A000FREE02:
                    $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_XML_UPLOAD,
                        'from_page' => $noticeDetailBtn->from_page,
                    ], [
                        //
                    ]);
                    break;
                case A205_HIKI:
                case A205_SHU:
                case A205S:
                    $noticeDetail = $noticeDetailBtn->noticeDetail;

                    // Update filling_date
                    $targetPage = parse_url($noticeDetail->target_page);

                    $targetPagePath = $targetPage['path'] ?? '';
                    $targetPagePath = explode('/', $targetPagePath);
                    parse_str($targetPage['query'], $params);

                    $comparisonTrademarkResultID = $targetPagePath[count($targetPagePath) - 1] ?? null;
                    $trademarkPlanID = $params['trademark_plan_id'] ?? null;
                    $docSubmissionID = $params['doc_submission_id'] ?? null;

                    if (!empty($docSubmissionID)) {
                        $docSubmission = DocSubmission::where('id', $docSubmissionID)->first();
                        if (empty($docSubmission->filing_date)) {
                            $docSubmission->update([
                                'filing_date' => now(),
                            ]);
                        }
                    }

                    $htmlUrl = $this->exportService->a205Group([
                        'comparison_trademarkResult_id' => $comparisonTrademarkResultID,
                        'trademark_plan_id' => $trademarkPlanID,
                        'doc_submission_id' => $docSubmissionID,
                    ]);

                    $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_XML_UPLOAD,
                        'from_page' => $noticeDetailBtn->from_page,
                    ], [
                        //
                    ]);
                    break;
                case FROM_PAGE_U032:
                    $noticeDetailBtn = $noticeDetailBtn->load(['trademarkDocuments', 'noticeDetail.notice']);
                    $noticeDetail = $noticeDetailBtn->noticeDetail;
                    $notice = $noticeDetail->notice->load('trademark');
                    $trademark = $notice->trademark;
                    // $trademark->update(['application_date' => Carbon::now()->format('Y-m-d')]);

                    $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_XML_UPLOAD,
                        'from_page' => $noticeDetailBtn->from_page,
                    ], [
                        //
                    ]);

                    $htmlUrl = $this->exportService->u032($trademark->id);
                    break;
                case A210Alert:
                case A210Over:
                    $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_XML_UPLOAD,
                        'from_page' => $noticeDetailBtn->from_page,
                    ], [
                        //
                    ]);

                    $noticeDetail = $noticeDetailBtn->noticeDetail;
                    $notice = $noticeDetail->notice->load('trademark');
                    $trademark = $notice->trademark;

                    if ($noticeDetailBtn->from_page == A210Over) {
                        $htmlUrl = $this->exportService->a210over($trademark);
                    } elseif ($noticeDetailBtn->from_page == A210Alert) {
                        $htmlUrl = $this->exportService->a210alert($trademark);
                    }
                    break;
                case A302:
                    $noticeDetailBtn = $noticeDetailBtn->load(['trademarkDocuments', 'noticeDetail.notice']);
                    $noticeDetail = $noticeDetailBtn->noticeDetail;
                    $notice = $noticeDetail->notice->load('trademark');
                    $trademark = $notice->trademark;
                    $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_XML_UPLOAD,
                        'from_page' => $noticeDetailBtn->from_page,
                    ]);
                    $htmlUrl = $this->exportService->a302($trademark);
                    break;
                case A302_402_5YR_KOUKI:
                    $url = $noticeDetailBtn->url;
                    if ($url) {
                        $strParam = last(explode('/', $url));
                        $id = str_replace('?type=' . VIEW, '', $strParam);
                        $registerTrademark = $this->registerTrademarkService->find($id);
                        if ($registerTrademark && !$registerTrademark->filing_date) {
                            $this->registerTrademarkService->update($registerTrademark, [
                                'filing_date' => now(),
                            ]);
                        }
                    }
                    $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_XML_UPLOAD,
                        'from_page' => $noticeDetailBtn->from_page,
                    ]);
                    $noticeDetailBtnPDF = $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                        'from_page' => $noticeDetailBtn->from_page,
                    ]);
                    session()->put(SESSION_NOTICE_A302_402_5YR_KOUKI_G, [
                        'notice_detail_btn_id' => $noticeDetailBtnPDF->id,
                    ]);
                    break;
                case A402:
                    $url = $noticeDetailBtn->url;
                    if ($url) {
                        $id = last(explode('/', $url));
                        $registerTrademark = $this->registerTrademarkService->find($id);
                        if ($registerTrademark && $registerTrademark->filing_date) {
                            $this->registerTrademarkService->update($registerTrademark, [
                                'filing_date' => Carbon::now(),
                            ]);
                        }
                    }
                    $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_XML_UPLOAD,
                        'from_page' => $noticeDetailBtn->from_page,
                    ]);
                    $noticeDetailBtnPDF = $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                        'from_page' => $noticeDetailBtn->from_page,
                    ]);
                    session()->put(SESSION_NOTICE_A402, [
                        'notice_detail_btn_id' => $noticeDetailBtnPDF->id,
                    ]);
                    break;
            }
            DB::commit();

            return response()->json([
                'data' => $noticeDetailBtn,
                'data_html' => $htmlUrl ?? null,
            ], CODE_SUCCESS_200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'messages' => __('messages.error'),
            ], CODE_ERROR_500);
        }
    }

    /**
     * Upload XML
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function uploadXML(Request $request, int $id)
    {
        $attachment = [];

        try {
            DB::beginTransaction();

            $noticeDetailBtn = $this->noticeDetailBtnService->find($id);

            if (!$request->has('s') && (empty($noticeDetailBtn) || empty($request->xml_file))) {
                CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
                return redirect()->back();
            }

            $noticeDetailBtn = $noticeDetailBtn->load(['trademarkDocuments', 'noticeDetail.notice']);
            $noticeDetail = $noticeDetailBtn->noticeDetail;
            $notice = $noticeDetail->notice;
            $trademark = $notice->trademark;
            $trademarkDocuments = $noticeDetailBtn->trademarkDocuments;

            if ($request->has('s') && $request->s) {
                $sessionData = Session::get($request->s);

                // STEP 1 click modal cancel
                if ($request->cancel) {
                    FileHelper::unlink($sessionData['url']);
                    Session::forget($request->s);

                    return response()->json([], 200);
                }

                $attachment[] = $sessionData['url'];

                // STEP 3
                $data[] = new Procedure($sessionData);
                $this->importService->saveData(collect($data), MatchingResult::IMPORT_ANKEN_TOP);

                $submissionDate = $sessionData['procedure-infomation']['submission-date']['date'];
                $submissionDateFormat = Carbon::parse($submissionDate)->format('Y-m-d');

                // STEP 3.1
                if (isset($sessionData['procedure-infomation'])
                    && isset($sessionData['procedure-infomation']['document-name'])
                    && isset($sessionData['procedure-infomation']['document-name']['text'])
                    && $sessionData['procedure-infomation']['document-name']['text'] === ProcedureInfomation::TRADEMARK_REGISTRATION_APPLICATION
                    && $notice->flow == Notice::FLOW_APP_TRADEMARK) {
                    $dataUpdateTrademark = [
                        'application_date' => $submissionDateFormat,
                    ];

                    if (isset($sessionData['procedure-infomation']) &&
                        isset($sessionData['procedure-infomation']['application-reference']) &&
                        isset($sessionData['procedure-infomation']['application-reference']['application-number'])) {
                        $exist = $this->trademarkService->findByCondition([
                            'application_number' => $sessionData['procedure-infomation']['application-reference']['application-number'],
                        ])->where('id', '!=', $trademark->id)->count();

                        if ($exist) {
                            return response()->json(['message' => '他の商標と重複する出願番号、もう一度確認してください。'], 400);
                        }
                        if ($trademark->application_number == null) {
                            $dataUpdateTrademark['application_number'] = $sessionData['procedure-infomation']['application-reference']['application-number'];
                        }
                    }

                    $trademark->update($dataUpdateTrademark);
                } else {
                    //step 3.2
                    $noticeDetail->update([
                        'completion_date' => $submissionDateFormat,
                    ]);
                }

                $this->trademarkDocumentService->create([
                    'notice_detail_btn_id' => $noticeDetailBtn->id,
                    'trademark_id' => $notice->trademark_id,
                    'name' => $sessionData['name'],
                    'url' => $sessionData['url'],
                ]);
            } elseif (count($request->xml_file) > 0) {
                // Handle Upload File and save to Trademark documents
                foreach ($request->xml_file as $file) {
                    $filePath = FileHelper::uploads($file, [], '/uploads/trademark-documents');
                    $filepath = $attachment[] = $filePath[0]['filepath'] ?? null;

                    $name = basename($filepath);

                    setlocale(LC_ALL, 'ja_JP.UTF-8');
                    $content = file_get_contents($file);
                    $content = mb_convert_encoding($content, "UTF-8", "SJIS-win");
                    $content = $this->importService->changeXMLContent($content);

                    $content = mb_convert_encoding($content, "SJIS-win", "UTF-8");
                    $fileXml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA, 'jpopc', true);
                    $array = json_decode(json_encode($fileXml), true);

                    // STEP 0 if
                    if (isset($array['procedure-infomation'])
                        && isset($array['procedure-infomation']['file-reference-id'])
                        && $array['procedure-infomation']['file-reference-id'] === $trademark->trademark_number) {
                        if (isset($array['procedure-infomation'])
                            && isset($array['procedure-infomation']['submission-date'])
                            && $array['procedure-infomation']['submission-date']) {
                            $submissionDate = $array['procedure-infomation']['submission-date']['date'] ?? null;
                            $array['name'] = $name;
                            $array['url'] = $filepath;
                            $key = Str::random(11);
                            Session::put($key, collect($array));

                            // STEP 1 if
                            if ($submissionDate != now()->format('Ymd')) {
                                return redirect()->back()->with([
                                    'message_upload' => '日付が異なりますが、大丈夫ですか？',
                                    's' => $key,
                                    'btn_notice_id' => $id,
                                ]);
                            } else {
                                // STEP 3
                                $data[] = new Procedure($array);
                                $this->importService->saveData(collect($data), MatchingResult::IMPORT_ANKEN_TOP);

                                $submissionDate = $array['procedure-infomation']['submission-date']['date'];
                                $submissionDateFormat = Carbon::parse($submissionDate)->format('Y-m-d');

                                // STEP 3.1
                                if (isset($array['procedure-infomation'])
                                    && isset($array['procedure-infomation']['document-name'])
                                    && isset($array['procedure-infomation']['document-name']['text'])
                                    && $array['procedure-infomation']['document-name']['text'] === ProcedureInfomation::TRADEMARK_REGISTRATION_APPLICATION
                                    && $notice->flow == Notice::FLOW_APP_TRADEMARK) {
                                    $dataUpdateTrademark = [
                                        'application_date' => $submissionDateFormat,
                                    ];
                                    if (isset($array['procedure-infomation']) &&
                                        isset($array['procedure-infomation']['application-reference']) &&
                                        isset($array['procedure-infomation']['application-reference']['application-number']) &&
                                        $trademark->application_number == null) {
                                        $dataUpdateTrademark['application_number'] = $array['procedure-infomation']['application-reference']['application-number'];
                                    }

                                    $trademark->update($dataUpdateTrademark);
                                } else {
                                    //STEP 3.2
                                    $noticeDetail->update([
                                        'completion_date' => $submissionDateFormat,
                                    ]);
                                }
                            }
                        }
                    } else {
                        // STEP 0 else
                        foreach ($attachment as $path) {
                            FileHelper::unlink($path);
                        }

                        return redirect()->back()->with([
                            'File_XML_err' => 'XML ファイルが正しくありません。もう一度確認してください。',
                        ]);
                    }

                    $this->trademarkDocumentService->create([
                        'notice_detail_btn_id' => $noticeDetailBtn->id,
                        'trademark_id' => $notice->trademark_id,
                        'name' => $name,
                        'url' => $filepath,
                    ]);
                }
            }

            if (count($attachment) > 0) {
                foreach ($trademarkDocuments as $trademarkDocument) {
                    FileHelper::unlink($trademarkDocument->url);
                    $trademarkDocument->delete();
                }
            }

            // Handle for each from_page
            switch ($noticeDetailBtn->from_page) {
                case A000FREE_S:
                case A000FREE02:
                    if (empty($noticeDetailBtn->date_click)) {
                        $noticeDetail->update(['completion_date' => Carbon::now()]);

                        $this->noticeUploadXMLFreeHistory($noticeDetailBtn);
                    }
                    break;
                case FROM_PAGE_U032:
                    if (empty($noticeDetailBtn->date_click)) {
                        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
                            'completion_date' => null,
                        ])->with('notice')->get()
                            ->where('notice.trademark_id', $trademark->id)
                            ->where('notice.user_id', $trademark->user_id)
                            ->whereIn('notice.flow', [Notice::FLOW_APP_TRADEMARK]);
                        $stepBeforeNotice->map(function ($item) {
                            $item->update([
                                'completion_date' => now(),
                            ]);
                        });

                        $this->noticeService->sendNotice([
                            'notices' => [
                                'trademark_id' => $notice->trademark_id,
                                'trademark_info_id' => null,
                                'user_id' => $notice->user_id,
                                'flow' => Notice::FLOW_APP_TRADEMARK,
                            ],
                            'notice_details' => [
                                [
                                    'target_id' => null,
                                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                                    'target_page' => route('admin.application-detail.index', $notice->trademark_id),
                                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                    'content' => '出願：完了',
                                    'attribute' => '特許庁から',
                                    'response_deadline' => null,
                                    'completion_date' => null,
                                    'buttons' => [
                                        [
                                            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                                            'from_page' => $noticeDetailBtn->from_page,
                                        ],
                                    ],
                                ],
                            ],
                        ]);
                    }
                    break;
                case A205_HIKI:
                case A205_SHU:
                case A205S:
                    if (empty($noticeDetailBtn->date_click)) {
                        $this->noticeUploadXMLA205Group($noticeDetailBtn);
                    }
                    break;
                case A210Alert:
                case A210Over:
                    if (empty($noticeDetailBtn->date_click)) {
                        $noticeDetail->update(['completion_date' => Carbon::now()]);

                        $trademark->load('comparisonTrademarkResults');
                        $comparisonTrademarkResult = $trademark->comparisonTrademarkResults->last();

                        $responseDeadline = $comparisonTrademarkResult->response_deadline ?? null;
                        if (!empty($responseDeadline)) {
                            $responseDeadline = Carbon::parse($responseDeadline);

                            if ($noticeDetailBtn->from_page == A210Alert) {
                                $comparisonTrademarkResult->update([
                                    'response_deadline' => $responseDeadline->addMonth(1),
                                ]);
                            } elseif ($noticeDetailBtn->from_page == A210Over) {
                                $comparisonTrademarkResult->update([
                                    'response_deadline' => $responseDeadline->addMonth(2),
                                ]);
                            }
                        }

                        $newNoticeDetail = $this->noticeDetailService->create([
                            'notice_id' => $noticeDetail->notice_id,
                            'target_id' => $noticeDetail->target_id,
                            'type_acc' => $noticeDetail->type_acc,
                            'target_page' => $noticeDetail->target_page,
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'content' => $noticeDetailBtn->from_page == A210Alert ? '事務担当　期限日前期間延長請求書提出完了' : '事務担当　期間外延長請求書提出完了',
                            'attribute' => '特許庁から',
                            'completion_date' => null,
                        ]);
                        $this->noticeDetailBtnService->updateOrCreate([
                            'notice_detail_id' => $newNoticeDetail->id,
                            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                            'from_page' => $noticeDetailBtn->from_page,
                        ], [
                            //
                        ]);
                    }
                    break;
                case A302:
                    if (empty($noticeDetailBtn->date_click)) {
                        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
                            'completion_date' => null,
                        ])->with('notice')->get()
                            ->where('notice.trademark_id', $trademark->id)
                            ->where('notice.user_id', $trademark->user_id)
                            ->whereIn('notice.flow', [Notice::FLOW_REGISTER_TRADEMARK]);
                        $stepBeforeNotice->map(function ($item) {
                            $item->update([
                                'completion_date' => now(),
                            ]);
                        });

                        $registerTrademark = $trademark->registerTrademark ?? null;

                        // Set content
                        $content = '登録査定：登録料納付完了';
                        if (!empty($registerTrademark) && $registerTrademark->isChangeStatus()) {
                            $content = '登録査定：手続補正書(内容)提出完了';
                        }

                        $jimu = Auth::user();
                        $result = $this->noticeDetailService->create([
                            'target_id' => $jimu->id,
                            'notice_id' => $noticeDetail->notice_id,
                            'type_acc' => ROLE_MANAGER,
                            'target_page' => $noticeDetail->target_page,
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'content' => $content,
                            'attribute' => '特許庁から',
                        ]);

                        $this->noticeDetailBtnService->updateOrCreate([
                            'notice_detail_id' => $result->id,
                            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                            'from_page' => $noticeDetailBtn->from_page,
                        ]);
                    }
                    break;
            }

            // Update date_click
            if (!in_array($noticeDetailBtn->from_page, [A210Over, A210Alert])) {
                $noticeDetailBtn->update(['date_click' => Carbon::now()]);
            }

            DB::commit();

            CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
            if ($request->has('s') && $request->s) {
                return response()->json([], 200);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            foreach ($attachment as $path) {
                FileHelper::unlink($path);
            }

            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Upload PDF
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function uploadPDF(Request $request, int $id): RedirectResponse
    {
        $attachment = [];

        try {
            DB::beginTransaction();

            $noticeDetailBtn = $this->noticeDetailBtnService->find($id);
            if (empty($noticeDetailBtn) || empty($request->pdf_file)) {
                CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
                return redirect()->back();
            }

            $noticeDetailBtn = $noticeDetailBtn->load(['trademarkDocuments', 'noticeDetail.notice']);
            $noticeDetail = $noticeDetailBtn->noticeDetail;
            $notice = $noticeDetail->notice;
            $trademarkDocuments = $noticeDetailBtn->trademarkDocuments;

            // Handle Upload File and save to Trademark documents
            if (count($request->pdf_file) > 0) {
                foreach ($request->pdf_file as $file) {
                    $filePath = FileHelper::uploads($file, [], '/uploads/trademark-documents');
                    $filepath = $attachment[] = $filePath[0]['filepath'] ?? null;

                    $type = null;
                    if ($noticeDetailBtn->from_page == A201A) {
                        $type = TrademarkDocument::TYPE_1;
                    } elseif ($noticeDetailBtn->from_page == A301) {
                        $type = TrademarkDocument::TYPE_5;
                    } elseif (in_array($noticeDetailBtn->from_page, [A303, A402_HOSOKU_02])) {
                        $type = TrademarkDocument::TYPE_8;
                    } elseif (in_array($noticeDetailBtn->from_page, [A700_SHUTSUGANNIN02, A700_SHUTSUGANNIN03])) {
                        $type = TrademarkDocument::TYPE_7;
                    } elseif (in_array($noticeDetailBtn->from_page, [A302_402_5YR_KOUKI, A302])) {
                        $type = TrademarkDocument::TYPE_6;
                    } elseif (in_array($noticeDetailBtn->from_page, [A402])) {
                        $type = TrademarkDocument::TYPE_9;
                    } elseif (in_array($noticeDetailBtn->from_page, [A205S, A205_SHU, A205_HIKI])) {
                        $type = TrademarkDocument::TYPE_2;
                    } elseif (in_array($noticeDetailBtn->from_page, [A700KENRISHA03])) {
                        $type = TrademarkDocument::TYPE_10;
                    } elseif (in_array($noticeDetailBtn->from_page, [A210Alert, A210Over])) {
                        $type = TrademarkDocument::TYPE_4;
                    }
                    $name = basename($filepath);
                    $this->trademarkDocumentService->create([
                        'notice_detail_btn_id' => $noticeDetailBtn->id,
                        'trademark_id' => $notice->trademark_id,
                        'type' => $type,
                        'name' => $name,
                        'url' => $filepath,
                    ]);
                }
            }

            if (count($attachment) > 0) {
                foreach ($trademarkDocuments as $trademarkDocument) {
                    FileHelper::unlink($trademarkDocument->url);
                    $trademarkDocument->delete();
                }
            }

            // Handle for each from_page
            switch ($noticeDetailBtn->from_page) {
                case A000FREE_S:
                    break;
                case A201A:
                case A303:
                case FROM_PAGE_U032:
                case A302:
                case A205S:
                case A205_HIKI:
                case A205_SHU:
                case A210Alert:
                case A210Over:
                    $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_CONTACT_CUSTOMER,
                        'from_page' => $noticeDetailBtn->from_page,
                    ], [
                        'date_click' => null,
                    ]);
                    break;
                case A700_SHUTSUGANNIN02:
                    if (empty($noticeDetailBtn->date_click)) {
                        $notice->load('trademark');
                        $trademark = $notice->trademark;
                        $trademark->load('machingResults', 'registerTrademark');
                        $machingResult = $trademark->machingResults->sortByDesc('id')->first();
                        $registerTrademark = $trademark->registerTrademark;
                        $redirectPage = route('admin.registration.document.modification.product', [
                            'id' => $machingResult->id,
                            'register_trademark_id' => $registerTrademark->id,
                        ]);
                        //Update column F|G of No.114
                        $targetPage = str_replace(request()->root(), '', $noticeDetail->target_page);
                        $stepNoticeBefore = $this->noticeDetailService->findByCondition([
                            'completion_date' => null,
                        ])->with('notice')->get()
                            ->where('notice.trademark_id', $notice->trademark_id)
                            ->where('notice.user_id', $notice->user_id)
                            ->where('notice.flow', Notice::FLOW_REGISTER_TRADEMARK)
                            ->where('type_acc', '!=', NoticeDetail::TYPE_USER);
                        $stepNoticeBefore->map(function ($item) {
                            $item->update([
                                'completion_date' => Carbon::now(),
                            ]);
                        });

                        //create notice
                        $this->noticeService->sendNotice([
                            'notices' => [
                                'trademark_id' => $notice->trademark_id,
                                'trademark_info_id' => null,
                                'flow' => Notice::FLOW_REGISTER_TRADEMARK,
                                'user_id' => $notice->user_id,
                            ],
                            'notice_details' => [
                                // A-000anken_top
                                [
                                    'target_id' => null,
                                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                                    'target_page' => $noticeDetail->target_page,
                                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                    'content' => '事務担当　登録査定：出願人住所変更届提出完了',
                                    'attribute' => '特許庁へ',
                                    'completion_date' => now(),
                                ],
                            ],
                        ]);
                    }
                    break;
                case A700_SHUTSUGANNIN03:
                    if (empty($noticeDetailBtn->date_click)) {
                        $this->noticeUploadPdfA700shutshuganin03($noticeDetailBtn);
                    }
                    break;
                case A700KENRISHA03:
                    if (empty($noticeDetailBtn->date_click)) {
                        $this->noticeUploadPdfA700kenrisha03($noticeDetailBtn);
                    }
                    break;
                case A301:
                    $noticeDetail->update([
                        'completion_date' => now(),
                    ]);

                    $this->noticeDetailBtnService->updateOrCreate([
                        'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
                        'btn_type' => NoticeDetailBtn::BTN_CONTACT_CUSTOMER,
                        'from_page' => $noticeDetailBtn->from_page,
                    ], [
                        'date_click' => null,
                    ]);
                    break;
                case A302_402_5YR_KOUKI:
                    if (empty($noticeDetailBtn->date_click)) {
                        $notice = $noticeDetail->notice;

                        //update completion_date
                        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                            'completion_date' => null,
                        ])->with('notice')->get()
                            ->where('notice.trademark_id', $notice->trademark_id)
                            ->where('notice.user_id', $notice->user_id)
                            ->where('notice.flow', Notice::FLOW_RENEWAL)
                            ->where('notice.step', Notice::STEP_1);
                        $stepBeforeNotice->map(function ($item) {
                            $item->update([
                                'completion_date' => Carbon::now(),
                            ]);
                        });
                        $jimu = Admin::where('role', ROLE_OFFICE_MANAGER)->first();

                        $noticeDetail = $this->noticeDetailService->create([
                            'target_id' => $jimu->id,
                            'notice_id' => $notice->id,
                            'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                            'target_page' => $noticeDetail->target_page,
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'content' => '後期納付手続き：商標登録料納付書提出完了',
                            'attribute' => '特許庁から',
                            'completion_date' => Carbon::now(),
                            'created_at' => Carbon::now(),
                        ]);
                        $this->noticeDetailBtnService->create([
                            'notice_detail_id' => $noticeDetail->id,
                            'btn_type' => NoticeDetailBtn::BTN_CONTACT_CUSTOMER,
                            'from_page' => A302_402_5YR_KOUKI,
                        ]);
                    }
                    break;
                case A402:
                    if (empty($noticeDetailBtn->date_click)) {
                        //update notice G No.173
                        $noticeDetailA000Ankentop = $this->noticeDetailService->update($noticeDetail, ['completion_date' => Carbon::now()]);
                        $jimu = Admin::where('role', ROLE_OFFICE_MANAGER)->first();

                        $noticeBtnNew = $this->noticeDetailService->create([
                            'notice_id' => $noticeDetailA000Ankentop->notice_id,
                            'target_id' => $jimu->id,
                            'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                            'target_page' => $noticeDetailA000Ankentop->target_page, //a402
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'content' => '事務担当　更新手続き：商標権存続期間更新登録申請書提出完了',
                            'attribute' => '特許庁から',
                            'completion_date' => Carbon::now(),
                        ]);

                        $this->noticeDetailBtnService->create([
                            'notice_detail_id' => $noticeBtnNew->id,
                            'from_page' => A402,
                            'btn_type' => NoticeDetailBtn::BTN_CONTACT_CUSTOMER,
                        ]);
                    }
                    break;
                case A402_HOSOKU_02:
                    if (empty($noticeDetailBtn->date_click)) {
                        $this->noticeUploadPdfA402Hosoku02($noticeDetailBtn);
                    }
                    break;
            }
            // Update date_click
            $noticeDetailBtn->update(['date_click' => Carbon::now()]);

            DB::commit();
            CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));

            return redirect()->back();
        } catch (\Exception $e) {
            foreach ($attachment as $path) {
                FileHelper::unlink($path);
            }

            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Send Notice each type
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function noticeUploadXMLFreeHistory(Model $noticeDetailBtn)
    {
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $targetPage = $noticeDetail->target_page;
        $explode = explode('/', $targetPage);
        $freeHistoryID = $explode[count($explode) - 1];

        $noticeOld = $noticeDetail->notice->load('trademark');
        $trademark = $noticeOld->trademark->load('freeHistories');
        $freeHistories = $trademark->freeHistories;
        $freeHistory = $freeHistories->where('id', $freeHistoryID)->first();

        if (str_contains($targetPage, 're-confirm')) {
            $targetPage = route('admin.free-history.re-confirm', $freeHistory->id);
        } else {
            $targetPage = route('admin.free-history.edit', $freeHistory->id);
        }

        $notice = [
            'flow' => Notice::FLOW_FREE_HISTORY,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // Send Notice Jimu
            [
                'notice_id' => $noticeOld->id,
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '(書類名)対応完了',
                'attribute' => '所内処理',
                'response_deadline' => $freeHistory->user_response_deadline ?? null,
                'completion_date' => now(),
            ],
        ];

        if ($noticeDetailBtn->from_page == A000FREE02) {
            $noticeDetails[0]['buttons'] = [
                [
                    'btn_type' => NoticeDetailBtn::BTN_CONTACT_CUSTOMER,
                    'from_page' => $noticeDetailBtn->from_page,
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send Notice A700shutshuganin03.
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function noticeUploadPdfA700shutshuganin03($noticeDetailBtn)
    {
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice;
        $notice->load('trademark');
        $trademark = $notice->trademark;
        $trademark->load('machingResults', 'registerTrademark');
        $machingResult = $trademark->machingResults->sortByDesc('id')->first();

        //Update column F|G of No.124
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ], ['notice'])->get()
            ->where('notice.trademark_id', $notice->trademark_id)
            ->where('notice.user_id', $notice->user_id)
            ->where('notice.flow', Notice::FLOW_REGISTER_TRADEMARK)
            ->where('type_acc', '!=', NoticeDetail::TYPE_USER);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => Carbon::now(),
            ]);
        });

        //create notice
        $createNotice = [
            'trademark_id' => $notice->trademark_id,
            'trademark_info_id' => null,
            'flow' => $notice->flow,
            'user_id' => $notice->user_id,
        ];
        if (str_contains($noticeDetail->target_page, 'register_trademark_id')) {
            $explodeTargetPage = explode('?register_trademark_id=', $noticeDetail->target_page);
            $targetPage = route('admin.registration.change-address.index', ['id' => $trademark->id, 'register_trademark_id' => $explodeTargetPage[1]]);
            $targetPage = str_replace(request()->root(), '', $targetPage);
            $redirectPage = route('admin.registration.document.modification', [
                'id' => $machingResult->id,
                'register_trademark_id' => $explodeTargetPage[1],
            ]);
            $redirectPage = str_replace(request()->root(), '', $redirectPage);
            $createNoticeDetail = [
                // A-000top
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '登録査定：【商標登録料納付書 】付属【補正書】作成',
                    'is_action' => true,
                ],
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '事務担当　登録査定：出願人住所変更届提出完了',
                    'attribute' => '特許庁へ',
                    'completion_date' => now(),
                ],
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '事務担当　登録査定：【商標登録料納付書 】付属【補正書】作成',
                    'attribute' => '所内処理',
                ],
            ];
        } else {
            $explodeTargetPage = explode('?change_info_register_id=', $noticeDetail->target_page);
            $targetPage = route('admin.registration.change-address.index', ['id' => $trademark->id, 'change_info_register_id' => $explodeTargetPage[1]]);
            $targetPage = str_replace(request()->root(), '', $targetPage);
            $createNoticeDetail = [
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '事務担当　登録査定：出願人DB変更届提出完了',
                    'attribute' => '特許庁へ',
                    'completion_date' => now(),
                ],
                // U-000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '登録査定：出願人DB変更届提出完了',
                ],
                // U-000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '登録査定：出願人DB変更届提出完了',
                ],
            ];
        }
        $this->noticeService->sendNotice([
            'notices' => $createNotice,
            'notice_details' => $createNoticeDetail,
        ]);
    }

    /**
     * Send Notice A700kenrisha03.
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function noticeUploadPdfA700kenrisha03(Model $noticeDetailBtn)
    {
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark->load('registerTrademarks');
        $registerTrademark = $trademark->registerTrademarks->last();
        $adminId = auth()->user()->id;
        // Update Notice at A-700kenrisha03 (No 157: F G)
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS]);

        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => Carbon::now(),
            ]);
        });
        if (str_contains($noticeDetail->target_page, 'change_info_register_id')) {
            $explodeTargetPage = explode('?change_info_register_id=', $noticeDetail->target_page);
            $targetPage = route('admin.update.change_address.document', ['id' => $trademark->id, 'change_info_register_id' => $explodeTargetPage[1]]);
            $contentAtop = '事務担当　登録査定：登録名義人変更届提出完了';
            $contentUtop = '登録査定：登録名義人変更届提出完了';
            $contentUAnkentop = '登録査定：登録名義人変更届提出完了';
        } elseif (str_contains($noticeDetail->target_page, 'register_trademark_id')) {
            $explodeTargetPage = explode('?register_trademark_id=', $noticeDetail->target_page);
            $explodeType = explode('&type=', $noticeDetail->target_page);
            $targetPage = route('admin.update.change_address.document', [
                'id' => $trademark->id,
                'register_trademark_id' => (int) $explodeTargetPage[1],
                'type' => (int) $explodeType[1],
            ]);
            if (str_contains($noticeDetail->target_page, 'type=1')) {
                $redirectPage = route('admin.registration.procedure-latter-period.document', ['id' => $registerTrademark->id]);
                $contentAtop = '事務担当　後期納付手続き：【商標登録料納付書】作成';
                $contentAnkentop1 = '事務担当　後期納付手続き：登録名義人変更届提出完了';
                $contentAnkentop2 = '事務担当　後期納付手続き：【商標登録料納付書】作成';
            } elseif (str_contains($noticeDetail->target_page, 'type=2')) {
                $redirectPage = route('admin.update.document.modification.product.detail', ['id' => $registerTrademark->id]);
                $contentAtop = '事務担当　更新手続き：【商標権存続期間更新登録申請書】付属【補足書】および【委任状】作成';
                $contentAnkentop1 = '事務担当　更新手続き：登録名義人の表示変更登録申請書提出完了';
                $contentAnkentop2 = '事務担当　更新手続き：【商標権存続期間更新登録申請書】付属【補足書】および【委任状】作成';
            }
            $redirectPage = str_replace(request()->root(), '', $redirectPage);
        }
        $targetPage = str_replace(request()->root(), '', $targetPage);
        $notice = [
            'trademark_id' => $trademark->id,
            'trademark_info_id' => null,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RENEWAL,
        ];
        if (str_contains($noticeDetail->target_page, 'change_info_register_id')) {
            $noticeDetails = [
                //A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => $contentAtop,
                    'attribute' => '特許庁へ',
                    'completion_date' => now(),
                ],
                //U-000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => $contentUtop,
                ],
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => $contentUAnkentop,
                ],
            ];
        } else {
            $noticeDetails = [
                //A-000top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => $contentAtop,
                ],
                //A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => $contentAnkentop1,
                    'attribute' => '特許庁へ',
                    'completion_date' => now(),
                ],
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => $contentAnkentop2,
                    'attribute' => '所内処理',
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    public function noticeUploadPdfA402Hosoku02($noticeDetailBtn)
    {
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice->load('trademark');
        $explodeUrl = explode('document/', $noticeDetailBtn->url);
        $registerTrademarkId = $explodeUrl[2];
        $registerTrademark = RegisterTrademark::query()->where('id', $registerTrademarkId)->with('trademark')->first();
        $trademark = $registerTrademark->trademark;

        $noticeStepBefore = $this->noticeDetailService->findByCondition([
            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
            'completion_date' => null,
        ])->whereHas('notice', function ($query) use ($trademark) {
            $query->where('trademark_id', $trademark->id)
                ->where('user_id', $trademark->user_id)
                ->whereIn('flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS]);
        })->get();
        $noticeStepBefore->map(function ($item) {
            $item->update([
                'completion_date' => now(),
            ]);
        });

        $jimu = Admin::where('role', ROLE_OFFICE_MANAGER)->first();

        $noticeDetails = [
            // A-000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => route('admin.update.document.modification.product.document', ['id' => $registerTrademark->id]),
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => true,
                'content' => '事務担当　更新手続き：【商標権存続期間更新登録申請書】付属【補足書】および【委任状】提出完了',
                'attribute' => '特許庁から',
                'completion_date' => now(),
            ],
            // A-000top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => route('admin.update.document.modification.product.document', ['id' => $registerTrademark->id]),
                'redirect_page' => route('admin.update.procedure.document', ['id' => $registerTrademark->id]),
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'content' => '事務担当　更新手続き：【商標権存続期間更新登録申請書】作成',
            ],
            // A-000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => route('admin.update.document.modification.product.document', ['id' => $registerTrademark->id]),
                'redirect_page' => route('admin.update.procedure.document', ['id' => $registerTrademark->id]),
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => true,
                'content' => '事務担当　更新手続き：【商標権存続期間更新登録申請書】作成',
                'attribute' => '所内処理',
            ],
        ];

        foreach ($noticeDetails as $noticeDetail) {
            $noticeDetail['target_page'] = !empty($noticeDetail['target_page']) ? str_replace(request()->root(), '', $noticeDetail['target_page']) : null;
            $noticeDetail['redirect_page'] = !empty($noticeDetail['redirect_page']) ? str_replace(request()->root(), '', $noticeDetail['redirect_page']) : null;

            $this->noticeDetailService->create($noticeDetail);
        }
    }

    /**
     * Contact Customer
     *
     * @param mixed $request
     * @param mixed $id
     * @return void
     */
    public function contactCustomer(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $noticeDetailBtn = $this->noticeDetailBtnService->find($id);

            if (empty($noticeDetailBtn)) {
                return response()->json([
                    'messages' => __('messages.error'),
                ], CODE_ERROR_500);
            }
            $dataSendMail = [];
            // Handle for each from_page
            switch ($noticeDetailBtn->from_page) {
                case FROM_PAGE_U032:
                    $noticeDetailBtn->load(['noticeDetail.notice.trademark.user']);
                    $dataSendMail = [
                        'user' => $noticeDetailBtn->noticeDetail->notice->trademark->user ?? null,
                        'from_page' => $noticeDetailBtn->from_page,
                    ];
                    // $this->sendNoticeContactCustomerA201A($noticeDetailBtn);
                    $this->sendNoticeContactCustomer($noticeDetailBtn);
                    break;
                case A201A:
                    $noticeDetailBtn->load(['noticeDetail.notice.trademark.user', 'noticeDetail.notice.trademark.appTrademark']);

                    $dataSendMail = [
                        'user' => $noticeDetailBtn->noticeDetail->notice->trademark->user ?? null,
                        'from_page' => $noticeDetailBtn->from_page,
                    ];

                    $this->sendNoticeContactCustomerA201A($noticeDetailBtn);
                    break;
                case A205S:
                    $noticeDetailBtn->load(['noticeDetail.notice.trademark.user', 'noticeDetail.notice.trademark.appTrademark']);
                    $appTrademark = $noticeDetailBtn->noticeDetail->notice->trademark->appTrademark ?? null;
                    if ($appTrademark && $appTrademark->pack == AppTrademark::PACK_C) {
                        $dataSendMail = [
                            'user' => $noticeDetailBtn->noticeDetail->notice->trademark->user ?? null,
                            'from_page' => $noticeDetailBtn->from_page,
                        ];
                    }
                    $this->sendNoticeContactCustomerA205s($noticeDetailBtn);
                    break;
                case A205_HIKI:
                case A205_SHU:
                    $noticeDetailBtn->load(['noticeDetail.notice.trademark.user', 'noticeDetail.notice.trademark.appTrademark']);
                    $appTrademark = $noticeDetailBtn->noticeDetail->notice->trademark->appTrademark ?? null;
                    if ($appTrademark && $appTrademark->pack == AppTrademark::PACK_C) {
                        $dataSendMail = [
                            'user' => $noticeDetailBtn->noticeDetail->notice->trademark->user ?? null,
                            'from_page' => $noticeDetailBtn->from_page,
                        ];
                    }
                    $this->sendNoticeContactCustomerA205Kakunin($noticeDetailBtn);
                    break;
                case A210Alert:
                case A210Over:
                    $this->sendNoticeContactCustomerA210($noticeDetailBtn);
                    break;
                case A301:
                    $dataSendMail = [
                        'user' => $noticeDetailBtn->noticeDetail->notice->trademark->user ?? null,
                        'from_page' => $noticeDetailBtn->from_page,
                    ];
                    $this->sendNoticeContactCustomerA301($noticeDetailBtn);
                    break;
                case A303:
                    $dataSendMail = [
                        'user' => $noticeDetailBtn->noticeDetail->notice->trademark->user ?? null,
                        'from_page' => $noticeDetailBtn->from_page,
                    ];
                    $this->sendNoticeContactCustomerA303($noticeDetailBtn);
                    break;
                case A302:
                    $dataSendMail = [
                        'user' => $noticeDetailBtn->noticeDetail->notice->trademark->user ?? null,
                        'from_page' => $noticeDetailBtn->from_page,
                    ];
                    $this->sendNoticeContactCustomerA302($noticeDetailBtn);
                    break;
                case A302_402_5YR_KOUKI:
                    $dataSendMail = [
                        'user' => $noticeDetailBtn->noticeDetail->notice->trademark->user ?? null,
                        'from_page' => $noticeDetailBtn->from_page,
                    ];
                    $this->sendNoticeContactCustomerA302_402_5YR_KOUKI($noticeDetailBtn);
                    break;
                case A402:
                    $dataSendMail = [
                        'user' => $noticeDetailBtn->noticeDetail->notice->trademark->user ?? null,
                        'from_page' => $noticeDetailBtn->from_page,
                    ];
                    $this->sendNoticeContactCustomerA402($noticeDetailBtn);
                    break;
                case A000FREE02:
                    $noticeDetail = $noticeDetailBtn->noticeDetail;
                    $targetPage = $noticeDetail->target_page;
                    $explode = explode('/', $targetPage);
                    $freeHistoryID = $explode[count($explode) - 1];

                    $notice = $noticeDetail->notice->load('trademark');
                    $trademark = $notice->trademark->load('freeHistories');
                    $freeHistories = $trademark->freeHistories;
                    $freeHistory = $freeHistories->where('id', $freeHistoryID)->first();
                    if ($freeHistory && $freeHistory->type == FreeHistory::TYPE_4) {
                        $dataSendMail = [
                            'user' => $noticeDetailBtn->noticeDetail->notice->trademark->user ?? null,
                            'from_page' => A000FREE02,
                        ];
                    }
                    $this->sendNoticeContactCustomerA000Free02($noticeDetailBtn);
                    break;
            }

            // Update date_click
            $noticeDetailBtn->update(['date_click' => Carbon::now()]);

            DB::commit();

            if (!empty($dataSendMail)) {
                // Send mail 出願：提出書類ご確認
                $this->mailTemplateService->sendMailRequest($dataSendMail, MailTemplate::TYPE_ANKEN_TOP, MailTemplate::GUARD_TYPE_ADMIN);
            }

            CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E030'));

            return response()->json([], CODE_SUCCESS_200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'messages' => __('messages.error'),
            ], CODE_ERROR_500);
        }
    }

    /**
     * Send Notice Contact Customer A302_402_5YR_KOUKI
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function sendNoticeContactCustomerA302_402_5YR_KOUKI(Model $noticeDetailBtn)
    {
        $noticeDetailBtn = $noticeDetailBtn->load(['noticeDetail', 'noticeDetail.notice']);
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark;

        //update completion_date
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_RENEWAL);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => Carbon::now(),
            ]);
        });

        $jimu = Admin::where('role', ROLE_OFFICE_MANAGER)->first();
        $noticeDetailData = [
            //A-000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $noticeDetail->target_page,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '事務担当　後期納付手続き：完了連絡済',
                'attribute' => 'お客様へ',
                'completion_date' => Carbon::now(),
                'created_at' => Carbon::now(),
            ],
            [
                'notice_id' => $notice->id,
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $noticeDetail->target_page,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'content' => '後期納付手続き：手続き完了',
                'created_at' => Carbon::now(),
            ],
            [
                'notice_id' => $notice->id,
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $noticeDetail->target_page,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '後期納付手続き：手続き完了',
                'created_at' => Carbon::now(),
            ],
        ];

        $noticeDetailUAnkenTop = null;
        foreach ($noticeDetailData as $data) {
            $model = $this->noticeDetailService->create($data);
            if ($model && $model->type_acc == NoticeDetail::TYPE_USER && $model->type_page == NoticeDetail::TYPE_PAGE_ANKEN_TOP) {
                $noticeDetailUAnkenTop = $model;
            }
        }

        $noticeData = session()->get(SESSION_NOTICE_A302_402_5YR_KOUKI_G);
        $newNoticeDetailBtn = $this->noticeDetailBtnService->create([
            'notice_detail_id' => $noticeDetailUAnkenTop->id, //155
            'btn_type' => null,
            'date_click' => now(),
            'from_page' => $noticeDetailBtn->from_page,
        ]);

        $pdfBtn = $this->noticeDetailBtnService->findByCondition([
            'id' => $noticeData['notice_detail_btn_id'], //153
            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
        ])->with('trademarkDocuments')->first();

        $trademarkDocuments = $pdfBtn->trademarkDocuments->toArray();
        //delete session
        session()->forget(SESSION_NOTICE_A302_402_5YR_KOUKI_G);

        foreach ($trademarkDocuments as $trademarkDocument) {
            unset($trademarkDocument['id']);
            unset($trademarkDocument['created_at']);
            unset($trademarkDocument['updated_at']);

            $trademarkDocument['notice_detail_btn_id'] = $newNoticeDetailBtn->id;
            $this->trademarkDocumentService->create($trademarkDocument);
        }
    }

    /**
     * SendNoticeContactCustomerA402
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function sendNoticeContactCustomerA402(Model $noticeDetailBtn)
    {
        $noticeDetailBtn = $noticeDetailBtn->load(['noticeDetail', 'noticeDetail.notice']);
        $noticeDetail = $noticeDetailBtn->noticeDetail;

        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark;
        $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS])
            ->map(function ($item) {
                $item->update([
                    'completion_date' => Carbon::now(),
                ]);
            });
        $jimu = Admin::where('role', ROLE_OFFICE_MANAGER)->first();

        $noticeDetailData = [
            //A-000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $noticeDetail->target_page,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '事務担当　更新手続き：完了連絡済',
                'attribute' => 'お客様へ',
                'completion_date' => Carbon::now(),
                'created_at' => Carbon::now(),
            ],
            //U-000top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $noticeDetail->target_page,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'content' => '更新手続き：手続き完了',
                'attribute' => null,
                'completion_date' => null,
                'created_at' => Carbon::now(),
            ],
            //U-000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $noticeDetail->target_page,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '更新手続き：手続き完了',
                'attribute' => null,
                'completion_date' => null,
                'created_at' => Carbon::now(),
            ],
        ];

        $noticeDetailUAnkenTop = null;
        foreach ($noticeDetailData as $data) {
            $model = $this->noticeDetailService->create($data);
            if ($model && $model->type_acc == NoticeDetail::TYPE_USER && $model->type_page == NoticeDetail::TYPE_PAGE_ANKEN_TOP) {
                $noticeDetailUAnkenTop = $model;
            }
        }

        $noticeDataSession = session()->get(SESSION_NOTICE_A402);
        //176-I
        $newNoticeDetailBtn = $this->noticeDetailBtnService->create([
            'notice_detail_id' => $noticeDetailUAnkenTop->id,
            'btn_type' => null,
            'date_click' => now(),
            'from_page' => $noticeDetailBtn->from_page,
        ]);

        $pdfBtn = $this->noticeDetailBtnService->findByCondition([
            'id' => $noticeDataSession['notice_detail_btn_id'], //174
            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
        ])->with('trademarkDocuments')->first();

        $trademarkDocuments = $pdfBtn->trademarkDocuments->toArray();
        foreach ($trademarkDocuments as $trademarkDocument) {
            unset($trademarkDocument['id']);
            unset($trademarkDocument['created_at']);
            unset($trademarkDocument['updated_at']);

            $trademarkDocument['notice_detail_btn_id'] = $newNoticeDetailBtn->id;
            $this->trademarkDocumentService->create($trademarkDocument);
        }
        //delete session
        session()->forget(SESSION_NOTICE_A402);
    }

    /**
     * Send Notice Contact Customer
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function sendNoticeContactCustomer(Model $noticeDetailBtn)
    {
        $noticeDetailBtn = $noticeDetailBtn->load(['noticeDetail', 'noticeDetail.notice']);
        $noticeDetail = $noticeDetailBtn->noticeDetail;

        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark;
        $targetPage = route('admin.application-detail.index', ['id' => $trademark->id]);
        //send notice
        $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_APP_TRADEMARK)
            ->map(function ($item) {
                $item->update([
                    'is_answer' => NoticeDetail::IS_ANSWER,
                ]);
            });

        $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
        ])->whereNull('completion_date')->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_APP_TRADEMARK)
            ->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });

        $notice = [
            'flow' => Notice::FLOW_APP_TRADEMARK,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // A-000anken_top
            [
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '出願：完了連絡済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            // U-000top
            [
                'target_id' => $noticeDetailBtn->noticeDetail->notice->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'content' => '出願：完了',
            ],
            // U-000anken_top
            [
                'target_id' => $noticeDetailBtn->noticeDetail->notice->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '出願：完了',
            ],
        ];
        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);

        // Create Document Data
        $noticeData = session(SESSION_NOTICE);
        $noticeDetailData = $noticeData['notice_detail_id'] ?? null;
        $noticeDetailData = collect($noticeDetailData);
        $noticeDetailAnkenTop = $noticeDetailData->where('type_acc', NoticeDetail::TYPE_USER)
            ->where('type_page', NoticeDetail::TYPE_PAGE_ANKEN_TOP)
            ->first();

        $newNoticeDetailBtn = $this->noticeDetailBtnService->create([
            'notice_detail_id' => $noticeDetailAnkenTop->id,
            'btn_type' => null,
            'date_click' => now(),
            'from_page' => $noticeDetailBtn->from_page,
        ]);

        $pdfBtn = $this->noticeDetailBtnService->findByCondition([
            'notice_detail_id' => $noticeDetailBtn->notice_detail_id,
            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
        ])->with('trademarkDocuments')->first();
        $trademarkDocuments = $pdfBtn->trademarkDocuments->toArray();

        foreach ($trademarkDocuments as $trademarkDocument) {
            unset($trademarkDocument['id']);
            unset($trademarkDocument['created_at']);
            unset($trademarkDocument['updated_at']);

            $trademarkDocument['notice_detail_btn_id'] = $newNoticeDetailBtn->id;
            $this->trademarkDocumentService->create($trademarkDocument);
        }
    }

    /**
     * Send Notice Contact Customer A201A
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function sendNoticeContactCustomerA201A(Model $noticeDetailBtn)
    {
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark->load([
            'appTrademark',
            'comparisonTrademarkResult.machingResult',
        ]);
        $appTrademark = $trademark->appTrademark;
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;

        // Update date_click
        $noticeDetailBtn->update(['date_click' => Carbon::now()]);

        $redirectPageU000Top = '';
        $responseDeadlineAms = null;
        if ($appTrademark->pack == AppTrademark::PACK_A || $appTrademark->pack == AppTrademark::PACK_B) {
            $redirectPageU000Top = route('user.refusal.notification.index', [
                'id' => $comparisonTrademarkResult->id,
            ]);
            $responseDeadlineAms = $comparisonTrademarkResult->user_response_deadline ?? null;
        } elseif ($appTrademark->pack == AppTrademark::PACK_C) {
            $redirectPageU000Top = route('user.refusal.plans.pack', [
                'id' => $comparisonTrademarkResult->id,
            ]);
        }

        $targetPage = route('admin.refusal-request-review', [
            'id' => $trademark->id,
            'maching_result_id' => $trademark->comparisonTrademarkResult->machingResult->id,
        ]);

        // Set response deadline
        $responseDeadline = null;
        if (!empty($comparisonTrademarkResult)) {
            $machingResult = $comparisonTrademarkResult->machingResult;
            if (!empty($machingResult)) {
                $responseDeadline = $machingResult->calculateResponseDeadline();
            }
        }

        $this->noticeService->sendNotice([
            'notices' => [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'step' => Notice::STEP_1,
            ],
            'notice_details' => [
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '拒絶理由通知対応：拒絶理由通知書連絡済',
                    'attribute' => 'お客様へ',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
                ],
                // U-000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPageU000Top,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '審査結果のお知らせ（拒絶理由通知）、今後の流れのご説明',
                    'response_deadline' => $responseDeadline,
                    'response_deadline_ams' => $responseDeadlineAms,
                ],
                // U-000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPageU000Top,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '審査結果のお知らせ（拒絶理由通知）、今後の流れのご説明',
                    'response_deadline' => $responseDeadline,
                    'response_deadline_ams' => $responseDeadlineAms,
                ],
            ],
        ]);
    }

    /**
     * Send Notice Contact Customer
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function sendNoticeContactCustomerA205s(Model $noticeDetailBtn)
    {
        $noticeDetailBtn = $noticeDetailBtn->load(['noticeDetail', 'noticeDetail.notice']);
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $noticeDetail->update(['completion_date' => now()]);
        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark;
        $trademark->load('comparisonTrademarkResult', 'payment');
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult->load('planCorrespondence.trademarkPlans', 'machingResult');
        $machingResult = $comparisonTrademarkResult->machingResult;
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $trademarkPlan = $planCorrespondence->trademarkPlans->last();
        $trademarkPlanId = $trademarkPlan->id;
        $docSubMission = $this->docSubmissionService->findByCondition(['trademark_plan_id' => $trademarkPlanId])->first();

        $targetPage = route('admin.refusal.documents.supervisor', [
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlanId,
            'doc_submission_id' => $docSubMission->id ?? '',
        ]);
        $redirectPage = route('user.refusal_documents_confirm', [
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlanId,
            'doc_submission_id' => $docSubMission->id ?? '',
        ]);

        if ($comparisonTrademarkResult->planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-36);
            $responseDeadlineUser = $machingResult->calculateResponseDeadline(-37);
        } else {
            $responseDeadline = $machingResult->calculateResponseDeadline(-31);
            $responseDeadlineUser = $machingResult->calculateResponseDeadline(-32);
        }
        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'step' => Notice::STEP_5,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // A-000anken_top
            [
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '拒絶理由通知対応：対応完了連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadline,
                'completion_date' => now(),
            ],
            // U-000top
            [
                'target_id' => $noticeDetailBtn->noticeDetail->notice->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：対応完了',
                'response_deadline' => $responseDeadlineUser,
            ],
            // U-000anken_top
            [
                'target_id' => $noticeDetailBtn->noticeDetail->notice->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '拒絶理由通知対応：対応完了',
                'response_deadline' => $responseDeadlineUser,
            ],
        ];
        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send Notice Contact Customer A205 Kakunin
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function sendNoticeContactCustomerA205Kakunin(Model $noticeDetailBtn)
    {
        $noticeDetailBtn = $noticeDetailBtn->load(['noticeDetail', 'noticeDetail.notice']);
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice->load('trademark');

        $trademark = $notice->trademark;
        $trademark->load('comparisonTrademarkResult', 'payment');
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult->load('planCorrespondence.trademarkPlans', 'machingResult');
        $machingResult = $comparisonTrademarkResult->machingResult;
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $trademarkPlan = $planCorrespondence->trademarkPlans->last();
        $trademarkPlanId = $trademarkPlan->id;
        $docSubMission = $this->docSubmissionService->findByCondition(['trademark_plan_id' => $trademarkPlanId])->first();

        // Row 97: Update notice_details no.91
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
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
                'completion_date' => Carbon::now(),
            ]);
        });

        $targetPage = $noticeDetail->target_page;
        $redirectPage = route('user.refusal_documents_confirm', [
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlanId,
            'doc_submission_id' => $docSubMission->id ?? '',
        ]);

        if ($comparisonTrademarkResult->planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-36);
        } else {
            $responseDeadline = $machingResult->calculateResponseDeadline(-31);
        }
        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'step' => Notice::STEP_5,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // A-000anken_top
            [
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '拒絶理由通知対応：対応完了連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadline,
                'completion_date' => now(),
            ],
            // U-000top
            [
                'target_id' => $noticeDetailBtn->noticeDetail->notice->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：対応完了',
                'response_deadline' => $responseDeadline,
            ],
            // U-000anken_top
            [
                'target_id' => $noticeDetailBtn->noticeDetail->notice->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '拒絶理由通知対応：対応完了',
                'response_deadline' => $responseDeadline,
            ],
        ];
        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send Notice Contact Customer A210 Alert
     *
     * @param mixed $noticeDetailBtn
     * @return void
     */
    public function sendNoticeContactCustomerA210($noticeDetailBtn)
    {
        $noticeDetailBtn = $noticeDetailBtn->load(['noticeDetail', 'noticeDetail.notice']);
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark;
        $trademark->load([
            'comparisonTrademarkResult',
            'payment',
            'registerTrademark.registerTrademarkRenewals' => function ($query) {
                return $query->where('type', RegisterTrademarkRenewal::TYPE_EXTENSION_PERIOD_BEFORE_DEADLINE);
            },
        ]);
        $registerTrademarkRenewal = $trademark->registerTrademarkRenewals->last();
        $redirectPage = route('user.refusal.notice', [
            'id' => $trademark->id,
            'register_trademark_renewal_id' => $registerTrademarkRenewal->id,
        ]);
        $targetPage = $noticeDetail->target_page;

        // Update block_by of trademark
        $trademark->update(['block_by' => null]);

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_1) {
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

        $notice = [
            'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // A-000anken_top
            [
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => $noticeDetailBtn->from_page == A210Alert ? '事務担当　期限日前期間延長請求書提出完了連絡済' : '事務担当者　期限日前期間延長請求書提出完了連絡済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            // U-000top
            [
                'target_id' => $noticeDetailBtn->noticeDetail->notice->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'content' => '期間延長のお知らせ',
            ],
            // U-000anken_top
            [
                'target_id' => $noticeDetailBtn->noticeDetail->notice->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '期間延長のお知らせ',
            ],
        ];
        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);

        $lastNoticeDetail = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'target_id' => $trademark->user_id,
            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'is_action' => NoticeDetail::IS_ACTION_TRUE,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_RESPONSE_REASON)
            ->last();

        if (!empty($lastNoticeDetail)) {
            $newNoticeDetails = [
                // U-000top
                [
                    'notice_id' => $lastNoticeDetail->notice_id,
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => null,
                    'redirect_page' => $lastNoticeDetail->redirect_page,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                    'content' => $lastNoticeDetail->content,
                    'response_deadline' => $lastNoticeDetail->response_deadline,
                ],
                // U-000anken_top
                [
                    'notice_id' => $lastNoticeDetail->notice_id,
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => null,
                    'redirect_page' => $lastNoticeDetail->redirect_page,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => $lastNoticeDetail->content,
                    'response_deadline' => $lastNoticeDetail->response_deadline,
                ],
            ];

            $this->noticeService->sendNotice([
                'notice_details' => $newNoticeDetails,
            ]);
        }
    }

    /**
     * Send Notice Contact Customer A301
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function sendNoticeContactCustomerA301(Model $noticeDetailBtn)
    {
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark->load('machingResults');
        $machingResults = $trademark->machingResults;
        $registerTrademark = $trademark->registerTrademark;
        // Update date_click
        $noticeDetailBtn->update(['date_click' => Carbon::now()]);

        // Update Notice at A-301 (No 110: G)
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([])
            ->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_REGISTER_TRADEMARK)
            ->where('type_acc', '!=', NoticeDetail::TYPE_USER)
            ->whereNull('completion_date');

        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => Carbon::now(),
            ]);
        });

        $targetPage = $noticeDetail->target_page;

        // get machingResult of targetPage
        $arrTargetPage = explode("=", $targetPage);
        $machingResult = $machingResults->where('id', $arrTargetPage[1])->first();

        $responseDeadline = $machingResult->calculateResponseDeadline();
        $responseDeadlineUser = $registerTrademark->user_response_deadline;

        $redirectPage = route('user.registration.procedure', ['id' => $arrTargetPage[1], 'register_trademark_id' => $registerTrademark->id]);
        $redirectPage = str_replace(request()->root(), '', $redirectPage);

        $jimu = Admin::where('role', ROLE_OFFICE_MANAGER)->first();
        $dataNoticeDetail = [
            // A-000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '事務担当　登録査定：お客様へ連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadline,
                'completion_date' => now(),
            ],
            // U-000top
            [
                'notice_id' => $notice->id,
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '登録査定のお知らせ・登録手続きのお申し込み',
                'response_deadline' => $responseDeadlineUser,
                'response_deadline_ams' => $responseDeadlineUser,
            ],
            // U-000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '登録査定のお知らせ・登録手続きのお申し込み',
                'response_deadline' => $responseDeadlineUser,
                'response_deadline_ams' => $responseDeadlineUser,
            ],
        ];

        $noticeDetailUAnkenTop = null;
        foreach ($dataNoticeDetail as $data) {
            $model = $this->noticeDetailService->create($data);
            if ($model && $model->type_acc == NoticeDetail::TYPE_USER && $model->type_page == NoticeDetail::TYPE_PAGE_ANKEN_TOP) {
                $noticeDetailUAnkenTop = $model;
            }
        }
        $newNoticeDetailBtn = $this->noticeDetailBtnService->create([
            'notice_detail_id' => $noticeDetailUAnkenTop->id,
            'btn_type' => null,
            'date_click' => now(),
            'from_page' => A301,
        ]);

        $pdfBtn = $this->noticeDetailBtnService->findByCondition([
            'notice_detail_id' => $noticeDetailBtn->notice_detail_id, //174
            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
        ])->with('trademarkDocuments')->first();

        $trademarkDocuments = $pdfBtn->trademarkDocuments->toArray();
        foreach ($trademarkDocuments as $trademarkDocument) {
            unset($trademarkDocument['id']);
            unset($trademarkDocument['created_at']);
            unset($trademarkDocument['updated_at']);

            $trademarkDocument['notice_detail_btn_id'] = $newNoticeDetailBtn->id;
            $this->trademarkDocumentService->create($trademarkDocument);
        }
    }

    /**
     * Send Notice Contact Customer A303
     *
     * @param mixed $noticeDetailBtn
     * @return void
     */
    public function sendNoticeContactCustomerA303($noticeDetailBtn)
    {
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark;
        $registerTrademark = $trademark->registerTrademark;
        $targetPage = $noticeDetail->target_page;
        $redirectPage = route('user.registration.notify-number', ['id' => $trademark->id, 'register_trademark_id' => $registerTrademark->id]);

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '!=', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_REGISTER_TRADEMARK);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => Carbon::now(),
            ]);
        });

        $this->noticeService->sendNotice([
            'notices' => [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_REGISTER_TRADEMARK,
            ],
            'notice_details' => [
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '登録証：送付済',
                    'completion_date' => now(),
                ],
                // U-000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '登録証発行のお知らせ',
                ],
                // U-000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '登録証発行のお知らせ',
                ],
            ],
        ]);
    }

    /**
     * Send Notice Contact Customer A303
     *
     * @param mixed $noticeDetailBtn
     * @return void
     */
    public function sendNoticeContactCustomerA302($noticeDetailBtn)
    {
        $jimu = Admin::where('role', ROLE_OFFICE_MANAGER)->first();
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $noticeDetail->notice->load('trademark');
        $notice = $noticeDetail->notice;
        $trademark = $notice->trademark;
        $machingResult = $trademark->getMatchingResultFrmDocName($notice->flow);

        $registerTrademark = $trademark->registerTrademark;

        $noticeDetails = [
            // A-000_top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $noticeDetail->target_page,
                'redirect_page' => route('admin.registration.input_number', [
                    'id' => $machingResult->id ?? 0,
                    'register_trademark_id' => $registerTrademark->id,
                ]),
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '登録証：登録情報入力',
            ],
            // A-000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $noticeDetail->target_page,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '登録査定：対応完了連絡済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $noticeDetail->target_page,
                'redirect_page' => route('admin.registration.input_number', [
                    'id' => $machingResult->id ?? 0,
                    'register_trademark_id' => $registerTrademark->id,
                ]),
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '登録証：登録情報入力',
                'attribute' => '特許庁から',
            ],
            // U-000top
            [
                'notice_id' => $notice->id,
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $noticeDetail->target_page,
                'redirect_page' => route('user.registration.document.completed', [
                    'id' => $trademark->id ?? 0,
                    'register_trademark_id' => $registerTrademark->id,
                ]),
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '登録手続き完了',
            ],
            // U-000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $noticeDetail->target_page,
                'redirect_page' => route('user.registration.document.completed', [
                    'id' => $trademark->id ?? 0,
                    'register_trademark_id' => $registerTrademark->id,
                ]),
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '登録手続き完了',
            ],

        ];

        foreach ($noticeDetails as $noticeDetail) {
            $noticeDetail['target_page'] = !empty($noticeDetail['target_page']) ? str_replace(request()->root(), '', $noticeDetail['target_page']) : null;
            $noticeDetail['redirect_page'] = !empty($noticeDetail['redirect_page']) ? str_replace(request()->root(), '', $noticeDetail['redirect_page']) : null;

            $this->noticeDetailService->create($noticeDetail);
        }
    }

    /**
     * Send Notice Upload XML A205Group
     *
     * @param Model $noticeDetailBtn
     * @return void
     */
    public function noticeUploadXMLA205Group(Model $noticeDetailBtn)
    {
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark->load([
            'comparisonTrademarkResults.machingResult',
            'comparisonTrademarkResults.planCorrespondence',
        ]);
        $comparisonTrademarkResults = $trademark->comparisonTrademarkResults;

        $targetPage = $noticeDetail->target_page;
        $targetPage = explode('/', $targetPage);
        $targetPage = explode('?', $targetPage[count($targetPage) - 1]);

        $comparisonTrademarkResultID = $targetPage[0] ?? null;
        $comparisonTrademarkResult = $comparisonTrademarkResults->where('id', $comparisonTrademarkResultID)->first();
        $machingResult = $comparisonTrademarkResult->machingResult;
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        $responseDeadline = $machingResult->calculateResponseDeadline(-31);
        if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-36);
        }

        //send notice
        $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '!=', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $notice->trademark_id)
            ->where('notice.user_id', $notice->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_5) {
                    return true;
                } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                    return true;
                }
                return false;
            })
            ->map(function ($item) {
                $item->update([
                    'completion_date' => Carbon::now(),
                ]);
            });

        $this->noticeService->createNoticeDetail([
            'notice_id' => $notice->id,
            'target_id' => null,
            'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
            'target_page' => $noticeDetail->target_page,
            'redirect_page' => null,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
            'content' => '拒絶理由通知対応：対応完了',
            'attribute' => '特許庁から',
            'response_deadline' => $responseDeadline,
            'completion_date' => null,
            'buttons' => [
                [
                    'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                    'from_page' => $noticeDetailBtn->from_page,
                ],
            ],
        ]);
    }

    /**
     * Send notice when click お客様へ表示 on a000anken_top
     *
     * @param Model $noticeDetailBtn
     */
    public function sendNoticeContactCustomerA000Free02(Model $noticeDetailBtn)
    {
        $noticeDetail = $noticeDetailBtn->noticeDetail;
        $notice = $noticeDetail->notice->load('trademark');
        $trademark = $notice->trademark->load([
            'comparisonTrademarkResults.machingResult',
            'comparisonTrademarkResults.planCorrespondence',
        ]);

        // Update old notice
        $noticeBefore = $this->noticeDetailService->findByCondition([
            'flow' => Notice::FLOW_FREE_HISTORY,
            'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
            'trademark_id' => $trademark->id,
        ])->whereNull('completion_date')
            ->get();

        $noticeBefore->map(function ($item) {
            $item->update([
                'completion_date' => Carbon::now(),
            ]);
        });
        $jimu = Admin::where('role', Admin::ROLE_ADMIN_JIMU)->first();

        $noticeDetails = [
            // Send notice for jimu
            // A000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $jimu->id,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $noticeDetail->target_page,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '(書類名)対応連絡',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            // u000top
            [
                'notice_id' => $notice->id,
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $noticeDetail->target_page,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'content' => '(書類名)対応完了',
            ],
            // U000anken_top
            [
                'notice_id' => $notice->id,
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => str_replace(request()->root(), '', route('admin.application-detail.index', $trademark->id)),
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '出願：完了',
            ],
        ];
        foreach ($noticeDetails as $noticeDetail) {
            $this->noticeService->createNoticeDetail($noticeDetail);
        }
    }
}
