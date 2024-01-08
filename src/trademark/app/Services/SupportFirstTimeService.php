<?php

namespace App\Services;

use App\Helpers\FileHelper;
use App\Models\History;
use App\Models\MDistinction;
use App\Models\MProductCode;
use App\Models\Admin;
use App\Models\AgentGroup;
use App\Models\AppTrademark;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SFTComment;
use App\Models\SFTContentProduct;
use App\Models\AppTrademarkProd;
use App\Models\MailTemplate;
use App\Models\MCode;
use App\Models\MPriceList;
use App\Models\MProduct;
use App\Models\SFTKeepDataProd;
use App\Models\SFTSuitableProduct;
use App\Models\SupportFirstTime;
use App\Models\Trademark;
use App\Models\User;
use App\Repositories\AdminRepository;
use App\Repositories\MDistinctionRepository;
use App\Services\Common\NoticeService;
use App\Repositories\SFTSuitableProductRepository;
use App\Repositories\SupportFirstTimeRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SupportFirstTimeService extends BaseService
{
    protected SupportFirstTimeRepository $supportFirstTimeRepository;
    protected PaymentService $paymentService;
    protected TrademarkService $trademarkService;
    protected PayerInfoService $payerInfoService;
    protected MProductService $mProductService;
    protected MCodeService $mCodeService;
    protected SFTCommentService $sFTCommentService;
    protected ProductCodeService $productCodeService;
    protected MPriceListService $mPriceListService;
    protected TrademarkInfoService $trademarkInfoService;
    protected AppTrademark $appTrademark;
    protected AppTrademarkService $appTrademarkService;
    protected SFTSuitableProductService $sFTSuitableProductService;
    protected SFTContentProductService $sftContentProductService;
    protected SFTSuitableProductRepository $sFTSuitableProductRepository;
    protected HistoryService $historyService;
    protected SFTKeepDataService $sftKeepDataService;
    protected SFTKeepDataProdService $sftKeepDataProdService;
    protected SFTKeepDataProdCodeService $sftKeepDataProdCodeService;
    protected SFTComment $sftComment;
    private NoticeService $noticeService;
    protected MDistinctionRepository $distinctionRepository;
    protected AdminRepository $adminRepository;
    protected MailTemplateService $mailTemplateService;

    /**
     * Initializing the instances and variables
     *
     * @param SupportFirstTimeRepository $supportFirstTimeRepository
     * @param SFTContentProductService $sftContentProductService
     * @param TrademarkService $trademarkService
     * @param PayerInfoService $payerInfoService
     * @param AppTrademarkService $appTrademarkService
     * @param MPriceListService $mPriceListService
     * @param PaymentService $paymentService
     * @param MProductService $mProductService
     * @param SFTSuitableProductService $sFTSuitableProductService
     * @param MCodeService $mCodeService
     * @param ProductCodeService $productCodeService
     * @param SFTCommentService $sFTCommentService
     * @param SFTSuitableProductRepository $sFTSuitableProductRepository
     * @param TrademarkInfoService $trademarkInfoService
     * @param AppTrademark $appTrademark
     * @param HistoryService $historyService
     * @param SFTKeepDataService $sftKeepDataService
     * @param SFTKeepDataProdService $sftKeepDataProdService
     * @param SFTKeepDataProdCodeService $sftKeepDataProdCodeService
     * @param SFTComment $sftComment
     * @param NoticeService $noticeService
     * @param MDistinctionRepository $distinctionRepository
     * @param NoticeDetailService $noticeDetailService
     * @param AdminRepository $adminRepository
     * @param MailTemplateService $mailTemplateService
     */
    public function __construct(
        SupportFirstTimeRepository   $supportFirstTimeRepository,
        SFTContentProductService     $sftContentProductService,
        TrademarkService             $trademarkService,
        PayerInfoService             $payerInfoService,
        AppTrademarkService          $appTrademarkService,
        MPriceListService            $mPriceListService,
        PaymentService               $paymentService,
        MProductService              $mProductService,
        SFTSuitableProductService    $sFTSuitableProductService,
        MCodeService                 $mCodeService,
        ProductCodeService           $productCodeService,
        SFTCommentService            $sFTCommentService,
        SFTSuitableProductRepository $sFTSuitableProductRepository,
        TrademarkInfoService         $trademarkInfoService,
        AppTrademark                 $appTrademark,
        HistoryService               $historyService,
        SFTKeepDataService           $sftKeepDataService,
        SFTKeepDataProdService       $sftKeepDataProdService,
        SFTKeepDataProdCodeService   $sftKeepDataProdCodeService,
        SFTComment                   $sftComment,
        NoticeService                $noticeService,
        MDistinctionRepository       $distinctionRepository,
        NoticeDetailService          $noticeDetailService,
        AdminRepository              $adminRepository,
        MailTemplateService          $mailTemplateService
    )
    {
        $this->repository = $supportFirstTimeRepository;
        $this->sftContentProductService = $sftContentProductService;
        $this->trademarkService = $trademarkService;
        $this->payerInfoService = $payerInfoService;
        $this->appTrademarkService = $appTrademarkService;
        $this->mPriceListService = $mPriceListService;
        $this->paymentService = $paymentService;
        $this->mProductService = $mProductService;
        $this->sFTSuitableProductService = $sFTSuitableProductService;
        $this->mCodeService = $mCodeService;
        $this->productCodeService = $productCodeService;
        $this->sFTCommentService = $sFTCommentService;
        $this->sFTSuitableProductRepository = $sFTSuitableProductRepository;
        $this->trademarkInfoService = $trademarkInfoService;
        $this->appTrademark = $appTrademark;
        $this->historyService = $historyService;
        $this->sftKeepDataService = $sftKeepDataService;
        $this->sftKeepDataProdService = $sftKeepDataProdService;
        $this->sftKeepDataProdCodeService = $sftKeepDataProdCodeService;
        $this->sftComment = $sftComment;
        $this->noticeService = $noticeService;
        $this->distinctionRepository = $distinctionRepository;
        $this->noticeDetailService  = $noticeDetailService;
        $this->adminRepository = $adminRepository;
        $this->mailTemplateService = $mailTemplateService;
    }

    /**
     * Create trademark, payment and support first time.
     *
     * @param array $params
     *
     * @return array
     */
    public function createSFTTrademark(array $params): array
    {
        try {
            // Create trademark
            $input = [
                'id' => $params['trademark_id'] ?? 0,
                'name_trademark' => $params['name_trademark'] ?? '',
                'type_trademark' => $params['type_trademark'] ?? 1,
                'image_trademark' => $params['image_trademark'] ?? ($params['image_trademark_old'] ?? null),
                'reference_number' => $params['reference_number'] ?? null,
            ];

            if ($input['type_trademark'] == Trademark::TRADEMARK_TYPE_LETTER) {
                if (!empty($input['image_trademark'])) {
                    FileHelper::unlink($input['image_trademark']);
                }

                $input['image_trademark'] = null;
            } elseif ($input['type_trademark'] == Trademark::TRADEMARK_TYPE_OTHER) {
                $input['name_trademark'] = null;
            }

            $trademark = $this->trademarkService->updateOrCreateTrademarkSFT($input);
            $adminTanto = $this->adminRepository->findByCondition([
                'role' => Admin::ROLE_ADMIN_TANTO,
            ])->first();
            $sft = $this->updateOrCreate([
                'id' => $params['sft_id'] ?? 0,
            ], [
                "admin_id" => $adminTanto->id,
                "trademark_id" => $trademark->id,
                "is_mailing_register_cert" => SupportFirstTime::IS_MAILING_REGISTER_CERT_FALSE,
                "is_cancel" => SupportFirstTime::IS_CANCEL_FALSE
            ]);

            if (isset($params['product_names'])) {
                $sftContentProductIds = [];
                foreach ($params['product_names'] as $name) {
                    // Create sft content product
                    if (empty($name)) {
                        continue;
                    }
                    $sftContentProduct = $this->sftContentProductService->updateOrCreate([
                        'support_first_time_id' => $sft->id,
                        'name' => $name,
                    ], [
                        'support_first_time_id' => $sft->id,
                        'name' => $name,
                        'is_choice_admin' => SFTContentProduct::IS_CHOICE_ADMIN_FAlSE
                    ]);
                    $sftContentProductIds[] = $sftContentProduct->id;
                }

                $sftContentProducts = $this->sftContentProductService->findByCondition([
                    'support_first_time_id' => $sft->id,
                ])->get();

                $sftContentProductDelete = $sftContentProducts->whereNotIn('id', $sftContentProductIds);
                $sftContentProductDelete->map(function ($item) {
                    $item->delete();
                });
            }

            return [
                'sft' => $sft,
                'trademark' => $trademark,
            ];
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Create trademark, payment and support first time.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function getList(array $input)
    {
        $sft = $this->repository->findByCondition($input, ['StfContentProduct', 'trademark', 'trademark.user', 'admin', 'stfComment'])->first();

        return $sft;
    }

    /**
     * GetInfoProduct
     *
     * @param array $input
     * @return Model
     */
    public function getInfoProduct(array $input): Model
    {
        $product = $this->mProductService->findByCondition($input, [
            'mDistinction' => function ($query) {
                return $query->select('id', 'name');
            },
            'productCode' => function ($query) {
                return $query->join('m_code', 'm_product_codes.m_code_id', '=', 'm_code.id')
                    ->select('m_product_codes.*', 'm_code.name as code_name');
            }
        ])->first();

        return $product;
    }

    /**
     * SearchRecommend
     *
     * @param array $input
     * @return mixed
     */
    public function searchRecommendQuery(array $input)
    {
        $input['types'] = [MProduct::TYPE_ORIGINAL_CLEAN, MProduct::TYPE_REGISTERED_CLEAN];
        $query = $this->mProductService->findByCondition($input, [
            'mDistinction' => function ($query) {
                return $query->select('id', 'name');
            },
            'productCode' => function ($query) {
                return $query->join('m_code', 'm_product_codes.m_code_id', '=', 'm_code.id')
                    ->select('m_product_codes.*', 'm_code.name as code_name');
            }
        ]);

        if (!empty($input['distinctionId'])) {
            $query = $query->where('m_distinction_id', $input['distinctionId']);
        }

        return $query;
    }

    /**
     * SearchRecommend
     *
     * @param array $input
     * @return mixed
     */
    public function searchRecommend(array $input)
    {
        return $this->searchRecommendQuery($input)->get();
    }

    /**
     * SearchRecommend
     *
     * @param array $input
     * @return mixed
     */
    public function searchRecommendGetItem(array $input)
    {
        return $this->searchRecommendQuery($input)->first();
    }

    /**
     * Create Support First Time.
     *
     * @param array $input
     * @return boolean
     */
    public function createSupportFirstTimeAdmin(array $input): bool
    {
        DB::beginTransaction();
        try {
            $admin = auth()->guard(ADMIN_ROLE)->user();
            $sft = $this->repository->find($input['support_first_time_id']);
            //not update if flag_role = 2
            if ($sft->flag_role == SupportFirstTime::FLAG_ROLE_SEKI) {
                return false;
            }
            $sft->update(['admin_id' => $admin->id]);

            //update sft_content_products
            $sft->StfContentProduct()->update(['is_choice_admin' => SFTContentProduct::IS_CHOICE_ADMIN_FAlSE]);
            if (!empty($input['is_choice_admin'])) {
                $sft->StfContentProduct()->whereIn('id', $input['is_choice_admin'])
                    ->update(['is_choice_admin' => SFTContentProduct::IS_CHOICE_ADMIN_TRUE]);
            }

            $input['admin_id'] = auth()->guard('admin')->user()->id;
            if (isset($input['code']) && $input['code'] == NOTICE_CODE) {
                $sft->update([
                    'flag_role' => SupportFirstTime::FLAG_ROLE_SEKI,
                ]);
            }
            foreach ($input['data'] as $value) {
                if (!$value['name']) {
                    continue;
                }
                if (isset($value['delete_item']) && $value['delete_item'] == 'on' && !empty($value['sft_suitable_product_id'])) {
                    $sftSuitableProd = $this->sFTSuitableProductRepository->find($value['sft_suitable_product_id']);
                    if ($sftSuitableProd) {
                        $this->sFTSuitableProductRepository->delete($sftSuitableProd);
                        continue;
                    }
                }
                if (!empty($value['type'])) {
                    if (in_array($value['type'], [ORIGINAL_CLEAN, REGISTER_CLEAN])) {
                        //type = 1 or 2
                        $product = $this->mProductService->findByCondition([
                            'name' => $value['name'],
                            'type' => $value['type'],
                        ])->first();

                        if ($product && !empty($value['sft_suitable_product_id'])) {
                            // update
                            $sftSuitableProd = $this->sFTSuitableProductRepository->find($value['sft_suitable_product_id']);
                            $this->sFTSuitableProductService->update($sftSuitableProd, [
                                'admin_id' => $input['admin_id'],
                                'support_first_time_id' => $input['support_first_time_id'],
                                'm_product_id' => $product->id,
                                'is_choice_user' => true,
                            ]);
                        } else {
                            // store
                            $this->sFTSuitableProductService->create([
                                'admin_id' => $input['admin_id'],
                                'support_first_time_id' => $input['support_first_time_id'],
                                'm_product_id' => $product->id,
                                'is_choice_user' => true,
                            ]);
                        }
                    } elseif ($value['type'] == CREATIVE_CLEAN) {
                        //type = 3
                        $value['support_first_time_id'] = $input['support_first_time_id'];
                        $this->createCustomSupportFirstTime($value);
                    } elseif ($value['type'] == SEMI_CLEAN) {
                        //type = 4
                        $value['support_first_time_id'] = $input['support_first_time_id'];
                        if (!empty($value['distinction'])) {
                            //check distinction has data
                            $distinction = MDistinction::find($value['distinction']);
                            if ($distinction) {
                                $this->creativeSupportFirstTime($value);
                            }
                        }
                    }
                }
            }

            //save comment
            if ($input['comment']) {
                $this->sFTCommentService->createData($input['comment'], $input['support_first_time_id']);
            }

            //save histories
            $this->historyService->create([
                'admin_id' => auth('admin')->user()->id,
                'target_id' => $input['support_first_time_id'],
                'page' => 'a011.html',
                'action' => History::ACTION_CREATE,
                'type' => History::TYPE_SUPPORT_FIRST_TIME
            ]);

            if (isset($input['code']) && $input['code'] == NOTICE_CODE) {
                $sft = $sft->load('trademark');
                $trademark = $sft->trademark ?? null;

                if (!empty($trademark)) {
                    //update comment notices
                    $this->noticeService->updateComment(
                        Notice::FLOW_SFT,
                        $input['comment'][1]['content'] ?? '',
                        $trademark->id
                    );
                    // Send Notice
                   //update notice detail No 50
                    $targetPage = route('user.sft.index');
                    $targetPage = str_replace(request()->root(), '', $targetPage);
                    $noticeDetailPeriouNo50G = $this->noticeDetailService->findByCondition([
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => 'はじめからサポート：提案内容作成',
                        'completion_date' => null,
                    ], ['notice'])->orderBy('id', 'DESC')->get()
                        ->where('notice.flow', Notice::FLOW_SFT)
                        ->where('notice.trademark_id', $trademark->id)
                        ->first();
                    if ($noticeDetailPeriouNo50G) {
                        $noticeDetailPeriouNo50G->update([
                            'completion_date' => Carbon::now(),
                        ]);
                    }

                    $noticeDetailPeriouNo50H1 = $this->noticeDetailService->findByCondition([
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => 'はじめからサポート：お申し込み受領',
                        'attribute' => 'お客様から',
                        'completion_date' => null,
                    ], ['notice'])->orderBy('id', 'DESC')->get()
                        ->where('notice.flow', Notice::FLOW_SFT)
                        ->where('notice.trademark_id', $trademark->id)
                        ->first();
                    if ($noticeDetailPeriouNo50H1) {
                        $noticeDetailPeriouNo50H1->update([
                            'completion_date' => Carbon::now(),
                        ]);
                    }

                    $noticeDetailPeriouNo50H2 = $this->noticeDetailService->findByCondition([
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => 'はじめからサポート：決済確認済',
                        'attribute' => 'お客様へ',
                        'completion_date' => null,
                    ], ['notice'])->orderBy('id', 'DESC')->get()
                        ->where('notice.flow', Notice::FLOW_SFT)
                        ->where('notice.trademark_id', $trademark->id)
                        ->first();
                    if ($noticeDetailPeriouNo50H2) {
                        $noticeDetailPeriouNo50H2->update([
                            'completion_date' => Carbon::now(),
                        ]);
                    }

                    $noticeDetailPeriouNo50H3 = $this->noticeDetailService->findByCondition([
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '担当者　はじめからサポート：提案内容作成',
                        'attribute' => '所内処理',
                        'completion_date' => null,
                    ], ['notice'])->orderBy('id', 'DESC')->get()
                        ->where('notice.flow', Notice::FLOW_SFT)
                        ->where('notice.trademark_id', $trademark->id)
                        ->first();
                    if ($noticeDetailPeriouNo50H3) {
                        $noticeDetailPeriouNo50H3->update([
                            'completion_date' => Carbon::now(),
                        ]);
                    }
                    //end update notice detail No 50

                    //update notice detail No 52
                    $noticeDetailPeriouNo52G = $this->noticeDetailService->findByCondition([
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => 'はじめからサポート：提案内容作成',
                        'completion_date' => null,
                    ], ['notice'])->orderBy('id', 'DESC')->get()
                        ->where('notice.flow', Notice::FLOW_SFT)
                        ->where('notice.trademark_id', $trademark->id)
                        ->first();
                    if ($noticeDetailPeriouNo52G) {
                        $noticeDetailPeriouNo52G->update([
                            'completion_date' => Carbon::now(),
                        ]);
                    }

                    $noticeDetailPeriouNo52H1 = $this->noticeDetailService->findByCondition([
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => 'はじめからサポート：決済確認済',
                        'attribute' => 'お客様へ',
                        'completion_date' => null,
                    ], ['notice'])->orderBy('id', 'DESC')->get()
                        ->where('notice.flow', Notice::FLOW_SFT)
                        ->where('notice.trademark_id', $trademark->id)
                        ->first();
                    if ($noticeDetailPeriouNo52H1) {
                        $noticeDetailPeriouNo52H1->update([
                            'completion_date' => Carbon::now(),
                        ]);
                    }

                    $noticeDetailPeriouNo52H2 = $this->noticeDetailService->findByCondition([
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '担当者　はじめからサポート：提案内容作成',
                        'attribute' => '所内処理',
                        'completion_date' => null,
                    ], ['notice'])->orderBy('id', 'DESC')->get()
                        ->where('notice.flow', Notice::FLOW_SFT)
                        ->where('notice.trademark_id', $trademark->id)
                        ->first();
                    if ($noticeDetailPeriouNo52H2) {
                        $noticeDetailPeriouNo52H2->update([
                            'completion_date' => Carbon::now(),
                        ]);
                    }
                    //end update notice detail No 52
                    $this->noticeService->sendSupervisor([
                        'notices' => [
                            'flow' => Notice::FLOW_SFT,
                            'user_id' => $trademark->user_id,
                            'trademark_id' => $trademark->id,
                        ],
                        'notice_details' => [
                            [
                                'content' => 'はじめからサポート：提案内容作成・確認',
                                'target_page' => route('admin.support-first-time.create', $trademark->id),
                                'redirect_page' => route('admin.support-first-time.index', $trademark->id),
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                                'is_action' => true,
                            ],
                            [
                                'content' => '責任者　はじめからサポート：提案内容作成　判断・承認',
                                'target_page' => route('admin.support-first-time.create', $trademark->id),
                                'redirect_page' => route('admin.support-first-time.index', $trademark->id),
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                'attribute' => '所内処理',
                                'is_action' => true,
                            ],
                        ],
                    ]);
                }
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            throw new \Exception($e);

            return false;
        }
    }

    /**
     * Create custom support first time. type = 3
     *
     * @param array $input
     * @return Model
     */
    public function createCustomSupportFirstTime(array $input): Model
    {
        //create data when type product = 3
        $input['admin_id'] = auth()->guard('admin')->user()->id;

        if (!empty($input['sft_suitable_product_id'])) {
            $sftSuitableProduct = $this->sFTSuitableProductRepository->find($input['sft_suitable_product_id']);
            if ($sftSuitableProduct) {
                //check product
                if (!empty($input['m_product_id'])) {
                    $product = $this->mProductService->findByCondition([
                        'id' => $input['m_product_id'],
                        'type' => MProduct::TYPE_CREATIVE_CLEAN,
                    ])->first();
                    if (!$product) {
                        $this->mProductService->update($product, [
                            'name' => $input['name'],
                            'm_distinction_id' => $input['distinction'],
                        ]);
                    } else {
                        //create product type = 3
                        $product = $this->mProductService->create([
                            'm_distinction_id' => $input['distinction'],
                            'admin_id' => $input['admin_id'],
                            'products_number' => $this->generateProductNumber($input['type'], $input['distinction']),
                            'name' => mb_convert_kana($input['name'], 'NR'),
                            'type' => $input['type'],
                        ]);
                    }
                }
            }
        } else {
            //create product type = 3
            $product = $this->mProductService->create([
                'm_distinction_id' => $input['distinction'],
                'admin_id' => $input['admin_id'],
                'products_number' => $this->generateProductNumber($input['type'], $input['distinction']),
                'name' => mb_convert_kana($input['name'], 'NR'),
                'type' => $input['type'],
            ]);
        }

        //update or create m_codes
        foreach ($input['code'] as $value) {
            //check name code has value
            if (!empty($value['name'])) {
                //old data m_code
                if (!empty($value['id'])) {
                    $code = $this->mCodeService->find($value['id']);
                    if ($code) {
                        $checkCodeNameExists = $this->mCodeService->findByCondition(['name' => $value['name']])->where('id', '!=', $code->id)->first();
                        if (!$checkCodeNameExists) {
                            //update name m_codes
                            $this->mCodeService->update($code, [
                                'name' => $value['name'],
                            ]);
                        }
                    }
                } else {
                    //create new m_codes
                    $code = $this->mCodeService->findByCondition(['name' => $value['name']])->first();
                    if (!$code) {
                        $code = $this->mCodeService->create([
                            'name' => $value['name'],
                            'type' => $input['type'],
                            'admin_id' => $input['admin_id'],
                        ]);
                    }
                }
                //update or create m_product_codes
                $this->productCodeService->updateOrCreate([
                    'm_product_id' => $product->id,
                    'm_code_id' => $code->id,
                ]);
            }
        }
        if (!empty($input['sft_suitable_product_id'])) {
            // update
            $sftSuitableProd = $this->sFTSuitableProductRepository->find($input['sft_suitable_product_id']);
            $sftSuitableProduct = $this->sFTSuitableProductService->update($sftSuitableProd, [
                'admin_id' => $input['admin_id'],
                'support_first_time_id' => $input['support_first_time_id'],
                'm_product_id' => $product->id,
                'is_choice_user' => true,
            ]);
        } else {
            // store
            $sftSuitableProduct = $this->sFTSuitableProductService->create([
                'admin_id' => $input['admin_id'],
                'support_first_time_id' => $input['support_first_time_id'],
                'm_product_id' => $product->id,
                'is_choice_user' => true,
            ]);
        }

        return $sftSuitableProduct;
    }

    /**
     * Creative support first time.
     *
     * @param array $input
     * @return Model
     */
    public function creativeSupportFirstTime(array $input): Model
    {
        //type product = 4
        $input['admin_id'] = auth()->guard('admin')->user()->id;
        //if exists sft_suitable_product_id: edit data old
        if (!empty($input['sft_suitable_product_id'])) {
            $sftSuitableProduct = $this->sFTSuitableProductRepository->find($input['sft_suitable_product_id']);
            if ($sftSuitableProduct) {
                //old data product
                if (!empty($input['m_product_id'])) {
                    $product = $this->mProductService->findByCondition([
                        'id' => $input['m_product_id'],
                        'type' => MProduct::TYPE_SEMI_CLEAN,
                    ])->first();
                    if ($product) {
                        //update name product old data for type = 4
                        $product = $this->mProductService->update($product, [
                            'name' => $input['name'],
                            'm_distinction_id' => $input['distinction'],
                        ]);
                    } else {
                        $product = $this->mProductService->create([
                            'm_distinction_id' => $input['distinction'],
                            'admin_id' => $input['admin_id'],
                            'products_number' => $this->generateProductNumber($input['type'], $input['distinction']),
                            'name' => mb_convert_kana($input['name'], 'NR'),
                            'type' => $input['type']
                        ]);
                    }
                }
                $sftSuitableProduct = $this->sFTSuitableProductService->updateOrCreate([
                    'id' => $sftSuitableProduct->id,
                ], [
                    'admin_id' => $input['admin_id'],
                    'support_first_time_id' => $input['support_first_time_id'],
                    'm_product_id' => $product->id,
                ]);
            }
        } else {
            //if add new row
            $product = $this->mProductService->create([
                'm_distinction_id' => $input['distinction'],
                'admin_id' => $input['admin_id'],
                'products_number' => $this->generateProductNumber($input['type'], $input['distinction']),
                'name' => mb_convert_kana($input['name'], 'NR'),
                'type' => $input['type']
            ]);

            $sftSuitableProduct = $this->sFTSuitableProductService->create([
                'admin_id' => $input['admin_id'],
                'support_first_time_id' => $input['support_first_time_id'],
                'm_product_id' => $product->id,
                'is_choice_user' => true,
            ]);
        }

        //create or update m_product_codes
        $dataCodeId = [];

        if (!empty($input['code_ids'])) {
            $dataCodeId = explode(',', $input['code_ids']);
        }
        foreach ($dataCodeId as $codeId) {
            if ($codeId) {
                $code = $this->productCodeService->updateOrCreate([
                    'm_product_id' => $product->id,
                    'm_code_id' => $codeId
                ]);
            }
        }

        return $sftSuitableProduct;
    }

    /**
     * Generate product number.
     *
     * @param int $type .
     * @param int $distinction .
     * @return string
     */
    public function generateProductNumber(int $type, int $distinction): string
    {
        $productNumber = '';
        $product = $this->mProductService->findByCondition(['m_distinction_id' => $distinction])->orderBy('id', 'desc')->first();

        switch ($type) {
            case ORIGINAL_CLEAN:
                return $productNumber = '0' . $distinction . str_pad((int) substr($product->products_number, -4) + 1, 4, 0, STR_PAD_LEFT);
            case REGISTER_CLEAN:
                return $productNumber = '1' . $distinction . str_pad((int) substr($product->products_number, -4) + 1, 4, 0, STR_PAD_LEFT);
            case CREATIVE_CLEAN:
                return $productNumber = '2' . $distinction . str_pad((int) substr($product->products_number, -4) + 1, 4, 0, STR_PAD_LEFT);
            case SEMI_CLEAN:
                return $productNumber = '3' . $distinction . str_pad((int) substr($product->products_number, -4) + 1, 4, 0, STR_PAD_LEFT);

                return $productNumber;
        }
    }

    /**
     * Get list support first time
     *
     * @param int $id - sft_id
     * @return Model
     */
    public function getSupportFirstTime(int $id)
    {
        $sft = $this->repository->getSupportFirstTime($id);

        return $sft;
    }

    /**
     * Get list support first time
     *
     * @param array $input .
     * @return Collection
     */
    public function getDistinctionService($id): Collection
    {
        return $this->repository->getDistinctionRepository($id);
    }

    /**
     * Get Price Pack Service
     *
     * @return void
     */
    public function getPricePackService()
    {
        return $this->repository->getPricePackService();
    }

    public function calculatorFeeTax()
    {
        $priceFeeTaxPackage = $this->repository->getPricePackRepository();
        $countPricePackage = count($priceFeeTaxPackage);
        $percentTax = $this->getSetting();
        $feeTax = $percentTax->value / 100;
        for ($i = 0; $i < $countPricePackage; $i++) {
            for ($j = 0; $j < $countPricePackage + 1; $j++) {
                $priceFeeTaxPackage[$i][$j]['base_price'] *= $feeTax;
            }
        }

        return $priceFeeTaxPackage;
    }

    /**
     * Get Price Original Pack
     *
     * @return void
     */
    public function getPricePackOriginal()
    {
        return $this->repository->getPricePackRepository();
    }

    /**
     * Get Price Pack Product Four Service
     *
     * @return void
     */
    public function getPricePackProductFourService()
    {
        return $this->repository->getPricePackProductFourRepository();
    }

    /**
     * Get Mail Register CertService
     *
     * @return void
     */
    public function getMailRegisterCertService()
    {
        return $this->repository->getMailRegisterCertRepository();
    }

    /**
     * Get Period Registration Service
     *
     * @return void
     */
    public function getPeriodRegistrationService(int $serviceType, string $packageType)
    {
        return $this->repository->getPeriodRegistrationRepository($serviceType, $packageType);
    }

    /**
     * Get Fee Submit
     *
     * @return void
     */
    public function getFeeSubmit()
    {
        return $this->repository->getFeeSubmit();
    }

    /**
     * Get Product Service
     *
     * @param mixed $id
     * @return void
     */
    public function getProductService($id)
    {
        return $this->repository->getProductRepository($id);
    }

    /**
     * Send Session
     *
     * @param mixed $request
     * @return void
     */
    public function sendSession($request)
    {
        $data = [];
        $key = Str::random(11);
        $sft = $this->repository->getSupportFirstTime($request['id']);
        switch ($request['redirect_to']) {
            case SupportFirstTime::SENT_SESSION_TO_QUOTE:
                $request->session()->put(SupportFirstTime::SENT_SESSION_TO_QUOTE, [
                    'referer' => FROM_SUPPORT_FIRST_TIME,
                    'support_first_time_id' => $sft->id,
                    'trademark_id' => $sft->trademark->id,
                ]);
                $request->session()->put(SESSION_QUOTE, $request['productIds']);

                return [
                    'redirect_to' => SupportFirstTime::REDIRECT_TO_QUOTE,
                    'key_session' => SESSION_QUOTE,
                ];
                break;
            case SupportFirstTime::SENT_SESSION_TO_ANKEN_TOP:
                $request->session()->put(SupportFirstTime::SENT_SESSION_TO_ANKEN_TOP, [
                    'referer' => FROM_SUPPORT_FIRST_TIME,
                    'support_first_time_id' => $sft->id,
                    'trademark_id' => $sft->trademark->id,
                ]);

                $request->session()->put(SESSION_ANKEN_TOP, $request['productIds']);

                return [
                    'redirect_to' => SupportFirstTime::REDIRECT_TO_ANKEN_TOP,
                    'key_session' => SESSION_ANKEN_TOP,
                ];
                break;
            case SupportFirstTime::TYPE_SUBMIT_SENT_SESSION:
                $request->session()->put(SESSION_REFERER_SEARCH_AI, [
                    'referer' => FROM_SUPPORT_FIRST_TIME,
                    'support_first_time_id' => $sft->id,
                    'trademark_id' => $sft->trademark->id,
                ]);
                $request->session()->put(SESSION_SUGGEST_PRODUCT, $request['productIds']);

                return [
                    'redirect_to' => SupportFirstTime::REDIRECT_TO_SUGGEST_AI,
                    'key_session' => SESSION_SUGGEST_PRODUCT,
                ];
                break;
            case SupportFirstTime::SEND_SESSION_TO_APPLY_TRADEMARK_WITH_NUMBER:
                $request->session()->put(SESSION_APPLY_TRADEMARK_WITH_NUMBER, $request['productIds']);

                return [
                    'redirect_to' => SupportFirstTime::REDIRECT_TO_APPLY_TRADEMARK_WITH_NUMBER,
                    'key_session' => SESSION_SUGGEST_PRODUCT,
                ];
            case SupportFirstTime::SENT_SESSION_TO_U021:
            case SupportFirstTime::SENT_SESSION_TO_U021C:
                $request->session()->put($key, [
                    'from_page' => $request['redirect_to'],
                    'data' => [
                        'm_product_ids' => $request['productIdsChecked'] ?? $request['productIds'],
                        'products' => $request['products'] ?? [],
                    ]
                ]);

                return [
                    'redirect_to' => $request['redirect_to'],
                    'key_session' => $key,
                ];
            case SupportFirstTime::SAVE_DATA_NO_SENT_SESSION:
                $suitableProductDetail = [];
                if (isset($request['productIds'])) {
                    $countProduct = count($request['productIds']);
                    if (isset($request['productIds']) && count($request['productIds']) > 1) {
                        for ($i = 0; $i < $countProduct; $i++) {
                            $suitableProductDetail[] = $this->sFTSuitableProductRepository->find($request['productIds'][$i])->update([
                                'm_product_id' => $request['productIds'][$i],
                                'is_choid_user' => 0,
                                'updated_at' => Carbon::now()
                            ]);
                        }
                    } else {
                        return [
                            'redirect_to' => 'false_ajax',
                            'key_session' => 0,
                        ];
                    }
                } else {
                    return [
                        'redirect_to' => 'false_ajax',
                        'key_session' => 0,
                    ];
                }


                $request->session()->put($key, json_encode($request->all()));
                return [
                    'redirect_to' => SupportFirstTime::REDIRECT_TO_U031_PASS,
                    'key_session' => $key,
                ];

            default:
                break;
        }
    }

    /**
     * Update Product For Support First Time
     *
     * @param mixed $request
     * @return void
     */
    public function updateProduct($request, $payment = null)
    {
        $sft = $this->repository->getSupportFirstTime($request['id']);
        $suitableProduct = $sft->StfSuitableProduct->pluck('id')->toArray();
        $countProduct = count($suitableProduct);
        $productIds = [];
        foreach ($request->all() as $key => $value) {
            if (str_contains($key, 'is_choice_user_') && $value) {
                $arrKey = explode('_', $key);
                array_push($productIds, $arrKey[count($arrKey) - 1]);
            }
        }
        $key = Str::random(11);
        $request->session()->put(SESSION_SUGGEST_PRODUCT, $productIds);

        switch ($request['redirect_to']) {
            case SupportFirstTime::REDIRECT_TO_QUOTE:
                return [
                    'redirect_to' => SupportFirstTime::REDIRECT_TO_QUOTE,
                    'key_session' => '',
                    'payment_id' => $payment->id,
                ];
            case SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT:
                if (count($productIds) == 0) {
                    return [
                        'redirect_to' => 'false',
                        'key_session' => '',
                    ];
                }
                $params = $request->all();
                $params['productIds'] = $productIds;
                $params['payment_id'] = $payment->id;
                $params['app_trademark_id'] = $payment->target_id;
                $request->session()->put($key, $params);

                return [
                    'redirect_to' => SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT,
                    'key_session' => $key,
                ];
            case SupportFirstTime::REDIRECT_TO_ANKEN_TOP:
                $countProduct = count($productIds);
                if ($countProduct == 0) {
                    return [
                        'redirect_to' => 'false',
                        'key_session' => '',
                    ];
                }

                return [
                    'redirect_to' => SupportFirstTime::REDIRECT_TO_ANKEN_TOP,
                    'key_session' => '',
                ];
            default:
                # code...
                break;
        }
    }

    /**
     * Create Payment Sft
     *
     * @param mixed $request
     * @return void
     */
    public function createPaymentSftService($request)
    {
        try {
            if ($request['redirect_to'] == SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT ||
                $request['redirect_to'] == SupportFirstTime::REDIRECT_TO_QUOTE ||
                $request['redirect_to'] == SupportFirstTime::REDIRECT_TO_ANKEN_TOP
            ) {
                DB::beginTransaction();
                $params = $request->all();
                $sft = $this->findOrFail($request['id']);
                $trademark = $this->trademarkService->findOrFail($sft->trademark_id);
                $agentGroup = AgentGroup::where('status_choice', AgentGroup::STATUS_CHOICE_TRUE)->first();
                $setting = $this->getSetting();
                //product all
                $productAll = $params['productIds'];
                //product is checked
                $productIds = [];

                foreach ($params as $key => $value) {
                    if (str_contains($key, 'is_choice_user_') && $value) {
                        $arrKey = explode('_', $key);
                        array_push($productIds, $arrKey[count($arrKey) - 1]);
                    }
                }

                // If haven't product is choose then return error
                if (!count($productIds)) {
                    throw new \Exception('PROD_REQUIRED');
                }
                // update or create app trademark
                $conditionAppTrademark = [
                    'trademark_id' => $trademark->id,
                    'admin_id' => $sft->admin_id,
                    'agent_group_id' => $agentGroup->id,
                    'status' => AppTrademark::STATUS_UNREGISTERED_SAVE,
                    'pack' => $request->pack,
                    'is_mailing_regis_cert' => $request->is_mailing_register_cert ? AppTrademark::IS_MAILING_REGIS_CERT_TRUE : AppTrademark::IS_MAILING_REGIS_CERT_FAlSE,
                    'period_registration' => $request->period_registration,
                    'cancellation_deadline' => Carbon::now(),
                ];

                if (isset($request['from_page'])) {
                    if ($request['from_page'] == U011B) {
                        $conditionAppTrademark['type_page'] = AppTrademark::PAGE_TYPE_U011B;
                    } elseif ($request['from_page'] == U011B_31) {
                        $conditionAppTrademark['type_page'] = AppTrademark::PAGE_TYPE_U011B_31;
                    }
                }

                // create or update App trademark
                $appTrademark = $this->appTrademark->updateOrCreate(['trademark_id' => $trademark->id], $conditionAppTrademark);

                $this->sFTSuitableProductService->findByCondition(['support_first_time_id' => $sft->id])->update([
                    'is_choice_user' => SFTSuitableProduct::IS_CHOICE_USER_FALSE,
                ]);

                // update Or create Trademark Info
                if (isset($request['data']) && $request['data']) {
                    $this->trademarkInfoService->updateOrCreateTrademarkInfo($request['data'], $trademark->id);
                }

                $payerInfo = $this->payerInfoService->updateOrCreate([
                    'target_id' => $sft->id,
                    'type' => TYPE_APP_TRADEMARK
                ], [
                    'target_id' => $sft->id,
                    'payment_type' => $request['payment_type'] ?? null,
                    'payer_type' => $request['payer_type'] ?? 0,
                    'm_nation_id' => $request['m_nation_id'] ?? 0,
                    'payer_name' => $request['payer_name'] ?? '',
                    'payer_name_furigana' => $request['payer_name_furigana'] ?? '',
                    'postal_code' => $request['postal_code'] ?? null,
                    'm_prefecture_id' => $request['m_prefecture_id'] ?? null,
                    'address_second' => $request['address_second'] ?? '',
                    'address_three' => $request['address_three'] ?? '',
                    'type' => TYPE_APP_TRADEMARK,
                ]);

                // Create payment with payment status is
                $dataSFT = [
                    'target_id' => $appTrademark->id,
                    'trademark_id' => $trademark->id,
                    'payer_info_id' => $payerInfo->id,
                    'cost_bank_transfer' => null,
                    'subtotal' => floor($request['subtotal']) ?? 0,
                    'commission' => floor($request['commission']) ?? 0,
                    'reduce_number_distitions' => $request['reduce_number_distitions'] ?? 0,
                    'tax' => floor($request['tax']) ?? 0,
                    'cost_service_base' => floor($request['cost_service_base']) ?? 0,
                    'total_amount' => floor($request['subtotal']) ?? 0,
                    'cost_5_year_one_distintion' => floor($request['cost_5_year_one_distintion']) ?? 0,
                    'cost_10_year_one_distintion' => floor($request['cost_10_year_one_distintion']) ?? 0,
                    'payment_status' => $request['payment_status'] ?? Payment::STATUS_SAVE,
                    'tax_withholding' => 0,
                    'payment_amount' => 0,
                    'type' => TYPE_APP_TRADEMARK,
                    'from_page' => $request['from_page'] ?? '',
                ];

                $pricePackage = $this->getPricePackService();
                $feeSubmit = $this->getFeeSubmit();
                if ($request['payment_type'] == Payment::BANK_TRANSFER) {
                    $paymentFee = $this->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
                    $dataSFT['cost_bank_transfer'] = $paymentFee['cost_service_base'] ?? 0;
                }
                if ($request->has('is_mailing_register_cert') && $request->is_mailing_register_cert) {
                    $mailRegisterCert = $this->getSystemFee(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
                    $dataSFT['cost_registration_certificate'] = $mailRegisterCert['cost_service_base'];
                }
                $dataSFT['cost_service_add_prod'] = (int) $request['cost_service_add_prod'];
                $dataSFT['sub_total'] = (int) $request['subtotal'];
                $dataSFT['total_amount'] = (int) $request['total_amount'];

                $dataSFT['cost_5_year_one_distintion'] = $feeSubmit['pof_1st_distinction_5yrs'];
                $dataSFT['cost_print_application_one_distintion'] = $pricePackage[0][2]['pof_1st_distinction_5yrs'];
                $dataSFT['cost_print_application_add_distintion'] = $pricePackage[1][0]['pof_2nd_distinction_5yrs'];
                $dataSFT['cost_10_year_one_distintion']  = 0;
                if ($request->has('period_registration') && $request->period_registration == AppTrademark::PERIOD_REGISTRATION_TRUE) {
                    $registerTermChange = $this->getSystemFee(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
                    $dataSFT['cost_change_registration_period'] = $registerTermChange['cost_service_base'];
                    $dataSFT['cost_print_application_one_distintion'] = $pricePackage[0][2]['pof_1st_distinction_10yrs'];
                    $dataSFT['cost_print_application_add_distintion'] = $pricePackage[1][0]['pof_2nd_distinction_10yrs'];
                    $dataSFT['cost_5_year_one_distintion'] = 0;
                    $dataSFT['cost_10_year_one_distintion'] = $feeSubmit['pof_1st_distinction_10yrs'];
                }
                $dataSFT['tax_withholding'] = floor($this->getTaxWithHolding($payerInfo, $dataSFT['sub_total']));
                $payment = $this->paymentService->createPaymentWithSFT($dataSFT);

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

                $dataPaymentPro = [];
                $dataPaymentPro['payment_id'] = $payment->id;
                $dataPaymentPro['productIds'] = $productIds;

                $this->paymentService->createPaymentProds($dataPaymentPro);

                $request['target_id'] = $payment->target_id;
                $request['trademark_id'] = $trademark->id;
                $request['payer_info_id'] = $payerInfo->id;
                $request['tax'] = $setting['value'];

                foreach ($productAll as $key => $prodId) {
                    $isChoiceUser = in_array($prodId, $productIds) ? SFTSuitableProduct::IS_CHOICE_USER_TRUE : SFTSuitableProduct::IS_CHOICE_USER_FALSE;
                    $isApply = in_array($prodId, $productIds) ? AppTrademarkProd::IS_APPLY : AppTrademarkProd::IS_NOT_APPLY;

                    $this->sFTSuitableProductService->updateOrCreate(
                        [
                            'support_first_time_id' => $sft->id,
                            'm_product_id' => $prodId
                        ],
                        [
                            'admin_id' => Admin::getAdminIdByRole(Admin::ROLE_ADMIN_TANTO),
                            'support_first_time_id' => $sft->id,
                            'm_product_id' => $prodId,
                            'is_choice_user' => $isChoiceUser,
                        ]
                    );

                    AppTrademarkProd::updateOrCreate([
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $prodId
                    ], [
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $prodId,
                        'is_apply' => $isApply
                    ]);
                }
                DB::commit();

                return $this->updateProduct($request, $payment);
            }

            return $this->updateProduct($request);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get info support first time edit
     *
     * @param int $id - support_first_time_id
     * @return mixed
     */
    public function getInfoSupportFirstTimeEdit(int $id)
    {
        return $this->repository->getInfoSupportFirstTimeEdit($id);
    }

    /**
     * Update or create comment
     *
     * @param $params
     * @param $id
     * @return bool
     */
    protected function updateOrCreateComment($params, $id)
    {
        $sft = $this->repository->find($id);
        $admin = auth()->guard('admin')->user();
        if (!empty($params['comments']['to_admin'])) {
            $this->sftComment->create([
                'admin_id' => $admin->id,
                'support_first_time_id' => $sft->id,
                'type' => SFTComment::TYPE_COMMENT_INSIDER,
                'content' => $params['comments']['to_admin']
            ]);
        }
        if (!empty($params['comments']['to_user'])) {
            $this->sftComment->updateOrCreate([
                'support_first_time_id' => $sft->id,
                'type' => SFTComment::TYPE_COMMENT_CUSTOMER
            ], [
                'admin_id' => $admin->id,
                'support_first_time_id' => $id,
                'type' => SFTComment::TYPE_COMMENT_CUSTOMER,
                'content' => $params['comments']['to_user']
            ]);
        }
        return true;
    }

    /**
     * Edit post sft admin
     *
     * @param array $params
     * @param int $id - support_first_time_id
     *
     * @return boolean
     */
    public function editSFT(array $params, int $id): bool
    {
        DB::beginTransaction();
        try {
            $sft = $this->repository->find($id);
            $admin = auth()->guard(ADMIN_ROLE)->user();
            $sft->update(['admin_id' => $admin->id]);

            //update sft_content_products
            $sft->StfContentProduct()->update(['is_choice_admin' => SFTContentProduct::IS_CHOICE_ADMIN_FAlSE]);
            if (!empty($params['stf_content_product_ids']) && count($params['stf_content_product_ids']) > 0) {
                $sft->StfContentProduct()->whereIn('id', $params['stf_content_product_ids'])
                    ->update(['is_choice_admin' => SFTContentProduct::IS_CHOICE_ADMIN_TRUE]);
            }

            //submit data
            if (isset($params['code']) && $params['code'] == NOTICE_CODE) {
                //1.submit form send to end user
                $this->submitSftToEndUserA011shu($params, $id);
            } else {
                //2.save to draft
                $this->saveSftDraftA011shu($params, $id);
            }

            //delete item
            if (!empty($params['data'])) {
                foreach ($params['data'] as $item) {
                    //delete item && break
                    if (isset($item['delete_item']) && $item['delete_item'] == 'on') {
                        //delete keep data prod
                        if (!empty($item['id'])) {
                            $sftKeepDataProd = $this->sftKeepDataProdService->find($item['id']);
                            if ($sftKeepDataProd) {
                                $sftKeepDataProd->update([
                                    'is_delete' => true,
                                ]);
                            }
                        }
                    }
                }
                $sftKeepData = $this->sftKeepDataService->findByCondition(['support_first_time_id' => $id])->first();
            }

            //update support_first_times
            $this->repository->findByCondition(['id' => $id])->update(['flag_role' => SupportFirstTime::FLAG_ROLE_SEKI]);

            //save histories
            $this->historyService->create([
                'admin_id' => auth('admin')->user()->id,
                'target_id' => $id,
                'page' => 'a011shu.html',
                'action' => History::ACTION_EDIT,
                'type' => History::TYPE_SUPPORT_FIRST_TIME
            ]);

            //send notice
            if (isset($params['code']) && $params['code'] == NOTICE_CODE) {
                //update support_first_times is_confirm = 1
                $sft->update([
                    'is_confirm' => SupportFirstTime::IS_CONFIRM,
                ]);
                $sft = $sft->load('trademark');
                $trademark = $sft->trademark ?? null;

                if (!empty($trademark)) {
                    // Send Notice
                    //update notice detail No 53
                    $targetPage = route('admin.support-first-time.create', $trademark->id);
                    $targetPage = str_replace(request()->root(), '', $targetPage);
                    $noticeDetailPeriouNo53G = $this->noticeDetailService->findByCondition([
                        'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => NoticeDetail::IS_ACTION_TRUE,
                        'content' => 'はじめからサポート：提案内容作成・確認',
                        'completion_date' => null,
                    ])->with('notice')->get()
                        ->where('notice.flow', Notice::FLOW_SFT)
                        ->where('notice.trademark_id', $trademark->id)
                        ->where('notice.user_id', $trademark->user_id);
                    $noticeDetailPeriouNo53G->map(function ($item) {
                        $item->update([
                            'completion_date' => Carbon::now(),
                        ]);
                    });

                    $noticeDetailPeriouNo53H = $this->noticeDetailService->findByCondition([
                        'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '責任者　はじめからサポート：提案内容作成　判断・承認',
                        'completion_date' => null,
                    ])->with('notice')->get()
                        ->where('notice.flow', Notice::FLOW_SFT)
                        ->where('notice.trademark_id', $trademark->id)
                        ->where('notice.user_id', $trademark->user_id);
                    $noticeDetailPeriouNo53H->map(function ($item) {
                        $item->update([
                            'completion_date' => Carbon::now(),
                        ]);
                    });

                    $this->noticeService->sendNotice([
                        'notices' => [
                            'flow' => Notice::FLOW_SFT,
                            'user_id' => $trademark->user_id,
                            'trademark_id' => $trademark->id,
                        ],
                        'notice_details' => [
                            // Send Notice Seki
                            [
                                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                                'target_page' => route('admin.support-first-time.edit', $sft->id),
                                'redirect_page' => null,
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                'content' => 'はじめからサポート：提案完了',
                                'attribute' => 'お客様へ',
                                'completion_date' => Carbon::now(),
                                'comment' => $params['comments']['to_admin']
                            ],
                            // Send Notice User
                            [
                                'target_id' => $trademark->user_id,
                                'type_acc' => NoticeDetail::TYPE_USER,
                                'target_page' => route('admin.support-first-time.edit', $sft->id),
                                'redirect_page' => route('user.support.first.time.u011b', $sft->id),
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                                'is_action' => true,
                                'content' => 'はじめからサポート：AMSからの提案',
                            ],
                            [
                                'target_id' => $trademark->user_id,
                                'type_acc' => NoticeDetail::TYPE_USER,
                                'target_page' => route('admin.support-first-time.edit', $sft->id),
                                'redirect_page' => route('user.support.first.time.u011b', $sft->id),
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                'content' => 'はじめからサポート：AMSからの提案',
                            ],
                        ],
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }

    /**
     * Submit SFT to end user a011shu
     *
     * @param array $params
     * @param int $sftId
     * @return void
     */
    public function submitSftToEndUserA011shu(array $params, int $sftId)
    {
        //update or create sft_comments
        $this->updateOrCreateComment($params, $sftId);

        //delete sft_keep_datas, sft_keep_data_prods, sft_keep_data_prod_codes
        $keepData = $this->sftKeepDataService->findByCondition(['support_first_time_id' => $sftId])->first();
        if ($keepData) {
            if ($keepData->sftKeepDataProds->count() > 0) {
                foreach ($keepData->sftKeepDataProds as $sftKeepDataProd) {
                    $sftKeepDataProd->sftKeepDataProdCodes()->delete();
                    $sftKeepDataProd->delete();
                }
            }
            $keepData->delete();
        }

        if (!empty($params['data'])) {
            foreach ($params['data'] as $item) {
                $item['is_block'] = $item['is_block'] ?? SFTSuitableProduct::NOT_IS_BLOCK;
                $dataDecision = $item['data_decision'];

                //if is_decision = 2
                if ($item['is_decision'] == SFTKeepDataProd::EDIT_IS_DECISION || $item['is_decision'] == SFTKeepDataProd::DRAFT_IS_DECISION) {
                    //product_type = 4
                    if ($item['product_type'] == SFTKeepDataProd::TYPE_SEMI_CLEAN) {
                        $product = $this->mProductService->create([
                            'admin_id' => auth()->user()->id,
                            'm_distinction_id' => $dataDecision['m_distinction_id'],
                            'products_number' => Str::random(8),
                            'name' => $dataDecision['m_product_name'] ?? $dataDecision['product_name_edit'],
                            'type' => $item['product_type'],
                        ]);

                        //create mCode
                        $codeData = explode(',', $dataDecision['m_code']);
                        if (count($codeData) > 0) {
                            foreach ($codeData as $codeName) {
                                $code = MCode::getMCodeByName($codeName);
                                if (!$code) {
                                    //create code
                                    $code = $this->mCodeService->create([
                                        'admin_id' => auth()->user()->id,
                                        'name' => $codeName,
                                        'type' => SFTKeepDataProd::TYPE_SEMI_CLEAN
                                    ]);
                                }

                                $this->productCodeService->updateOrCreate([
                                    'm_product_id' => $product->id,
                                    'm_code_id' => $code->id
                                ]);
                            }
                        }
                        if (isset($item['sft_suitable_product_id']) && !empty($item['sft_suitable_product_id'])) {
                            $sftSuittableProdModel = $this->sFTSuitableProductRepository->updateOrCreate([
                                'id' => $item['sft_suitable_product_id'],
                            ], [
                                'admin_id' => auth()->user()->id,
                                'support_first_time_id' => $sftId,
                                'm_product_id' => $product->id,
                                'is_block' => $item['is_block']
                            ]);
                        } else {
                            $sftSuittableProdModel = $this->sFTSuitableProductRepository->create([
                                'admin_id' => auth()->user()->id,
                                'support_first_time_id' => $sftId,
                                'm_product_id' => $product->id,
                                'is_block' => $item['is_block']
                            ]);
                        }
                    } elseif ($item['product_type'] == SFTKeepDataProd::TYPE_CREATIVE_CLEAN) {
                        //product_type = 3
                        //create product
                        $product = $this->mProductService->create([
                            'admin_id' => auth()->user()->id,
                            'm_distinction_id' => $dataDecision['m_distinction_id'],
                            'products_number' => Str::random(8),
                            'name' => $dataDecision['m_product_name'] ?? $dataDecision['product_name_edit'],
                            'type' => $item['product_type'],
                        ]);

                        //create mCode
                        $codeData = explode(',', $dataDecision['m_code']);
                        if (count($codeData) > 0) {
                            foreach ($codeData as $codeName) {
                                $code = MCode::getMCodeByName($codeName);
                                if (!$code) {
                                    //create code
                                    $code = $this->mCodeService->create([
                                        'admin_id' => auth()->user()->id,
                                        'name' => $codeName,
                                        'type' => SFTKeepDataProd::TYPE_CREATIVE_CLEAN
                                    ]);
                                }

                                $this->productCodeService->updateOrCreate([
                                    'm_product_id' => $product->id,
                                    'm_code_id' => $code->id
                                ]);
                            }
                        }

                        $this->productCodeService->updateOrCreate([
                            'm_product_id' => $product->id,
                            'm_code_id' => $code->id
                        ]);

                        if (isset($item['sft_suitable_product_id']) && !empty($item['sft_suitable_product_id'])) {
                            $sftSuittableProdModel = $this->sFTSuitableProductRepository->updateOrCreate([
                                'id' => $item['sft_suitable_product_id'],
                            ], [
                                'admin_id' => auth()->user()->id,
                                'support_first_time_id' => $sftId,
                                'm_product_id' => $product->id,
                                'is_block' => $item['is_block']
                            ]);
                        } else {
                            $sftSuittableProdModel = $this->sFTSuitableProductRepository->create([
                                'admin_id' => auth()->user()->id,
                                'support_first_time_id' => $sftId,
                                'm_product_id' => $product->id,
                                'is_block' => $item['is_block']
                            ]);
                        }
                    } else {
                        //product_type = 1,2
                        if (isset($item['sft_suitable_product_id']) && !empty($item['sft_suitable_product_id']) && !empty($dataDecision['product_id'])) {
                            $sftSuittableProdModel = $this->sFTSuitableProductRepository->updateOrCreate([
                                'id' => $item['sft_suitable_product_id'],
                            ], [
                                'admin_id' => auth()->user()->id,
                                'support_first_time_id' => $sftId,
                                'm_product_id' => $dataDecision['product_id'],
                                'is_block' => $item['is_block']
                            ]);
                        } else {
                            if (!empty($dataDecision['product_id'])) {
                                $sftSuittableProdModel = $this->sFTSuitableProductRepository->create([
                                    'admin_id' => auth()->user()->id,
                                    'support_first_time_id' => $sftId,
                                    'm_product_id' => $dataDecision['product_id'],
                                    'is_block' => $item['is_block']
                                ]);
                            }
                        }
                    }
                }

                if (isset($item['delete_item'])) {
                    //delete sft_suitable_products
                    if (isset($item['sft_suitable_product_id'])) {
                        $this->sFTSuitableProductService->findByCondition([
                            'id' => $item['sft_suitable_product_id'],
                        ])->delete();
                    }
                }
            }
        }
    }

    /**
     * Save sft draft a011shu
     *
     * @param array $params
     * @param int $sftId
     *
     * @return void
     */
    public function saveSftDraftA011shu(array $params, int $sftId)
    {
        // create or update sft_keep_data
        $sftKeepData = $this->sftKeepDataService->updateOrCreate([
            'support_first_time_id' => $sftId,
        ], [
            'support_first_time_id' => $sftId,
            'comment_from_ams' => $params['comments']['to_user'],
            'comment_internal' => $params['comments']['to_admin'],
            'content_product' => json_encode($params['stf_content_product_ids'] ?? [])
        ]);

        if (!empty($params['data'])) {
            foreach ($params['data'] as $item) {
                $dataDecision = $item['data_decision'];
                $dataEdit = $item['data_edit'];
                //save sft_keep_data_prods when save draft
                if (empty($params['code'])) {
                    $dataKeepSave = [];
                    $dataKeepSave['sft_keep_data_id'] = $sftKeepData->id;
                    $dataKeepSave['type_product'] = $item['product_type'] ?? 0;
                    $dataKeepSave['is_decision'] = $item['is_decision'] ?? 0;
                    $dataKeepSave['is_block'] = $item['is_block'] ?? 0;
                    $dataKeepSave['m_distinction_id'] = $dataEdit['m_distinction_id'];
                    $dataKeepSave['is_delete'] = isset($item['delete_item']);

                    if (!empty($item['sft_suitable_product_id'])) {
                        $dataKeepSave['sft_suitable_product_id'] = $item['sft_suitable_product_id'];
                    } else {
                        //create sft_keep_data_prods
                        $dataKeepSave['sft_suitable_product_id'] = null;
                        //if type_product = 4, 3
                        if (in_array($item['product_type'], [MProduct::TYPE_CREATIVE_CLEAN, MProduct::TYPE_SEMI_CLEAN])) {
                            $dataKeepSave['product_type'] = $item['product_type'];
                            $dataKeepSave['product_name_edit'] = $dataEdit['m_product_name'];
                            $dataKeepSave['product_id'] = null;
                        } else {
                            //if type_product = 1,2
                            $mProduct = $this->mProductService->find($dataEdit['product_id']);
                            if ($mProduct) {
                                $dataKeepSave['product_id'] = $mProduct->id;
                            }
                        }
                    }

                    if ($item['is_decision'] == SFTKeepDataProd::EDIT_IS_DECISION || empty($item['is_decision'])) {
                        if (in_array($item['product_type'], [MProduct::TYPE_CREATIVE_CLEAN, MProduct::TYPE_SEMI_CLEAN])) {
                            $dataKeepSave['product_type'] = $item['product_type'];
                            $dataKeepSave['product_name_edit'] = $dataEdit['m_product_name'];
                            $dataKeepSave['product_id'] = null;
                        } else {
                            //if type_product = 1,2
                            $mProduct = $this->mProductService->find($dataEdit['product_id']);
                            if ($mProduct) {
                                $dataKeepSave['product_id'] = $mProduct->id;
                            }
                        }
                    } else {
                        //is_decision = 1
                        $dataKeepSave['product_name_edit'] = $dataEdit['m_product_name'];
                        $dataKeepSave['product_id'] = $dataEdit['product_id'];
                    }

                    //update or create table sft_keep_data_prods
                    $sftKeepDataProdModel = $this->sftKeepDataProdService->updateOrCreate([
                        'sft_keep_data_id' => $sftKeepData->id,
                        'id' => $item['id'] ?? null
                    ], $dataKeepSave);

                    //update or create sft_keep_data_prod_codes: product_type = 3 or 4
                    //1.type = 3
                    if (isset($dataEdit['code']) && count($dataEdit['code']) > 0) {
                        if ($sftKeepDataProdModel->type_product == MProduct::TYPE_CREATIVE_CLEAN) {
                            foreach ($dataEdit['code'] as $code) {
                                if ($code && !empty($code['name'])) {
                                    if (!empty($code['old_id'])) {
                                        $this->sftKeepDataProdCodeService->updateOrCreate([
                                            'sft_keep_data_prod_id' => $sftKeepDataProdModel->id,
                                            'id' => $code['old_id']
                                        ], [
                                            'code' => $code['name'],
                                        ]);
                                    } else {
                                        $this->sftKeepDataProdCodeService->create([
                                            'sft_keep_data_prod_id' => $sftKeepDataProdModel->id,
                                            'code' => $code['name']
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                    //2.type = 4
                    if (!empty($dataEdit['m_code']) && $sftKeepDataProdModel->type_product == MProduct::TYPE_SEMI_CLEAN) {
                        $codesString = explode(',', $dataEdit['m_code']);
                        foreach ($codesString as $code) {
                            $mCode = MCode::getMCodeByName($code);
                            if ($mCode) {
                                // sft_keep_data_prod_codes: type 4
                                $this->sftKeepDataProdCodeService->updateOrCreate([
                                    'sft_keep_data_prod_id' => $sftKeepDataProdModel->id,
                                    'm_code_id' => $mCode->id
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return $sftKeepData;
    }

    /**
     * Update role sft
     *
     * @param array $inputs
     * @param int $idSft
     * @return bool
     */
    public function updateRoleSft(array $inputs, int $idSft): bool
    {
        DB::beginTransaction();
        try {
            $sft = $this->repository->findByCondition(['id' => $idSft])->first();
            $sft->update([
                'flag_role' => SupportFirstTime::FLAG_ROLE_SEKI,
                'is_confirm' => SupportFirstTime::IS_CONFIRM,
            ]);

            //histories
            $this->historyService->create([
                'admin_id' => auth('admin')->user()->id,
                'target_id' => $idSft,
                'page' => 'a011s.html',
                'action' => History::ACTION_CREATE,
                'type' => History::TYPE_TRADEMARK_REGISTER
            ]);

            //update sft_content_products
            $sft->StfContentProduct()->update(['is_choice_admin' => SFTContentProduct::IS_CHOICE_ADMIN_FAlSE]);
            if (isset($inputs['sft_content_product_ids'])) {
                $sft->StfContentProduct()->whereIn('id', $inputs['sft_content_product_ids'])->update(['is_choice_admin' => SFTContentProduct::IS_CHOICE_ADMIN_TRUE]);
            }

            $sft = $sft->load('trademark.user');
            $trademark = $sft->trademark;

            // Send Notice
            if (!empty($trademark)) {
                $targetPage = route('admin.support-first-time.create', $trademark->id);
                $targetPage = str_replace(request()->root(), '', $targetPage);
                //update notice detail No 53
                $noticeDetailPeriouNo53G = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'content' => 'はじめからサポート：提案内容作成・確認',
                    'completion_date' => null,
                ])->with('notice')->get()
                    ->where('notice.trademark_id', $trademark->id)
                    ->where('notice.user_id', $trademark->user_id)
                    ->where('notice.flow', Notice::FLOW_SFT);
                $noticeDetailPeriouNo53G->map(function ($item) {
                    $item->update([
                        'completion_date' => Carbon::now(),
                    ]);
                });

                $noticeDetailPeriouNo53H = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '責任者　はじめからサポート：提案内容作成　判断・承認',
                    'completion_date' => null,
                ])->with('notice')->get()
                    ->where('notice.trademark_id', $trademark->id)
                    ->where('notice.user_id', $trademark->user_id)
                    ->where('notice.flow', Notice::FLOW_SFT);
                $noticeDetailPeriouNo53H->map(function ($item) {
                    $item->update([
                        'completion_date' => Carbon::now(),
                    ]);
                });
                //end update notice detail No 53

                $this->noticeService->sendNotice([
                    'notices' => [
                        'flow' => Notice::FLOW_SFT,
                        'user_id' => $trademark->user_id,
                        'trademark_id' => $trademark->id,
                    ],
                    'notice_details' => [
                        // Send Notice Seki
                        [
                            'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                            'content' => 'はじめからサポート：提案完了',
                            'target_page' => route('admin.support-first-time.index', $trademark->id),
                            'redirect_page' => null,
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'attribute' => 'お客様へ',
                            'completion_date' => Carbon::now(),
                        ],
                        // Send Notice User
                        [
                            'target_id' => $trademark->user_id,
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'target_page' => route('admin.support-first-time.index', $trademark->id),
                            'redirect_page' => route('user.support.first.time.u011b', $sft->id),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'is_action' => true,
                            'content' => 'はじめからサポート：AMSからの提案',
                        ],
                        [
                            'target_id' => $trademark->user_id,
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'target_page' => route('admin.support-first-time.index', $trademark->id),
                            'redirect_page' => route('user.support.first.time.u011b', $sft->id),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'content' => 'はじめからサポート：AMSからの提案',
                        ],
                    ],
                ]);
            }
            // send mail submit a011s
            $mailData = [
                'from_page' => A011S,
                'user' => $sft->trademark->user
            ];
            $this->mailTemplateService->sendMailRequest($mailData, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }

    /**
     * Get Comment TanTou
     *
     * @param mixed $sft
     * @param mixed $type
     * @return mixed
     */
    public function getCommentTanTou($sft, $type)
    {
        return $sft->stfComment()->whereHas('admin', function ($j) {
            $j->where('role', Admin::ROLE_ADMIN_TANTO);
        })->where('type', $type)->orderBy('created_at', 'desc')->first();
    }
}
