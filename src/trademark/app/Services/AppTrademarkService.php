<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\AgentGroup;
use App\Models\AppTrademark;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\Payment;
use App\Models\Trademark;
use App\Models\AppTrademarkProd;
use App\Models\MailTemplate;
use App\Models\MPriceList;
use App\Models\MProduct;
use App\Models\Precheck;
use App\Models\SupportFirstTime;
use App\Repositories\AppTrademarkRepository;
use App\Repositories\AppTrademarkProdRepository;
use App\Repositories\NoticeDetailRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Services\TrademarkService;
use App\Services\TrademarkInfoService;
use App\Services\PayerInfoService;
use App\Services\MPriceListService;
use App\Services\MailTemplateService;
use App\Services\Common\NoticeService as CommonNoticeService;
use App\Services\MProductService;
use App\Repositories\PrecheckRepository;

class AppTrademarkService extends BaseService
{
    protected AppTrademarkRepository        $appTrademarkRepository;
    protected TrademarkService              $trademarkService;
    protected AppTrademarkProdRepository    $appTrademarkProdRepository;
    protected TrademarkInfoService          $trademarkInfoService;
    protected PayerInfoService              $payerInfoService;
    protected MPriceListService             $mPriceListService;
    protected PaymentRepository             $paymentRepository;
    protected CommonNoticeService           $commonNoticeService;
    protected MProductService               $mProductService;
    protected MailTemplateService           $mailTemplateService;
    protected PrecheckRepository            $precheckRepository;
    protected NoticeDetailRepository        $noticeDetailRepository;
    /**
     * Initializing the instances and variables
     *
     * @param AppTrademarkRepository $appTrademarkRepository
     * @param TrademarkService $trademarkService
     * @param AppTrademarkProdRepository $appTrademarkProdRepository
     * @param TrademarkInfoService $trademarkInfoService
     * @param PaymentRepository $paymentRepository
     * @param CommonNoticeService $commonNoticeService
     * @param MProductService $mProductService
     * @param PrecheckRepository $precheckRepository
     */
    public function __construct(
        AppTrademarkRepository $appTrademarkRepository,
        TrademarkService $trademarkService,
        AppTrademarkProdRepository $appTrademarkProdRepository,
        TrademarkInfoService $trademarkInfoService,
        PayerInfoService $payerInfoService,
        MPriceListService $mPriceListService,
        PaymentRepository $paymentRepository,
        CommonNoticeService $commonNoticeService,
        MProductService $mProductService,
        MailTemplateService $mailTemplateService,
        PrecheckRepository $precheckRepository,
        NoticeDetailRepository $noticeDetailRepository
    )
    {
        $this->repository = $appTrademarkRepository;
        $this->trademarkService = $trademarkService;
        $this->appTrademarkProdRepository = $appTrademarkProdRepository;
        $this->trademarkInfoService = $trademarkInfoService;
        $this->payerInfoService = $payerInfoService;
        $this->mPriceListService = $mPriceListService;
        $this->paymentRepository = $paymentRepository;
        $this->commonNoticeService = $commonNoticeService;
        $this->mProductService = $mProductService;
        $this->mailTemplateService = $mailTemplateService;
        $this->precheckRepository = $precheckRepository;
        $this->noticeDetailRepository = $noticeDetailRepository;
    }

    /**
     * Cancel app trademark
     *
     * @param int $id - app_trademark_id
     * @return boolean
     */
    public function cancelAppTrademark(int $id): bool
    {
        DB::beginTransaction();
        try {
            $appTradeMark = $this->getAppTradeMarkOfUser($id);

            if (!$appTradeMark) {
                return false;
            }

            $appTradeMark->update(['is_cancel' => AppTrademark::IS_CANCEL_TRUE]);
            $trademark = $this->trademarkService->find($appTradeMark->trademark_id);
            $trademark->load('user');

            if ($trademark) {
                // Update Notice
                $stepBeforeNotice = $this->noticeDetailRepository->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                ])->with('notice')->get()
                    ->where('notice.trademark_id', $trademark->id)
                    ->where('notice.user_id', $trademark->user_id)
                    ->where('notice.flow', Notice::FLOW_APP_TRADEMARK);
                $stepBeforeNotice->map(function ($item) {
                    $item->update([
                        'is_answer' => NoticeDetail::IS_ANSWER,
                    ]);
                });

                $this->commonNoticeService->sendNotice([
                    'notices' => [
                        'flow' => Notice::FLOW_APP_TRADEMARK,
                        'user_id' => Auth::user()->id,
                        'trademark_id' => $trademark->id,
                    ],
                    'notice_details' => [
                        // A-000top
                        [
                            'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                            'content' => '出願：中止指示受領',
                            'target_page' => route('user.apply-trademark.cancel-register', $trademark->id),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'attribute' => 'お客様から',
                        ],
                        // Send Notice jimu
                        [
                            'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                            'content' => '出願：中止指示受領',
                            'target_page' => route('user.apply-trademark.cancel-register', $trademark->id),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'attribute' => 'お客様から',
                            'completion_date' => Carbon::now(),
                        ],
                        // Send Notice user
                        [
                            'target_id' => $trademark->user_id,
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'content' => '出願：中止',
                            'target_page' => route('user.apply-trademark.cancel-register', $trademark->id),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'is_action' => 1,
                        ],
                        [
                            'target_id' => $trademark->user_id,
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'content' => '出願：中止',
                            'target_page' => route('user.apply-trademark.cancel-register', $trademark->id),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        ],
                    ],
                ]);
            }
            //TODO: send notification
            DB::commit();

