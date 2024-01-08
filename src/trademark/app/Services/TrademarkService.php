<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Helpers\FileHelper;
use App\Models\AgentGroup;
use App\Models\AgentGroupMap;
use App\Models\Trademark;
use App\Models\AppTrademark;
use App\Models\AppTrademarkProd;
use App\Models\MPriceList;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Models\PayerInfo;
use App\Models\Payment;
use App\Models\Precheck;
use App\Models\PrecheckProduct;
use App\Models\RegisterTrademarkProd;
use App\Models\RegisterTrademarkRenewal;
use App\Models\Setting;
use App\Models\TrademarkDocument;
use App\Models\TrademarkInfo;
use App\Repositories\MPriceListRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PrecheckRepository;
use App\Repositories\SettingRepository;
use App\Repositories\TrademarkRepository;
use App\Repositories\TrademarkDocumentRepository;
use App\Repositories\NoticeDetailBtnRepository;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\MProductRepository;
use App\Repositories\PayerInfoRepository;
use App\Repositories\RegisterTrademarkRenewalRepository;
use App\Repositories\RegisterTrademarkRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\TrademarkInfoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TrademarkService extends BaseService
{
    protected RegisterTrademarkRenewalRepository $registerTrademarkRenewalRepository;
    protected PaymentRepository $paymentRepository;
    protected PayerInfoRepository $payerInfoRepository;
    protected RegisterTrademarkRepository $registerTrademarkRepository;
    protected MProductRepository $mProductRepository;
    protected TrademarkDocumentRepository $trademarkDocumentRepository;
    protected MPriceListRepository $mPriceListRepository;
    protected SettingRepository $settingRepository;
    protected PrecheckRepository $precheckRepository;
    protected TrademarkInfoRepository $trademarkInfoRepository;
    protected NoticeDetailBtnRepository $noticeDetailBtnRepository;

    /**
     * Initializing the instances and variables
     *
     * @param TrademarkRepository $trademarkRepository
     * @param TrademarkDocumentRepository $trademarkDocumentRepository
     * @param MPriceListRepository $mPriceListRepository
     * @param SettingRepository $settingRepository
     * @param PrecheckRepository $precheckRepository
     * @param PaymentRepository $paymentRepository
     * @param NoticeDetailBtnRepository $noticeDetailBtnRepository;
     */
    public function __construct(
        TrademarkRepository $trademarkRepository,
        TrademarkDocumentRepository $trademarkDocumentRepository,
        MPriceListRepository $mPriceListRepository,
        SettingRepository $settingRepository,
        PrecheckRepository $precheckRepository,
        PaymentRepository $paymentRepository,
        TrademarkInfoRepository $trademarkInfoRepository,
        NoticeDetailBtnRepository $noticeDetailBtnRepository,
        RegisterTrademarkRenewalRepository $registerTrademarkRenewalRepository,
        PayerInfoRepository $payerInfoRepository,
        RegisterTrademarkRepository $registerTrademarkRepository,
        MProductRepository $mProductRepository
    )
    {
        $this->repository = $trademarkRepository;
        $this->trademarkDocumentRepository = $trademarkDocumentRepository;
        $this->mPriceListRepository = $mPriceListRepository;
        $this->settingRepository = $settingRepository;
        $this->precheckRepository = $precheckRepository;
        $this->paymentRepository = $paymentRepository;
        $this->trademarkInfoRepository = $trademarkInfoRepository;
        $this->noticeDetailBtnRepository = $noticeDetailBtnRepository;
        $this->registerTrademarkRenewalRepository = $registerTrademarkRenewalRepository;
        $this->payerInfoRepository = $payerInfoRepository;
        $this->registerTrademarkRepository = $registerTrademarkRepository;
        $this->mProductRepository = $mProductRepository;
    }

    /**
     * Create trademark.
     *
     * @param  array $input Data insert trademark table.
     * @return Model        Data insert success.
     */
    public function createTrademark(array $input): Model
    {
        $user = auth()->guard('web')->user();
        $input['user_id'] = $user->id;
        $trademarks = DB::select("
            SELECT
                max(SUBSTRING(trademark_number, 6, 6)) AS max_trademark_number
            FROM trademarks
                WHERE user_id = " . $user->id . "
                AND trademark_number LIKE 'L%'
        ");

        if (!empty($trademarks) && isset($trademarks[0])) {
            $trademarkNumber = $trademarks[0]->max_trademark_number;
        }

        // get trademark number = user_number + last 2 digits of the year + ordinal numbers from 1 ~ 9999 (ascending)
        $userNumber = $user->user_number;
        $input['trademark_number'] = $userNumber . date('y') . str_pad((int) substr($trademarkNumber, -4) + 1, 4, 0, STR_PAD_LEFT);

        // upload file
        if (!empty($input['image_trademark']) && isset($input['image_trademark']['filename'])) {
            $file = $input['image_trademark'];
            $image = FileHelper::uploads($file);
            $input['image_trademark'] = $image[0]['filepath'] ?? null;
        }

        return $this->create($input);
    }

    /**
     * Update or create trademark.
     *
     * @param  array $input Data insert trademark table.
     */
    public function updateOrCreateTrademark(array $input)
    {
        $user = auth()->guard('web')->user();
        $input['user_id'] = $user->id;
        $trademarkNumber = 0;
        $trademarks = DB::select("
            SELECT
                max(SUBSTRING(trademark_number, 6, 6)) AS max_trademark_number
            FROM trademarks
                WHERE user_id = " . $user->id . "
                AND trademark_number LIKE 'L%'
        ");

        if (!empty($trademarks) && isset($trademarks[0])) {
            $trademarkNumber = $trademarks[0]->max_trademark_number;
        }

        // get trademark number = user_number + last 2 digits of the year + ordinal numbers from 1 ~ 9999 (ascending)
        $userNumber = $user->user_number;
        $input['trademark_number'] = $userNumber . date('y') . str_pad((int) substr($trademarkNumber, -4) + 1, 4, 0, STR_PAD_LEFT);

        // upload file
        if (!empty($input['image_trademark']) && !is_string($input['image_trademark'])) {
            $file = $input['image_trademark'];
            $image = FileHelper::uploads($file);
            $input['image_trademark'] = $image[0]['filepath'] ?? null;
        }

        return $this->updateOrCreate(
            [
                'id' => $input['id'] ?? 0,
            ],
            $input
        );
    }

    /**
     * Update or create trademark.
     *
     * @param  array $input Data insert trademark table.
     * @return Model        Data insert success.
     */
    public function updateOrCreateTrademarkSFT(array $input, $real = false): Model
    {
        $user = auth()->guard('web')->user();
        $input['user_id'] = $user->id;
        $id = isset($input['id']) && $input['id'] ? $input['id'] : 0;
        $userNumber = $user->user_number;
        $trademark = [];
        if (!$id) {
            $trademarkNumber = 0;
            $trademarks = DB::select("
                SELECT
                    max(SUBSTRING(trademark_number, 6, 6)) AS max_trademark_number
                FROM trademarks
                    WHERE user_id = " . $user->id . "
                    AND trademark_number LIKE 'L%'
            ");

            if (!empty($trademarks) && isset($trademarks[0])) {
                $trademarkNumber = $trademarks[0]->max_trademark_number;
            }
            if ($trademarkNumber) {
                // get trademark number = user_number + last 2 digits of the year + ordinal numbers from 1 ~ 9999 (ascending)
                $input['trademark_number'] = $userNumber . date('y') . str_pad((int) substr($trademarkNumber, -4) + 1, 4, 0, STR_PAD_LEFT);
            } else {
                $input['trademark_number'] = $userNumber . date('y') . str_pad(1, 4, 0, STR_PAD_LEFT);
            }
        } else {
            $trademark = $this->repository->find($id);
            $input['trademark_number'] = $trademark->trademark_number;
        }
        // upload file
        if (!empty($input['image_trademark']) && !is_string($input['image_trademark'])) {
            $file = $input['image_trademark'];
            $image = FileHelper::uploads($file);
            $input['image_trademark'] = $image[0]['filepath'] ?? null;
        }

        return $this->updateOrCreate(
            [
                'id' => $input['id'],
            ],
            $input
        );
    }

    /**
     * Find Registrant Information
     *
     * @param  integer $id
     * @return mixed
     */
    public function findRegistrantInformation($id)
    {
        $information = $this->repository->findRegistrantInformation($id);

        return $information;
    }

    /**
     * Show Trademark
     *
     * @param  int $id
     * @return Model
     */
    public function showTrademark($id)
    {
        $trademark = $this->repository->showTrademark($id);

        return $trademark;
    }

    /**
     * Find trademark by id
     *
     * @param   integer $id - id trademark
     * @return  object $trademark
     */
    public function findById(int $id): object
    {
        $trademark = $this->repository->findById($id);

        if (!is_null($trademark)) {
            if ($trademark->appTrademark) {
                // get pack name
                switch ($trademark->appTrademark->pack) {
                    case AppTrademark::PACK_A:
                        $trademark['pack_name'] = 'パックA';
                        break;
                    case AppTrademark::PACK_B:
                        $trademark['pack_name'] = 'パックB';
                        break;
                    case AppTrademark::PACK_C:
                        $trademark['pack_name'] = 'パックC';
                        break;
                }
            } else {
                $trademark['pack_name'] = '';
            }

            // get distinguishing points
            $trademark['distinguishing_points'] = 0;
            $trademark['total_distinguishing_points'] = $trademark->appTrademark && $trademark->appTrademark->appTrademarkProd->count()
                ? $trademark->appTrademark->appTrademarkProd->count()
                : 0;
            $trademark['trademark_register'] = [];

            if ($trademark->registerTrademark && $trademark->registerTrademark->registerTrademarkProds->count()) {
                $trademark['distinguishing_points'] = $trademark->registerTrademark->count();
                $trademark['data_register'] = $trademark->registerTrademark->registerTrademarkProds;
            } elseif ($trademark->appTrademark && $trademark->appTrademark->appTrademarkProd->count() && !$trademark->registerTrademark) {
                // get count prod apply register
                $dataRegisterTrademark = array_filter($trademark->appTrademark->appTrademarkProd->toArray(), function ($item) {
                    return $item['is_apply'] == AppTrademarkProd::IS_APPLY;
                });
                $trademark['distinguishing_points'] = count($dataRegisterTrademark);
                $trademark['data_register'] = $dataRegisterTrademark;
            }

            // get renewal period
            if ($trademark->registerTrademark) {
                $timePeriod = $trademark->registerTrademark->period_registration == PERIOD_REGISTRATION_FIVE_YEAR ? 5 : 10;
                $trademark['renewal_period'] = Carbon::createFromFormat('Y-m-d H:i:s', $trademark->registerTrademark->created_at)
                    ->addYear($timePeriod)->format('Y/m/d');
            }
        }

        return $trademark;
    }

    /**
     * Get info payment precheck
     *
     * @param array $inputs
     * @return mixed
     */
    public function getInfoPaymentPrecheck(array $inputs)
    {
        $data = [
            'cost_service_base' => 0,
            'cost_service_base_not_tax' => 0,
            'cost_service_add_prod' => 0,
            'subtotal' => 0,
            'commission' => 0,
            'tax_percentage' => 0,
            'tax' => 0,
            'cost_bank_transfer' => 0,
        ];
        $typePrecheck = (int) $inputs['type_precheck'];
        $idsProduct = isset($inputs['m_product_ids']) ? $inputs['m_product_ids'] : [];

        //tax query
        $taxData = $this->settingRepository->findByCondition(['key' => SETTING::KEY_TAX])->first();
        if ($taxData) {
            $data['tax_percentage'] = $taxData->value;
        }
        $countProduct = count($idsProduct);
        if ($countProduct > 0) {
            $serviceType = MPriceList::BEFORE_FILING;
            if ($typePrecheck == Precheck::TYPE_PRECHECK_SIMPLE_REPORT) {
                $packageTypeServiceBase = 1.2;
                $packageTypeServiceAddProd = 1.3;
            } elseif ($typePrecheck == Precheck::TYPE_PRECHECK_DETAILED_REPORT) {
                $packageTypeServiceBase = 1.4;
                $packageTypeServiceAddProd = 1.5;
            }

            //cost_service_base && commission && tax
            $priceCommon = $this->mPriceListRepository->getPriceCommonOfPrecheck($serviceType, $packageTypeServiceBase);
            if ($priceCommon) {
                $data['cost_service_base'] = $priceCommon->base_price + $priceCommon->base_prices * $data['tax_percentage'] / 100;
                $data['commission'] = $priceCommon->base_price;
                $data['tax'] = $priceCommon->base_prices * $data['tax_percentage'] / 100;
            }

            //cost_service_add_prod && tax && commission if choose large more 3 product
            $getCostServiceAddProd = $this->_getCostServiceAddProd($idsProduct, $packageTypeServiceAddProd, $data['tax_percentage']);
            $data['cost_service_add_prod'] = $getCostServiceAddProd['costServiceAddProd'];
            $data['tax'] += $getCostServiceAddProd['taxServiceAddProd'];
            $data['commission'] += $getCostServiceAddProd['costServiceAddProdNotTax'];

            //subtotal
            $data['subtotal'] = $data['cost_service_base'] + $data['cost_service_add_prod'];

            //if method payment is bank transfer
            if (isset($inputs['payment_type']) && (int) $inputs['payment_type'] == PayerInfo::PAYMENT_BANK_TRANSFER) {
                $priceCommonFeeBank = $this->mPriceListRepository->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, 6.1);
                $data['cost_bank_transfer'] = $priceCommonFeeBank->base_price + $priceCommonFeeBank->base_price * $data['tax_percentage'] / 100;
                $data['subtotal'] += $data['cost_bank_transfer'];
                $data['tax'] += $priceCommonFeeBank->base_price * $data['tax_percentage'] / 100;
            }
        }

        return $data;
    }

    /**
     * Register precheck post
     *
     * @param array $params
     * @param array $infoBill
     * @param integer $tradmarkId
     * @return mixed
     */
    public function savePrecheckPost(array $params, array $infoBill, int $tradmarkId)
    {
        DB::beginTransaction();
        try {
            //store precheck
            $precheck = $this->precheckRepository->create([
                'trademark_id' => $tradmarkId,
                'type_precheck' => $params['type_precheck'],
            ]);
            //store precheck_products
            if ($precheck) {
                $precheck->products()->attach(
                    $params['m_product_ids'],
                    ['is_register_product' => PrecheckProduct::IS_PRECHECK_PRODUCT, 'admin_id' => 3]
                );
            }
            //store payments
            $this->paymentRepository->create([
                'cost_service_base' => $infoBill['cost_service_base'],
                'cost_service_add_prod' => $infoBill['cost_service_add_prod'],
                'subtotal' => $infoBill['subtotal'],
                'commission' => $infoBill['commission'],
                'tax' => $infoBill['tax'],
                'cost_bank_transfer' => $infoBill['cost_bank_transfer'],
            ]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Get cost service add product has tax & not tax
     *
     * @param $idsProduct
     * @param $packType
     * @param $tax
     * @return array
     */
    protected function _getCostServiceAddProd($idsProduct, $packType, $taxPercent)
    {
        $costServiceAddProd = 0;
        $taxServiceAddProd = 0;
        $costServiceAddProdNotTax = 0;
        $totalCup = 0;
        $numberProdAdd = count($idsProduct) - NUMBER_PRODUCT_EXTRA_FEE_LIMIT;
        if ($numberProdAdd >= 1 && $numberProdAdd <= NUMBER_PRODUCT_EXTRA_FEE_LIMIT) {
            $totalCup = 1;
        }
        if ($numberProdAdd > NUMBER_PRODUCT_EXTRA_FEE_LIMIT) {
            $totalCup = ceil((count($idsProduct) - NUMBER_PRODUCT_EXTRA_FEE_LIMIT) / NUMBER_PRODUCT_EXTRA_FEE_LIMIT);
        }
        $priceCommon = $this->mPriceListRepository->getPriceCommonOfPrecheck(MPriceList::BEFORE_FILING, $packType);
        if ($priceCommon) {
            $costServiceAddProd = ($priceCommon->base_price + $priceCommon->base_price * $taxPercent / 100) * $totalCup;
            $taxServiceAddProd = ($priceCommon->base_price * $taxPercent / 100) * $totalCup;
            $costServiceAddProdNotTax = $priceCommon->base_price * $totalCup;
        }
        return [
            'costServiceAddProd' => $costServiceAddProd,
            'taxServiceAddProd' => $taxServiceAddProd,
            'costServiceAddProdNotTax' => $costServiceAddProdNotTax,
        ];
    }

    /**
     * Get Trademark Notice
     *
     * @param  mixed $tradmarkId
     * @return void
     */
    public function getTrademarkNotice($tradmarkId)
    {
        return $this->repository->getTrademarkNotice($tradmarkId);
    }

    /**
     * Get Trademark for top page.
     *
     * @param array $params
     */
    public function getTrademarkTop(array $params = []): ?Collection
    {
        return $this->repository->findByCondition($params)->with([
            'notice.noticeDetails' => function ($q) {
                return $q->where([
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP
                ]);
            },
        ])->get();
    }

    /**
     * Format Data Notice Detail
     *
     * @param Collection $data
     * @return  Collection
     */
    public function formatListUser(Collection $data): Collection
    {
        $data = $data->load([
            'notices',
            'notices.noticeDetails',
            'appTrademark',
            'appTrademark.trademarkInfo',
            'comparisonTrademarkResults',
        ]);

        $data = $data->map(function ($item) {
            // Get Notice Detail
            $isExpired = false;
            $noticeDetail = null;
            $noticeDetailUpdatedAt = null;
            $noticeDetailResponseDeadline = null;

            $notices = $item->notices;
            $lastNotice = $notices->last();
            if (!empty($lastNotice)) {
                $noticeDetails = $lastNotice->noticeDetails
                    ->where('type_acc', NoticeDetail::TYPE_USER)
                    ->whereIn('type_notify', [NoticeDetail::TYPE_NOTIFY_TODO, NoticeDetail::TYPE_NOTIFY_DEFAULT])
                    ->whereIn('type_page', [NoticeDetail::TYPE_PAGE_TOP, NoticeDetail::TYPE_PAGE_ANKEN_TOP]);
                $lastNoticeDetail = $noticeDetails->last();
                if (!empty($lastNoticeDetail)) {
                    $noticeDetail = $lastNoticeDetail;
                    $responseDeadline = $noticeDetail->response_deadline ?? null;

                    $noticeDetail->response_deadline_format = CommonHelper::formatTime($responseDeadline, 'Y/m/d');
                    $noticeDetailUpdatedAt = $noticeDetail->updated_at->format('Y-m-d H:i:s');
                    $noticeDetailResponseDeadline = $noticeDetail->response_deadline;
                }
            }
            $item->notice_detail = $noticeDetail;

            $comparisonTrademarkResult = $item->comparisonTrademarkResults->last();
            if (!empty($comparisonTrademarkResult->response_deadline)) {
                $responseDeadline = Carbon::parse($comparisonTrademarkResult->response_deadline);
                $now = Carbon::now();
                if ($now >= $responseDeadline) {
                    $isExpired = true;
                }
            }
            $item->is_expired = $isExpired;

            // Trademark Info Name
            $item->trademark_info_name = '';
            $appTrademark = $item->appTrademark;
            if (!empty($appTrademark)) {
                $trademarkInfos = $appTrademark->trademarkInfo->where('type', TrademarkInfo::TYPE_TRADEMARK);
                $lastTrademarkInfo = $trademarkInfos->last();
                if (!empty($lastTrademarkInfo)) {
                    $item->trademark_info_name = $lastTrademarkInfo->name;
                }
            }

            // Soft
            $item->soft_trademark_number = $item->trademark_number ?? null;
            $item->application_number = $item->application_number ?? null;
            $item->soft_register_number = null;
            $item->soft_created_at = $noticeDetailUpdatedAt ?? null;
            $item->soft_response_deadline = $noticeDetailResponseDeadline ?? null;

            return $item;
        });

        return $data;
    }

    /**
     * Format data list trademark
     *
     * @param Collection $trademarks
     * @return  Collection
     */
    public function listTrademark(Collection $trademarks): Collection
    {
        $trademarks->load([
            'notices.noticeDetails',
            'appTrademark.trademarkInfo',
            'comparisonTrademarkResult',
        ]);

        $trademarks->map(function ($item) {
            $isExpired = false;
            $noticeDetail = null;
            $noticeDetailUpdatedAt = null;
            $noticeDetailResponseDeadline = null;
            $isCancel = false;
            $isStatusManagement = false;

            $notices = $item->notices;
            $comparisonTrademarkResult = $item->comparisonTrademarkResult;
            $appTrademark = $item->appTrademark;

            $lastNotice = $notices->last();

            if (!empty($lastNotice)) {
                $noticeDetails = $lastNotice->noticeDetails
                    ->where('type_acc', NoticeDetail::TYPE_USER)
                    ->whereIn('type_notify', [NoticeDetail::TYPE_NOTIFY_TODO, NoticeDetail::TYPE_NOTIFY_DEFAULT])
                    ->whereIn('type_page', [NoticeDetail::TYPE_PAGE_TOP, NoticeDetail::TYPE_PAGE_ANKEN_TOP]);
                $lastNoticeDetail = $noticeDetails->last();

                if (!empty($lastNoticeDetail)) {
                    $noticeDetail = $lastNoticeDetail;
                    $responseDeadline = $noticeDetail->response_deadline ?? null;

                    $noticeDetail->response_deadline_format = CommonHelper::formatTime($responseDeadline, 'Y/m/d');
                    $noticeDetailUpdatedAt = $noticeDetail->updated_at->format('Y-m-d H:i:s');
                    $noticeDetailResponseDeadline = $noticeDetail->response_deadline;
                }
            }

            if (!empty($comparisonTrademarkResult)) {
                if ($comparisonTrademarkResult->is_cancel) {
                    $isCancel = true;
                }
                $responseDeadline = $comparisonTrademarkResult->response_deadline ? Carbon::parse($comparisonTrademarkResult->response_deadline) : null;
                if ($responseDeadline) {
                    if (Carbon::now() <= $responseDeadline) {
                        $isExpired = true;
                    }
                }
            }

            $item->notice_detail = $noticeDetail;
            $item->is_expired = $isExpired;
            $item->trademark_info_name = '';
            $item->is_not_status_unregistered_save = 0;

            if (!empty($appTrademark)) {
                if ($appTrademark->is_cancel) {
                    $isCancel = true;
                }
                $item->is_status_app_trademark = $appTrademark->status ?? 0;
                $trademarkInfos = $appTrademark->trademarkInfo->where('type', TrademarkInfo::TYPE_TRADEMARK);
                $lastTrademarkInfo = $trademarkInfos->last();
                if (!empty($lastTrademarkInfo)) {
                    $item->trademark_info_name = $lastTrademarkInfo->name;
                }
            }

            if ($item->status_management) {
                $isStatusManagement = true;
            }

            $item->is_cancel = $isCancel;
            $item->is_status_management = $isStatusManagement;

            // Soft
            $item->soft_trademark_number = $item->trademark_number ?? null;
            $item->application_number = $item->application_number ?? null;
            $item->soft_register_number = null;
            $item->soft_created_at = $noticeDetailUpdatedAt ?? null;
            $item->soft_response_deadline = $noticeDetailResponseDeadline ?? null;

            return $item;
        });

        return $trademarks;
    }

    /**
     * Format data list register trademark
     *
     * @param Collection $registerTrademarks
     * @return  Collection
     */
    public function listRegisterTrademark(Collection $registerTrademarks): Collection
    {
        $registerTrademarks->load([
            'appTrademark',
            'registerTrademark',
            'comparisonTrademarkResult',
        ]);


        $registerTrademarks->map(function ($item) {
            $appTrademark = $item->appTrademark;
            $registerTrademark = $item->registerTrademark;
            $comparisonTrademarkResult = $item->comparisonTrademarkResult;
            $isCancel = false;
            $isStatusManagement = false;

            if (!empty($appTrademark)) {
                if ($appTrademark->is_cancel) {
                    $isCancel = true;
                }
            }
            if (!empty($comparisonTrademarkResult)) {
                if ($comparisonTrademarkResult->is_cancel) {
                    $isCancel = true;
                }
            }
            if ($item->status_management) {
                $isStatusManagement = true;
            }

            $item->deadline_update_format = CommonHelper::formatTime($registerTrademark->deadline_update, 'Y/m/d');
            $item->register_trademark = $registerTrademark;
            $item->is_cancel = $isCancel;
            $item->is_status_management = $isStatusManagement;

            // Soft
            $item->register_deadline_update = $registerTrademark->deadline_update ?? null;
            $item->register_number = $registerTrademark->register_number ?? null;
            $item->register_updated_at = $registerTrademark->updated_at ?? null;
            return $item;
        });

        return $registerTrademarks;
    }

    /**
     * Get Trademark Apply Document To Check
     *
     * @param  mixed $id
     */
    public function getTrademarkApplyDocumentToCheck($id)
    {
        return $this->repository->getTrademarkApplyDocumentToCheck($id);
    }

    /**
     * Upload PDF
     *
     * @param array $params
     */
    public function uploadPDF(array $params)
    {
        DB::beginTransaction();
        try {
            $this->noticeDetailBtnRepository->find($params['notice_detail_btn_id'])->update([
                'date_click' => now(),
            ]);

            $docOld = $this->trademarkDocumentRepository->findByCondition([
                'notice_detail_btn_id' => $params['notice_detail_btn_id'],
                'trademark_id' => $params['trademark_id'],
            ])->get();

            foreach ($docOld as $doc) {
                FileHelper::unlink($doc->url);
                $doc->delete();
            }

            if (!empty($params['url']) && count($params['url']) > 0) {
                foreach ($params['url'] as $file) {
                    $image = FileHelper::uploads($file, [], '/uploads/trademark-documents');
                    $filepath = $image[0]['filepath'] ?? null;
                    $name = basename($filepath, ".pdf");
                    $this->trademarkDocumentRepository->create([
                        'notice_detail_btn_id' => $params['notice_detail_btn_id'],
                        'trademark_id' => $params['trademark_id'],
                        'type' => TrademarkDocument::TYPE_1,
                        'name' => $name,
                        'url' => $filepath,
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            FileHelper::unlink($filepath);
            DB::rollBack();
            return false;
        }
    }

    /**
     * Get Trademark Info
     *
     * @param  mixed $trademarkId
     * @return array
     */
    public function getTrademarkInfo($trademarkId): array
    {
        $trademark = $this->find($trademarkId)->load(
            'appTrademark.trademarkInfo',
        );
        // Get Trademark Info
        $trademarkInfo = $trademark->appTrademark->trademarkInfo->sortByDesc('id')->first();
        if ($trademarkInfo) {
            $trademarkInfo->load('mPrefecture');
        }

        // Get Address
        $prefecture = $trademarkInfo->mPrefecture;
        if ($trademark->appTrademark) {
            $trademark->appTrademark->load([
                'agentGroup' => function ($query) {
                    return $query->where('status_choice', AgentGroup::STATUS_CHOICE_TRUE);
                },
            ]);
        }

        // Get Agent Group
        $agentGroup = $trademark->appTrademark->agentGroup;
        if ($agentGroup) {
            $agentGroup->load([
                'collectAgent' => function ($query) {
                    return $query->where('type', AgentGroupMap::TYPE_NOMINATED);
                },
            ]);
            // Get Agent Group Map
            $agentGroup->collectAgent->load('agent');
            $agentGroupMap = $agentGroup->collectAgent->first();
            $agent = $agentGroupMap->agent;
        }

        // Get Maching Result
        $trademark->load('machingResults');
        $machingResult = $trademark->machingResults->last();


        $data = [
            'trademark_number' => $trademark->trademark_number ?? '',
            'application_date' => $trademark->application_date ?? '',
            'application_number' => $trademark->application_number ?? '',
            'application_number_format' => $trademark->formatApplicationNumber() ?? '',
            'prefecture_name' => $prefecture->name ?? '',
            'address_second' => $trademarkInfo->address_second ?? '',
            'address_three' => $trademarkInfo->address_three ?? '',
            'trademark_info_name' => $trademarkInfo->name ?? '',
            'identification_number' => $agent->identification_number ?? '',
            'agent_name' => $agent->name ?? '',
            'pi_dispatch_number' => $machingResult->pi_dispatch_number ?? '',
        ];

        return $data;
    }

    /**
     * Get Data Trademark Register
     *
     * @param  model $trademark
     * @param  array $params
     * @return array
     */
    public function getDataTrademarkRegister($trademark, $params): array
    {
        $registerTrademark = $this->registerTrademarkRepository->findByCondition([
            'id' => $params['register_trademark_id'],
            'trademark_id' => $trademark->id,
        ])->first();
        $mProductIds = [];
        if ($registerTrademark) {
            $registerTrademark->load('prefecture', 'registerTrademarkProds');
            $registerTrademarkProds = $registerTrademark->registerTrademarkProds->where('is_apply', RegisterTrademarkProd::IS_NOT_APPLY);
            $mProductIds = $registerTrademarkProds->pluck('m_product_id')->toArray();
            $mDistincts = $this->mProductRepository->getDataMproduct($mProductIds)->toArray();
        }
        // Get Address
        if ($trademark->appTrademark) {
            $trademark->appTrademark->load([
                'agentGroup' => function ($query) {
                    return $query->where('status_choice', AgentGroup::STATUS_CHOICE_TRUE);
                },
            ]);
        }
        // Get Agent Group
        $agentGroup = $trademark->appTrademark->agentGroup;
        if ($agentGroup) {
            $agentGroup->load([
                'collectAgent' => function ($query) {
                    return $query->where('type', AgentGroupMap::TYPE_NOMINATED);
                },
            ]);
            // Get Agent Group Map
            $agentGroup->collectAgent->load('agent');
            $agentGroupMap = $agentGroup->collectAgent->first();
            $agent = $agentGroupMap->agent;
        }
        $address = ($registerTrademark->prefecture->name ?? '') . ($registerTrademark->trademark_info_address_second ?? '') . ($registerTrademark->trademark_info_address_three ?? '');
        $data = [
            'trademark_number' => $trademark->trademark_number ?? '',
            'application_number' => $trademark->application_number ?? '',
            'address' => $address ?? '',
            'trademark_info_name' => $registerTrademark->trademark_info_name ?? '',
            'identification_number' => $agent->identification_number ?? '',
            'agent_name' => $agent->name ?? '',
            'm_distincts' => array_keys($mDistincts) ?? [],
            'register_trademark_id' => $registerTrademark->id ?? '',
            'register_trademark' => $registerTrademark,
        ];

        return $data;
    }

    /**
     * Get Trademark
     *
     * @param  Model $trademarkId
     * @param  string $type
     * @return array
     */
    public function getTrademark(Model $trademark, string $type): array
    {
        $trademark->load([
            'notices' => function ($query) {
                return $query->where('flow', Notice::FLOW_RENEWAL_BEFORE_DEADLINE);
            },
            'appTrademark.trademarkInfo',
            'machingResults',
            'payments' => function ($query) {
                return $query->where('type', Payment::BEFORE_DUE_DATE);
            }
        ]);

        $trademarkInfo = $trademark->appTrademark->trademarkInfo->sortByDesc('id')->first();
        $trademarkInfo->load('mNation', 'mPrefecture');

        if ($trademark->appTrademark) {
            $trademark->appTrademark->load([
                'agentGroup' => function ($query) {
                    return $query->where('status_choice', AgentGroup::STATUS_CHOICE_TRUE);
                },
            ]);
        }

        // Get Agent
        $agent = DB::table('agents')
            ->leftJoin('agent_group_maps', 'agents.id', 'agent_group_maps.agent_id')
            ->leftJoin('agent_groups', 'agent_group_maps.agent_group_id', 'agent_groups.id')
            ->leftJoin('app_trademarks', 'agent_groups.id', 'app_trademarks.agent_group_id')
            ->where('agent_groups.status_choice', AgentGroup::STATUS_CHOICE_TRUE)
            ->where('agent_group_maps.type', AgentGroupMap::TYPE_NOMINATED)
            ->select('agents.*')
            ->first();

        $matchingResult = $trademark->machingResults->where('pi_dispatch_number', '!=', '')->last();
        $payment = null;
        if ($trademark->payments) {
            $payments = $trademark->payments;

            if ($type == RegisterTrademarkRenewal::TYPE_EXTENSION_PERIOD_BEFORE_DEADLINE) {
                $payment = $payments->where('from_page', U210_ALERT_02)->last();
            } elseif ($type == RegisterTrademarkRenewal::TYPE_EXTENSION_OUTSIDE_PERIOD) {
                $payment = $payments->where('from_page', U210_OVER_02)->last();
            }
        }

        $registerTrademarkRenewals = $trademark->registerTrademarkRenewals->where('type', $type);

        $data = [
            'trademark_number'              => $trademark->trademark_number ?? '',
            'application_number'            => $trademark->application_number ?? '',
            'nation_name'                   => $trademarkInfo->mNation->name ?? '',
            'prefecture_name'               => $trademarkInfo->mPrefecture->name ?? '',
            'address_second'                => $trademarkInfo->address_second ?? '',
            'address_three'                 => $trademarkInfo->address_three ?? '',
            'trademark_info_name'           => $trademarkInfo->name ?? '',
            'identification_number'         => $agent->identification_number ?? '',
            'agent_name'                    => $agent->name ?? '',
            'deposit_type'                  => $agent->deposit_type ?? null,
            'deposit_account_number'        => $agent->deposit_account_number ?? '',
            'pi_dispatch_number'            => $matchingResult->pi_dispatch_number ?? '',
            'print_fee'                     => $payment->print_fee ?? '',
            'register_trademark_renewals'   => $registerTrademarkRenewals ?? collect([]),
            'matching_result_id'            => $matchingResult->id,
        ];

        return $data;
    }

    /**
     * Calculator Extension Period
     *
     * @param  array $params
     * @return array
     */
    public function calculatorExtensionPeriod($params): array
    {
        $costService = $this->calculatorTax($params['cost_service']['base_price'], $params['tax']);
        $costBankTransfer = isset($params['payment_type']) && $params['payment_type'] == BANK_TRANSFER ? $this->calculatorTax($params['cost_bank_transfer'], $params['tax']) : 0;
        $subTotal = $costService + $costBankTransfer;
        $commission = $subTotal / (1 + ($params['tax'] / 100));
        $totalAmount = $subTotal + $params['cost_service']['pof_1st_distinction_5yrs'];
        $tax = $subTotal - $commission;
        $data = [
            'cost_service_base' => $costService,
            'print_fee' => $params['cost_service']['pof_1st_distinction_5yrs'],
            'cost_bank_transfer' => $costBankTransfer,
            'commission' => round($commission),
            'tax' => round($tax),
            'tax_percent' => $params['tax'],
            'total_amount' => $totalAmount,
            'sub_total' => $subTotal,
        ];

        return $data;
    }

    /**
     * Calculator Tax
     *
     * @param  mixed $price
     * @param  mixed $tax
     * @return int
     */
    public function calculatorTax($price, $tax): int
    {
        return $price * (1 + $tax / 100);
    }

    /**
     * Save Data Extension Period
     *
     * @param  mixed $params
     * @return array
     */
    public function saveDataExtensionPeriod($params): array
    {
        try {
            DB::beginTransaction();
            $checkResponseDeadline = true;
            $trademark = $this->find($params['trademark_id']);
            $trademark->load('comparisonTrademarkResult', 'appTrademark', 'registerTrademark');
            $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
            $appTrademark = $trademark->appTrademark;
            $redirectTo = null;
            $key = Str::random(11);
            $strErrorResponseDeadline = $comparisonTrademarkResult->checkResponseDeadlinePaymentCreditCard($params['payment_type']);

            if ($strErrorResponseDeadline && $params['from_page'] == U210_ALERT_02) {
                $checkResponseDeadline = false;
            }
            if ($checkResponseDeadline) {
                $dataRegisterTrademarkRenewal = [
                    'trademark_id' => $trademark->id,
                    'type' => $params['from_page'] == U210_ALERT_02 ? RegisterTrademarkRenewal::TYPE_EXTENSION_PERIOD_BEFORE_DEADLINE : RegisterTrademarkRenewal::TYPE_EXTENSION_OUTSIDE_PERIOD,
                    'registration_period' => now(),
                    'start_date' => $params['from_page'] == U210_ALERT_02 ? $params['responde_date'] : Carbon::parse($params['responde_date'])->addMonths(1),
                    'end_date' => $params['from_page'] == U210_ALERT_02 ? Carbon::parse($params['responde_date'])->addMonths(1) : Carbon::parse($params['responde_date'])->addMonths(2),
                    'status' => RegisterTrademarkRenewal::SAVE_DRAFT,
                ];
                if (isset($params['register_trademark_renewal_ids'])) {
                    $registerTrademarkRenewals = $this->registerTrademarkRenewalRepository->findByCondition([
                        'ids' => $params['register_trademark_renewal_ids'],
                    ])->get();
                    foreach ($registerTrademarkRenewals as $registerTrademarkRenewal) {
                        $registerTrademarkRenewal->update($dataRegisterTrademarkRenewal);
                    }
                } else {
                    $this->registerTrademarkRenewalRepository->create($dataRegisterTrademarkRenewal);
                }
                $payerInfo = $this->payerInfoRepository->updateOrCreate([
                    'id' => $params['payer_info_id'],
                ], [
                    'target_id' => $appTrademark->id,
                    'payment_type' => $params['payment_type'] ?? null,
                    'payer_type' => $params['payer_type'] ?? 0,
                    'm_nation_id' => $params['m_nation_id'] ?? 0,
                    'payer_name' => $params['payer_name'] ?? '',
                    'payer_name_furigana' => $params['payer_name_furigana'] ?? '',
                    'postal_code' => $params['postal_code'] ?? null,
                    'm_prefecture_id' => $params['m_prefecture_id'] ?? null,
                    'address_second' => $params['address_second'] ?? '',
                    'address_three' => $params['address_three'] ?? '',
                    'type' => TYPE_EXTENSION_OF_PERIOD,
                ]);
                $quoteNumber = $this->generateQIR($trademark->trademark_number, 'quote');
                $taxWithHolding = floor($this->getTaxWithHolding($payerInfo, $params['sub_total']));
                $paymentAmount = $params['total_amount'] - $taxWithHolding;

                $dataPayment = [
                    'target_id' => $appTrademark->id,
                    'trademark_id' => $trademark->id,
                    'payer_info_id' => $payerInfo->id,
                    'cost_service_base' => floor($params['cost_service_base']),
                    'cost_bank_transfer' => floor($params['cost_bank_transfer']),
                    'commission' => floor($params['commission']) ?? 0,
                    'tax' => floor($params['tax']) ?? 0,
                    'payment_status' => $params['payment_status'] ?? Payment::STATUS_SAVE,
                    'subtotal' => floor($params['sub_total']) ?? 0,
                    'total_amount' => floor($params['total_amount']) ?? 0,
                    'print_fee' => floor($params['print_fee']) ?? 0,
                    'tax_withholding' => floor($taxWithHolding),
                    'payment_amount' => floor($paymentAmount),
                    'type' => TYPE_EXTENSION_OF_PERIOD,
                    'from_page' => $params['from_page'] ?? '',
                    'is_treatment' => Payment::IS_TREATMENT_WAIT,
                    'payment_status' => Payment::STATUS_SAVE
                ];

                $payment = $this->paymentRepository->updateOrCreate([
                    'id' => $params['payment_id'],
                ], $dataPayment);

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

                switch ($params['submit_type']) {
                    case SUBMIT:
                        $dataPayment['payment_id'] = $payment->id;
                        $dataPayment['payment_type'] = $params['payment_type'];

                        Session::put($key, $dataPayment);
                        $redirectTo = SUBMIT;
                        break;
                    case QUOTE:
                        $redirectTo = QUOTE;
                        break;
                }
            } else {
                $redirectTo = $strErrorResponseDeadline;
            }
            DB::commit();

            return [
                'redirect_to' => $redirectTo,
                'payment_id' => $payment->id ?? '',
                'trademark' => $trademark ?? '',
                'key_session' => $key,
            ];
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get TradeMark Register
     *
     * @return mixed
     */
    public function getTradeMarkRegister()
    {
        $userId = auth()->id();
        return $this->repository->getTradeMarkRegister($userId)->map(function ($trademark) {
            if (!$trademark->appTrademark) {
                return false;
            }
            $trademark->name = $trademark->appTrademark->name ?? '';
            $trademark->nations_name = $trademark->appTrademark->nations_name ?? '';
            $trademark->prefectures_name = $trademark->appTrademark->prefectures_name ?? '';
            $trademark->address_second = $trademark->appTrademark->address_second ?? '';
            $trademark->address_three = $trademark->appTrademark->address_three ?? '';
            $trademark->nation_id = $trademark->appTrademark->nation_id ?? '';
            $trademark->m_nation_id = $trademark->appTrademark->m_nation_id ?? '';
            $trademark->m_prefecture_id = $trademark->appTrademark->m_prefecture_id ?? '';
            $trademark->type_acc = $trademark->appTrademark->type_acc ?? '';
            $trademark->full_address = $trademark->appTrademark->nations_name
            . '-' . $trademark->appTrademark->prefectures_name
            . '-' . $trademark->appTrademark->address_second
            . '-' . $trademark->appTrademark->address_three;
            return $trademark->only([
                'name',
                'nations_name',
                'prefectures_name',
                'address_second',
                'address_three',
                'nation_id',
                'm_nation_id',
                'm_prefecture_id',
                'type_acc',
                'full_address',
            ]);
        })->reject(function ($value) {
            return $value === false;
        })->unique(function ($item) {
            return $item['name'].$item['full_address'];
        });
    }
}
