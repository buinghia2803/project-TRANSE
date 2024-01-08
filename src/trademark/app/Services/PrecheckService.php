<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\Precheck;
use App\Models\MProduct;
use App\Models\PaymentProd;
use App\Models\AgentGroup;
use App\Models\AppTrademark;
use App\Models\MPriceList;
use App\Models\PayerInfo;
use App\Models\SupportFirstTime;
use App\Models\PrecheckProduct;
use App\Models\AppTrademarkProd;
use App\Repositories\MPriceListRepository;
use App\Repositories\PayerInfoRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PrecheckProductRepository;
use App\Repositories\AppTrademarkProdRepository;
use App\Repositories\PrecheckRepository;
use App\Repositories\SettingRepository;
use App\Repositories\TrademarkRepository;
use App\Repositories\AppTrademarkRepository;
use App\Repositories\MPrefectureRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Services\AppTrademarkService;
use App\Services\TrademarkService;
use App\Services\TrademarkInfoService;
use App\Services\PayerInfoService;
use App\Services\MProductService;

class PrecheckService extends BaseService
{
    protected MProductService               $mProductService;
    protected PrecheckRepository            $precheckRepository;
    protected PrecheckProductRepository     $precheckProductRepository;
    protected PaymentRepository             $paymentRepository;
    protected PayerInfoRepository           $payerInfoRepository;
    protected SettingRepository             $settingRepository;
    protected MPriceListRepository          $mPriceListRepository;
    protected TrademarkRepository           $trademarkRepository;
    protected TrademarkService              $trademarkService;
    protected AppTrademarkService           $appTrademarkService;
    protected TrademarkInfoService          $trademarkInfoService;
    protected PayerInfoService              $payerInfoService;
    protected MPriceListService             $mPriceListService;
    protected AppTrademarkProdRepository    $appTrademarkProdRepository;
    protected MPrefectureRepository         $mPrefectureRepository;
    protected AppTrademarkRepository        $appTrademarkRepository;
    /**
     * Initializing the instances and variables
     *
     * @param MProductService $mProductService
     * @param PrecheckRepository $precheckRepository
     * @param PrecheckProductRepository $precheckProductRepository
     * @param PaymentRepository $paymentRepository
     * @param PayerInfoRepository $payerInfoRepository
     * @param SettingRepository $settingRepository
     * @param MPriceListRepository $mPriceListRepository
     * @param TrademarkService $trademarkService
     * @param AppTrademarkService $appTrademarkService
     * @param TrademarkInfoService $trademarkInfoService
     * @param AppTrademarkProdRepository $appTrademarkProdRepository
     * @param MPrefectureRepository $mPrefectureRepository
     * @param PrecheckProductRepository $precheckProductRepository
     * @param AppTrademarkRepository $appTrademarkRepository
     * @param TrademarkRepository $trademarkRepository,
     */
    public function __construct(
        MProductService $mProductService,
        PrecheckRepository $precheckRepository,
        PaymentRepository $paymentRepository,
        PayerInfoRepository $payerInfoRepository,
        SettingRepository $settingRepository,
        MPriceListRepository $mPriceListRepository,
        TrademarkRepository $trademarkRepository,
        TrademarkService $trademarkService,
        AppTrademarkService $appTrademarkService,
        TrademarkInfoService $trademarkInfoService,
        PayerInfoService $payerInfoService,
        MPriceListService $mPriceListService,
        AppTrademarkProdRepository $appTrademarkProdRepository,
        MPrefectureRepository $mPrefectureRepository,
        PrecheckProductRepository $precheckProductRepository,
        AppTrademarkRepository $appTrademarkRepository
    )
    {
        $this->mProductService = $mProductService;
        $this->repository = $precheckRepository;
        $this->precheckProductRepository = $precheckProductRepository;
        $this->paymentRepository = $paymentRepository;
        $this->payerInfoRepository = $payerInfoRepository;
        $this->settingRepository = $settingRepository;
        $this->mPriceListRepository = $mPriceListRepository;
        $this->trademarkRepository = $trademarkRepository;
        $this->trademarkService = $trademarkService;
        $this->appTrademarkService = $appTrademarkService;
        $this->trademarkInfoService = $trademarkInfoService;
        $this->payerInfoService = $payerInfoService;
        $this->mPriceListService = $mPriceListService;
        $this->appTrademarkProdRepository = $appTrademarkProdRepository;
        $this->mPrefectureRepository = $mPrefectureRepository;
        $this->precheckProductRepository = $precheckProductRepository;
        $this->appTrademarkRepository = $appTrademarkRepository;
    }

    /**
     * Get precheck of user
     *
     * @param integer $trademarkId
     * @return void
     */
    public function getPrecheckOfUser(int $trademarkId)
    {
        return $this->repository->getPrecheckOfUser($trademarkId);
    }

    /**
     * Get precheck of user by status register
     *
     * @param integer $trademarkId
     * @param integer $trademarkId
     * @return void
     */
    public function getPrecheckOfUserByStatusRegis(int $trademarkId, int $statusRegis)
    {
        return $this->repository->getPrecheckOfUserByStatusRegis($trademarkId, $statusRegis);
    }



    /**
     * Get info precheck show table
     *
     * @param integer $idPrecheck
     * @return Collection
     */
    public function getInfoPrecheckShowTable(int $idPrecheck): Collection
    {
        return $this->repository->getInfoPrecheckShowTable($idPrecheck);
    }