            $params = [
                'from_page' => U032_CANCEL,
                'user' => $trademark->user
            ];
            // Send mail 出願：提出書類ご確認
            $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_OTHER);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }

    /**
     * Create Payment precheck
     *
     * @param mixed $dataRequest
     * @return mixed
     */
    public function applyTrademarkFreeInputCreate($dataRequest)
    {
        try {
            DB::beginTransaction();
            $request = $dataRequest->all();
            if ($request['redirect_to'] == AppTrademark::REDIRECT_TO_COMMON_PAYMENT ||
                $request['redirect_to'] == AppTrademark::REDIRECT_TO_QUOTE ||
                $request['redirect_to'] == AppTrademark::REDIRECT_TO_ANKEN_TOP
            ) {
                $agentGroup = AgentGroup::where('status_choice', AgentGroup::STATUS_CHOICE_TRUE)->first();

                // Create trademark
                $inputs['id'] = $request['trademark_id'];
                $inputs['type_trademark'] = $request['type_trademark'];
                $inputs['name_trademark'] = $request['name_trademark'];
                if (!empty($request['image_trademark']) && $dataRequest->hasFile('image_trademark')) {
                    $inputs['image_trademark'] = $request['image_trademark'];
                }
                $inputs['reference_number'] = $request['reference_number'];
                $trademark = $this->trademarkService->updateOrCreateTrademark($inputs);

                // Create App Trademark
                $conditionAppTrademark = [
                    'trademark_id' => $trademark->id,
                    'admin_id' => 1,
                    'agent_group_id' => $agentGroup->id,
                    'status' => AppTrademark::STATUS_UNREGISTERED_SAVE,
                    'pack' => AppTrademark::PACK_A,
                    'is_mailing_regis_cert' => AppTrademark::IS_MAILING_REGIS_CERT_TRUE,
                    'period_registration' => AppTrademark::PERIOD_REGISTRATION_FALSE,
                    'cancellation_deadline' => Carbon::now(),
                    'type_page' => AppTrademark::PAGE_TYPE_U031EDIT
                ];
                $appTrademark = $this->updateOrCreate(['trademark_id' => $trademark->id], $conditionAppTrademark);

                // Insert product
                $prodNotApply = array_diff($request['mProducts'] ?? [], $request['m_product_ids'] ?? []);

                // Prod no apply
                foreach ($prodNotApply as $idProd) {
                    AppTrademarkProd::updateOrCreate([
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $idProd
                    ], [
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $idProd,
                        'is_apply' => TYPE_APP_TRADEMARK_PRODUCT_UNCHECKED,
                    ]);
                }

                // Prod apply
                foreach ($request['m_product_ids'] ?? [] as $idProd) {
                    AppTrademarkProd::updateOrCreate([
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $idProd
                    ], [
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $idProd,
                        'is_apply' => TYPE_APP_TRADEMARK_PRODUCT_CHECKED,
                    ]);
                }

                // Insert product
                $arrAddProduct = [];
                $prods = $request['prod'] ?? [];
                if (!empty($prods)) {
                    foreach ($prods as $prod) {
                        $appTrademarkProdID = $prod['app_trademark_prod_id'] ?? null;
                        $nameProduct = $prod['name_product'] ?? null;
                        $mDistinctionID = $prod['m_distinction_id'] ?? null;

                        if (!empty($nameProduct) && !empty($mDistinctionID)) {
                            $mProduct = $this->mProductService->findByCondition([
                                'm_distinction_id' => $mDistinctionID,
                                'name' => $nameProduct,
                            ])->first();

                            if (empty($mProduct)) {
                                $productsNumber = $this->mProductService
                                    ->generateProductCode(MProduct::TYPE_CREATIVE_CLEAN, $mDistinctionID);

                                $mProduct = $this->mProductService->create([
                                    'products_number' => $productsNumber,
                                    'name' => $nameProduct,
                                    'm_distinction_id' => $mDistinctionID,
                                    'admin_id' => 1,
                                    'type' => MProduct::TYPE_CREATIVE_CLEAN,
                                ]);
                            }

                            if (!empty($appTrademarkProdID)) {
                                $this->appTrademarkProdRepository->updateOrCreate([
                                    'id' => $appTrademarkProdID,
                                ], [
                                    'app_trademark_id' => $appTrademark->id,
                                    'm_product_id' => $mProduct->id,
                                    'is_apply' => isset($prod['check']) ? TYPE_APP_TRADEMARK_PRODUCT_CHECKED : TYPE_APP_TRADEMARK_PRODUCT_UNCHECKED,
                                ]);

                                if (isset($prod['check'])) {
                                    $arrAddProduct[] = $mProduct->id;
                                }
                            } else {
                                $this->appTrademarkProdRepository->updateOrCreate([
                                    'app_trademark_id' => $appTrademark->id,
                                    'm_product_id' => $mProduct->id
                                ], [
                                    'app_trademark_id' => $appTrademark->id,
                                    'm_product_id' => $mProduct->id,
                                    'is_apply' => isset($prod['check']) ? TYPE_APP_TRADEMARK_PRODUCT_CHECKED : TYPE_APP_TRADEMARK_PRODUCT_UNCHECKED,
                                ]);
                                if (isset($prod['check'])) {
                                    $arrAddProduct[] = $mProduct->id;
                                }
                            }
                        }
                    }
                }

                // Create Or Update Trademark Info
                $this->trademarkInfoService->updateOrCreateTrademarkInfo($request['data'], $trademark->id);

                // Create Or Update payer information
                $payerInfo = null;
                if ($appTrademark->id && $request['m_nation_id'] && $request['payer_name'] && $request['payer_name_furigana']) {
                    if (isset($request['m_prefecture_id']) && $request['m_prefecture_id'] == "null") {
                        $request['m_prefecture_id'] = null;
                    }
                    $payerInfo = $this->payerInfoService->updateOrCreate([
                        'target_id' => $appTrademark->id,
                        'type' => TYPE_APP_TRADEMARK
                    ], [
                        "target_id" => $appTrademark->id,
                        "payment_type" => $request['payment_type'] ?? null,
                        "payer_type" => $request['payer_type'] ?? 0,
                        "m_nation_id" => $request['m_nation_id'] ?? null,
                        "payer_name" => $request['payer_name'] ?? '',
                        "payer_name_furigana" => $request['payer_name_furigana'] ?? '',
                        "postal_code" => $request['postal_code'] ?? null,
                        "m_prefecture_id" => $request['m_prefecture_id'] ?? null,
                        "address_second" => $request['address_second'] ?? '',
                        "address_three" => $request['address_three'] ?? '',
                        "type" => TYPE_APP_TRADEMARK,
                    ]);
                }

                $countAddProd = 0;
                if (isset($request['prod'])) {
                    foreach ($request['prod'] as $key => $value) {
                        if (isset($value['check'])) {
                            $countAddProd++;
                        };
                    }
                }

                $countProductApply = (isset($request['m_product_ids']) ? count($request['m_product_ids']) : 0) + $countAddProd;
                $reduceNumberDistitions = $countProductApply > 3 ? $countProductApply - 3 : 0;
                $application = $this->precheckRepository->getPriceOnePackService(MPriceList::APPLICATION, MPriceList::PACK_A_UP_3_ITEMS);
                $arrProdSystemAndAdd = [];
                // Create payment with payment status is
                if ($payerInfo != null) {
                    // Create payment with payment status is
                    $createPayment = [
                        "target_id" => $appTrademark->id,
                        "payer_info_id" => $payerInfo->id,
                        "trademark_id" => $trademark->id,
                        "cost_bank_transfer" => null,
                        "subtotal" => floor($request['subtotal']) ?? 0,
                        "commission" => floor($request['commission']) ?? 0,
                        "tax" => floor($request['tax']) ?? 0,
                        "cost_service_base" => floor($request['cost_service_base']) ?? 0,
                        "cost_service_add_prod" => floor($request['cost_service_add_prod']) ?? 0,
                        "total_amount" => floor($request['total_amount']) ?? 0,
                        "tax_withholding" => 0,
                        "payment_amount" => 0,
                        "type" => TYPE_APP_TRADEMARK,
                        "cost_print_application_one_distintion" => $application['pof_1st_distinction_5yrs'] ?? 0,
                        "cost_print_application_add_distintion" => $application['pof_2nd_distinction_5yrs'] ?? 0,
                        "cost_5_year_one_distintion" => (int) $request['cost_5_year_one_distintion'] ?? 0,
                        "reduce_number_distitions" => $reduceNumberDistitions,
                        "payment_date" => now(),
                        "is_treatment" => Payment::IS_TREATMENT_WAIT,
                        "payment_status" => Payment::STATUS_SAVE,
                        "from_page" => U031EDIT
                    ];

                    $paymentFee = $this->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
                    if ($request['payment_type'] == Payment::BANK_TRANSFER) {
                        $createPayment['cost_bank_transfer'] = $paymentFee['cost_service_base'] ?? 0;
                    }

                    $createPayment['tax_withholding'] = floor($this->getTaxWithHolding($payerInfo, $request['total_amount']));

                    $createPayment['payment_amount'] = floor($request['total_amount'] - $createPayment['tax_withholding']);

                    $payment = $this->paymentRepository->createPayment($createPayment);

                    $dataUpdatePayment = [];
                    if (!$payment->quote_number) {
                        $dataUpdatePayment['quote_number'] = $this->generateQIR($trademark->trademark_number, 'quote');
                    }
                    if (!$payment->invoice_number) {
                        $dataUpdatePayment['invoice_number'] = $this->generateQIR('', 'invoice');
                    }
                    if (!$payment->invoice_number) {
                        $dataUpdatePayment['receipt_number'] = $this->generateQIR('', 'receipt');
                    }
                    $payment->update($dataUpdatePayment);

                    $arrProdSystemAndAdd = array_merge(array_values($request['m_product_ids'] ?? []), array_values($arrAddProduct));

                    $dataPaymentPro['payment_id'] = $payment->id;
                    $dataPaymentPro['productIds'] = $arrProdSystemAndAdd;

                    $this->paymentRepository->createPaymentProds($dataPaymentPro);
                }
                Session::forget(SESSION_APPLY_TRADEMARK_EDIT);
            }

            DB::commit();

            $key = Str::random(11);
            switch ($request['redirect_to']) {
                case AppTrademark::REDIRECT_TO_QUOTE:
                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_QUOTE,
                        'payment_id' => $payment->id,
                    ];
                case AppTrademark::REDIRECT_TO_COMMON_PAYMENT:
                    if (count($arrProdSystemAndAdd) == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }

                    $params = $request;
                    $params['payment_id'] = $payment->id;
                    $params['from_page'] = U031EDIT;
                    $params['productIds'] = $arrProdSystemAndAdd;
                    $params['payment_type'] = $payerInfo->payment_type;
                    $params['trademark_id'] = $trademark->id;
                    $params['app_trademark_id'] = $appTrademark->id;
                    unset($params['image_trademark']);
                    Session::put($key, $params);

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_COMMON_PAYMENT,
                        'key_session' => $key,
                    ];

                case AppTrademark::REDIRECT_TO_ANKEN_TOP:
                    if (isset($results['isAjax']) && $request['isAjax']) {
                        return [
                            'redirect_to' => Precheck::REDIRECT_TO_ANKEN_TOP,
                            'trademark_id' => $trademark->id,
                            'isAjax' => true
                        ];
                    }
                    return [
                        'redirect_to' => Precheck::REDIRECT_TO_ANKEN_TOP,
                        'trademark_id' => $trademark->id,
                    ];
                default:
                    return [];
                    break;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }

    /**
     * Get app trademark of user
     *
     * @param int $appTrademarkId
     * @return Model
     */
    public function getAppTradeMarkOfUser(int $appTrademarkId): ?Model
    {
        return $this->repository->findByCondition(['id' => $appTrademarkId])->whereHas('trademark', function ($q) {
            $q->where('user_id', auth()->user()->id);
        })->first();
    }

    /**
     * Get app trademark of user find by condition
     *
     * @param array $input
     * @return Model
     */
    public function getAppTradeMarkOfUserFindByCondition(array $input): ?Model
    {
        return $this->repository->findByCondition($input)->whereHas('trademark', function ($q) {
            $q->where('user_id', auth()->user()->id);
        })->first();
    }

    /**
     * Get app trademark not applied yet.
     */
    public function getAppTrademarkApply($params = []): ?Builder
    {
        $data = $this->repository->findByCondition($params, []);

        return $data;
    }

    /**
     * Get app trademark not applied yet.
     *
     * @param array $params
     */
    public function getAppTrademark($params = []): ?Builder
    {
        $data = $this->repository->findByCondition($params, [
            'trademark' => function ($query) use ($params) {
                return $query->with([
                    'payment' => function ($query) {
                        return $query->whereIn('type', [Payment::TYPE_TRADEMARK, Payment::TYPE_SUPPORT_FIRST_TIME, Payment::TYPE_PRECHECK]);
                    },
                ]);
            },
            'appTrademarkProd' => function ($query) {
                return $query->with(['mProduct.mDistinction'])->where('is_apply', AppTrademarkProd::IS_APPLY);
            },
        ]);
        if (isset($params['status']) && $params['status'] == AppTrademark::NOT_APPLY) {
            $data->whereHas('trademark.appTrademark');
        }
        if (isset($params['status']) && $params['status'] == AppTrademark::APPLY) {
            $data->whereHas('trademark', function ($query) {
                return $query->orWhereHas('supportFirstTime', function ($query) {
                    return $query->where('status_register', 0);
                })->orWhereHas('prechecks', function ($query) {
                    return $query->where('status_register', 0);
                });
            });
        }

        return $data;
    }

    /**
     * Get app trademark waiting apply.
     *
     * @param array $params
     */
    public function getAppTrademarkWaitingApply($params)
    {
        $user = Auth::guard('web')->user();
        $data = $this->repository->findByCondition($params, [
            'trademark' => function ($query) use ($user) {
                return $query->with([
                    'payment' => function ($query) {
                        return $query->whereIn('type', [Payment::TYPE_TRADEMARK, Payment::TYPE_SUPPORT_FIRST_TIME, Payment::TYPE_PRECHECK]);
                    },
                ])->where('user_id', $user->id)->OrWhereHas('supportFirstTime')->orWhereHas('prechecks');
            },
            'appTrademarkProd' => function ($query) {
                return $query->with(['mProduct.mDistinction'])->where('is_apply', AppTrademarkProd::IS_APPLY);
            },
        ])->whereHas('trademark', function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        });

        return $data;
    }

    /**
     * Get app trademark waiting apply.
     *
     * @param array $params
     */
    public function getAppTrademarkNotApply($params)
    {
        $user = Auth::guard('web')->user();

        $trademark = DB::select("
            SELECT
                trademarks.id,
                trademarks.user_id,
                trademarks.trademark_number,
                trademarks.type_trademark,
                trademarks.name_trademark,
                trademarks.image_trademark,
                trademarks.reference_number,
                app_trademarks.`status`,
                CASE
                    WHEN app_trademarks.`status` = " . $params['status'] . "
                        AND  app_trademarks.`type_page` IN (" . implode(',', $params['type_pages']) . ") THEN '1'
                    WHEN EXISTS(SELECT * FROM support_first_times AS sft
                        WHERE(sft.trademark_id = trademarks.id)
                        AND sft.status_register = " . SupportFirstTime::NOT_REGISTER . ")
                        AND NOT EXISTS(
                            SELECT * FROM app_trademarks WHERE(
                                app_trademarks.trademark_id = trademarks.id
                            )) then '1'
                    WHEN EXISTS(SELECT * FROM prechecks WHERE(prechecks.trademark_id = trademarks.id)
                        AND prechecks.status_register = " . Precheck::NOT_STATUS_REGISTER . ")
                        AND NOT EXISTS(SELECT * FROM app_trademarks WHERE(app_trademarks.trademark_id = trademarks.id)) then '1'
                END AS `accept`,
                CASE
                    WHEN EXISTS(SELECT * FROM app_trademarks WHERE(app_trademarks.trademark_id = trademarks.id)) THEN app_trademarks.created_at
                    WHEN EXISTS(SELECT * FROM support_first_times WHERE(support_first_times.trademark_id = trademarks.id)
                        AND support_first_times.status_register = " . SupportFirstTime::NOT_REGISTER . ") THEN sft.created_at
                    WHEN EXISTS(SELECT * FROM prechecks WHERE(prechecks.trademark_id = trademarks.id)
                        AND prechecks.status_register = " . Precheck::NOT_STATUS_REGISTER . ") THEN prechecks.created_at
                END as created_at
            FROM trademarks
                LEFT JOIN support_first_times AS sft ON trademarks.`id` = sft.`trademark_id`
                LEFT JOIN prechecks ON prechecks.`id` = prechecks.`trademark_id`
                LEFT JOIN app_trademarks ON trademarks.id = app_trademarks.trademark_id
                WHERE trademarks.user_id = $user->id AND trademarks.deleted_at IS NULL
                ORDER BY `created_at`" . $params['sort_type']);

        $trademark = collect($trademark)->unique()->where('accept', 1)->map((function ($item) {
            $element = new Trademark((array) $item);
            $element->load([
                'payment' => function ($query) {
                    return $query->whereIn('type', [Payment::TYPE_TRADEMARK, Payment::TYPE_SUPPORT_FIRST_TIME, Payment::TYPE_PRECHECK]);
                },
                'appTrademark.appTrademarkProd' => function ($query) {
                    return $query->where('is_apply', AppTrademarkProd::IS_APPLY);
                },
                'supportFirstTime.StfSuitableProduct', 'prechecks.products'
            ]);

            return $element;
        }));

        return $trademark;
    }

    /**
     * Get Product App Trademark
     *
     * @param  mixed $tradeMarkId
     * @return Collection
     */
    public function getProductAppTrademark($tradeMarkId): Collection
    {
        return $this->repository->getProductAppTrademark($tradeMarkId);
    }

    /**
     * Filter App List
     *
     * @param  array $fillter
     * @return Collection
     */
    public function filterAppList(array $fillter = []): Collection
    {
        $datas = $this->repository->findByCondition([])
            ->where('status', '<>', 0)
            ->get();

        $datas = $this->formatDataList($datas);
        $withClose = false;
        if (!empty($fillter)) {
            $searchHasClose = $fillter['searchHasClose'] ?? 'false';
            if ($searchHasClose == 'true') {
                $withClose = true;
            }

            $searchType = $fillter['searchType'] ?? 'and';
            $searchData = $fillter['searchData'] ?? [];
            if ($searchType == 'or') {
                $dataCollection = collect();
                $hasFilter = false;
                foreach ($searchData as $search) {
                    if (!empty($search['value'])) {
                        $hasFilter = true;
                        $field = $search['field'] ?? '';
                        $value = $search['value'];
                        $condition = $search['condition'] ?? '';
                        // equal start_from consists_of is_greater_than is_less_than
                        switch ($condition) {
                            case 'equal':
                                $dataFilter = $datas->filter(function ($item) use ($field, $value) {
                                    return $item->search[$field] == $value;
                                });
                                $dataCollection = $dataCollection->merge($dataFilter);
                                break;
                            case 'start_from':
                                $dataFilter = $datas->filter(function ($item) use ($field, $value) {
                                    return str_starts_with($item->search[$field], $value);
                                });
                                $dataCollection = $dataCollection->merge($dataFilter);
                                break;
                            case 'consists_of':
                                $dataFilter = $datas->filter(function ($item) use ($field, $value) {
                                    return str_contains($item->search[$field], $value);
                                });
                                $dataCollection = $dataCollection->merge($dataFilter);
                                break;
                            case 'is_greater_than':
                                $dataFilter = $datas->filter(function ($item) use ($field, $value) {
                                    return $value >= $item->search[$field];
                                });
                                $dataCollection = $dataCollection->merge($dataFilter);
                                break;
                            case 'is_less_than':
                                $dataFilter = $datas->filter(function ($item) use ($field, $value) {
                                    return $value <= $item->search[$field];
                                });
                                $dataCollection = $dataCollection->merge($dataFilter);
                                break;
                        }
                    }
                }
                if ($hasFilter == true) {
                    $datas = $dataCollection->unique();
                }
            } else {
                foreach ($searchData as $search) {
                    if (!empty($search['value'])) {
                        $field = $search['field'] ?? '';
                        $value = $search['value'];
                        $condition = $search['condition'] ?? '';
                        // equal start_from consists_of is_greater_than is_less_than
                        switch ($condition) {
                            case 'equal':
                                $datas = $datas->filter(function ($item) use ($field, $value) {
                                    return $item->search[$field] == $value;
                                });
                                break;
                            case 'start_from':
                                $datas = $datas->filter(function ($item) use ($field, $value) {
                                    return str_starts_with($item->search[$field], $value);
                                });
                                break;
                            case 'consists_of':
                                $datas = $datas->filter(function ($item) use ($field, $value) {
                                    return str_contains($item->search[$field], $value);
                                });
                                break;
                            case 'is_greater_than':
                                $datas = $datas->filter(function ($item) use ($field, $value) {
                                    return $value >= $item->search[$field];
                                });
                                break;
                            case 'is_less_than':
                                $datas = $datas->filter(function ($item) use ($field, $value) {
                                    return $value <= $item->search[$field];
                                });
                                break;
                        }
                    }
                }
            }
        }

        if ($withClose == false) {
            $datas = $datas->where('is_app_cancel', false);
        }

        return $datas;
    }

    /**
     * Format Data App List
     *
     * @param  Collection $datas
     * @return Collection
     */
    public function formatDataList(Collection $datas): Collection
    {
        $datas = $datas->load([
            'trademarkInfo',
            'trademark',
            'trademark.user',
            'trademark.registerTrademark',
            'trademark.comparisonTrademarkResult',
            'trademark.notices',
            'trademark.notices.noticeDetails',
        ]);

        $datas->map(function ($item) {
            $trademark = $item->trademark;
            $user = $trademark->user;
            $registerTrademark = $trademark->registerTrademark;

            // Set Trademark Relation
            $item->user = $user;
            $item->register_trademark = $registerTrademark;

            // user_info_name
            $item->user_info_name = (!empty($user)) ? $user->info_name : '';

            // trademark_info_name
            $trademarkInfos = $item->trademarkInfo;
            $lastTrademarkInfo = $trademarkInfos->last();

            $trademarkInfoName = '';
            if (!empty($registerTrademark) && !empty($registerTrademark->register_number)) {
                $trademarkInfoName = $registerTrademark->trademark_info_name;
            } else {
                if (!empty($lastTrademarkInfo)) {
                    $trademarkInfoName = $lastTrademarkInfo->name;
                }
            }
            $item->trademark_info_name = $trademarkInfoName;

            // Last Notice
            $notices = $trademark->notices;
            $lastNotice = $notices->last();
            $notice_detail = null;
            if (!empty($lastNotice)) {
                $noticeDetails = $lastNotice->noticeDetails;
                $lastNoticeDetail = $noticeDetails->last();
                $notice_detail = $lastNoticeDetail;
            }
            $item->notice_detail = $notice_detail;

            // Get comparison response_deadline
            $item->is_expried = false;
            $item->is_coming_deadline = false;
            if (!empty($trademark)) {
                $comparisonTrademarkResult = $trademark->comparisonTrademarkResult ?? null;

                if (!empty($comparisonTrademarkResult)) {
                    $comparisonResponseDeadline = $comparisonTrademarkResult->response_deadline;
                    if (!empty($comparisonResponseDeadline)) {
                        $comparisonResponseDeadline = Carbon::parse($comparisonResponseDeadline);
                        $now = Carbon::now();

                        if ($comparisonResponseDeadline < $now) {
                            $item->is_expried = true;
                        } elseif ($now > $comparisonResponseDeadline->subDay(10)) {
                            $item->is_coming_deadline = true;
                        }
                    }
                }
            }

            $item->is_app_cancel = $this->isCancel($item);

            // Order Field
            $item->trademark_number = $trademark->trademark_number ?? null;
            $item->register_number = $registerTrademark->register_number ?? null;
            $item->notice_updated_at = $notice_detail->updated_at ?? null;

            // Search
            $item->search = [
                'trademark_number' => $trademark->trademark_number ?? null,
                'created_at' => (!empty($trademark->created_at)) ? $trademark->created_at->format('Y-m-d') : null,
                'name_trademark' => $trademark->name_trademark ?? null,
                'application_date' => $trademark->application_date ?? null,
                'application_number' => $trademark->application_number ?? null,
                'date_register' => $registerTrademark->date_register ?? null,
                'register_number' => $registerTrademark->register_number ?? null,
                'notice_content' => $item->notice_detail->content ?? null,
                'user_info_name' => $item->user_info_name ?? null,
                'trademark_info_name' => (!empty($lastTrademarkInfo)) ? $lastTrademarkInfo->name : null,
                'register_trademark_info_name' => $registerTrademark->trademark_info_name ?? null,
                'user_id' => $trademark->user_id ?? null
            ];

            return $item;
        });

        return $datas;
    }

    /**
     * Is Cancel Notice
     *
     * @param AppTrademark $item
     * @return boolean
     */
    public function isCancel(AppTrademark $item): bool
    {
        if ($item->is_cancel == true) {
            return true;
        }

        $trademark = $item->trademark;
        if (!empty($trademark) && $trademark->status_management == false) {
            return true;
        }

        if ($item->is_expried == true) {
            return true;
        }

        $registerTrademark = $trademark->registerTrademark;
        if (!empty($registerTrademark) && !empty($registerTrademark->date_register)) {
            $now = Carbon::now();

            $dateRegister = $registerTrademark->date_register;
            $dateRegister = Carbon::parse($dateRegister);

            if ($registerTrademark->period_registration == AppTrademark::PERIOD_REGISTRATION_5_YEAR) {
                $nextPeriod = $dateRegister->addYear(5);
            } elseif ($registerTrademark->period_registration == AppTrademark::PERIOD_REGISTRATION_10_YEAR) {
                $nextPeriod = $dateRegister->addYear(10);
            }

            if (isset($nextPeriod) && $now > $nextPeriod) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get data product of u031 pass
     *
     * @param int $id
     * @return array
     */
    public function getDataProductByAppTrademark(int $id)
    {
        $data = $this->repository->getDataProductByAppTrademark($id)->groupBy('trademark_info_name');
        $countProduct = count($this->repository->getDataProductByAppTrademark($id));
        $datas = [];
        foreach ($data as $key => $item) {
            $datas[] = [
                'trademark_info_name' => $key,
                'distinction' => $item,
            ];
        }
        foreach ($datas as &$data) {
            $valueGroupBy = $data['distinction']->groupBy('distinction_name');
            $dataMap = [];
            foreach ($valueGroupBy as $key => &$value) {
                $dataMap[] = [
                    'distinction_name' => $key,
                    'product' => $value
                ];
            }
            $data['distinction'] = $dataMap;
        }
        foreach ($datas as &$data) {
            $countProduct = 0;
            foreach ($data['distinction'] as $dataItem) {
                if (count($dataItem['product']) >= 1) {
                    $countProduct += count($dataItem['product']);
                }
            }
            $data['countProduct'] = $countProduct;
        }
        return ['datas' => $datas, 'countProduct' => $countProduct];
    }

    /**
     * Save data apply trademark after search
     *
     * @param array $params
     * @return void
     */
    public function saveDataApplyTrademarkAfterSearch(array $params)
    {
        DB::beginTransaction();
        try {
            $agentGroup = AgentGroup::where('status_choice', AgentGroup::STATUS_CHOICE_TRUE)->first();
            //package_type, is_mailing_register_cert, period_registration, total_distinction, m_product_ids
            $billParams = [
                'm_product_ids' => $params['m_product_ids_choose'],
                'is_mailing_register_cert' => $params['is_mailing_regis_cert'] ?? 0,
                'period_registration' => $params['period_registration'] ?? 0,
                'package_type' => $params['pack'],
                'payment_type' => $params['payment_type'],
                'total_distinction' => $params['total_distinction'],
                'from_page' => U031B,
            ];

            //info cart
            $dataBill = $this->paymentRepository->ajaxGetCartInfoPayment($billParams);
            //1. Create or update trademark
            $inputs['id'] = $params['trademark_id'];
            $inputs['type_trademark'] = $params['type_trademark'];
            $inputs['name_trademark'] = $params['name_trademark'];
            $inputs['image_trademark'] = $params['image_trademark'] ?? '';
            $inputs['reference_number'] = $params['reference_number'];

            $trademark = $this->trademarkService->updateOrCreateTrademark($inputs);

            //2.app_trademarks
            $conditionAppTrademark = [
                'trademark_id' => $trademark->id,
                'admin_id' => 1,
                'agent_group_id' => $agentGroup->id,
                'status' => AppTrademark::STATUS_UNREGISTERED_SAVE,
                'pack' => $params['pack'],
                'is_mailing_regis_cert' => $params['is_mailing_regis_cert'] ?? 0,
                'period_registration' => $params['period_registration'] ?? AppTrademark::PERIOD_REGISTRATION_5_YEAR,
                'type_page' => AppTrademark::PAGE_TYPE_U031B
            ];
            $appTrademark = $this->updateOrCreate(['trademark_id' => $trademark->id], $conditionAppTrademark);

            //2. create or update app_trademark_prods
            if ($appTrademark) {
                foreach ($params['m_product_ids'] as $productId) {
                    $isApply = AppTrademarkProd::IS_NOT_APPLY;
                    if (in_array($productId, $params['m_product_ids_choose'])) {
                        $isApply = AppTrademarkProd::IS_APPLY;
                    }
                    $this->appTrademarkProdRepository->updateOrCreate([
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $productId,
                    ], [
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $productId,
                        'is_apply' => $isApply
                    ]);
                }
            }
            //3.trademark_infos
            $trademarkInfo = $this->trademarkInfoService->updateOrCreateTrademarkInfo($params['data'], $trademark->id);

            //4.payer_infos
            $payerInfo = $this->payerInfoService->updateOrCreate([
                'target_id' => $appTrademark->id,
                'type' => TYPE_APP_TRADEMARK
            ], [
                "target_id" => $appTrademark->id,
                "payment_type" => $params['payment_type'] ?? null,
                "payer_type" => $params['payer_type'] ?? 0,
                "m_nation_id" => $params['m_nation_id'] ?? null,
                "payer_name" => $params['payer_name'] ?? '',
                "payer_name_furigana" => $params['payer_name_furigana'] ?? '',
                "postal_code" => $params['postal_code'] ?? null,
                "m_prefecture_id" => $params['m_prefecture_id'] ?? null,
                "address_second" => $params['address_second'] ?? '',
                "address_three" => $params['address_three'] ?? '',
                "type" => TYPE_APP_TRADEMARK,
            ]);

            $countProductApply = (isset($params['m_product_ids_choose']) ? count($params['m_product_ids_choose']) : 0);
            $reduceNumberDistitions = $countProductApply > 3 ? $countProductApply - 3 : 0;
            //5.payments
            $createPayment = [
                "target_id" => $appTrademark->id,
                "payer_info_id" => $payerInfo->id,
                "trademark_id" => $trademark->id,
                "cost_bank_transfer" => floor($dataBill['cost_bank_transfer']),
                "subtotal" => floor($dataBill['subtotal']) ?? 0,
                "commission" => floor($dataBill['commission']) ?? 0,
                "tax" => floor($dataBill['tax']) ?? 0,
                "cost_service_base" => floor($dataBill['cost_service_base']) ?? 0,
                "cost_service_add_prod" => floor($dataBill['cost_service_add_prod_default']) ?? 0,
                "total_amount" => floor($dataBill['total_amount']) ?? 0,
                "tax_withholding" => 0,
                "payment_amount" => 0,
                "type" => TYPE_APP_TRADEMARK,
                "cost_print_application_one_distintion" => $dataBill['cost_print_application_one_distintion'] ?? 0,
                "cost_print_application_add_distintion" => $dataBill['cost_print_application_add_distintion'] ?? 0,
                "reduce_number_distitions" => $reduceNumberDistitions,
                "cost_registration_certificate" => $dataBill['cost_registration_certificate'],
                "cost_change_registration_period" => $dataBill['cost_change_registration_period'],
                "payment_date" => now(),
                "is_treatment" => Payment::IS_TREATMENT_WAIT,
                "payment_status" => Payment::STATUS_SAVE,
            ];
            if (isset($params['period_registration']) && $params['period_registration'] == AppTrademark::PERIOD_REGISTRATION_TRUE) {
                $createPayment['cost_10_year_one_distintion'] = $dataBill['cost_year_one_distintion'] ?? 0;
            } else {
                $createPayment['cost_5_year_one_distintion'] = $dataBill['cost_year_one_distintion'] ?? 0;
            }

            $createPayment['tax_withholding'] = floor($this->getTaxWithHolding($payerInfo, $dataBill['total_amount']));
            $createPayment['payment_amount'] = floor($dataBill['total_amount'] - $createPayment['tax_withholding']);
            $createPayment['from_page'] = U031B;
            $payment = $this->paymentRepository->createPayment($createPayment);

            $dataUpdatePayment = [];
            if (!$payment->quote_number) {
                $dataUpdatePayment['quote_number'] = $this->generateQIR($trademark->trademark_number, 'quote');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['invoice_number'] = $this->generateQIR('', 'invoice');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['receipt_number'] = $this->generateQIR('', 'receipt');
            }
            $payment->update($dataUpdatePayment);

            //6.Create or update payment_prods
            $dataPaymentPro['payment_id'] = $payment->id;
            $dataPaymentPro['productIds'] = $params['m_product_ids_choose'];

            $this->paymentRepository->createPaymentProds($dataPaymentPro);

            Session::forget(SESSION_SUGGEST_PRODUCT);
            DB::commit();

            $key = Str::random(11);
            switch ($params['redirect_to']) {
                case AppTrademark::REDIRECT_TO_QUOTE:
                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_QUOTE,
                        'payment_id' => $payment->id,
                    ];
                case AppTrademark::REDIRECT_TO_COMMON_PAYMENT:
                    if (count($params['m_product_ids_choose']) == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }

                    $params['payment_id'] = $payment->id;
                    $params['from_page'] = U031B;
                    $params['productIds'] = $params['m_product_ids_choose'];
                    $params['sum_distintion'] = $params['total_distinction'];
                    $params['payment_type'] = $payerInfo->payment_type;
                    $params['trademark_id'] = $trademark->id;
                    $params['app_trademark_id'] = $appTrademark->id;
                    unset($params['image_trademark']);
                    Session::put($key, $params);

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_COMMON_PAYMENT,
                        'key_session' => $key,
                    ];

                case AppTrademark::REDIRECT_TO_ANKEN_TOP:
                    $countProduct = count($params['m_product_ids_choose']);
                    if ($countProduct == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_ANKEN_TOP,
                        'trademark_id' => $trademark->id,
                    ];
                case AppTrademark::REDIRECT_TO_U021:
                    $countProduct = count($params['m_product_ids_choose']);
                    if ($countProduct == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }
                    $dataSessionPrecheck['data']['m_product_ids'] = $params['m_product_ids_choose'];
                    $dataSessionPrecheck['from_page'] = U031B;
                    Session::put($key, $dataSessionPrecheck);

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_U021,
                        'trademark_id' => $trademark->id,
                        'key_session' => $key
                    ];
                default:
                    return [];
                    break;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return [];
        }
    }

    /**
     * Save data apply trademark with number
     *
     * @param array $params
     * @return void
     */
    public function saveDataApplyTrademarkWithNumber(array $params)
    {
        DB::beginTransaction();
        try {
            $agentGroup = AgentGroup::where('status_choice', AgentGroup::STATUS_CHOICE_TRUE)->first();

            //package_type, is_mailing_register_cert, period_registration, total_distinction, m_product_ids
            $billParams = [
                'm_product_ids' => $params['m_product_ids_choose'],
                'is_mailing_register_cert' => $params['is_mailing_register_cert'] ?? 0,
                'period_registration' => $params['period_registration'] ?? 0,
                'package_type' => $params['pack'],
                'total_distinction' => $params['total_distinction'],
            ];
            //info cart
            $dataBill = $this->paymentRepository->ajaxGetCartInfoPayment($billParams);
            //1. Create or update trademark
            $inputs['type_trademark'] = $params['type_trademark'];
            $inputs['name_trademark'] = $params['name_trademark'];
            $inputs['image_trademark'] = isset($params['image_trademark']) ? $params['image_trademark'] : ($params['image_trademark_old'] ?? '');
            $inputs['reference_number'] = $params['reference_number'];
            $trademark = $this->trademarkService->updateOrCreateTrademark($inputs);

            //2.app_trademarks
            $conditionAppTrademark = [
                'trademark_id' => $trademark->id,
                'admin_id' => 1,
                'agent_group_id' => $agentGroup->id,
                'status' => AppTrademark::STATUS_UNREGISTERED_SAVE,
                'pack' => $params['pack'],
                'is_mailing_regis_cert' => $params['is_mailing_regis_cert'] ?? 0,
                'period_registration' => $params['period_registration'] ?? AppTrademark::PERIOD_REGISTRATION_5_YEAR,
                'type_page' => AppTrademark::PAGE_TYPE_U031D
            ];
            $appTrademark = $this->updateOrCreate(['trademark_id' => $trademark->id], $conditionAppTrademark);
            //2. create or update app_trademark_prods
            if ($appTrademark) {
                foreach ($params['m_product_ids'] as $productId) {
                    $isApply = AppTrademarkProd::IS_NOT_APPLY;
                    if (in_array($productId, $params['m_product_ids_choose'])) {
                        $isApply = AppTrademarkProd::IS_APPLY;
                    }
                    $this->appTrademarkProdRepository->updateOrCreate([
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $productId,
                    ], [
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $productId,
                        'is_apply' => $isApply
                    ]);
                }
            }
            //3.trademark_infos
            $trademarkInfo = $this->trademarkInfoService->updateOrCreateTrademarkInfo($params['data'], $trademark->id);
            //4.payer_infos
            $payerInfo = $this->payerInfoService->updateOrCreate([
                'target_id' => $appTrademark->id,
                'type' => TYPE_APP_TRADEMARK
            ], [
                "target_id" => $appTrademark->id,
                "payment_type" => $params['payment_type'] ?? null,
                "payer_type" => $params['payer_type'] ?? 0,
                "m_nation_id" => $params['m_nation_id'] ?? null,
                "payer_name" => $params['payer_name'] ?? '',
                "payer_name_furigana" => $params['payer_name_furigana'] ?? '',
                "postal_code" => $params['postal_code'] ?? null,
                "m_prefecture_id" => $params['m_prefecture_id'] ?? null,
                "address_second" => $params['address_second'] ?? '',
                "address_three" => $params['address_three'] ?? '',
                "type" => TYPE_APP_TRADEMARK,
            ]);

            $countProductApply = (isset($params['m_product_ids_choose']) ? count($params['m_product_ids_choose']) : 0);
            $reduceNumberDistitions = $countProductApply > 3 ? $countProductApply - 3 : 0;

            //5.payments
            $createPayment = [
                "target_id" => $appTrademark->id,
                "payer_info_id" => $payerInfo->id,
                "trademark_id" => $trademark->id,
                "cost_bank_transfer" => $dataBill['cost_bank_transfer'],
                "subtotal" => (int) $dataBill['subtotal'] ?? 0,
                "commission" => (int) $dataBill['commission'] ?? 0,
                "tax" => (int) $dataBill['tax'] ?? 0,
                "cost_service_base" => (int) $dataBill['cost_service_base'] ?? 0,
                "cost_service_add_prod" => (int) $dataBill['cost_service_add_prod_default'] ?? 0,
                "total_amount" => (int) $dataBill['total_amount'] ?? 0,
                "tax_withholding" => 0,
                "payment_amount" => 0,
                "type" => TYPE_APP_TRADEMARK,
                "cost_print_application_one_distintion" => $dataBill['cost_print_application_one_distintion'] ?? 0,
                "cost_print_application_add_distintion" => $dataBill['cost_print_application_add_distintion'] ?? 0,
                "reduce_number_distitions" => $reduceNumberDistitions,
                "cost_registration_certificate" => $dataBill['cost_registration_certificate'],
                "cost_change_registration_period" => $dataBill['cost_change_registration_period'],
                "payment_date" => now(),
                "is_treatment" => Payment::IS_TREATMENT_WAIT,
                "payment_status" => Payment::STATUS_SAVE,
            ];
            if ($appTrademark->period_registration == AppTrademark::PERIOD_REGISTRATION_TRUE) {
                $createPayment['cost_10_year_one_distintion'] = $dataBill['cost_year_one_distintion'];
            } else {
                $createPayment['cost_5_year_one_distintion'] = $dataBill['cost_year_one_distintion'];
            }

            $createPayment['tax_withholding'] = $this->getTaxWithHolding($payerInfo, $dataBill['total_amount']);

            $createPayment['payment_amount'] = $dataBill['total_amount'] - $createPayment['tax_withholding'];
            $createPayment['from_page'] = U031D;
            $payment = $this->paymentRepository->createPayment($createPayment);

            $dataUpdatePayment = [];
            if (!$payment->quote_number) {
                $dataUpdatePayment['quote_number'] = $this->generateQIR($trademark->trademark_number, 'quote');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['invoice_number'] = $this->generateQIR('', 'invoice');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['receipt_number'] = $this->generateQIR('', 'receipt');
            }
            $payment->update($dataUpdatePayment);

            //6.Create or update payment_prods
            $dataPaymentPro['payment_id'] = $payment->id;
            $dataPaymentPro['productIds'] = $params['m_product_ids_choose'];

            $this->paymentRepository->createPaymentProds($dataPaymentPro);

            DB::commit();

            $key = Str::random(11);
            switch ($params['redirect_to']) {
                case AppTrademark::REDIRECT_TO_QUOTE:
                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_QUOTE,
                        'payment_id' => $payment->id,
                    ];
                case AppTrademark::REDIRECT_TO_COMMON_PAYMENT:
                    if (count($params['m_product_ids_choose']) == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }

                    $params['payment_id'] = $payment->id;
                    $params['from_page'] = U031D;
                    $params['productIds'] = $params['m_product_ids_choose'];
                    $params['payment_type'] = $payerInfo->payment_type;
                    $params['trademark_id'] = $trademark->id;
                    $params['app_trademark_id'] = $appTrademark->id;
                    unset($params['image_trademark']);
                    Session::put($key, $params);

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_COMMON_PAYMENT,
                        'key_session' => $key,
                    ];

                case AppTrademark::REDIRECT_TO_ANKEN_TOP:
                    $countProduct = count($params['m_product_ids_choose']);
                    if ($countProduct == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_ANKEN_TOP,
                        'trademark_id' => $trademark->id,
                    ];
                case AppTrademark::REDIRECT_TO_U021:
                    $countProduct = count($params['m_product_ids_choose']);
                    if ($countProduct == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }
                    $dataSessionPrecheck['data']['m_product_ids'] = $params['m_product_ids_choose'];
                    $dataSessionPrecheck['from_page'] = U031D;
                    Session::put($key, $dataSessionPrecheck);

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_U021,
                        'trademark_id' => $trademark->id,
                        'key_session' => $key
                    ];
                default:
                    return [];
                    break;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return [];
        }
    }

    /**
     * Save data apply trademark with product copied
     *
     * @param array $params
     * @return void
     */
    public function saveDataApplyTrademarkWithProductCopied(array $params)
    {
        DB::beginTransaction();
        try {
            $agentGroup = AgentGroup::where('status_choice', AgentGroup::STATUS_CHOICE_TRUE)->first();
            //package_type, is_mailing_register_cert, period_registration, total_distinction, m_product_ids
            $billParams = [
                'm_product_ids' => $params['m_product_ids_choose'],
                'is_mailing_register_cert' => $params['is_mailing_regis_cert'] ?? 0,
                'period_registration' => $params['period_registration'] ?? 0,
                'package_type' => $params['pack'],
                'total_distinction' => $params['total_distinction'],
            ];
            //info cart
            $dataBill = $this->paymentRepository->ajaxGetCartInfoPayment($billParams);
            //1. Create or update trademark
            $inputs['type_trademark'] = $params['type_trademark'];
            $inputs['name_trademark'] = $params['name_trademark'];
            $inputs['image_trademark'] = isset($params['image_trademark']) ? $params['image_trademark'] : ($params['image_trademark_old'] ?? '');
            $inputs['reference_number'] = $params['reference_number'];
            $trademark = $this->trademarkService->updateOrCreateTrademark($inputs);
            //2.app_trademarks
            $conditionAppTrademark = [
                'trademark_id' => $trademark->id,
                'admin_id' => 1,
                'agent_group_id' => $agentGroup->id,
                'status' => AppTrademark::STATUS_UNREGISTERED_SAVE,
                'pack' => $params['pack'],
                'is_mailing_regis_cert' => $params['is_mailing_regis_cert'] ?? 0,
                'period_registration' => $params['period_registration'] ?? AppTrademark::PERIOD_REGISTRATION_5_YEAR,
                'type_page' => AppTrademark::PAGE_TYPE_U031C
            ];
            $appTrademark = $this->updateOrCreate(['trademark_id' => $trademark->id], $conditionAppTrademark);
            //2. create or update app_trademark_prods
            if ($appTrademark) {
                foreach ($params['m_product_ids'] as $productId) {
                    $isApply = AppTrademarkProd::IS_NOT_APPLY;
                    if (in_array($productId, $params['m_product_ids_choose'])) {
                        $isApply = AppTrademarkProd::IS_APPLY;
                    }
                    $this->appTrademarkProdRepository->updateOrCreate([
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $productId,
                    ], [
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $productId,
                        'is_apply' => $isApply
                    ]);
                }
            }
            //3.trademark_infos
            $trademarkInfo = $this->trademarkInfoService->updateOrCreateTrademarkInfo($params['data'], $trademark->id);
            //4.payer_infos
            $payerInfo = $this->payerInfoService->updateOrCreate([
                'target_id' => $appTrademark->id,
                'type' => TYPE_APP_TRADEMARK
            ], [
                "target_id" => $appTrademark->id,
                "payment_type" => $params['payment_type'] ?? null,
                "payer_type" => $params['payer_type'] ?? 0,
                "m_nation_id" => $params['m_nation_id'] ?? null,
                "payer_name" => $params['payer_name'] ?? '',
                "payer_name_furigana" => $params['payer_name_furigana'] ?? '',
                "postal_code" => $params['postal_code'] ?? null,
                "m_prefecture_id" => $params['m_prefecture_id'] ?? null,
                "address_second" => $params['address_second'] ?? '',
                "address_three" => $params['address_three'] ?? '',
                "type" => TYPE_APP_TRADEMARK,
            ]);

            $countProductApply = (isset($params['m_product_ids_choose']) ? count($params['m_product_ids_choose']) : 0);
            $reduceNumberDistitions = $countProductApply > 3 ? $countProductApply - 3 : 0;
            //5.payments
            $createPayment = [
                "target_id" => $appTrademark->id,
                "payer_info_id" => $payerInfo->id,
                "trademark_id" => $trademark->id,
                "cost_bank_transfer" => $dataBill['cost_bank_transfer'],
                "subtotal" => (int) $dataBill['subtotal'] ?? 0,
                "commission" => (int) $dataBill['commission'] ?? 0,
                "tax" => (int) $dataBill['tax'] ?? 0,
                "cost_service_base" => (int) $dataBill['cost_service_base'] ?? 0,
                "cost_service_add_prod" => (int) $dataBill['cost_service_add_prod_default'] ?? 0,
                "total_amount" => (int) $dataBill['total_amount'] ?? 0,
                "tax_withholding" => 0,
                "payment_amount" => 0,
                "type" => TYPE_APP_TRADEMARK,
                "cost_print_application_one_distintion" => $dataBill['cost_print_application_one_distintion'] ?? 0,
                "cost_print_application_add_distintion" => $dataBill['cost_print_application_add_distintion'] ?? 0,
                "reduce_number_distitions" => $reduceNumberDistitions,
                "cost_registration_certificate" => $dataBill['cost_registration_certificate'],
                "cost_change_registration_period" => $dataBill['cost_change_registration_period'],
                "payment_date" => now(),
                "is_treatment" => Payment::IS_TREATMENT_WAIT,
                "payment_status" => Payment::STATUS_SAVE,
            ];
            if ($appTrademark->period_registration == AppTrademark::PERIOD_REGISTRATION_TRUE) {
                $createPayment['cost_10_year_one_distintion'] = $dataBill['cost_year_one_distintion'];
            } else {
                $createPayment['cost_5_year_one_distintion'] = $dataBill['cost_year_one_distintion'];
            }
            $createPayment['tax_withholding'] = $this->getTaxWithHolding($payerInfo, $dataBill['total_amount']);

            $createPayment['payment_amount'] = $dataBill['total_amount'] - $createPayment['tax_withholding'];
            $createPayment['from_page'] = U031C;
            $payment = $this->paymentRepository->createPayment($createPayment);

            $dataUpdatePayment = [];
            if (!$payment->quote_number) {
                $dataUpdatePayment['quote_number'] = $this->generateQIR($trademark->trademark_number, 'quote');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['invoice_number'] = $this->generateQIR('', 'invoice');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['receipt_number'] = $this->generateQIR('', 'receipt');
            }
            $payment->update($dataUpdatePayment);

            //6.Create or update payment_prods
            $dataPaymentPro['payment_id'] = $payment->id;
            $dataPaymentPro['productIds'] = $params['m_product_ids_choose'];

            $this->paymentRepository->createPaymentProds($dataPaymentPro);

            DB::commit();

            $key = Str::random(11);
            switch ($params['redirect_to']) {
                case AppTrademark::REDIRECT_TO_QUOTE:
                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_QUOTE,
                        'payment_id' => $payment->id,
                    ];
                case AppTrademark::REDIRECT_TO_COMMON_PAYMENT:
                    if (count($params['m_product_ids_choose']) == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }

                    $params['payment_id'] = $payment->id;
                    $params['from_page'] = U031C;
                    $params['productIds'] = $params['m_product_ids_choose'];
                    $params['payment_type'] = $payerInfo->payment_type;
                    $params['trademark_id'] = $trademark->id;
                    $params['app_trademark_id'] = $appTrademark->id;
                    unset($params['image_trademark']);
                    Session::put($key, $params);

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_COMMON_PAYMENT,
                        'key_session' => $key,
                    ];

                case AppTrademark::REDIRECT_TO_ANKEN_TOP:
                    $countProduct = count($params['m_product_ids_choose']);
                    if ($countProduct == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_ANKEN_TOP,
                        'trademark_id' => $trademark->id,
                    ];
                case AppTrademark::REDIRECT_TO_U021:
                    $countProduct = count($params['m_product_ids_choose']);
                    if ($countProduct == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }
                    $dataSessionPrecheck['data']['m_product_ids'] = $params['m_product_ids_choose'];
                    $dataSessionPrecheck['from_page'] = U031C;
                    Session::put($key, $dataSessionPrecheck);

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_U021,
                        'trademark_id' => $trademark->id,
                        'key_session' => $key
                    ];
                default:
                    return [];
                    break;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return [];
        }
    }
}
