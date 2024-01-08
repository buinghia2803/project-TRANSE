<?php

namespace App\Services\Common;

use App\Helpers\CommonHelper;
use App\Models\Agent;
use App\Models\DocSubmission;
use App\Models\DocSubmissionCmt;
use App\Models\MPriceList;
use App\Models\PlanDetailProduct;
use App\Models\RegisterTrademark;
use App\Models\RegisterTrademarkRenewal;
use App\Models\Trademark;
use App\Services\AgentService;
use App\Services\BaseService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\DocSubmissionService;
use App\Services\PrecheckService;
use App\Services\RegisterTrademarkProdService;
use App\Services\TrademarkPlanService;
use App\Services\TrademarkService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExportService extends BaseService
{
    private TrademarkService $trademarkService;
    private AgentService $agentService;
    private TrademarkPlanService $trademarkPlanService;
    private DocSubmissionService $docSubmissionService;
    private RegisterTrademarkProdService $registerTrademarkProdService;
    private PrecheckService $precheckService;
    private ComparisonTrademarkResultService $comparisonTrademarkResultService;

    /**
     * Initializing the instances and variables
     */
    public function __construct(
        TrademarkService                 $trademarkService,
        AgentService                     $agentService,
        TrademarkPlanService             $trademarkPlanService,
        DocSubmissionService             $docSubmissionService,
        RegisterTrademarkProdService     $registerTrademarkProdService,
        PrecheckService                  $precheckService,
        ComparisonTrademarkResultService $comparisonTrademarkResultService
    )
    {
        $this->trademarkService = $trademarkService;
        $this->agentService = $agentService;
        $this->trademarkPlanService = $trademarkPlanService;
        $this->docSubmissionService = $docSubmissionService;
        $this->registerTrademarkProdService = $registerTrademarkProdService;
        $this->precheckService = $precheckService;
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
    }

    /**
     * Format Data HTML
     *
     * @param array $dataExport
     * @param string $htmlPath
     * @param array $option
     * @return string
     */
    public function genHTML(array $dataExport, string $htmlPath, array $option = []): string
    {
        $html = view('exports.html.general', [
            'dataExport' => $dataExport ?? [],
            'option' => $option,
        ]);
        $html = mb_convert_encoding($html, "SJIS");

        $disk = env('FILE_STORAGE_DISK', 'local');
        Storage::disk($disk)->put($htmlPath, $html);

        return $htmlPath;
    }

    /**
     * Format Export Data HTML
     *
     * @param array $data
     * @return array
     */
    public function formatExportData(array $dataExport): array
    {
        $dataHTML = [];
        foreach ($dataExport as $key => $data) {
            if (!empty($data['label'])) {
                $data = array_merge($data, [
                    'label' => isset($data['no_convert_label']) && $data['no_convert_value'] ? $data['label'] : mb_convert_kana($data['label'], 'N'),
                ]);
            }
            if (!empty($data['value'])) {
                $data = array_merge($data, [
                    'value' => isset($data['no_convert_value']) && $data['no_convert_value'] ? $data['value'] : mb_convert_kana($data['value'], 'N'),
                ]);
            }

            switch ($key) {
                default:
                    $dataHTML[] = array_merge($data, [
                        'label' => $data['label'] ?? '',
                    ]);
            }
        }

        return [
            'dataHTML' => $dataHTML,
        ];
    }

    /**
     * Export HTML for U032
     *
     * @param int $trademarkID
     * @return array
     */
    public function u032(int $trademarkID): array
    {
        $trademark = $this->trademarkService->getTrademarkApplyDocumentToCheck($trademarkID);

        $agentIdentifierCodeNominated = $this->agentService->getIdentifierCodeNominated($trademarkID);
        $agentIdentifierCodeNotNominated = $this->agentService->getIdentifierCodeNotNominated($trademarkID);

        $data = [];
        $attachments = [];

        // 【書類名】
        $data['document_name'] = [
            'type' => TEXT,
            'label' => __('labels.apply_trademark.text_1') . '　　　　　　　　　　　　　　　',
            'value' => __('labels.apply_trademark.text_2'),
        ];

        // 【整理番号】
        $data['trademark_number'] = [
            'type' => TEXT,
            'label' => __('labels.apply_trademark.trademark_number') . '　　　　　　　　　　　　　　',
            'value' => $trademark['trademark_number'] ? mb_convert_kana($trademark['trademark_number'], 'R') : '',
        ];

        // 【提出日】
        $currentDate = mb_convert_kana(CommonHelper::formatTime(date("Y-m-d"), 'Ee年n月j日'), 'N');

        $data['application_date'] = [
            'type' => TEXT,
            'label' => __('labels.a205_common.trademark_info.application_date') . '　　　　　　　　　　　　　　　',
            'value' => $currentDate ?? '',
        ];

        // 【あて先】
        $data['address'] = [
            'type' => TEXT,
            'label' => __('labels.apply_trademark.text_3') . '　　　　　　　　　　　　　　　',
            'value' => __('labels.apply_trademark.text_4'),
        ];

        // 【商標登録を受けようとする商標】
        if (!empty($trademark['type_trademark']) && $trademark['type_trademark'] == Trademark::TRADEMARK_TYPE_LETTER) {
            $data['name_trademark'] = [
                'type' => TITLE,
                'label' => __('labels.apply_trademark.name_trademark'),
            ];
            $data['name_trademark_text'] = [
                'type' => TITLE,
                'label' => $trademark['name_trademark'] ?? '',
            ];
        } else {
            $imageTrademark = $trademark['image_trademark'] ?? '';

            if (!empty($imageTrademark)) {
                $name = explode('/', $imageTrademark);
                $name = end($name);

                $attachments[] = [
                    'file_source' => $imageTrademark,
                    'file_name' => $name,
                ];

                $data['name_trademark'] = [
                    'type' => TITLE,
                    'label' => __('labels.apply_trademark.name_trademark'),
                ];
                $data['image_trademark'] = [
                    'type' => IMAGE,
                    'value' => $name ?? '',
                    'style' => 'max-height:120px;',
                    'no_convert_value' => true,
                ];
            }
        }

        // 【指定商品又は指定役務並びに商品及び役務の区分】
        $data['product_title'] = [
            'type' => TITLE,
            'label' => __('labels.apply_trademark.text_11'),
        ];

        foreach ($trademark['data'] as $key => $mDistinct) {
            $productNameArray = $mDistinct->pluck('product_name')->toArray();
            $productNameStr = implode('，', $productNameArray);

            // 【第x類】
            $data['product_item_name_distinct_' . $key] = [
                'type' => TITLE,
                'label' => '　　' . __('labels.apply_trademark.name_distinct', ['attr' => $key]),
            ];

            // 【指定商品（指定役務）】
            $data['product_item_name_product_' . $key] = [
                'type' => TEXT,
                'label' => '　　' . __('labels.apply_trademark.name_product') . '　　　　　　',
                'value' => $productNameStr ?? '',
            ];
        }

        // 【商標登録出願人】
        $data['trademark_info'] = [
            'type' => TITLE,
            'label' => __('labels.apply_trademark.text_5'),
        ];

        // 【住所又は居所】
        $data['trademark_info_address'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.apply_trademark.address') . '　　　　　　　　　　',
            'value' => ($trademark['prefecture_name'] ?? '') . ($trademark['address_second'] ?? '') . ($trademark['address_three'] ?? ''),
        ];

        // 【氏名又は名称】
        $data['trademark_info_name'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.apply_trademark.trademark_info_name') . '　　　　　　　　　　',
            'value' => $trademark['trademark_info_name'] ?? '',
        ];

        // 【代理人】
        $data['agent_first'] = [
            'type' => TITLE,
            'label' => __('labels.apply_trademark.text_7'),
        ];

        // 【識別番号】
        $data['agent_first_identification_number'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.apply_trademark.identification_number_first') . '　　　　　　　　　　　　',
            'value' => $agentIdentifierCodeNominated->identification_number ?? '',
        ];

        // 【弁理士】
        $data['agent_first_patent_attorney'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.apply_trademark.text_8') . '　　　　　　　　　　　　　',
            'value' => null,
        ];

        // 【氏名又は名称】
        $data['agent_first_name'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.apply_trademark.name_agent') . '　　　　　　　　　　',
            'value' => $agentIdentifierCodeNominated->name ?? '',
        ];

        if (isset($agentIdentifierCodeNotNominated->identification_number)
            || isset($agentIdentifierCodeNotNominated->name)) {
            // 【手数料の表示】
            $data['agent_second'] = [
                'type' => TITLE,
                'label' => __('labels.apply_trademark.text_9'),
            ];

            // 【識別番号】
            $data['agent_second_identification_number'] = [
                'type' => TEXT,
                'label' => '　' . __('labels.apply_trademark.identification_number_second') . '　　　　　　　　　　　　',
                'value' => $agentIdentifierCodeNotNominated->identification_number ?? '',
            ];

            // 【弁理士】
            $data['agent_second_patent_attorney'] = [
                'type' => TEXT,
                'label' => '　' . __('labels.apply_trademark.text_8') . '　　　　　　　　　　　　　',
                'value' => '',
            ];

            // 【氏名又は名称】
            $data['agent_second_name'] = [
                'type' => TEXT,
                'label' => '　' . __('labels.apply_trademark.name_agent') . '　　　　　　　　　　',
                'value' => $agentIdentifierCodeNotNominated->name ?? '',
            ];
        }

        // 【手数料の表示】
        $data['info_payment'] = [
            'type' => TITLE,
            'label' => __('labels.apply_trademark.text_10'),
        ];

        if (isset($agentIdentifierCodeNominated)) {
            if ($agentIdentifierCodeNominated->deposit_type == Agent::DEPOSIT_TYPE_ADVENCE) {
                // 【予納台帳番号】
                $data['deposit_account_number'] = [
                    'type' => TEXT,
                    'label' => '　' . __('labels.apply_trademark.deposit_account_number') . '　　　　　　　　　　',
                    'value' => $agentIdentifierCodeNominated->deposit_account_number ?? '',
                ];
            } elseif ($agentIdentifierCodeNominated->deposit_type == Agent::DEPOSIT_TYPE_CREDIT) {
                $data['deposit_account_number'] = [
                    'type' => TEXT,
                    'label' => '　' . __('labels.apply_trademark.deposit_account_number_v2') . '　　　　　　　　　　',
                    'value' => '',
                ];
            }
        }

        // 【納付金額】
        $costPrint = isset($trademark['cost_print_application_one_distintion'])
            ? mb_convert_kana($trademark['cost_print_application_one_distintion']
                + $trademark['cost_print_application_add_distintion'] * (count($trademark['data']) - 1), 'KVN')
            : 0;
        $data['cost_print'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.apply_trademark.cost_print') . '　　　　　　　　　　　　',
            'value' => $costPrint,
        ];

        $htmlName = '商標登録願.html';
        $htmlPath = FOLDER_TEMP . '/u302/';

        // Delete OLD folder
        $disk = env('FILE_STORAGE_DISK', 'local');
        $allFiles = Storage::disk($disk)->allFiles($htmlPath);
        foreach ($allFiles as $file) {
            if (Storage::exists($file)) {
                Storage::delete($file);
            }
        }

        $dataExport = $this->formatExportData($data);
        $htmlKu302 = $htmlPath . $htmlName;
        $this->genHTML($dataExport['dataHTML'], $htmlKu302, [
            'title' => '【書類名】　　　商標登録願',
        ]);

        if (!empty($attachments)) {
            // Copy attachment file
            foreach ($attachments as $attachment) {
                $fileSource = $attachment['file_source'];
                $fileName = $attachment['file_name'];

                $attachmentDir = $htmlPath . $fileName;

                Storage::disk($disk)->copy($fileSource, $attachmentDir);
            }

            // Export Zip
            $outputZipFile = '商標登録願.zip';
            $outputPath = public_path($htmlPath . $outputZipFile);

            CommonHelper::exportZip($outputPath, $htmlPath);

            $resultPath = $htmlPath . $outputZipFile;
            $resultName = $outputZipFile;
        } else {
            $resultPath = $htmlKu302;
            $resultName = $htmlName;
        }

        return [
            'html_path' => asset($resultPath),
            'html_name' => $resultName,
        ];
    }

    /**
     * A302
     *
     * @param Model $trademark
     * @return mixed
     */
    public function a302(Model $trademark)
    {
        $htmlName = '納付書.html';
        $htmlPath = FOLDER_TEMP . '/' . $htmlName;
        $data = [];

        $registerTrademarkId = $trademark->register_trademark_id;
        $registerTrademark = $trademark->registerTrademark;
        $appTrademark = $trademark->appTrademark;
        $registerTrademarkProds = $registerTrademark->registerTrademarkProds;
        $countDistinctionIsApply = $this->registerTrademarkProdService->countDistinctionWithCondition($registerTrademarkProds, APPLY);
        $countDistinction = $this->registerTrademarkProdService->countDistinctionWithCondition($registerTrademarkProds, ALL_APPLY);
        $agent = null;
        $agentGroup = $appTrademark->agentGroup;

        $checkIsSend = false;
        if ($registerTrademark->is_send == RegisterTrademark::IS_SEND) {
            $checkIsSend = true;
        }
        if ($checkIsSend == true) {
            $displayInfoStatus = $registerTrademark->display_info_status ?? null;
            $displayInfoStatus = !empty($displayInfoStatus) ? json_decode($displayInfoStatus) : [];

            $law = in_array(LAW, $displayInfoStatus);
            $changeName = in_array(CHANGE_NAME, $displayInfoStatus);
            $changeAddress = in_array(CHANGE_ADDRESS, $displayInfoStatus);
        }

        if ($agentGroup) {
            $collectAgent = $agentGroup->collectAgent->first();
            $agent = $collectAgent ? $collectAgent->agent : null;
        }

        $print1stRegistration = $this->precheckService->getPriceOnePackService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
        $printDistinction = 0;
        if ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_5_YEAR) {
            $printDistinction = $print1stRegistration['pof_1st_distinction_5yrs'] * $countDistinction;
        } elseif ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_10_YEAR) {
            $printDistinction = $print1stRegistration['pof_1st_distinction_10yrs'] * $countDistinction;
        }

        $data['document_name'] = [
            'type' => TEXT,
            'label' => __('labels.a302.dt_1') . '　　　　　　　　　　　　',
            'value' => __('labels.a302.dd_2') ?? '',
        ];

        $data['trademark_number'] = [
            'type' => TEXT,
            'label' => __('labels.a302.dt_2') . '　　　　　　　　　　　',
            'value' => $trademark['trademark_number'] ? mb_convert_kana($trademark['trademark_number'], 'R') : '',
        ];

        $data['address'] = [
            'type' => TEXT,
            'label' => __('labels.a302.dt_3') . '　　　　　　　　　　　　',
            'value' => __('labels.a302.dd_2') ?? '',
        ];

        $data['number_or_category_of_goods_and_services'] = [
            'type' => TEXT,
            'label' => __('labels.a302.dt_4') . '　　　　　　　　　　　',
            'value' => $countDistinctionIsApply ?? '',
        ];


        $data['trademark_register'] = [
            'type' => TEXT,
            'label' => __('labels.a302.h4_1') . '　　　　　　　　　　',
        ];

        $data['trademark_info_name'] = [
            'type' => TEXT,
            'label' => __('labels.a302.dt_6') . '　　　　　　　　　',
            'value' => $registerTrademark->trademark_info_name ?? '',
        ];

        $data['payer'] = [
            'type' => TITLE,
            'label' => __('labels.a302.h4_2') . '　　　　　　　　　　　　　　　',
        ];

        $data['identification_number'] = [
            'type' => TEXT,
            'label' => __('labels.a302.dt_7') . '　　　　　　　　　　　',
            'value' => $agent->identification_number ?? '',
        ];

        $data['agent_name'] = [
            'type' => TEXT,
            'label' => __('labels.a302.dt_6') . '　　　　　　　　　',
            'value' => $agent->name ?? '',
        ];

        $data['display_registration_fee'] = [
            'type' => TITLE,
            'label' => __('labels.a302.h4_3') . '　　　　　　　　　　　　　　　',
        ];

        $data['deposit_account_number'] = [
            'type' => TEXT,
            'label' => __('labels.a302.dt_8') . '　　　　　　　　　　　　　　　',
            'value' => $agent->deposit_account_number ?? '',
        ];

        $data['deposit_account_number'] = [
            'type' => TEXT,
            'label' => __('labels.a302.dt_8') . '　　　　　　　　　　　　　　　',
            'value' => $printDistinction ?? '',
        ];

        $data['deposit_account_number'] = [
            'type' => TEXT,
            'label' => __('labels.a302.dt_10') . '　　　　　　　　　　　　　　　',
            'value' => '',
        ];

        if ($law) {
            $data['deposit_account_number_1'] = [
                'type' => TEXT,
                'label' => '　　',
                'value' => __('labels.a302.checkbox_1'),
            ];
        }

        if ($changeName) {
            $data['deposit_account_number_2'] = [
                'type' => TEXT,
                'label' => '　　',
                'value' => __('labels.a302.checkbox_2'),
            ];
        }

        if ($changeAddress) {
            $data['deposit_account_number_3'] = [
                'type' => TEXT,
                'label' => '　　',
                'value' => __('labels.a302.checkbox_3'),
            ];
        }

        $dataExport = $this->formatExportData($data);
        $this->genHTML($dataExport['dataHTML'], $htmlPath, [
            'title' => '【書類名】商標登録料納付書',
        ]);

        return [
            'html_path' => asset($htmlPath),
            'html_name' => $htmlName,
        ];
    }

    /**
     * Export HTML for a205 group
     *
     * @param array $param
     * @return array
     */
    public function a205Group(array $param): array
    {
        $comparisonTrademarkResultID = $param['comparison_trademarkResult_id'] ?? null;
        $trademarkPlanID = $param['trademark_plan_id'] ?? null;
        $docSubmissionID = $param['doc_submission_id'] ?? null;
        $isRenderHTML = $param['is_render_html'] ?? true;

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($comparisonTrademarkResultID);
        $data = $this->trademarkService->getTrademarkInfo($comparisonTrademarkResult->trademark->id);

        if (!empty($docSubmissionID)) {
            $docSubmission = $this->docSubmissionService->getDocSubmission([
                'id' => $docSubmissionID,
                'trademark_plan_id' => $trademarkPlanID,
            ], DocSubmissionCmt::TYPE_COMMENT_OF_STEP_1);
        } else {
            //get docSubmission first
            $docSubmission = $this->docSubmissionService->getDocSubmission([
                'trademark_plan_id' => $trademarkPlanID,
                'flag_role' => DocSubmission::FLAG_ROLE_1,
                'is_reject' => DocSubmission::IS_REJECT_FALSE,
            ], DocSubmissionCmt::TYPE_COMMENT_OF_STEP_1);
        }

        // a205hosei01window
        $dataHoseiWindow = [];
        $dataCommonA205Hosei01 = $this->trademarkPlanService->getCommonA205Hosei01Window($trademarkPlanID);

        // 【書類名】
        $dataHoseiWindow['document_name'] = [
            'type' => TEXT,
            'label' => __('labels.a205_common.trademark_info.content') . '　　　　　　　',
            'value' => __('手続補正書'),
        ];

        // A205 Data
        // 【整理番号】
        $dataHoseiWindow['trademark_number'] = [
            'type' => TEXT,
            'label' => __('labels.a205_common.trademark_info.trademark_number') . '　　　　　　',
            'value' => $data['trademark_number'] ?? null,
        ];
        // 【提出日】
        $dataHoseiWindow['application_date'] = [
            'type' => TEXT,
            'label' => __('labels.a205_common.trademark_info.application_date') . '　　　　　　　',
            'value' => !empty($docSubmission->filing_date)
                ? mb_convert_kana(CommonHelper::formatTime($docSubmission->filing_date, 'Ee年n月j日'), 'N')
                : '',
        ];
        // 【あて先】
        $dataHoseiWindow['content_2'] = [
            'type' => TEXT,
            'label' => __('labels.a205_common.trademark_info.content_2') . '　　　　　　　',
            'value' => __('labels.a205_common.trademark_info.content_3'),
        ];

        // 【事件の表示】
        $dataHoseiWindow['content_4'] = [
            'type' => TITLE,
            'label' => __('labels.a205_common.trademark_info.content_4'),
        ];
        // 【出願番号】
        $dataHoseiWindow['application_number'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a205_common.trademark_info.application_number') . '　　　　',
            'value' => $data['application_number_format'] ?? null,
        ];

        // 【商標登録出願人】
        $dataHoseiWindow['content_5_v2'] = [
            'type' => TITLE,
            'label' => __('labels.a205_common.trademark_info.content_5_v2'),
        ];
        // 【住所又は居所】
        $dataHoseiWindow['trademark_info_address'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a205_common.trademark_info.address') . '　　',
            'value' => ($data['prefecture_name'] ?? null) . ($data['address_second'] ?? null) . ($data['address_three'] ?? null),
        ];
        // 【氏名又は名称】
        $dataHoseiWindow['trademark_info_name'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a205_common.trademark_info.trademark_info_name') . '　　',
            'value' => $data['trademark_info_name'] ?? null,
        ];

        // 【代理人】
        $dataHoseiWindow['agent_title'] = [
            'type' => TITLE,
            'label' => __('labels.a205_common.trademark_info.agent_title'),
        ];
        // 【識別番号】
        $dataHoseiWindow['identification_number'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a205_common.trademark_info.identification_number') . '　　　　',
            'value' => $data['identification_number'] ?? null,
        ];
        // 【弁理士】
        $dataHoseiWindow['content_6'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a205_common.trademark_info.content_6') . '　　　　　',
            'value' => '',
        ];
        // 【氏名又は名称】
        $dataHoseiWindow['agent_name'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a205_common.trademark_info.agent_name') . '　　',
            'value' => $data['agent_name'] ?? null,
        ];
        // 【発送番号】
        $dataHoseiWindow['pi_dispatch_number'] = [
            'type' => TEXT,
            'label' => __('labels.a205_common.trademark_info.pi_dispatch_number') . '　　　　　　',
            'value' => $data['pi_dispatch_number'] ?? null,
        ];

        // END A205 Data

        // 【手続補正】
        $dataHoseiWindow['amendment'] = [
            'type' => TITLE,
            'label' => __('labels.a205hosei01window.amendment_v2'),
        ];
        // 【補正対象書類名】
        $dataHoseiWindow['name_of_document'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a205hosei01window.name_of_document') . '　',
            'value' => __('labels.a205hosei01window.regis_app'),
        ];
        // 【補正対象項目名】
        $dataHoseiWindow['target_item'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a205hosei01window.target_item') . '　',
            'value' => __('labels.a205hosei01window.goods_and_services'),
        ];
        // 【補正方法】
        $dataHoseiWindow['method'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a205hosei01window.method') . '　　　　',
            'value' => __('labels.a205hosei01window.change'),
        ];
        // 【補正の内容】
        $dataHoseiWindow['correction'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a205hosei01window.correction'),
            'value' => '',
        ];

        // 【第x類】
        foreach ($dataCommonA205Hosei01['data_products'] as $mDistinctionName => $itemData) {
            $dataHoseiWindow['distinct_' . $mDistinctionName] = [
                'type' => TEXT,
                'label' => '　　　　【' . __('labels.name_distinct', ['attr' => $mDistinctionName]) . '】',
                'value' => '',
            ];
            $dataHoseiWindow['distinct_' . $mDistinctionName . '_prod'] = [
                'type' => TEXT,
                'label' => '　　　' . __('labels.a205hosei01window.product_and_distintion') . '　',
                'value' => $itemData->implode('name', ', '),
            ];
        }

        if ($dataCommonA205Hosei01['slag_show_total_amount']) {
            // 【手数料の表示】
            $dataHoseiWindow['info_payment'] = [
                'type' => TITLE,
                'label' => __('labels.a205hosei01window.info_payment'),
            ];

            if ($dataCommonA205Hosei01['deposit_type'] == Agent::DEPOSIT_TYPE_ADVENCE) {
                // 【予納台帳番号】
                $dataHoseiWindow['deposit_account_number'] = [
                    'type' => TEXT,
                    'label' => '　' . __('labels.a205hosei01window.deposit_account_number') . '　　　　　',
                    'value' => $dataCommonA205Hosei01['deposit_account_number'] ?? '',
                ];
            } elseif ($dataCommonA205Hosei01['deposit_type'] == Agent::DEPOSIT_TYPE_CREDIT) {
                // 【指定立替納付】
                $dataHoseiWindow['deposit_account_number'] = [
                    'type' => TEXT,
                    'label' => '　' . __('labels.a205hosei01window.deposit_account_number_v2') . '　　　　　',
                    'value' => '',
                ];
            }

            // 【納付金額】
            $dataHoseiWindow['total_amount'] = [
                'type' => TEXT,
                'label' => '　' . __('labels.a205hosei01window.total_amount') . '　　　　　　　',
                'value' => number_format($dataCommonA205Hosei01['total_amount']) ?? 0,
            ];
        }

        // a205shu02window
        $dataShuWindow = [];
        $attachments = [];
        $oldDataCommonShu02Window = $this->docSubmissionService->getDataDocSubmission($docSubmissionID);
        $dataProductCommonA205Shu02 = $this->docSubmissionService->getDataProductCommonA205Shu02($trademarkPlanID);

        // 【意見の内容】
        $dataShuWindow['info_payment'] = [
            'type' => TITLE,
            'label' => __('labels.a205iken02window.content_title'),
        ];

        // description_written_opinion
        $descriptionWrittenOpinion = $oldDataCommonShu02Window->description_written_opinion ?? null;
        if ($oldDataCommonShu02Window->is_written_opinion == IS_WRITTEN_OPINION) {
            $descriptionWrittenOpinion = __('labels.a205shu02_window.opinion_not_required');
        }

        $dataShuWindow['description_written_opinion'] = [
            'type' => TEXT,
            'label' => '　　　　　　　　',
            'value' => $descriptionWrittenOpinion ?? '',
        ];

        // 第xx類
        foreach ($dataProductCommonA205Shu02 as $mDistinctionName => $products) {
            $dataDelete = $products->filter(function ($value, $key) {
                return $value->plan_detail_product_deleted_at;
            });

            $dataAdd = $products->filter(function ($value, $key) {
                return !$value->plan_detail_product_deleted_at
                    && in_array($value->role_add, [PlanDetailProduct::ROLL_ADD_MANAGER, PlanDetailProduct::ROLL_ADD_SUPERVISOR]);
            });

            $valueText = '';
            if ($dataDelete->count() > 0) {
                $valueText .= __('labels.agent.delete') . '　' . $dataDelete->implode('name', ', ') . '<br>';
            }
            if ($dataAdd->count() > 0) {
                $valueText .= __('labels.agent.addition') . '　' . $dataAdd->implode('name', ', ') . '<br>';
            }

            $dataShuWindow['distinct_' . $mDistinctionName] = [
                'type' => TEXT,
                'label' => '【' . __('labels.name_distinct', ['attr' => $mDistinctionName]) . '】　　　',
                'value' => $valueText ?? '',
            ];
        }
        // 【添付物件】
        $dataShuWindow['list_file'] = [
            'type' => TITLE,
            'label' => __('labels.a205iken02window.list_file'),
        ];
        //
        if ($oldDataCommonShu02Window && $oldDataCommonShu02Window->docSubmissionAttachProperties) {
            $docSubmissionAttachProperties = $oldDataCommonShu02Window->docSubmissionAttachProperties->map(function ($item) {
                $docSubmissionAttachments = $item->docSubmissionAttachments ?? collect([]);
                $docSubmissionAttachments = $docSubmissionAttachments->sortBy('file_no');

                $fileNo = $docSubmissionAttachments->first()->file_no;
                $fileNo = (int) mb_convert_kana($fileNo, 'n');

                $item->file_no = $fileNo ?? 9999;

                return $item;
            })->sortBy('file_no')->values();

            foreach ($docSubmissionAttachProperties as $k => $docSubmissionAttachProperty) {
                // 【物件名】
                $dataShuWindow['attach_' . $docSubmissionAttachProperty->id] = [
                    'type' => TEXT,
                    'label' => __('labels.a205iken02window.name_submission') . '　　　',
                    'value' => $docSubmissionAttachProperty->name ?? '',
                ];

                if ($docSubmissionAttachProperty->docSubmissionAttachments) {
                    $docSubmissionAttachments = $docSubmissionAttachProperty->docSubmissionAttachments;
                    $docSubmissionAttachments = $docSubmissionAttachments->map(function ($item) {
                        $item->file_no_covert = (int) mb_convert_kana($item->file_no, 'n');
                        return $item;
                    })->sortBy('id')->sortBy('file_no_covert');

                    $images = [];
                    foreach ($docSubmissionAttachments as $i => $attachment) {
                        if (!empty($isRenderHTML)) {
                            $name = explode('/', $attachment->attach_file);
                            $name = end($name);

                            $attachments[] = [
                                'file_source' => $attachment->attach_file,
                                'file_name' => $name,
                            ];

                            $attachFile = $name;
                        } else {
                            $attachFile = asset($attachment->attach_file);
                        }

                        $images[] = [
                            'attach_file' => $attachFile,
                        ];
                    }

                    $dataShuWindow['attach_' . $docSubmissionAttachProperty->id . '_file'] = [
                        'type' => MULTI_IMAGE,
                        'label' => __('labels.a205iken02window.content_file'),
                        'images' => $images,
                    ];
                }
            }
        }

        $htmlPath = FOLDER_TEMP . '/a205group/';

        // Delete OLD folder
        $disk = env('FILE_STORAGE_DISK', 'local');
        $allFiles = Storage::disk($disk)->allFiles($htmlPath);
        foreach ($allFiles as $file) {
            if (Storage::exists($file)) {
                Storage::delete($file);
            }
        }

        // a205hosei01window
        $htmlNameHoseiWindow = '補正書.html';
        $htmlPathHoseiWindow = $htmlPath . $htmlNameHoseiWindow;
        $dataExportHoseiWindow = $this->formatExportData($dataHoseiWindow);
        $this->genHTML($dataExportHoseiWindow['dataHTML'], $htmlPathHoseiWindow);

        // a205shu02window
        $htmlNameShuWindow = '/意見書.html';
        $htmlPathShuWindow = $htmlPath . $htmlNameShuWindow;
        $dataExportShuWindow = $this->formatExportData($dataShuWindow);
        $this->genHTML($dataExportShuWindow['dataHTML'], $htmlPathShuWindow);

        if (!empty($attachments)) {
            // Copy attachment file
            foreach ($attachments as $attachment) {
                $fileSource = $attachment['file_source'];
                $fileName = $attachment['file_name'];

                $attachmentDir = $htmlPath . $fileName;

                if (Storage::exists($fileSource)) {
                    Storage::disk($disk)->copy($fileSource, $attachmentDir);
                }
            }
        }

        // Export Zip
        $outputZipFile = 'html_' . $comparisonTrademarkResultID . '_' . $trademarkPlanID . '_' . $docSubmissionID . '.zip';
        $outputPath = public_path($htmlPath . $outputZipFile);

        CommonHelper::exportZip($outputPath, $htmlPath);

        return [
            'html_path' => asset($htmlPath . $outputZipFile),
            'html_name' => $outputZipFile,
        ];
    }

    /**
     * A210over
     *
     * @param Model $trademark
     * @return array
     */
    public function a210over(Model $trademark): array
    {
        $data = [];

        $trademark = $this->trademarkService->getTrademark($trademark, RegisterTrademarkRenewal::TYPE_EXTENSION_OUTSIDE_PERIOD);

        //【書類名】
        $data['text_2'] = [
            'type' => TEXT,
            'label' => __('labels.a210.text_2') . '　　　　　　　　　　　　',
            'value' => __('labels.a210.text_3') ?? '',
        ];
        // 【整理番号】
        $data['trademark_number'] = [
            'type' => TEXT,
            'label' => __('labels.a210.trademark_number') . '　　　　　　　　　　　',
            'value' => $trademark['trademark_number'] ?? '',
        ];
        // 【提出日】
        $data['date_click'] = [
            'type' => TEXT,
            'label' => __('labels.a210.date_click') . '　　　　　　　　　　　　',
            'value' => mb_convert_kana(CommonHelper::formatTime(now(), 'Ee年n月j日'), 'N'),
        ];
        // 【あて先】
        $data['text_4'] = [
            'type' => TEXT,
            'label' => __('labels.a210.text_4') . '　　　　　　　　　　　　',
            'value' => __('labels.a210.text_5') ?? '',
        ];

        // 【事件の表示】
        $data['text_6'] = [
            'type' => TITLE,
            'label' => __('labels.a210.text_6'),
        ];
        // 【出願番号】
        $data['application_number'] = [
            'type' => TEXT,
            'label' => __('labels.a210.application_number') . '　　　　　　　　　　',
            'value' => $trademark['application_number'] ?? '',
        ];

        // 【請求人】
        $data['text_7'] = [
            'type' => TITLE,
            'label' => __('labels.a210.text_7'),
        ];
        // 【住所又は居所】
        $data['address'] = [
            'type' => TEXT,
            'label' => __('labels.a210.address') . '　　　　　　　　',
            'value' => ($trademark['nation_name'] ?? null) . ($trademark['prefecture_name'] ?? null) . ($trademark['address_second'] ?? null) . ($trademark['address_three'] ?? null),
        ];
        // 【氏名又は名称】
        $data['trademark_info_name'] = [
            'type' => TEXT,
            'label' => __('labels.a210.trademark_info_name') . '　　　　　　　　',
            'value' => $trademark['trademark_info_name'] ?? '',
        ];

        // 【代理人】
        $data['text_8'] = [
            'type' => TITLE,
            'label' => __('labels.a210.text_8'),
        ];
        // 【識別番号】
        $data['identification_number'] = [
            'type' => TEXT,
            'label' => __('labels.a210.identification_number') . '　　　　　　　　　　',
            'value' => $trademark['identification_number'] ?? '',
        ];
        // 【弁理士】
        $data['text_9'] = [
            'type' => TEXT,
            'label' => __('labels.a210.text_9') . '　　　　　　　　　　　',
            'value' => '',
        ];
        // 【氏名又は名称】
        $data['agent_name'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a210.agent_name') . '　　　　　　　　',
            'value' => $trademark['agent_name'] ?? '',
        ];

        // 【発送番号】
        $data['pi_dispatch_number'] = [
            'type' => TEXT,
            'label' => __('labels.a210.pi_dispatch_number') . '　　　　　　　　　　　',
            'value' => $trademark['pi_dispatch_number'] ?? '',
        ];
        // 【請求の内容】
        $data['text_10'] = [
            'type' => TEXT,
            'label' => __('labels.a210.text_10') . '　　　　　　　　　　',
            'value' => __('labels.a210.text_11_over'),
        ];

        if (isset($trademark['print_fee']) && $trademark['print_fee'] > 0) {
            // 【手数料の表示】
            $data['text_12'] = [
                'type' => TITLE,
                'label' => __('labels.a210.text_12'),
            ];

            if ($trademark['deposit_type'] == \App\Models\Agent::DEPOSIT_TYPE_ADVENCE) {
                // 【予納台帳番号】
                $data['deposit_account_number'] = [
                    'type' => TEXT,
                    'label' => __('labels.a210.deposit_account_number') . '　　　　　　　　',
                    'value' => $trademark['deposit_account_number'] ?? '',
                ];
            } elseif ($trademark['deposit_type'] == \App\Models\Agent::DEPOSIT_TYPE_CREDIT) {
                // 【指定立替納付】
                $data['deposit_account_number_v2'] = [
                    'type' => TEXT,
                    'label' => __('labels.a210.deposit_account_number_v2') . '　　　　　　　　',
                    'value' => '',
                ];
            }

            // 【納付金額】
            $data['print_fee'] = [
                'type' => TEXT,
                'label' => __('labels.a210.print_fee') . '　　　　　　　　　',
                'value' => $trademark['print_fee'] ?? '',
            ];
        }

        $htmlName = '期間延長請求書（期間徒過）.html';
        $htmlPath = FOLDER_TEMP . '/' . $htmlName;
        $dataExport = $this->formatExportData($data);
        $this->genHTML($dataExport['dataHTML'], $htmlPath, [
            'title' => '期間延長請求書（期間徒過）',
        ]);

        return [
            'html_path' => asset($htmlPath),
            'html_name' => $htmlName,
        ];
    }

    /**
     * A210alert
     *
     * @param Model $trademark
     * @return array
     */
    public function a210alert(Model $trademark): array
    {
        $data = [];

        $trademark = $this->trademarkService->getTrademark($trademark, RegisterTrademarkRenewal::TYPE_EXTENSION_PERIOD_BEFORE_DEADLINE);

        //【書類名】
        $data['text_2'] = [
            'type' => TEXT,
            'label' => __('labels.a210.text_2') . '　　　　　　　　　　　　',
            'value' => __('labels.a210.text_3') ?? '',
        ];
        // 【整理番号】
        $data['trademark_number'] = [
            'type' => TEXT,
            'label' => __('labels.a210.trademark_number') . '　　　　　　　　　　　',
            'value' => $trademark['trademark_number'] ?? '',
        ];
        // 【提出日】
        $data['date_click'] = [
            'type' => TEXT,
            'label' => __('labels.a210.date_click') . '　　　　　　　　　　　　',
            'value' => mb_convert_kana(CommonHelper::formatTime(now(), 'Ee年n月j日'), 'N'),
        ];
        // 【あて先】
        $data['text_4'] = [
            'type' => TEXT,
            'label' => __('labels.a210.text_4') . '　　　　　　　　　　　　',
            'value' => __('labels.a210.text_5') ?? '',
        ];

        // 【事件の表示】
        $data['text_6'] = [
            'type' => TITLE,
            'label' => __('labels.a210.text_6'),
        ];
        // 【出願番号】
        $data['application_number'] = [
            'type' => TEXT,
            'label' => __('labels.a210.application_number') . '　　　　　　　　　　',
            'value' => $trademark['application_number'] ?? '',
        ];

        // 【請求人】
        $data['text_7'] = [
            'type' => TITLE,
            'label' => __('labels.a210.text_7'),
        ];
        // 【住所又は居所】
        $data['address'] = [
            'type' => TEXT,
            'label' => __('labels.a210.address') . '　　　　　　　　',
            'value' => ($trademark['nation_name'] ?? null) . ($trademark['prefecture_name'] ?? null) . ($trademark['address_second'] ?? null) . ($trademark['address_three'] ?? null),
        ];
        // 【氏名又は名称】
        $data['trademark_info_name'] = [
            'type' => TEXT,
            'label' => __('labels.a210.trademark_info_name') . '　　　　　　　　',
            'value' => $trademark['trademark_info_name'] ?? '',
        ];

        // 【代理人】
        $data['text_8'] = [
            'type' => TITLE,
            'label' => __('labels.a210.text_8'),
        ];
        // 【識別番号】
        $data['identification_number'] = [
            'type' => TEXT,
            'label' => __('labels.a210.identification_number') . '　　　　　　　　　　',
            'value' => $trademark['identification_number'] ?? '',
        ];
        // 【弁理士】
        $data['text_9'] = [
            'type' => TEXT,
            'label' => __('labels.a210.text_9') . '　　　　　　　　　　　',
            'value' => '',
        ];
        // 【氏名又は名称】
        $data['agent_name'] = [
            'type' => TEXT,
            'label' => '　' . __('labels.a210.agent_name') . '　　　　　　　　',
            'value' => $trademark['agent_name'] ?? '',
        ];

        // 【発送番号】
        $data['pi_dispatch_number'] = [
            'type' => TEXT,
            'label' => __('labels.a210.pi_dispatch_number') . '　　　　　　　　　　　',
            'value' => $trademark['pi_dispatch_number'] ?? '',
        ];
        // 【請求の内容】
        $data['text_10'] = [
            'type' => TEXT,
            'label' => __('labels.a210.text_10') . '　　　　　　　　　　',
            'value' => __('labels.a210.text_11'),
        ];

        if (isset($trademark['print_fee']) && $trademark['print_fee'] > 0) {
            // 【手数料の表示】
            $data['text_12'] = [
                'type' => TITLE,
                'label' => __('labels.a210.text_12'),
            ];

            if ($trademark['deposit_type'] == \App\Models\Agent::DEPOSIT_TYPE_ADVENCE) {
                // 【予納台帳番号】
                $data['deposit_account_number'] = [
                    'type' => TEXT,
                    'label' => __('labels.a210.deposit_account_number') . '　　　　　　　　',
                    'value' => $trademark['deposit_account_number'] ?? '',
                ];
            } elseif ($trademark['deposit_type'] == \App\Models\Agent::DEPOSIT_TYPE_CREDIT) {
                // 【指定立替納付】
                $data['deposit_account_number_v2'] = [
                    'type' => TEXT,
                    'label' => __('labels.a210.deposit_account_number_v2') . '　　　　　　　　',
                    'value' => '',
                ];
            }

            // 【納付金額】
            $data['print_fee'] = [
                'type' => TEXT,
                'label' => __('labels.a210.print_fee') . '　　　　　　　　　',
                'value' => $trademark['print_fee'] ?? '',
            ];
        }

        $htmlName = '期間延長請求書.html';
        $htmlPath = FOLDER_TEMP . '/' . $htmlName;
        $dataExport = $this->formatExportData($data);
        $this->genHTML($dataExport['dataHTML'], $htmlPath, [
            'title' => '期間延長請求書',
        ]);

        return [
            'html_path' => asset($htmlPath),
            'html_name' => $htmlName,
        ];
    }
}
