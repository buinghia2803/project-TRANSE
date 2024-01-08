<?php

namespace App\Repositories;

use App\Models\AppTrademark;
use App\Models\MPriceList;
use App\Models\PayerInfo;
use App\Models\Payment;
use App\Models\PaymentProd;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PaymentRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param Payment $payment
     * @return  void
     */
    public function __construct(
        Payment $payment,
        SupportFirstTimeRepository $supportFirstTimeRepository,
        MPriceListRepository $mPriceListRepository,
        SettingRepository $settingRepository
    )
    {
        $this->model = $payment;
        $this->supportFirstTimeRepository = $supportFirstTimeRepository;
        $this->settingRepository = $settingRepository;
        $this->mPriceListRepository = $mPriceListRepository;
    }

    /**
     * @param Builder $query
     * @param string $column
     * @param mixed $data
     *
     * @return  Builder
     */
    public function mergeQuery(Builder $query, string $column, $data): Builder
    {
        switch ($column) {
            case 'like_receipt_number':
                $query->where('receipt_number', 'like', '%' . $data . '%');
                break;
            case 'like_invoice_number':
                $query->where('invoice_number', 'like', '%' . $data . '%');
                break;
            case 'like_quote_number':
                $query->where('quote_number', 'like', '%' . $data . '%');
                break;
            case 'target_id':
            case 'id':
            case 'payment_status':
            case 'from_page':
            case 'trademark_id':
            case 'type':
                $query->where($column, $data);
                break;
            case 'from_page_n':
                $query->where('from_page', 'like', '%' . $data . '%');
                break;
            case 'from_page_array':
                $query->whereIn('from_page', $data);
                break;
            case 'payment_status_not_zero':
                $query->where('payment_status', '<>', $data);
                break;
            default:
                return $query;
        }

        return $query;
    }

    /**
     * Calculate subtotal from conditions.
     *
     * @param array $params
     */
    public function calculateSubtotal(array $params): int
    {
        $costBankTransfer = 0;
        if (isset($params['payment_type']) && $params['payment_type'] == Payment::BANK_TRANSFER) {
            $costBankTransfer = $params['cost_bank_transfer'] ?? 0;
        }

        $costServiceAddProd = 0;
        if (isset($params['pack']) && $params['pack']) {
            $pricePackage = $this->supportFirstTimeRepository->getPricePackService();
            $basePack = $pricePackage[1][$params['pack'] - 1]['base_price'];
            $countProd = floor(count($params['productIds']) / 3);
            $costServiceAddProd = $basePack * $countProd;
        }

        $subTotal = ($params['cost_service_base'] ?? 0)
            + $costServiceAddProd
            + $costBankTransfer
            + ($params['cost_registration_certificate'] ?? 0)
            + ($params['extension_of_period_before_expiry'] ?? 0)
            + ($params['application_discount'] ?? 0);

        return $subTotal;
    }

    /**
     * Calculate total amount from conditions.
     *
     * @param array $params
     */
    public function calculateTotalAmount(array $params): int
    {
        $subTotal = $this->calculateSubtotal($params);

        $totalProduct = 0;
        if (isset($params['productIds'])) {
            $totalProduct = count($params['productIds']);
        }
        $total = $subTotal
            + ($params['print_fee'] ?? 0)
            + ($params['cost_print_application_one_distintion'] ?? 0)
            + ($params['cost_print_application_add_distintion'] ?? 0) * (($totalProduct ?? 0) - 1)
            + ($params['costs_correspondence_of_one_prod'] ?? 0)
            + ($params['reduce_number_distitions'] ?? 0)
            + ($params['cost_5_year_one_distintion'] ?? 0)
            + ($params['cost_10_year_one_distintion'] ?? 0)
            + ($params['cost_change_address'] ?? 0)
            + ($params['cost_change_name'] ?? 0)
            + ($params['cost_print_name'] ?? 0)
            + ($params['cost_print_address'] ?? 0);

        return $total;
    }

    /**
     * Calculate payment amount from conditions
     *
     * @param array $params
     */
    public function calculatePaymentAmount(array $params): int
    {
        $totalAmount = $this->calculateTotalAmount($params);

        return $totalAmount - ($params['tax_withholding'] ?? 0);
    }

    /**
     * Create payment products.
     *
     * @param array $data
     * @return void
     */
    public function createPaymentProds(array $data): void
    {
        if (isset($data['productIds']) && count($data['productIds'])) {
            PaymentProd::where('payment_id', $data['payment_id'])->delete();

            foreach ($data['productIds'] as $mProdId) {
                PaymentProd::updateOrCreate([
                    "payment_id" => $data['payment_id'],
                    "m_product_id" => $mProdId,
                ], [
                    "payment_id" => $data['payment_id'],
                    "m_product_id" => $mProdId,
                    "created_at" => now(),
                ]);
            }
        }
    }

    /**
     * Create payment for support the first time.
     *
     * @param array $condition .
     */
    public function createPaymentWithSFT(array $conditions): Model
    {
        // TODO: waiting calculate
        // $subTotal = $this->calculateSubtotal($conditions);
        // $totalAmount = $this->calculateTotalAmount($conditions);

        $paymentAmount = ($conditions['total_amount'] ?? 0) - ($conditions['tax_withholding'] ?? 0);
        $payment = $this->updateOrCreate(['target_id' => $conditions['target_id'], 'from_page' => $conditions['from_page']], [
            'type' => $conditions['type'] ?? TYPE_APP_TRADEMARK,
            'total_amount' => $conditions['total_amount'] ?? 0,
            'payment_status' => Payment::STATUS_SAVE,
            'trademark_id' => $conditions['trademark_id'] ?? 0,
            'tax_withholding' => $conditions['tax_withholding'] ?? 0,
            'payment_amount' => $paymentAmount,
            'cost_service_add_prod' => $conditions['cost_service_add_prod'] ?? 0,
            'payment_date' => now()->format('Y-m-d H:i:s'),
            'target_id' => $conditions['target_id'],
            'payer_info_id' => $conditions['payer_info_id'],
            'cost_bank_transfer' => $conditions['cost_bank_transfer'] ?? null,
            'reduce_number_distitions' => isset($conditions['reduce_number_distitions']) ? (int) $conditions['reduce_number_distitions'] : 0,
            'subtotal' => $conditions['subtotal'] ?? 0,
            'commission' => $conditions['commission'] ?? 0,
            'tax' => str_replace(',', '', $conditions['tax']) ?? 0,
            'quote_number' => $conditions['quote_number'] ?? null,
            'invoice_number' => $conditions['invoice_number'] ?? null,
            'receipt_number' => $conditions['receipt_number'] ?? null,
            'cost_service_base' => $conditions['cost_service_base'] ?? 0,
            'cost_print_application_one_distintion' => $conditions['cost_print_application_one_distintion'] ?? 0,
            'cost_print_application_add_distintion' => $conditions['cost_print_application_add_distintion'] ?? 0,
            'cost_change_registration_period' => $conditions['cost_change_registration_period'] ?? 0,
            'cost_registration_certificate' => $conditions['cost_registration_certificate'] ?? 0,
            'cost_5_year_one_distintion' => $conditions['cost_5_year_one_distintion'] ?? 0,
            'cost_10_year_one_distintion' => $conditions['cost_10_year_one_distintion'] ?? 0,
            'from_page' => $conditions['from_page'] ?? U011,
        ]);

        return $payment;
    }

    /**
     * Get list payment all
     *
     * @param array $params
     * @return void
     */
    public function getListPaymentAll($params)
    {
        return $this->queryListPaymentAll($params)->paginate(PAGE_LIMIT_10);
    }

    /**
     * Query List Payment All
     *
     * @param $params
     * @return mixed
     */
    public function queryListPaymentAll($params)
    {
        $query = $this->model->newQuery();
        //filters
        if (!empty($params['search'])) {
            $searchConditions = $params['search'];
            $typeSearch = $params['type_search'] ?? 'and';
            foreach ($searchConditions as $k => $search) {
                //type search and
                if ($typeSearch == 'and') {
                    if (!empty($search['field']) && !empty($search['value'])) {
                        $field = $search['field'];
                        $value = $search['value'];
                        $condition = $search['condition'] ?? '=';
                        $typing = $search['typing'] ?? 'text';
                        //filter condition query
                        switch ($condition) {
                            case 'equal':
                                //search date or search text
                                if ($typing == 'date') {
                                    $query->whereDate($field, $value);
                                } else {
                                    if ($field == 'trademark_number') {
                                        $query->whereHas('trademark', function ($q) use ($field, $value) {
                                            $q->where('trademark_number', $value);
                                        });
                                    } elseif ($field == 'payer_info_payer_name') {
                                        $query->whereHas('payerInfo', function ($q) use ($field, $value) {
                                            $q->where('payer_name', $value);
                                        });
                                    } elseif ($field == 'user_info_name') {
                                        $query->whereHas('trademark', function ($q) use ($field, $value) {
                                            $q->whereHas('user', function ($j) use ($field, $value) {
                                                $j->where('info_name', $value);
                                            });
                                        });
                                    } else {
                                        $query->where($field, $value);
                                    }
                                }
                                break;
                            case 'start_from':
                                //search text
                                if ($field == 'trademark_number') {
                                    $query->whereHas('trademark', function ($q) use ($field, $value) {
                                        $q->where('trademark_number', 'like', "{$value}%");
                                    });
                                } elseif ($field == 'payer_info_payer_name') {
                                    $query->whereHas('payerInfo', function ($q) use ($field, $value) {
                                        $q->where('payer_name', 'like', "{$value}%");
                                    });
                                } elseif ($field == 'user_info_name') {
                                    $query->whereHas('trademark', function ($q) use ($field, $value) {
                                        $q->whereHas('user', function ($j) use ($field, $value) {
                                            $j->where('info_name', 'like', "{$value}%");
                                        });
                                    });
                                } else {
                                    $query->where($field, 'like', "{$value}%");
                                }
                                break;
                            case 'consists_of':
                                //search text
                                if ($field == 'trademark_number') {
                                    $query->whereHas('trademark', function ($q) use ($field, $value) {
                                        $q->where('trademark_number', 'like', "%{$value}%");
                                    });
                                } elseif ($field == 'payer_info_payer_name') {
                                    $query->whereHas('payerInfo', function ($q) use ($field, $value) {
                                        $q->where('payer_name', 'like', "%{$value}%");
                                    });
                                } elseif ($field == 'user_info_name') {
                                    $query->whereHas('trademark', function ($q) use ($field, $value) {
                                        $q->whereHas('user', function ($j) use ($field, $value) {
                                            $j->where('info_name', 'like', "%{$value}%");
                                        });
                                    });
                                } else {
                                    $query->where($field, 'like', "%{$value}%");
                                }
                                break;
                            case 'is_greater_than':
                                //search date
                                $query->whereDate($field, '>=', $value);
                                break;
                            case 'is_less_than':
                                //search date
                                $query->whereDate($field, '<=', $value);
                                break;
                        }
                    }
                } else {
                    //type search or
                    if (!empty($search['field']) && !empty($search['value'])) {
                        $field = $search['field'];
                        $value = $search['value'];
                        $condition = $search['condition'] ?? '=';
                        $typing = $search['typing'] ?? 'text';
                        //filter condition query
                        switch ($condition) {
                            case 'equal':
                                //search date or search text
                                if ($typing == 'date') {
                                    $query->orWhereDate($field, $value);
                                } else {
                                    if ($field == 'trademark_number') {
                                        $query->orWhereHas('trademark', function ($q) use ($field, $value) {
                                            $q->where('trademark_number', $value);
                                        });
                                    } elseif ($field == 'payer_info_payer_name') {
                                        $query->orWhereHas('payerInfo', function ($q) use ($field, $value) {
                                            $q->where('payer_name', $value);
                                        });
                                    } elseif ($field == 'user_info_name') {
                                        $query->orWhereHas('trademark', function ($q) use ($field, $value) {
                                            $q->whereHas('user', function ($j) use ($field, $value) {
                                                $j->where('info_name', $value);
                                            });
                                        });
                                    } else {
                                        $query->orWhere($field, $value);
                                    }
                                }
                                break;
                            case 'start_from':
                                //search text
                                if ($field == 'trademark_number') {
                                    $query->orWhereHas('trademark', function ($q) use ($field, $value) {
                                        $q->where('trademark_number', 'like', "{$value}%");
                                    });
                                } elseif ($field == 'payer_info_payer_name') {
                                    $query->orWhereHas('payerInfo', function ($q) use ($field, $value) {
                                        $q->where('payer_name', 'like', "{$value}%");
                                    });
                                } elseif ($field == 'user_info_name') {
                                    $query->orWhereHas('trademark', function ($q) use ($field, $value) {
                                        $q->whereHas('user', function ($j) use ($field, $value) {
                                            $j->where('info_name', 'like', "{$value}%");
                                        });
                                    });
                                } else {
                                    $query->where($field, 'like', "{$value}%");
                                }
                                break;
                            case 'consists_of':
                                //search text
                                if ($field == 'trademark_number') {
                                    $query->orWhereHas('trademark', function ($q) use ($field, $value) {
                                        $q->where('trademark_number', 'like', "%{$value}%");
                                    });
                                } elseif ($field == 'payer_info_payer_name') {
                                    $query->orWhereHas('payerInfo', function ($q) use ($field, $value) {
                                        $q->where('payer_name', 'like', "%{$value}%");
                                    });
                                } elseif ($field == 'user_info_name') {
                                    $query->orWhereHas('trademark', function ($q) use ($field, $value) {
                                        $q->whereHas('user', function ($j) use ($field, $value) {
                                            $j->where('info_name', 'like', "%{$value}%");
                                        });
                                    });
                                } else {
                                    $query->orWhere($field, 'like', "%{$value}%");
                                }
                                break;
                            case 'is_greater_than':
                                //search date
                                $query->orWhereDate($field, '>=', $value);
                                break;
                            case 'is_less_than':
                                $query->orWhereDate($field, '<=', $value);
                                break;
                        }
                    }
                }
            }
        }

        return $query->where('is_treatment', Payment::IS_TREATMENT_DONE)
            ->where('payment_status', Payment::STATUS_PAID)
            ->with('payerInfo:id,target_id,payment_type,payer_name')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get Payment Bank Transfer
     *
     * @return void
     */
    public function getPaymentBankTransfer()
    {
        return $this->model->with([
            'payerInfo' => function ($q) {
                return $q->where('payment_type', PayerInfo::PAYMENT_BANK_TRANSFER);
            },
            'trademark',
            'trademark.user'
        ])->whereHas('payerInfo', function ($q) {
            return $q->where('payment_type', PayerInfo::PAYMENT_BANK_TRANSFER);
        })
            ->where('is_treatment', Payment::IS_TREATMENT_WAIT)->where('payment_status', Payment::STATUS_WAITING_PAYMENT)
            ->where('trademark_id', '<>', ID_DEFAULT)
            ->orderBy('created_at', SORT_TYPE_DESC)
            ->paginate(PAGE_LIMIT_10);
    }

    /*
     * Create payment for support the first time.
     *
     * @param array $condition .
     */
    public function createPayment(array $conditions): Model
    {
        $payment = $this->updateOrCreate([
            'target_id' => $conditions['target_id'],
            'from_page' => $conditions['from_page'],
        ], $conditions);
        return $payment;
    }

    /**
     * Get info payment precheck
     *
     * @param array $inputs - m_product_ids, package_type, is_mailing_register_cert, period_registration, total_distinction
     * @return mixed
     */
    public function ajaxGetCartInfoPayment(array $inputs)
    {
        $data = [
            'cost_service_base' => 0,
            'cost_service_add_prod' => 0,
            'cost_service_add_prod_default' => 0,
            'count_service_add_prod' => 0,
            'subtotal' => 0,
            'commission' => 0,
            'tax_percentage' => 0,
            'tax' => 0,
            'cost_bank_transfer' => 0,
            'cost_registration_certificate' => 0,
            'cost_change_registration_period' => 0,
            'cost_period_registration' => 0,
            'total_product_choose' => 0,
            'cost_print_application_one_distintion' => 0,
            'cost_print_application_add_distintion' => 0,
            'total_fee_register_for_csc' => 0,
            'fee_submit_register_year' => 0,
            'cost_year_one_distintion' => 0,
            'total_amount' => 0,
            'cost_prod_price' => 0,
            'sum_prod_price' => 0,
            'pack_prod' => 0
        ];

        if (isset($inputs['package_type'])) {
            $packageType = (int) $inputs['package_type'];
            $idsProduct = isset($inputs['m_product_ids']) ? $inputs['m_product_ids'] : [];
            $data['total_product_choose'] = count($idsProduct);
            //count_service_add_prod
            if ($data['total_product_choose'] > 3) {
                $data['count_service_add_prod'] = $data['total_product_choose'] - 3;
                $data['pack_prod'] = ceil($data['count_service_add_prod'] / 3);
            }

            //tax query
            $taxData = $this->settingRepository->findByCondition(['key' => Setting::KEY_TAX])->first();

            if ($taxData) {
                $data['tax_percentage'] = $taxData->value;
            }
            $serviceType = MPriceList::APPLICATION;
            if ($packageType == AppTrademark::PACK_A) {
                $packageTypeServiceBase = MPriceList::PACK_A_UP_3_ITEMS;
                $packageTypeServiceAddProd = MPriceList::PACK_A_EACH_3_ITEMS;
            } elseif ($packageType == AppTrademark::PACK_B) {
                $packageTypeServiceBase = MPriceList::PACK_B_UP_3_ITEMS;
                $packageTypeServiceAddProd = MPriceList::PACK_B_EACH_3_ITEMS;
            } elseif ($packageType == AppTrademark::PACK_C) {
                $packageTypeServiceBase = MPriceList::PACK_C_UP_3_ITEMS;
                $packageTypeServiceAddProd = MPriceList::PACK_C_EACH_3_ITEMS;
            }
            $packageType = $packageTypeServiceAddProd;
            if (isset($inputs['from_page']) && $inputs['from_page'] == U031B) {
                $packageType = $packageTypeServiceBase;
            }

            //cost_service_base && commission && tax
            $priceAddProdCommon = $this->mPriceListRepository->getPriceCommonOfPrecheck($serviceType, $packageTypeServiceAddProd);
            $data['cost_service_add_prod_default'] = $priceAddProdCommon->base_price + $priceAddProdCommon->base_price * $taxData->value / 100;

            $priceCommon = $this->mPriceListRepository->getPriceCommonOfPrecheck($serviceType, $packageType);
            if ($priceCommon) {
                $data['cost_service_base'] = floor($priceCommon->base_price + $priceCommon->base_price * $data['tax_percentage'] / 100);
                $data['commission'] += $priceCommon->base_price;
                $data['tax'] += $priceCommon->base_price * $data['tax_percentage'] / 100;
                $data['subtotal'] += $data['cost_service_base'];
            }

            //cost_registration_certificate: is_mailing_register_cert
            if ($inputs['is_mailing_register_cert'] == AppTrademark::IS_MAILING_REGIS_CERT_TRUE) {
                $priceCommonMailingRegisterCert = $this->mPriceListRepository->getPriceCommonOfPrecheck(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
                $data['cost_registration_certificate'] = floor($priceCommonMailingRegisterCert->base_price
                    + $priceCommonMailingRegisterCert->base_price * $data['tax_percentage'] / 100);
                $data['subtotal'] += ($data['cost_registration_certificate']);
                $data['commission'] += ($priceCommonMailingRegisterCert->base_price);
                $data['tax'] += ($priceCommonMailingRegisterCert->base_price * $data['tax_percentage'] / 100);
            }
            //period_registration
            $priceCommonPeriodRegistration = $this->mPriceListRepository->getPriceCommonOfPrecheck(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
            if ($inputs['period_registration'] == AppTrademark::PERIOD_REGISTRATION_5_YEAR) {
                $data['cost_period_registration'] = (($priceCommonPeriodRegistration->pof_1st_distinction_5yrs
                    + $priceCommonPeriodRegistration->pof_2nd_distinction_5yrs) * $inputs['total_distinction']);
            } elseif ($inputs['period_registration'] == AppTrademark::PERIOD_REGISTRATION_10_YEAR) {
                $data['cost_period_registration'] = (($priceCommonPeriodRegistration->pof_1st_distinction_10yrs
                    + $priceCommonPeriodRegistration->pof_2nd_distinction_10yrs) * $inputs['total_distinction']);
            }

            //cost_change_registration_period && cost_print_application_one_distintion && cost_print_application_add_distintion
            $priceCommonCostPrint = $this->mPriceListRepository->getPriceCommonOfPrecheck(MPriceList::APPLICATION, MPriceList::PACK_A_UP_3_ITEMS);
            $priceCommonFeeCSC = $this->mPriceListRepository->getPriceCommonOfPrecheck(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
            if (isset($inputs['period_registration']) && $inputs['period_registration'] == AppTrademark::PERIOD_REGISTRATION_10_YEAR) {
                //cost_change_registration_period - register 10 year
                $priceCommonMailingRegisPeriod = $this->mPriceListRepository->getPriceCommonOfPrecheck(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
                $data['cost_change_registration_period'] = floor($priceCommonMailingRegisPeriod->base_price
                    + $priceCommonMailingRegisPeriod->base_price * $data['tax_percentage'] / 100);
                $data['commission'] += ($priceCommonMailingRegisPeriod->base_price);
                $data['tax'] += ($priceCommonMailingRegisPeriod->base_price * $data['tax_percentage'] / 100);
                $data['subtotal'] += ($data['cost_change_registration_period']);

                //cost_print_application_one_distintion && cost_print_application_add_distintion of 10year
                $data['cost_print_application_one_distintion'] = ($priceCommonCostPrint->pof_1st_distinction_10yrs);
                $data['cost_print_application_add_distintion'] = ($priceCommonCostPrint->pof_2nd_distinction_10yrs);

                //cost_10_year_one_distintion
                $data['cost_year_one_distintion'] = ($priceCommonFeeCSC->pof_1st_distinction_10yrs);
            } else {
                //cost_print_application_one_distintion && cost_print_application_add_distintion of 5year
                $data['cost_print_application_one_distintion'] = ($priceCommonCostPrint->pof_1st_distinction_5yrs);
                $data['cost_print_application_add_distintion'] = ($priceCommonCostPrint->pof_2nd_distinction_5yrs);

                //cost_5_year_one_distintion
                $data['cost_year_one_distintion'] = ($priceCommonFeeCSC->pof_1st_distinction_5yrs);
            }
            //Fee for CSC
            if (count($idsProduct) > 0) {
                $data['total_fee_register_for_csc'] = ($data['cost_print_application_one_distintion']
                    + $data['cost_print_application_add_distintion'] * ($inputs['total_distinction'] - 1));
                if (isset($inputs['package_type']) && $inputs['package_type'] != AppTrademark::PACK_A) {
                    $data['fee_submit_register_year'] = ($data['cost_year_one_distintion'] * $inputs['total_distinction']);
                } else {
                    $data['fee_submit_register_year'] = 0;
                }
            }
            //cost_service_add_prod && tax && commission if choose large more 3 product
            $getCostServiceAddProd = $this->_getCostServiceAddProd($idsProduct, $packageTypeServiceAddProd, $data['tax_percentage']);
            $data['cost_service_add_prod'] = floor($getCostServiceAddProd['costServiceAddProd']);
            $data['tax'] += ($getCostServiceAddProd['taxServiceAddProd']);
            $data['commission'] += ($getCostServiceAddProd['costServiceAddProdNotTax']);
            $data['subtotal'] += ($data['cost_service_add_prod']);
            $data['cost_prod_price'] = ($data['cost_service_add_prod'] * $data['pack_prod']);
            //if method payment is bank transfer
            if (isset($inputs['payment_type']) && (int) $inputs['payment_type'] == PayerInfo::PAYMENT_BANK_TRANSFER) {
                $priceCommonFeeBank = $this->mPriceListRepository->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
                $data['cost_bank_transfer'] = floor($priceCommonFeeBank->base_price + $priceCommonFeeBank->base_price * $data['tax_percentage'] / 100);
                $data['tax'] += ($priceCommonFeeBank->base_price * $data['tax_percentage'] / 100);
                $data['commission'] += ($priceCommonFeeBank->base_price);
                //subtotal end
                $data['subtotal'] += ($data['cost_bank_transfer']);
            }
            $data['sum_prod_price'] = ($data['cost_prod_price'] + $data['cost_service_base']);
            //total_amount
            $data['total_amount'] = ($data['subtotal'] + $data['total_fee_register_for_csc'] + $data['fee_submit_register_year']);
        }

        return $data;
    }


    /**
     * Get cost service add product has tax & not tax
     *
     * @param array $idsProduct
     * @param string $packType
     * @param string $taxPercent
     * @return array
     */
    protected function _getCostServiceAddProd(array $idsProduct, string $packType, string $taxPercent)
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
        $priceCommon = $this->mPriceListRepository->getPriceCommonOfPrecheck(MPriceList::APPLICATION, $packType);
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
}
