<?php

namespace App\Services;

use App\Models\AppTrademark;
use App\Models\RegisterTrademark;
use App\Models\Notice;
use App\Models\Trademark;
use App\Models\NoticeDetail;
use App\Models\MatchingResult;
use App\Models\NoticeDetailBtn;
use App\Services\BaseService;
use App\Services\Common\NoticeService;
use App\Repositories\TrademarkRepository;
use App\Repositories\MatchingResultRepository;
use App\Repositories\MTApplicantArticleRepository;
use App\Services\XMLProcedures\Procedure;
use App\Services\XMLProcedures\ProcedureInfomation as PI;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportService extends BaseService
{
    protected NoticeService $noticeService;
    protected TrademarkRepository $trademarkRepository;
    protected MatchingResultRepository $matchingResultRepository;
    protected MTApplicantArticleRepository $mtApplicantArticleRepository;

    /**
     * Initializing the instances and variables
     *
     * @param     NoticeService $NoticeService
     * @param     TrademarkRepository $tradeMarkRepository
     * @param     MatchingResultRepository $matchingResultRepository
     * @param     MTApplicantArticleRepository $mtApplicantArticleRepository
     */
    public function __construct(
        NoticeService $noticeService,
        TrademarkRepository $trademarkRepository,
        MatchingResultRepository $matchingResultRepository,
        MTApplicantArticleRepository $mtApplicantArticleRepository
    )
    {
        $this->noticeService = $noticeService;
        $this->trademarkRepository = $trademarkRepository;
        $this->matchingResultRepository = $matchingResultRepository;
        $this->mtApplicantArticleRepository = $mtApplicantArticleRepository;
    }

    /**
     * Append tag for xml content.
     *
     * @param string content.
     */
    public function changeXMLContent($content): string
    {
        try {
            $regularExs = [
                ['text' => 'text', 'regex' => '/<jpopc:document-name.+>(.+)<\/jpopc:document-name>/'],
                ['text' => 'type', 'regex' => '/<jpopc:document-type .+>(.+)<\/jpopc:document-type>/'],
                ['text' => 'number', 'regex' => '/<jpopc:account-number .+>(.+)<\/jpopc:account-number>/'],
                ['text' => 'text', 'regex' => '/<jpopc:software-message .+>(.+)<\/jpopc:software-message>/'],
            ];
            foreach ($regularExs as $key => $val) {
                preg_match_all($val['regex'], $content, $matches, PREG_SET_ORDER, 1);
                if (is_array($matches) && isset($matches[0]) && $matches[0]) {
                    $content = preg_replace('/' . $matches[0][1] . '/u', '<jpopc:' . $val['text'] . '>' . $matches[0][1] . '</jpopc:' . $val['text'] . '>', $content);
                }
            }

            return $content;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get content from xml file.
     *
     * @param string $filePath
     * @return string.
     */
    public function getContentFileXML(string $filePath): ?string
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        // Read the file content in SJIS-Win.
        $content = file_get_contents($filePath);
        // Convert file content to SJIS-Win.
        $content = mb_convert_encoding($content, "UTF-8", "SJIS-win");

        return $content;
    }

    /**
     * Get Data Import
     *
     * @param  mixed $sessionData
     * @return array
     */
    public function getDataImport($sessionData): array
    {
        try {
            $trademark = null;
            $dataView = [];
            $dataNull = [];
            $trademarkDuplicate = [];
            $trademarkClose = [];
            $duplicates = [];
            if ($sessionData && $sessionData->count()) {
                $trademarkNums = [];
                $referenceIds = $sessionData->pluck('procedure_infomation.application_reference.reference_id');

                foreach ($referenceIds as $key => $referenceId) {
                    if ($referenceId != null && $referenceId != '') {
                        array_push($trademarkNums, "'" . $referenceId . "'");
                    }
                }
                $trademarkNums = implode(',', $trademarkNums);

                // Get all trademark have status is closed
                $prepareTrademarkClose = DB::select('
                    SELECT tm.id,
                        rtm.period_registration,
                        rtm.date_register,
                        tm.trademark_number,
                        tm.application_number,
                        tm.name_trademark,
                        tm.type_trademark,
                        tm.reference_number,
                        tm.status_management,
                        atm.is_cancel,
                        ctr.response_deadline,
                        ADDDATE(rtm.date_register, INTERVAL 5 YEAR) AS register_date_5_year,
                        ADDDATE(rtm.date_register, INTERVAL 10 YEAR) AS register_date_10_year
                    FROM trademarks AS tm
                        LEFT JOIN app_trademarks as atm ON tm.id = atm.trademark_id
                        LEFT JOIN comparison_trademark_results AS ctr ON tm.id = ctr.trademark_id
                        LEFT JOIN register_trademarks AS rtm ON tm.id = rtm.trademark_id
                        WHERE ' . (!empty($trademarkNums) ? 'tm.trademark_number IN (' . $trademarkNums . ') AND (' : '') . '
                        tm.status_management = ' . Trademark::TRADEMARK_STATUS_NOT_MANAGEMENT . '
                        OR atm.is_cancel = ' . AppTrademark::IS_CANCEL_TRUE . '
                        OR (ctr.response_deadline IS NOT NULL AND ctr.response_deadline < NOW())
	                    OR (rtm.date_register IS NOT NULL
                            AND rtm.period_registration IS NOT NULL
                            AND IF(
                                rtm.period_registration = ' . RegisterTrademark::PERIOD_REGISTRATION_5_YEAR . ',
                                ADDDATE(rtm.date_register, INTERVAL 5 YEAR),
                                ADDDATE(rtm.date_register, INTERVAL 10 YEAR)
                                ) < NOW()
                            )' . (!empty($trademarkNums) ? ')' : '') . '
                ');

                $prepareTrademarkClose = collect($prepareTrademarkClose);

                foreach ($sessionData as $key => $procedure) {
                    // Data File Xml All (Table 1)
                    $referenceId = $procedure->procedure_infomation->getReferenceId();
                    $appNumber = $procedure->procedure_infomation->getAppNumber();

                    if ($referenceId && $appNumber) {
                        $trademark = $this->trademarkRepository->findByCondition([
                            'trademark_number' => $referenceId,
                        ])->first();
                    }

                    $dataView[$key] = [
                        'document_name' => $procedure->procedure_infomation->getDocumentName(),
                        'application_number' => $procedure->procedure_infomation->getAppNumber(),
                        'date' => $procedure->procedure_infomation->getDispatchDate(),
                        'reference_id' => $referenceId,
                        'trademark_id' => $trademark->id ?? '',
                        'dispatch_number' => $procedure->procedure_infomation->getDispatchNumber(),
                    ];

                    // Data File Xml (Table 2)
                    if ((!$referenceId && !$appNumber) || ($referenceId && $appNumber && !$trademark)) {
                        $dataNull[$key] = [
                            'document_name' => $procedure->procedure_infomation->getDocumentName(),
                            'reference_id' => $referenceId,
                            'date' => $procedure->procedure_infomation->getDispatchDate(),
                            'application_number' => $procedure->procedure_infomation->getAppNumber(),
                        ];
                    }

                    //Data File Xml For Trademark Close (Table 3)
                    if ($prepareTrademarkClose && $prepareTrademarkClose->count()) {
                        $exist = $prepareTrademarkClose->where('trademark_number', $referenceId)->first();
                        if (!empty($exist)) {
                            $trademarkClose[$key]['trademark_id'] = $exist->id;
                            $trademarkClose[$key]['document_name'] = $procedure->procedure_infomation->getDocumentName();
                            $trademarkClose[$key]['application_number'] = $procedure->procedure_infomation->getAppNumber();
                            $trademarkClose[$key]['date'] = $procedure->procedure_infomation->getDispatchDate();
                            $trademarkClose[$key]['reference_id'] = $procedure->procedure_infomation->getReferenceId();
                            $trademarkClose[$key]['dispatch_number'] = $procedure->procedure_infomation->getDispatchNumber();
                        }
                    }

                    $exists = $this->matchingResultRepository->findByCondition([
                        // 'pi_document_code' => $procedure->procedure_infomation->document_name['document_code'],
                        // 'pi_file_reference_id' => $procedure->procedure_infomation->file_reference_id ?? '',
                        // 'pi_ip_date' => $procedure->procedure_infomation->input_date['date'] ?? ''
                        'pi_dispatch_number' => $procedure->procedure_infomation->dispatch_number ?? null,
                    ])->first();
                    if ($exists) {
                        $duplicates[$procedure->procedure_infomation->dispatch_number] = $procedure;
                    }
                }

                // Data File Xml For Trademark duplicate (Table 4)
                $prepareDuplicates = $sessionData->groupBy('procedure_infomation.dispatch_number');
                foreach ($prepareDuplicates as $key => $items) {
                    if ($key != null && $key != '' && $items->count() > 1) {
                        $duplicates[$key] = $items;
                    }
                }
            }

            foreach (collect($duplicates)->flatten() as $key => $procedure) {
                $matchingResult = $this->matchingResultRepository->findByCondition([
                    'pi_dispatch_number' => $procedure->procedure_infomation->dispatch_number ?? null,
                ])->first();

                $trademarkDuplicate[$key]['trademark_id'] = $matchingResult->trademark_id ?? '';
                $trademarkDuplicate[$key]['document_name'] = $procedure->procedure_infomation->getDocumentName();
                $trademarkDuplicate[$key]['application_number'] = $procedure->procedure_infomation->getAppNumber();
                $trademarkDuplicate[$key]['date'] = $procedure->procedure_infomation->getDispatchDate();
                $trademarkDuplicate[$key]['reference_id'] = $procedure->procedure_infomation->getReferenceId();
                $trademarkDuplicate[$key]['dispatch_number'] = $procedure->procedure_infomation->getDispatchNumber();
            }

            $data = [
                'dataView' => $dataView,
                'dataNull' => $dataNull,
                'trademarkClose' => $trademarkClose,
                'trademarkDuplicate' => $trademarkDuplicate,
            ];

            return $data;
        } catch (\Exception $e) {
            Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Save data from import xml.
     *
     * @param Collection $data
     */
    public function saveData(Collection $data, int $type = 1): bool
    {
        try {
            $admin = \Auth::guard('admin')->user();
            DB::beginTransaction();
            foreach ($data as $procedure) {
                $appNumber = $procedure->procedure_infomation->getAppNumber() ?? 0;
                // Find trademark with reference id from xml data
                $trademark = $this->trademarkRepository->findByCondition(['application_number' => $appNumber])->first();

                if ($type == MatchingResult::IMPORT_ANKEN_TOP) {
                    $fileReferenceId = $procedure->procedure_infomation->getFileReferenceId() ?? 0;
                    $trademark = $this->trademarkRepository->findByCondition(['trademark_number' => $fileReferenceId])->first();
                }

                if ($procedure->procedure_infomation->dispatch_date != null) {
                    $dispatchDate = Carbon::createFromFormat('Ymd', $procedure->procedure_infomation->dispatch_date['date'])->format('Y-m-d');
                    $dispatchTime = Carbon::createFromFormat('His', $procedure->procedure_infomation->dispatch_date['time'])->format('H:i:s');
                }
                $representationImg = $procedure->procedure_infomation->getRepresentationImg();
                $timeResponse = $procedure->procedure_infomation->getTimeResponse();
                $dataUpdate = [
                    'trademark_id' => $trademark->id ?? null,
                    'admin_id' => $admin->id,
                    'document_type' => $procedure->document_type['type'] ?? '',
                    'unconfirmed_state' => (int) $procedure->document_type['unconfirmed_state'] ?? '',
                    'computer_name' => $procedure->computer_name ?? '',
                    'user_name' => $procedure->user_name ?? '',
                    'distinction_number' => $procedure->distinction_number ?? '',
                    'rf_input_check_result' => $procedure->relation_file->input_check_result ?? '',
                    'rf_application_receipt_list' => $procedure->relation_file->application_receipt_list ?? '',
                    'pi_result_software_message' => $procedure->procedure_infomation->result->software_message['text'] ?? '',
                    'pi_result_level' => $procedure->procedure_infomation->result->software_message['level'] ?? null,
                    'pi_result_communication_result' => $procedure->procedure_infomation->result->communication_result ?? '',
                    'pi_result_fd_and_cdr' => $procedure->procedure_infomation->result->fd_and_cdr ?? '',
                    'pi_law' => $procedure->procedure_infomation->law ?? '',
                    'pi_document_name' => $procedure->procedure_infomation->document_name['text'] ?? '',
                    'pi_document_code' => $procedure->procedure_infomation->document_name['document_code'] ?? '',
                    'pi_file_reference_id' => $procedure->procedure_infomation->file_reference_id ?? '',
                    'pi_invention_title' => $procedure->procedure_infomation->invention_title ?? '',
                    'pi_ar_registration_number' => $procedure->procedure_infomation->application_reference->registration_number ?? '',
                    'pi_ar_application_number' => $procedure->procedure_infomation->getAppNumber() ?? '',
                    'pi_ar_application_date' => $procedure->procedure_infomation->application_reference->application_date->date ?? '',
                    'pi_ar_international_application_number' => $procedure->procedure_infomation->application_reference->international_application_number ?? '',
                    'pi_ar_international_application_date' => $procedure->procedure_infomation->application_reference->international_application_date->date ?? '',
                    'pi_ar_reference_id' => $procedure->procedure_infomation->getReferenceId(),
                    'pi_ar_appeal_reference_number' => $procedure->procedure_infomation->application_reference->appeal_reference_number ?? '',
                    'pi_ar_appeal_reference_date' => $procedure->procedure_infomation->application_reference->appeal_reference_date->date ?? '',
                    'pi_ar_number_of_annexation' => $procedure->procedure_infomation->application_reference->number_of_annexation ?? '',
                    'pi_sd_date' => $procedure->procedure_infomation->submission_date->date ?? '',
                    'pi_sd_time' => $procedure->procedure_infomation->submission_date->time ?? '',
                    'pi_page' => $procedure->procedure_infomation->page ?? '',
                    'pi_image_total' => $procedure->procedure_infomation->image_total ?? '',
                    'pi_size' => $procedure->procedure_infomation->size ?? '',
                    'pi_receipt_number' => $procedure->procedure_infomation->receipt_number ?? '',
                    'pi_wad_message_digest_compare' => $procedure->procedure_infomation->wad_message_digest_compare ?? '',
                    'pi_ip_date' => $procedure->procedure_infomation->input_date['date'] ?? '',
                    'pi_ip_time' => $procedure->procedure_infomation->input_date['time'] ?? '',
                    'pi_html_file_name' => $procedure->procedure_infomation->html_file_name ?? '',
                    'pi_aa_total' => $procedure->procedure_infomation->applicant_article->total ?? '',
                    'pi_claims_total' => json_encode($procedure->procedure_infomation->claims_total ?? ''),
                    'pi_abstract' => $procedure->procedure_infomation->abstract ?? '',
                    'pi_payment_account_number' => $procedure->procedure_infomation->payment->account_number->number ?? '',
                    'pi_payment_fee_code' => $procedure->procedure_infomation->payment->account_number['fee_code'] ?? '',
                    'pi_payment_amount' => $procedure->procedure_infomation->payment->amount ?? '',
                    'pi_ri_tile' => $representationImg['title'] ?? '',
                    'pi_ri_file_name' => $representationImg['file_name'] ?? '',
                    'pi_tfr_division' => $timeResponse['division'] ?? '',
                    'pi_tfr_period' => $timeResponse['period'] ?? '',
                    'pi_dispatch_number' => $procedure->procedure_infomation->dispatch_number ?? '',
                    'pi_dd_date' => $dispatchDate ?? '',
                    'pi_dd_time' => $dispatchTime ?? '',
                    'import_type' => $type ?? '',
                ];

                // Create record matching result with data from XML
                $matchingResult = $this->matchingResultRepository->create($dataUpdate);

                $applicantArticles = $procedure->procedure_infomation->applicant_article->applicant ?? [];
                if ($applicantArticles && count($applicantArticles)) {
                    foreach ($applicantArticles as $key => $appArticle) {
                        $this->mtApplicantArticleRepository->create([
                            'maching_result_id' => $matchingResult->id,
                            'applicant_division' => $appArticle->division ?? '',
                            'applicant_identification_number' => $appArticle->identification_number ?? '',
                            'applicant_name' => $appArticle->name ?? '',
                        ]);
                    }
                }

                if ($trademark) {
                    // Updating the trademark table with the application number.
                    if (empty($trademark->application_number)) {
                        $trademark->update([
                            'application_number' => $matchingResult->pi_ar_application_number,
                        ]);
                    }

                    if (empty($trademark->application_date) && !empty($matchingResult->pi_ar_application_date)) {
                        $trademark->update([
                            'application_date'  => $matchingResult->pi_ar_application_date,
                        ]);
                    }

                    if ($type != MatchingResult::IMPORT_ANKEN_TOP) {
                        $this->sendNotification($procedure, $trademark, $matchingResult);
                    }
                } else {
                    Log::warning('Trademark is not exist. So it is not send notice!');
                }
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            return false;
        }
    }

    /**
     * Send notification.
     *
     * @param Procedure $procedure
     * @param Trademark $trademark
     * @param MatchingResult $matchingResult
     * @return void
     */
    protected function sendNotification(Procedure $procedure, ?Trademark $trademark, MatchingResult $matchingResult): void
    {
        try {
            $trademark->load('appTrademark');
            $appTrademark = $trademark->appTrademark;
            $lastTrademarkInfos = null;

            if (!empty($appTrademark)) {
                $trademarkInfos = $appTrademark->trademarkInfo;
                if ($trademarkInfos->count()) {
                    $lastTrademarkInfos = $trademarkInfos->last();
                } else {
                    throw new \Exception('Can not find trademark info. Please check data again!');
                }
            }

            $documentName = $procedure->procedure_infomation->getDocumentName() ?? '';
            switch ($documentName) {
                case PI::REGISTRATION_ASSESSMENT:
                    $this->sendNoticeRegistration($procedure, $trademark, $lastTrademarkInfos, $matchingResult);
                    break;
                case PI::NOTIFICATION_REASONS_REFUSAL:
                    $this->sendNoticeReasonsRefusal($procedure, $trademark, $lastTrademarkInfos, $matchingResult);
                    break;
                case PI::DECISION_REFUSAL:
                    $trademark->update([
                        'is_refusal' => IS_REFUSAL,
                    ]);
                    $this->sendNoticeDecisionRefusal($procedure, $trademark, $lastTrademarkInfos, $matchingResult);
                    break;
                case PI::NOTICE:
                case PI::CORRECTION_ORDER:
                case PI::FILE_CORRECTION_NOTICE:
                    $this->sendNoticeNAF($procedure, $trademark, $lastTrademarkInfos, $matchingResult);
                    break;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send notification with type Notice | Amendment Notice | File Correction Notice
     * NAF: Notice | Amendment Notice | File Correction Notice
     *
     * @param Procedure $procedure
     * @param Trademark $trademark
     * @param MatchingResult $matchingResult
     * @return void
     */
    public function sendNoticeNAF(
        $procedure,
        $trademark,
        $lastTrademarkInfos,
        $matchingResult
    ): void
    {
        try {
            $admin = \Auth::guard('admin')->user();
            $timeResponse = $procedure->procedure_infomation->getTimeResponse();
            $responseDeadline = $procedure->procedure_infomation->convertDispatchDate()->addDays($timeResponse['period'])->format('Y-m-d H:i:s');
            $notice = [
                'flow' => Notice::FLOW_FREE_HISTORY,
                'trademark_id' => $trademark->id,
                'user_id' => $trademark->user_id,
                'trademark_info_id' => $lastTrademarkInfos->id ?? null,
            ];

            $targetPage = route('admin.import-doc-xml');
            $redirectPage = route('admin.free-history.create', ['id' => $trademark->id, 'maching_result_id' => $matchingResult->id]);
            $noticeDetails = [
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => 'フリー履歴追加',
                    'attribute' => null,
                ],
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => 'フリー履歴追加',
                    'attribute' => '特許庁から',
                    'response_deadline' => $responseDeadline,
                ],
            ];

            $this->noticeService->sendNotice([
                'notices' => $notice,
                'notice_details' => $noticeDetails,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send notice decision refusal.
     *
     * @param Procedure $procedure
     * @param Trademark $trademark
     * @param MatchingResult $matchingResult
     * @return void
     */
    public function sendNoticeDecisionRefusal(
        $procedure,
        $trademark,
        $lastTrademarkInfos,
        $matchingResult
    ): void
    {
        try {
            $admin = \Auth::guard('admin')->user();
            $timeResponse = $procedure->procedure_infomation->getTimeResponse();
            $responseDeadline = $procedure->procedure_infomation->convertDispatchDate()->addDays($timeResponse['period'])->format('Y-m-d H:i:s');
            $notice = [
                'flow' => Notice::FLOW_RESPONSE_NOTICE_REASON_REFUSAL,
                'trademark_id' => $trademark->id,
                'user_id' => $trademark->user_id,
                'trademark_info_id' => $lastTrademarkInfos->id ?? null,
            ];
            $redirectPage = route('admin.refusal.final-refusal.index', ['id' => $trademark->id, 'maching_result_id' => $matchingResult->id]);
            $targetPage = route('admin.import-doc-xml');

            $noticeDetails = [
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '拒絶査定受領：お客様へ連絡',
                    'attribute' => null,
                ],
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '拒絶査定受領：お客様へ連絡',
                    'attribute' => '特許庁から',
                    'response_deadline' => $responseDeadline,
                ],
            ];

            $this->noticeService->sendNotice([
                'notices' => $notice,
                'notice_details' => $noticeDetails,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send notice when registration assessment.
     *
     * @param Procedure $procedure
     * @param Trademark $trademark
     * @param MatchingResult $matchingResult
     * @return void
     */
    public function sendNoticeReasonsRefusal(
        $procedure,
        $trademark,
        $lastTrademarkInfos,
        $matchingResult
    ): void
    {
        try {
            $admin = \Auth::guard('admin')->user();
            $timeResponse = $procedure->procedure_infomation->getTimeResponse();
            $responseDeadline = $procedure->procedure_infomation->convertDispatchDate()->addDays($timeResponse['period'])->format('Y-m-d H:i:s');
            $notice = [
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'trademark_id' => $trademark->id,
                'user_id' => $trademark->user_id,
                'step' => Notice::STEP_1,
                'trademark_info_id' => $lastTrademarkInfos->id ?? null,
            ];

            $targetPage = route('admin.import-doc-xml');
            $redirectPage = route('admin.refusal-request-review', ['id' => $trademark->id, 'maching_result_id' => $matchingResult->id]);

            $noticeDetails = [
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '拒絶理由通知対応：お知らせ・対応検討依頼',
                    'attribute' => null,
                ],
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：お知らせ・対応検討依頼',
                    'attribute' => '特許庁から',
                    'response_deadline' => $responseDeadline,
                ],
            ];

            $this->noticeService->sendNotice([
                'notices' => $notice,
                'notice_details' => $noticeDetails,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send notice when registration assessment.
     *
     * @param Procedure $procedure
     * @param Trademark $trademark
     * @param MatchingResult $matchingResult
     * @return void
     */
    public function sendNoticeRegistration(
        $procedure,
        $trademark,
        $lastTrademarkInfos,
        $matchingResult
    ): void
    {
        try {
            $timeResponse = $procedure->procedure_infomation->getTimeResponse();
            $responseDeadline = $procedure->procedure_infomation->convertDispatchDate()->addDays($timeResponse['period'])->format('Y-m-d H:i:s');
            $admin = \Auth::guard('admin')->user();

            $notice = [
                'flow' => Notice::FLOW_REGISTER_TRADEMARK,
                'trademark_id' => $trademark->id,
                'user_id' => $trademark->user_id,
                'trademark_info_id' => $lastTrademarkInfos->id ?? null,
            ];
            $targetPage = route('admin.import-doc-xml');
            $redirectPage = route('admin.registration.notify', ['id' => $trademark->id, 'maching_result_id' => $matchingResult->id]);

            $noticeDetails = [
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '登録査定：お客様へ連絡',
                    'attribute' => null,
                ],
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '登録査定：お客様へ連絡',
                    'attribute' => '特許庁から',
                    'response_deadline' => $responseDeadline,
                ],
            ];

            $this->noticeService->sendNotice([
                'notices' => $notice,
                'notice_details' => $noticeDetails,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Validation data in file XML
     *
     * @param array $data
     *
     * @return array
     */
    public function validateDataXMLImport02($data)
    {
        $errors = [];
        foreach ($data as $item) {
            $array = json_decode(json_encode($item), true);
            if (isset($array['procedure_infomation']) && $array['procedure_infomation']) {
                $inputDate = $array['procedure_infomation']['input_date']['date'] ?? '';
                $inputTime = $array['procedure_infomation']['input_date']['time'] ?? '';

                if (empty($inputDate)) {
                    $error = [
                        'filename' => $array['fileName'],
                        'message' => __('messages.import_xml.system_error')
                    ];
                    if (isset($error) && count($error)) {
                        $errors[] = $error;
                    }
                    return $errors;
                }

                if (empty($inputTime)) {
                    $error = [
                        'filename' => $array['fileName'],
                        'message' => __('messages.import_xml.system_error')
                    ];
                    if (isset($error) && count($error)) {
                        $errors[] = $error;
                    }
                    return $errors;
                }
            }
        }
        return [];
    }
}