    /**
     * Update info precheck
     *
     * @param array $params
     * @param int $trademarkId
     *
     * @return mixed
     */
    public function savePrecheckPost(array $params, int $trademarkId)
    {
        DB::beginTransaction();
        try {
            $params['trademark_id'] = $trademarkId;
            //when form page u021c if isset name_trademark
            if (!empty($params['name_trademark'])) {
                $this->trademarkRepository->findByCondition(['id' => $trademarkId])->update(['name_trademark' => $params['name_trademark']]);
            }
            //get info bill payment
            $infoBill = $this->getInfoPaymentPrecheck([
                'type_precheck' => $params['type_precheck'],
                'm_product_choose' => $params['m_product_choose'],
                'payment_type' => $params['payment_type']
            ]);

            //add total_amount, tax_withholding, payment_amount
            $infoBill['total_amount'] = $infoBill['subtotal'];
            $infoBill['trademark_id'] = $trademarkId;

            //if payment type is credit card
            if ($params['payment_type'] == Payment::CREDIT_CARD) {
                $infoBill['cost_bank_transfer'] = 0;
            }

            //status_register of prechecks table
            $params['status_register'] = Precheck::NOT_STATUS_REGISTER;

            //save data u021 or u021n
            if (isset($params['from_page']) && $params['from_page'] == U021N) {
                //save from_page form u021n
                $payment = $this->_saveDataPrecheckU021n($params, $infoBill);
            } else {
                //save from_page form u021
                $payment = $this->_saveDataPrecheckU021($params, $infoBill);
            }

            //redirect to screen
            $respon = $this->getDataReturnPrecheck($params, $payment, $infoBill, $trademarkId);
            DB::commit();

            return $respon;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Save data precheck u021
     *
     * @param array $params
     * @param array $infoBill
     * @return void
     */
    public function _saveDataPrecheckU021(array $params, $infoBill)
    {
        //create or update type_precheck prechecks table
        $precheck = $this->repository->updateOrCreate([
            'id' => $params['id'] ?? 0,
        ], [
            'trademark_id' => $params['trademark_id'],
            'type_precheck' => $params['type_precheck'],
            'status_register' => $params['status_register'],
        ]);
        $trademark = $this->trademarkRepository->find($params['trademark_id']);
        if (!empty($params['name_trademark'])) {
            $trademark = $this->trademarkRepository->update($trademark, ['name_trademark' => $params['name_trademark']]);
        }
        $appTrademark  = $trademark->appTrademark;
        //create or update precheck_products
        if ($precheck) {
            foreach ($params['m_product_ids'] as $productId) {
                $isRegisterProduct = PrecheckProduct::IS_NOT_PRECHECK_PRODUCT;
                if (in_array($productId, $params['m_product_choose'])) {
                    $isRegisterProduct = PrecheckProduct::IS_PRECHECK_PRODUCT;
                }
                $this->precheckProductRepository->updateOrCreate([
                    'precheck_id' => $precheck->id,
                    'm_product_id' => $productId,
                ], [
                    'precheck_id' => $precheck->id,
                    'm_product_id' => $productId,
                    'is_register_product' => $isRegisterProduct,
                    'admin_id' => 3
                ]);
            }
        }
        //prefecture_id data
        if ($params['m_nation_id'] == NATION_JAPAN_ID && !empty($params['m_prefecture_id'])) {
            $prefecture_id = $params['m_prefecture_id'];
        } else {
            $mPrefecture = $this->mPrefectureRepository->findByCondition(['m_nation_id' => $params['m_nation_id']])->first();
            $prefecture_id = $mPrefecture->id;
        }
        //create payer_info:
        $payerInfo = $this->payerInfoRepository->updateOrCreate([
            'target_id' => $precheck->id,
            'type' => Payment::TYPE_PRECHECK
        ], [
            'target_id' => $precheck->id,
            'type' => Payment::TYPE_PRECHECK,
            'payment_type' => $params['payment_type'],
            'is_treatment' => $params['payment_type'] == Payment::BANK_TRANSFER ? Payment::IS_TREATMENT_DONE : null,
            'is_confirm' => $params['is_confirm'] ?? null,
            'payer_type' => $params['payer_type'],
            'payer_name' => $params['payer_name'],
            'm_nation_id' => $params['m_nation_id'],
            'payer_name_furigana' => $params['payer_name_furigana'],
            'postal_code' => $params['m_nation_id'] == NATION_JAPAN_ID ? $params['postal_code'] : null,
            'm_prefecture_id' => $prefecture_id,
            'address_second' => $params['m_nation_id'] == NATION_JAPAN_ID ? $params['address_second'] : null,
            'address_three' => $params['address_three'] ?? null,
        ]);

        //update info payments
        $tax_withholding = floor($this->getTaxWithHolding($payerInfo, $infoBill['total_amount']));

        //create or update payments
        $payment = $this->paymentRepository->updateOrCreate([
            'target_id' => $precheckNext->id ?? 0,
            'payer_info_id' => $payerInfoUpdate->id ?? 0,
            'trademark_id' => $params['trademark_id']
        ], [
            'target_id' => $precheck->id,
            'payer_info_id' => $payerInfo->id,
            'trademark_id' => $params['trademark_id'],
            'total_amount' => floor($infoBill['subtotal']),
            'tax_withholding' => floor($tax_withholding),
            'payment_amount' => floor($infoBill['subtotal'] - $tax_withholding),
            'payment_date' => Carbon::now(),
            'cost_service_base' => floor($infoBill['cost_service_base']),
            'cost_service_add_prod' => floor($infoBill['cost_service_add_prod']),
            'subtotal' => floor($infoBill['subtotal']),
            'commission' => floor($infoBill['commission']),
            'tax' => floor($infoBill['tax']),
            'cost_bank_transfer' => floor($infoBill['cost_bank_transfer']),
            'type' => Payment::TYPE_PRECHECK,
            'from_page' => $params['from_page']
        ]);

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
        $dataPaymentPro['productIds'] = $params['m_product_ids'];

        $this->paymentRepository->createPaymentProds($dataPaymentPro);

        return $payment;
    }

    /**
     * Save data precheck u021n
     *
     * @param array $params
     * @return Model
     */
    public function _saveDataPrecheckU021n(array $params, $infoBill): Model
    {
        //precheck new first
        $precheck = $this->repository->find($params['precheck_id']);
        $trademark = $this->trademarkService->find($params['trademark_id']);
        $precheckNext = $this->repository->findByCondition([
            'trademark_id' => $params['trademark_id'],
        ])->where('id', '>', $precheck->id)
        ->first();

        //prefecture_id data
        if ($params['m_nation_id'] == NATION_JAPAN_ID && !empty($params['m_prefecture_id'])) {
            $prefecture_id = $params['m_prefecture_id'];
        } else {
            $mPrefecture = $this->mPrefectureRepository->findByCondition(['m_nation_id' => $params['m_nation_id']])->first();
            $prefecture_id = $mPrefecture->id;
        }

        //update data
        if ($precheckNext) {
            $precheckNext = $this->repository->updateOrCreate([
                'id' => $precheckNext->id ?? 0,
            ], [
                'trademark_id' => $params['trademark_id'],
                'type_precheck' => $params['type_precheck'],
                'status_register' => $params['status_register'],
            ]);

            //create or update precheck_products
            foreach ($params['m_product_ids'] as $productId) {
                $isRegisterProduct = PrecheckProduct::IS_NOT_PRECHECK_PRODUCT;
                if (in_array($productId, $params['m_product_choose'])) {
                    $isRegisterProduct = PrecheckProduct::IS_PRECHECK_PRODUCT;
                }
                $this->precheckProductRepository->updateOrCreate([
                    'precheck_id' => $precheckNext->id ?? 0,
                    'm_product_id' => $productId,
                ], [
                    'precheck_id' => $precheckNext->id ?? 0,
                    'm_product_id' => $productId,
                    'is_register_product' => $isRegisterProduct,
                    'admin_id' => 3
                ]);
            }

            //create payer_info:
            $payerInfoUpdate = $this->payerInfoRepository->updateOrCreate([
                'target_id' => $precheckNext->id ?? 0,
                'type' => Payment::TYPE_PRECHECK
            ], [
                'target_id' => $precheckNext->id,
                'type' => Payment::TYPE_PRECHECK,
                'payment_type' => $params['payment_type'],
                'is_treatment' => $params['payment_type'] == Payment::BANK_TRANSFER ? Payment::IS_TREATMENT_DONE : null,
                'is_confirm' => $params['is_confirm'] ?? null,
                'payer_type' => $params['payer_type'],
                'payer_name' => $params['payer_name'],
                'm_nation_id' => $params['m_nation_id'],
                'payer_name_furigana' => $params['payer_name_furigana'],
                'postal_code' => $params['m_nation_id'] == NATION_JAPAN_ID ? $params['postal_code'] : null,
                'm_prefecture_id' => $prefecture_id,
                'address_second' => $params['m_nation_id'] == NATION_JAPAN_ID ? $params['address_second'] : null,
                'address_three' => $params['address_three'] ?? null,
            ]);

            //update info payments
            $tax_withholding = floor($this->getTaxWithHolding($payerInfoUpdate, $infoBill['total_amount']));

            //update payments
            $payment = $this->paymentRepository->updateOrCreate([
                'target_id' => $precheckNext->id ?? 0,
                'payer_info_id' => $payerInfoUpdate->id ?? 0,
                'trademark_id' => $params['trademark_id']
            ], [
                'target_id' => $precheckNext->id ?? 0,
                'payer_info_id' => $payerInfoUpdate->id ?? 0,
                'trademark_id' => $params['trademark_id'],
                'total_amount' => floor($infoBill['subtotal']),
                'tax_withholding' => $tax_withholding,
                'payment_amount' => floor($infoBill['subtotal'] - $tax_withholding),
                'payment_date' => Carbon::now(),
                'cost_service_base' => floor($infoBill['cost_service_base']),
                'cost_service_add_prod' => floor($infoBill['cost_service_add_prod']),
                'subtotal' => floor($infoBill['subtotal']),
                'commission' => floor($infoBill['commission']),
                'tax' => floor($infoBill['tax']),
                'cost_bank_transfer' => floor($infoBill['cost_bank_transfer']),
                'type' => Payment::TYPE_PRECHECK,
                'from_page' => $params['from_page'] . '_' . $precheckNext->id,
            ]);
        } else {
            //create or update type_precheck prechecks table
            $precheckStore = $this->repository->create([
                'trademark_id' => $params['trademark_id'],
                'type_precheck' => $params['type_precheck'],
                'status_register' => $params['status_register'],
            ]);
            //create or update precheck_products
            if ($precheckStore) {
                foreach ($params['m_product_ids'] as $productId) {
                    $isRegisterProduct = PrecheckProduct::IS_NOT_PRECHECK_PRODUCT;
                    if (in_array($productId, $params['m_product_choose'])) {
                        $isRegisterProduct = PrecheckProduct::IS_PRECHECK_PRODUCT;
                    }
                    $this->precheckProductRepository->create([
                        'precheck_id' => $precheckStore->id,
                        'm_product_id' => $productId,
                        'is_register_product' => $isRegisterProduct,
                        'admin_id' => 3
                    ]);
                }
            }

            //create payer_info:
            $payer_info = $this->payerInfoRepository->create([
                'target_id' => $precheckStore->id ?? 0,
                'type' => Payment::TYPE_PRECHECK,
                'payment_type' => $params['payment_type'],
                'is_treatment' => $params['payment_type'] == Payment::BANK_TRANSFER ? Payment::IS_TREATMENT_DONE : null,
                'is_confirm' => $params['is_confirm'] ?? null,
                'payer_type' => $params['payer_type'],
                'payer_name' => $params['payer_name'],
                'm_nation_id' => $params['m_nation_id'],
                'payer_name_furigana' => $params['payer_name_furigana'],
                'postal_code' => $params['m_nation_id'] == NATION_JAPAN_ID ? $params['postal_code'] : null,
                'm_prefecture_id' => $prefecture_id,
                'address_second' => $params['m_nation_id'] == NATION_JAPAN_ID ? $params['address_second'] : null,
                'address_three' => $params['address_three'] ?? null,
            ]);

            //update info payments
            $tax_withholding = floor($this->getTaxWithHolding($payer_info, $infoBill['total_amount']));

            //create payments
            $payment = $this->paymentRepository->create([
                'target_id' => $precheckStore->id ?? 0,
                'payer_info_id' => $payer_info->id ?? 0,
                'trademark_id' => $params['trademark_id'],
                'total_amount' => floor($infoBill['subtotal']),
                'tax_withholding' => floor($tax_withholding),
                'payment_amount' => floor($infoBill['subtotal'] - $tax_withholding),
                'payment_date' => Carbon::now(),
                'cost_service_base' => floor($infoBill['cost_service_base']),
                'cost_service_add_prod' => floor($infoBill['cost_service_add_prod']),
                'subtotal' => floor($infoBill['subtotal']),
                'commission' => floor($infoBill['commission']),
                'tax' => floor($infoBill['tax']),
                'cost_bank_transfer' => floor($infoBill['cost_bank_transfer']),
                'type' => Payment::TYPE_PRECHECK,
                'from_page' => $params['from_page'].'_'.$precheckStore->id,

            ]);
        }

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
        $dataPaymentPro['productIds'] = $params['m_product_ids'];

        $this->paymentRepository->createPaymentProds($dataPaymentPro);

        return $payment;
    }

    /**
     * Get Data Return Precheck
     *
     * @param array $params
     * @param $payment
     * @param array $infoBill
     * @param int $trademarkId
     *
     * @return void
     */
    protected function getDataReturnPrecheck(array $params, $payment, array $infoBill, int $trademarkId)
    {
        $respon = [];
        if (isset($params['code']) && !empty($params['code'])) {
            //redirect to anken-top
            if ($params['code'] == _ANKEN) {
                // redirect view anken_top
                $respon = [
                    'payment_id' => $payment->id,
                    'redirect_to' => route('user.application-detail.index', ['id' => $trademarkId, 'from' => FROM_U000_TOP]),
                ];
            } elseif ($params['code'] == _QUOTES) {
                // redirect view quotes
                $respon = [
                    'payment_id' => $payment->id,
                    'redirect_to' => route('user.quote', ['id' => $payment->id]),
                ];
            } elseif ($params['code'] == _PAYMENT) {
                //redirect to common payment
                $dataSession = $this->getDataSaveCommonPayment($payment, $infoBill);
                $key = $params['s'] ?? Str::random(11);
                $dataSession['from_page'] = $params['from_page'];
                $dataSession['payment_id'] = $payment->id;
                $dataSession['payment_type'] = $params['payment_type'] ?? null;
                $dataSession['productIds'] = $params['m_product_choose'] ?? [];
                $dataSession['m_product_ids'] = $params['m_product_ids'] ?? [];
                $dataSession['trademark_id'] = $trademarkId;
                $dataSession['precheck_id'] = $payment->target_id;
                if ($params['from_page'] == U021N) {
                    $dataSession['precheck_id_old'] = $params['precheck_id'];
                } else {
                    $dataSession['precheck_id_old'] = $payment->target_id;
                }

                Session::put($key, $dataSession);
                //type_payment = 4
                $respon = [
                    'payment_id' => $payment->id,
                    'redirect_to' => route('user.payment.index', ['s' => $key]),
                ];
            }
        }

        return $respon;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function getDataSaveCommonPayment($payment, array $data): array
    {
        return [
            'trademark_id' => $data['trademark_id'],
            'cost_service_base' => $data['cost_service_base'],
            'cost_service_add_prod' => $data['cost_service_add_prod'],
            'cost_bank_transfer' => $data['cost_bank_transfer'],
            'subtotal' => $data['subtotal'],
            'commission' => $data['commission'],
            'tax' => $data['tax'],
            'total_amount' => $data['total_amount'],
            'tax_withholding' => $payment->tax_withholding ?? 0,
            'payment_amount' => $payment->payment_amount ?? 0,
        ];
    }

    /**
     * GetPrecheckConfirm
     *
     * @param  mixed $input
     * @return void
     */
    public function getPrecheckConfirm($precheckIds)
    {
        $preCheck = MProduct::whereHas('prechecks', function ($q) use ($precheckIds) {
            $q->where('precheck_id', $precheckIds);
        })->with([
            'mDistinction', 'code', 'precheckProduct' => function ($q) {
                $q->join('precheck_results', 'precheck_products.id', '=', 'precheck_results.precheck_id')
                    ->select('precheck_products.*', 'precheck_results.created_at as precheck_results_created_at', 'precheck_results.result_identification_detail')
                    ->orderBy('id', 'desc');
            }
        ])->get()->groupBy('mDistinction.name');

        return $preCheck;
    }

    /**
     * Get info payment precheck
     *
     * @param array $inputs
     * @return array
     */
    public function getInfoPaymentPrecheck(array $inputs): array
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
        //tax query
        $taxData = $this->settingRepository->findByCondition(['key' => SETTING::KEY_TAX])->first();
        if ($taxData) {
            $data['tax_percentage'] = $taxData->value;
        }

        $priceCommonFeeBank = $this->mPriceListRepository->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $data['cost_bank_transfer'] = (int) $priceCommonFeeBank->base_price + (int) $priceCommonFeeBank->base_price * $data['tax_percentage'] / 100;

        if (isset($inputs['type_precheck'])) {
            $typePrecheck = (int) $inputs['type_precheck'];
            //product is choose: checked
            $idsProduct = isset($inputs['m_product_choose']) ? $inputs['m_product_choose'] : [];

            $countProduct = count($idsProduct);
            if ($countProduct > 0) {
                $serviceType = MPriceList::BEFORE_FILING;
                if ($typePrecheck == Precheck::TYPE_PRECHECK_SIMPLE_REPORT) {
                    $packageTypeServiceBase = MPriceList::PRECHECK1_SERVICE_UP_3_PRODS;
                    $packageTypeServiceAddProd = MPriceList::PRECHECK1_SERVICE_EACH_3_PRODS;
                } elseif ($typePrecheck == Precheck::TYPE_PRECHECK_DETAILED_REPORT) {
                    $packageTypeServiceBase = MPriceList::PRECHECK2_SERVICE_UP_3_PRODS;
                    $packageTypeServiceAddProd = MPriceList::PRECHECK2_SERVICE_EACH_3_PRODS;
                }

                //cost_service_base && commission && tax
                $priceCommon = $this->mPriceListRepository->getPriceCommonOfPrecheck($serviceType, $packageTypeServiceBase);
                if ($priceCommon) {
                    $priceCommonBasePrice = (int) $priceCommon->base_price;
                    $data['cost_service_base'] = $priceCommonBasePrice + $priceCommonBasePrice * $data['tax_percentage'] / 100;
                    $data['commission'] += $priceCommonBasePrice;
                    $data['tax'] = $priceCommonBasePrice * $data['tax_percentage'] / 100;
                }

                //cost_service_add_prod && tax && commission if choose large more 3 product
                $getCostServiceAddProd = $this->_getCostServiceAddProd($idsProduct, $packageTypeServiceAddProd, $data['tax_percentage']);
                $data['cost_service_add_prod'] = $getCostServiceAddProd['costServiceAddProd'];
                $data['tax'] += $getCostServiceAddProd['taxServiceAddProd'];
                $data['commission'] += $getCostServiceAddProd['costServiceAddProdNotTax'];

                //subtotal
                $data['subtotal'] = $data['cost_service_base'] + $data['cost_service_add_prod'];
                //if method payment is bank transfer
                if (isset($inputs['payment_type']) && $inputs['payment_type'] == PayerInfo::PAYMENT_BANK_TRANSFER) {
                    $data['subtotal'] += $data['cost_bank_transfer'];
                    $data['tax'] += (int) $priceCommonFeeBank->base_price * $data['tax_percentage'] / 100;
                    $data['commission'] += (int) $priceCommonFeeBank->base_price;
                }
            }
        }

        //floor value
        $data['cost_service_base'] = floor($data['cost_service_base']);
        $data['cost_service_add_prod'] = floor($data['cost_service_add_prod']);
        $data['cost_bank_transfer'] = floor($data['cost_bank_transfer']);
        $data['cost_service_base_not_tax'] = floor($data['cost_service_base_not_tax']);
        $data['subtotal'] = floor($data['subtotal']);
        $data['commission'] = floor($data['commission']);
        $data['tax'] = floor($data['tax']);

        return $data;
    }

    /**
     * GetPriceCommonOfPrecheck
     *
     * @param int $service_type
     * @param string $package_type
     * @return Collection
     */
    public function getPriceCommonOfPrecheck(int $service_type, string $package_type)
    {
        return $this->mPriceListRepository->getPriceCommonOfPrecheck($service_type, $package_type);
    }

    /**
     * Get cost service add product has tax & not tax
     *
     * @param array $idsProduct
     * @param string $packType
     * @param string $taxPercent
     * @return array
     */
    protected function _getCostServiceAddProd(array $idsProduct, string $packType, string $taxPercent): array
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
     * Get product of distinction
     *
     * @param integer $id
     * @return Collection
     */
    public function getProductOfDistinction(int $id)
    {
        return $this->repository->getProductOfDistinction($id);
    }

    /**
     * Get precheck
     *
     * @param integer $id
     * @return Collection
     */
    public function getPrecheck(int $id)
    {
        return $this->repository->getPrecheck($id);
    }

    /**
     * Get precheck v2.
     *
     * @param integer $id
     * @param integer $precheckId
     * @return Collection
     */
    public function getPrecheckWithId(int $id, int $precheckId)
    {
        return $this->repository->getPrecheckWithId($id, $precheckId);
    }

    /**
     * Get precheck
     *
     * @return array
     */
    public function getPricePackService()
    {
        return $this->repository->getPricePackService();
    }

    /**
     * Get Price One Pack
     *
     * @param int $serviceTypePackA
     * @param string $packageTypePackA
     * @return array
     */
    public function getPriceOnePackService(int $serviceTypePackA, string $packageTypePackA)
    {
        return $this->repository->getPriceOnePackService($serviceTypePackA, $packageTypePackA);
    }

    /**
     * Get Mail Register CertService
     *
     * @return array
     */
    public function getMailRegisterCertService()
    {
        return $this->repository->getMailRegisterCertRepository();
    }

    /**
     * Get Period Registration Service
     *
     * @return array
     */
    public function getPeriodRegistrationService()
    {
        return $this->repository->getPeriodRegistrationPrecheck();
    }

    /**
     * Create Payment precheck - u021b
     *
     * @param  mixed $request
     * @return void
     */
    public function createPaymentPrecheck($request)
    {
        try {
            DB::beginTransaction();

            if ($request['redirect_to'] == SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT ||
                $request['redirect_to'] == SupportFirstTime::REDIRECT_TO_QUOTE ||
                $request['redirect_to'] == SupportFirstTime::REDIRECT_TO_ANKEN_TOP
            ) {
                $trademark = $this->trademarkService->findOrFail($request['id']);
                if (isset($request['from_page'])) {
                    if ($request['from_page'] == U021B) {
                        $precheck = Precheck::where('id', $request['precheck_id'])->where('trademark_id', $trademark->id)->orderBy('id', 'DESC')->first();
                    } else {
                        $precheck = Precheck::where('trademark_id', $trademark->id)->orderBy('id', 'DESC')->first();
                    }
                }
                $agentGroup = AgentGroup::where('status_choice', AgentGroup::STATUS_CHOICE_TRUE)->first();

                // Create App Trademark
                $conditionAppTrademark = [
                    'trademark_id' => $trademark->id,
                    'admin_id' => 1,
                    'agent_group_id' => $agentGroup->id,
                    // 'status' => $request['redirect_to'] == AppTrademark::REDIRECT_TO_COMMON_PAYMENT
                    //     ? AppTrademark::STATUS_UNREGISTERED_SAVE : AppTrademark::STATUS_UNREGISTERED_SAVE,
                    'pack' => $request['pack'],
                    'is_mailing_regis_cert' => isset($request['is_mailing_register_cert'])
                        ? $request['is_mailing_register_cert']
                        : AppTrademark::IS_MAILING_REGIS_CERT_FAlSE,
                    'period_registration' => isset($request['period_registration'])
                        ? $request['period_registration']
                        : AppTrademark::PERIOD_REGISTRATION_FALSE,
                ];

                if (isset($request['from_page'])) {
                    if ($request['from_page'] == U021B) {
                        $conditionAppTrademark['type_page'] = AppTrademark::PAGE_TYPE_U021B;
                    } elseif ($request['from_page'] == U021B_31) {
                        $conditionAppTrademark['type_page'] = AppTrademark::PAGE_TYPE_U021B_31;
                    }
                }

                $appTrademark = $this->appTrademarkService->updateOrCreate(['trademark_id' => $trademark->id], $conditionAppTrademark);

                // Update or create Trademark Info
                if (isset($request['data']) && $request['data']) {
                    if (isset($request['from_page'])) {
                        if ($request['from_page'] == U021B) {
                            $fromPage = 'u021n_' . $precheck->id;
                            $this->trademarkInfoService->updateOrCreateTrademarkInfo($request['data'], $trademark->id, $request['from_page'], $fromPage);
                        } else {
                            $this->trademarkInfoService->updateOrCreateTrademarkInfo($request['data'], $trademark->id);
                        }
                    }
                }

                // Create payer information
                $payerInfo = $this->payerInfoService->updateOrCreate([
                    'target_id' => $appTrademark->id,
                    'type' => TYPE_APP_TRADEMARK
                ], [
                    'target_id' => $appTrademark->id,
                    'payment_type' => $request['payment_type'] ?? null,
                    'payer_type' => $request['payer_type'] ?? 0,
                    'm_nation_id' => $request['m_nation_id'] ?? null,
                    'payer_name' => $request['payer_name'] ?? '',
                    'payer_name_furigana' => $request['payer_name_furigana'] ?? '',
                    'postal_code' => $request['postal_code'] ?? null,
                    'm_prefecture_id' => $request['m_prefecture_id'] ?? null,
                    'address_second' => $request['address_second'] ?? '',
                    'address_three' => $request['address_three'] ?? '',
                    'type' => TYPE_APP_TRADEMARK,
                ]);

                $application = $this->getPriceOnePackService(MPriceList::APPLICATION, MPriceList::PACK_A_UP_3_ITEMS);
                // Create payment with payment status is
                $dataSFT = [
                    'target_id' => $appTrademark->id,
                    'payer_info_id' => $payerInfo->id,
                    'cost_bank_transfer' => null,
                    'subtotal' => (int) floor($request['subtotal']) ?? 0,
                    'commission' => (int) floor($request['commission']) ?? 0,
                    'tax' => (int) floor($request['tax']) ?? 0,
                    'cost_service_base' => (int) floor($request['cost_service_base']) ?? 0,
                    'total_amount' => (int) floor($request['total_amount']) ?? 0,
                    'payment_status' => $request['payment_status'] ?? Payment::STATUS_SAVE,
                    'tax_withholding' => 0,
                    'payment_amount' => 0,
                    'type' => TYPE_APP_TRADEMARK,
                    'cost_print_application_one_distintion' => $application['pof_1st_distinction_5yrs'] ?? 0,
                    'cost_print_application_add_distintion' => $application['pof_2nd_distinction_5yrs'] ?? 0,
                    'reduce_number_distitions' => count($request['is_choice_user']) - 3 > 0 ? count($request['is_choice_user']) - 3 : 0,
                    'payment_date' => now(),
                    'trademark_id' => $trademark->id,
                    'cost_service_add_prod' => floor($request['cost_service_add_prod']) ?? 0,
                    'from_page' => $request['from_page']
                ];

                // if (isset($request['from_page'])) {
                    // if ($request['from_page'] == U021B) {
                    //     $dataSFT['from_page'] = 'u021n_' . $precheck->id;
                    // }
                // }

                $paymentFee = $this->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
                if ($request['payment_type'] == Payment::BANK_TRANSFER) {
                    $dataSFT['cost_bank_transfer'] = $paymentFee['cost_service_base'] ?? 0;
                }
                $periodRegistration = $this->getPeriodRegistrationRepository(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
                if (isset($request['pack']) && $request['pack'] != AppTrademark::PACK_A) {
                    if (isset($request['period_registration']) && $request['period_registration'] == AppTrademark::PERIOD_REGISTRATION_TRUE) {
                        $dataSFT['cost_change_registration_period'] = $periodRegistration['base_price_multiplication_tax'];
                        $dataSFT['cost_10_year_one_distintion'] = $periodRegistration['pof_1st_distinction_10yrs'] ?? 0;
                    } else {
                        $dataSFT['cost_5_year_one_distintion'] = $periodRegistration['pof_1st_distinction_5yrs'] ?? 0;
                    }
                } else {
                    $dataSFT['cost_change_registration_period'] = 0;
                    $dataSFT['cost_10_year_one_distintion'] = 0;
                    $dataSFT['cost_5_year_one_distintion'] = 0;
                }

                $mailRegisterCert = $this->getPriceOnePackService(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
                if (isset($request['is_mailing_register_cert']) && $request['is_mailing_register_cert']) {
                    $dataSFT['cost_registration_certificate'] = $mailRegisterCert['base_price_multiplication_tax'];
                }

                $tax_withholding = floor($this->getTaxWithHolding($payerInfo, $request['total_amount']));

                $dataSFT['tax_withholding'] = floor($tax_withholding);
                $dataSFT['payment_amount'] = floor($request['total_amount'] - $dataSFT['tax_withholding']);

                $payment = $this->paymentRepository->createPayment($dataSFT);

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

                $dataPaymentPro['payment_id'] = $payment->id;
                $dataPaymentPro['productIds'] = $request['is_choice_user'];

                $this->paymentRepository->createPaymentProds($dataPaymentPro);

                // Update or Create app trademark prod
                $productID = $request['productIds'];

                foreach ($productID as $idProd) {
                    $isApplyAppTrademarkProd = TYPE_APP_TRADEMARK_PRODUCT_UNCHECKED;
                    $isApplyPrecheckProduct = PrecheckProduct::IS_APPLY_UN_CHECK;
                    if (in_array($idProd, $request['is_choice_user'])) {
                        $isApplyAppTrademarkProd = TYPE_APP_TRADEMARK_PRODUCT_CHECKED;
                        $isApplyPrecheckProduct = PrecheckProduct::IS_APPLY_CHECKED;
                    }
                    AppTrademarkProd::updateOrCreate([
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $idProd
                    ], [
                        'app_trademark_id' => $appTrademark->id,
                        'm_product_id' => $idProd,
                        'is_apply' => $isApplyAppTrademarkProd,
                    ]);

                    PrecheckProduct::updateOrCreate([
                        'precheck_id' => $precheck->id,
                        'm_product_id' => $idProd,
                    ], [
                        'is_apply' => $isApplyPrecheckProduct,
                        'admin_id' => Admin::getAdminIdByRole(Admin::ROLE_ADMIN_SEKI),
                    ]);
                }
            }
            DB::commit();
            $key = Str::random(11);
            switch ($request['redirect_to']) {
                case Precheck::REDIRECT_TO_QUOTE:
                    return [
                        'redirect_to' => Precheck::REDIRECT_TO_QUOTE,
                        'payment_id' => $payment->id,
                    ];
                case Precheck::REDIRECT_TO_COMMON_PAYMENT:
                    if (count($request['is_choice_user']) == 0) {
                        return [
                            'redirect_to' => 'false',
                            'key_session' => '',
                        ];
                    }
                    $params = $request;
                    $params['trademark_id'] = $request['id'];
                    $params['payment_id'] = $payment->id;
                    $params['productIds'] = $request['is_choice_user'];
                    $params['app_trademark_id'] = $appTrademark->id;

                    Session::put($key, $params);

                    return [
                        'redirect_to' => SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT,
                        'key_session' => $key,
                    ];
                case Precheck::REDIRECT_TO_ANKEN_TOP:
                    $countProduct = count($request['is_choice_user']);
                    if ($countProduct == 0) {
                        return [
                        'redirect_to' => 'false',
                        'key_session' => '',
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

            throw new \Exception($e);
        }
    }

    /**
     * Create Payment precheck
     *
     * @param  mixed $request
     * @return void
     */
    public function applyTrademarkWithNumberCreate($request)
    {
        try {
            DB::beginTransaction();
            if ($request['redirect_to'] == SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT ||
                $request['redirect_to'] == SupportFirstTime::REDIRECT_TO_QUOTE ||
                $request['redirect_to'] == SupportFirstTime::REDIRECT_TO_ANKEN_TOP
            ) {
                $agentGroup = AgentGroup::where('status_choice', AgentGroup::STATUS_CHOICE_TRUE)->first();
                $trademark = $this->trademarkService->findOrFail($request['trademark_id']);
                $jimu = Admin::where('role', ROLE_OFFICE_MANAGER)->first();
                // Create App Trademark
                $conditionAppTrademark = [
                    'trademark_id' => $trademark->id,
                    'admin_id' => $jimu->id,
                    'agent_group_id' => $agentGroup->id,
                    'status' => AppTrademark::STATUS_UNREGISTERED_SAVE,
                    'pack' => AppTrademark::PACK_A,
                    'is_mailing_regis_cert' => AppTrademark::IS_MAILING_REGIS_CERT_TRUE,
                    'period_registration' => AppTrademark::PERIOD_REGISTRATION_FALSE,
                    'cancellation_deadline' => Carbon::now(),
                    'type_page' => AppTrademark::PAGE_TYPE_U031EDIT_WITH_NUMBER
                ];

                $appTrademark = $this->appTrademarkService->updateOrCreate(['trademark_id' => $trademark->id], $conditionAppTrademark);

                // Insert product
                $prodNotApply = array_diff($request['mProducts'] ?? [], $request['m_product_ids'] ?? []);

                // Delete product 3 of app trademark prod now
                $productDeleteAppTrademarkProd = AppTrademarkProd::where('app_trademark_id', $appTrademark->id)->get();
                foreach ($productDeleteAppTrademarkProd as $key => $value) {
                    // $mProductCheck = MProduct::where('id', $value['m_product_id'])->first();
                    // if ($mProductCheck->type == MProduct::TYPE_CREATIVE_CLEAN) {
                    //     $mProductCheck->delete();
                    // }
                    // Delete app trademark prod
                    $value->forceDelete();
                }

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

                $arrAddProduct = [];
                if (isset($request['prod'])) {
                    foreach ($request['prod'] as $key => $value) {
                        $mProductCheck = MProduct::getMProductByName($value['name_product']);
                        if (!$mProductCheck) {
                            $mProduct = $this->mProductService->create([
                                'products_number' => Str::random(6),
                                'name' => $value['name_product'],
                                'm_distinction_id' => $value['m_distinction_id'],
                                'admin_id' => 1,
                                'type' => MProduct::TYPE_CREATIVE_CLEAN,
                            ]);
                            $this->appTrademarkProdRepository->updateOrCreate([
                                'app_trademark_id' => $appTrademark->id,
                                'm_product_id' => $mProduct->id
                            ], [
                                'app_trademark_id' => $appTrademark->id,
                                'm_product_id' => $mProduct->id,
                                'is_apply' => isset($value['check']) ? TYPE_APP_TRADEMARK_PRODUCT_CHECKED : TYPE_APP_TRADEMARK_PRODUCT_UNCHECKED,
                            ]);
                            if (isset($value['check'])) {
                                $arrAddProduct[] = $mProduct->id;
                            }
                        } else {
                            $this->appTrademarkProdRepository->updateOrCreate([
                                'app_trademark_id' => $appTrademark->id,
                                'm_product_id' => $mProductCheck->id
                            ], [
                                'app_trademark_id' => $appTrademark->id,
                                'm_product_id' => $mProductCheck->id,
                                'is_apply' => isset($value['check']) ? TYPE_APP_TRADEMARK_PRODUCT_CHECKED : TYPE_APP_TRADEMARK_PRODUCT_UNCHECKED,
                            ]);
                            if (isset($value['check'])) {
                                $arrAddProduct[] = $mProductCheck->id;
                            }
                        }
                    }
                }

                // Create Trademark Info
                $this->trademarkInfoService->updateOrCreateTrademarkInfo($request['data'], $trademark->id);
                // Create payer information
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
                $application = $this->getPriceOnePackService(MPriceList::APPLICATION, MPriceList::PACK_A_UP_3_ITEMS);
                // Create payment with payment status is
                $createPayment = [
                    "target_id" => $appTrademark->id,
                    "trademark_id" => $trademark->id,
                    "payer_info_id" => $payerInfo->id,
                    "cost_bank_transfer" => null,
                    "subtotal" => floor($request['subtotal']) ?? 0,
                    "commission" => floor($request['commission']) ?? 0,
                    "tax" => floor($request['tax']) ?? 0,
                    "cost_service_base" => floor($request['cost_service_base']) ?? 0,
                    "cost_service_add_prod" => floor($request['cost_service_add_prod']) ?? 0,
                    "total_amount" => floor($request['total_amount']) ?? 0,
                    "payment_status" => $request['payment_status'] ?? Payment::STATUS_SAVE,
                    "tax_withholding" => 0,
                    "payment_amount" => 0,
                    "type" => TYPE_APP_TRADEMARK,
                    "cost_print_application_one_distintion" => $application['pof_1st_distinction_5yrs'] ?? 0,
                    "cost_print_application_add_distintion" => $application['pof_2nd_distinction_5yrs'] ?? 0,
                    "reduce_number_distitions" => $reduceNumberDistitions,
                    "payment_date" => now(),
                    "from_page" => U031_EDIT_WITH_NUMBER
                ];

                $paymentFee = $this->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
                if ($request['payment_type'] == Payment::BANK_TRANSFER) {
                    $createPayment['cost_bank_transfer'] = $paymentFee['cost_service_base'] ?? 0;
                }

                $tax_withholding = floor($this->getTaxWithHolding($payerInfo, $request['total_amount']));
                $createPayment['tax_withholding'] = $tax_withholding;
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

                // Delete app trademark prod
                $productDeletePaymentProd = PaymentProd::where('payment_id', $payment->id)->get();
                foreach ($productDeletePaymentProd as $key => $value) {
                    $value->delete();
                }
                $arrProdSystemAndAdd = array_merge(array_values($request['m_product_ids'] ?? []), array_values($arrAddProduct));

                $dataPaymentPro['payment_id'] = $payment->id;
                $dataPaymentPro['productIds'] = $arrProdSystemAndAdd;

                $this->paymentRepository->createPaymentProds($dataPaymentPro);
                Session::forget(SESSION_APPLY_TRADEMARK_WITH_NUMBER);
            }

            DB::commit();

            $key = Str::random(11);
            switch ($request['redirect_to']) {
                case Precheck::REDIRECT_TO_QUOTE:
                    return [
                        'redirect_to' => Precheck::REDIRECT_TO_QUOTE,
                        'payment_id' => $payment->id,
                    ];
                case Precheck::REDIRECT_TO_COMMON_PAYMENT:
                    if (count($arrProdSystemAndAdd) == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }
                    $params = $request;
                    $params['payment_id'] = $payment->id;
                    $params['from_page'] = U031_EDIT_WITH_NUMBER;
                    $params['productIds'] = $arrProdSystemAndAdd;
                    $params['payment_type'] = $payerInfo->payment_type;
                    $params['trademark_id'] = $trademark->id;
                    $params['app_trademark_id'] = $appTrademark->id;
                    Session::put($key, $params);

                    return [
                        'redirect_to' => Precheck::REDIRECT_TO_COMMON_PAYMENT,
                        'key_session' => $key,
                    ];

                case Precheck::REDIRECT_TO_ANKEN_TOP:
                    $countProduct = count($arrProdSystemAndAdd);
                    if ($countProduct == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
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
     * Create Payment precheck
     *
     * @param  mixed $dataRequest
     * @return mixed
     */
    public function applyTrademarkRegisterCreate($dataRequest)
    {
        try {
            $request = $dataRequest->all();
            DB::beginTransaction();
            if ($request['redirect_to'] == SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT ||
                $request['redirect_to'] == SupportFirstTime::REDIRECT_TO_QUOTE ||
                $request['redirect_to'] == SupportFirstTime::REDIRECT_TO_ANKEN_TOP
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
                $countAppTrademark = $this->appTrademarkRepository->checkUserMax50Record();
                if ($countAppTrademark >= PAGE_LIMIT_50) {
                    if (isset($request['isAjax'])) {
                        return [
                            'trademark_id' => $trademark->id,
                            'redirect_to' => null,
                            'isAjax' => true
                        ];
                    }
                    return [
                        'redirect_to' => null,
                        'isAjax' => false
                    ];
                }

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
                    'type_page' => AppTrademark::PAGE_TYPE_U031
                ];
                $appTrademark = $this->appTrademarkService->updateOrCreate(['trademark_id' => $trademark->id], $conditionAppTrademark);

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

                $application = $this->getPriceOnePackService(MPriceList::APPLICATION, MPriceList::PACK_A_UP_3_ITEMS);
                $arrProdSystemAndAdd = [];
                // Create payment with payment status is
                if ($payerInfo != null) {
                    $createPayment = [
                        "target_id" => $appTrademark->id,
                        "payer_info_id" => $payerInfo->id,
                        "trademark_id" => $trademark->id,
                        "cost_bank_transfer" => null,
                        "subtotal" => (int) $request['subtotal'] ?? 0,
                        "commission" => $request['commission'] ?? 0,
                        "tax" => floor($request['tax']) ?? 0,
                        "cost_service_base" => floor($request['cost_service_base']) ?? 0,
                        "cost_service_add_prod" => floor($request['cost_service_add_prod']) ?? 0,
                        "total_amount" => (int) floor($request['total_amount']) ?? 0,
                        "tax_withholding" => 0,
                        "payment_amount" => 0,
                        "type" => TYPE_APP_TRADEMARK,
                        "cost_print_application_one_distintion" => floor($application['pof_1st_distinction_5yrs']) ?? 0,
                        "cost_print_application_add_distintion" => floor($application['pof_2nd_distinction_5yrs']) ?? 0,
                        "reduce_number_distitions" => $reduceNumberDistitions,
                        "payment_date" => now(),
                        "is_treatment" => Payment::IS_TREATMENT_WAIT,
                        "payment_status" => Payment::STATUS_SAVE,
                        "from_page" => U031
                    ];

                    $paymentFee = $this->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
                    if ($request['payment_type'] == Payment::BANK_TRANSFER) {
                        $createPayment['cost_bank_transfer'] = floor($paymentFee['cost_service_base']) ?? 0;
                    }

                    $tax_withholding = floor($this->getTaxWithHolding($payerInfo, $request['total_amount']));
                    $createPayment['tax_withholding'] = $tax_withholding;
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
            }

            DB::commit();

            $key = Str::random(11);
            switch ($request['redirect_to']) {
                case Precheck::REDIRECT_TO_QUOTE:
                    return [
                        'redirect_to' => Precheck::REDIRECT_TO_QUOTE,
                        'payment_id' => $payment->id,
                    ];
                case Precheck::REDIRECT_TO_COMMON_PAYMENT:
                    if (count($arrProdSystemAndAdd) == 0) {
                        return [
                            'redirect_to' => '',
                            'key_session' => '',
                        ];
                    }

                    $params = $request;
                    $params['from_page'] = U031;
                    $params['payment_id'] = $payment->id;
                    $params['productIds'] = $arrProdSystemAndAdd;
                    $params['payment_type'] = $payerInfo->payment_type;
                    $params['trademark_id'] = $trademark->id;
                    $params['app_trademark_id'] = $appTrademark->id;
                    unset($params['image_trademark']);
                    Session::put($key, $params);

                    return [
                        'redirect_to' => Precheck::REDIRECT_TO_COMMON_PAYMENT,
                        'key_session' => $key,
                    ];

                case Precheck::REDIRECT_TO_ANKEN_TOP:
                    if ($request['isAjax']) {
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
     * Redirect to page u021c with data session
     *
     * @param $inputs
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function redirectU021c($inputs)
    {
        if (isset($inputs['m_product_is']) && count($inputs['m_product_is'])) {
            $key = Str::random(11);
            Session::put($key, [
                'from_page' => U021B,
                'data' => [
                    'm_product_ids' => $inputs['m_product_is'],
                    'trademark_id' => $inputs['trademark_id'],
                    'support_first_time_id' => $inputs['support_first_time_id'] ?? null,
                    'precheck_id' => $inputs['precheck_id'] ?? null,
                ]
            ]);
            if (Session::has($key)) {
                return response()->json([
                    'status' => true,
                    'router_redirect' => route('user.precheck.register-different-brand', ['id' => $inputs['trademark_id'], 's' => $key]),
                    'message' => 'Success redirect page'
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Error redirect',
            ]);
        }
    }

    /**
     * Get Product Precheck
     *
     * @param  mixed $trademarkId
     * @return Collection
     */
    public function getProductPrecheck($trademarkId): Collection
    {
        return $this->repository->getProductPrecheck($trademarkId);
    }

    /**
     * Get Data Fee Default Precheck
     *
     * @return array
     */
    public function getDataFeeDefaultPrecheck(): Array
    {
        $setting = $this->settingRepository->findByCondition(['key' => Setting::KEY_TAX])->first();
        $tax = $setting ? $setting->value : 0;

        return [
            'precheck_simple' => [
                'cost_service_base' => floor(($tax / 100 + 1) * $this->getPriceByServiceTypeAndPackageType(MPriceList::BEFORE_FILING, MPriceList::PRECHECK1_SERVICE_UP_3_PRODS)),
                'cost_service_add_prod' => floor(($tax / 100 + 1) * $this->getPriceByServiceTypeAndPackageType(MPriceList::BEFORE_FILING, MPriceList::PRECHECK1_SERVICE_EACH_3_PRODS)),
            ],
            'precheck_detail' => [
                'cost_service_base' => floor(($tax / 100 + 1) * $this->getPriceByServiceTypeAndPackageType(MPriceList::BEFORE_FILING, MPriceList::PRECHECK2_SERVICE_UP_3_PRODS)),
                'cost_service_add_prod' => floor(($tax / 100 + 1) * $this->getPriceByServiceTypeAndPackageType(MPriceList::BEFORE_FILING, MPriceList::PRECHECK2_SERVICE_EACH_3_PRODS)),
            ],
        ];
    }

    /**
     * GetPriceByServiceTypeAndPackageType
     *
     * @param $serviceType
     * @param $packageType
     * @return int
     */
    public function getPriceByServiceTypeAndPackageType($serviceType, $packageType)
    {
        $mPriceList = $this->mPriceListRepository->findByCondition([
            'service_type' => $serviceType,
            'package_type' => $packageType,
        ])->first();
        if ($mPriceList) {
            return $mPriceList->base_price;
        }
        return 0;
    }
}
