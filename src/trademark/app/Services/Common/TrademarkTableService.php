<?php

namespace App\Services\Common;

use App\Helpers\CommonHelper;
use App\Models\Payment;
use App\Models\RegisterTrademark;
use App\Models\Trademark;
use App\Services\BaseService;
use App\Services\TrademarkService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TrademarkTableService extends BaseService
{
    private TrademarkService $trademarkService;
    private array $trademark;
    private array $appTrademark;
    private array $trademarkInfo;
    private array $registerTrademark;
    private array $options;
    private array $distinction;
    private array $payment;
    private array $user;

    /**
     * Initializing the instances and variables
     */
    public function __construct(TrademarkService $trademarkService)
    {
        $this->trademarkService = $trademarkService;
    }

    /**
     * Initializing the instances and variables
     *
     * @param string $type
     * @param integer $trademarkID
     * @param array $options
     *      SHOW_EDIT_REFERENCE_NUMBER => true - for TYPE_1 anken_top
     *      payment_id => id for TYPE_7
     *      payment_info => payment session for TYPE_7
     * @return array
     */
    public function getTrademarkTable(string $type, int $trademarkID, array $options = []): array
    {
        $trademark = $this->trademarkService->find($trademarkID)->load(['user']);
        $this->options = $options;
        $this->setTrademark($trademark);

        $data = [];
        switch ($type) {
            case TYPE_1:
            case TYPE_2:
            case TYPE_3:
            case TYPE_4:
            case TYPE_5:
            case TYPE_6:
            case TYPE_7:
            case TYPE_8:
                $data = $this->{'getConfigType' . $type}($trademark);
                break;
            case TYPE_ADMIN_1:
                $data = $this->getConfigTypeAdmin1($trademark);
                break;
            case TYPE_ADMIN_2:
                $data = $this->getConfigTypeAdmin2($trademark);
                break;
            case TYPE_ADMIN_3:
                $data = $this->getConfigTypeAdmin3($trademark);
                break;
            case TYPE_ADMIN_4:
                $data = $this->getConfigTypeAdmin4($trademark);
                break;
            default:
        }

        return $data;
    }

    /**
     * Set trademark
     *
     * @param Model|null $trademark
     * @return void
     */
    public function setTrademark(?Model $trademark)
    {
        $this->trademark = [];
        $this->trademark['reference_number'] = [
            'label' => __('labels.common.trademark_table.trademark.reference_number'),
            'value' => $trademark->reference_number ?? '-',
        ];
        if (!empty($this->options[SHOW_EDIT_REFERENCE_NUMBER]) && $this->options[SHOW_EDIT_REFERENCE_NUMBER]) {
            $this->trademark['reference_number'][SHOW_EDIT_REFERENCE_NUMBER] = true;
            $this->trademark['reference_number']['trademark_id'] = $trademark->id ?? 0;
        }
        $this->trademark['trademark_number'] = [
            'label' => __('labels.common.trademark_table.trademark.trademark_number'),
            'value' => $trademark->trademark_number ?? '-',
        ];
        $this->trademark['user_trademark_number'] = [
            'label' => __('labels.common.trademark_table.trademark.trademark_number'),
            'links' => [
                [
                    'label' => $trademark->trademark_number ?? '-',
                    'url' => route('user.application-detail.index', $trademark->id ?? 0),
                    'class' => '',
                ],
            ],
        ];
        if (!empty($this->options[SHOW_LINK_ANKEN_TOP]) && $this->options[SHOW_LINK_ANKEN_TOP]) {
            $this->trademark['trademark_number']['links'][] = [
                'label' => __('labels.common.trademark_table.trademark.anken_top_url'),
                'url' => route('admin.application-detail.index', $trademark->id ?? 0),
                'class' => '',
            ];
        }
        $this->trademark['application_number'] = [
            'label' => __('labels.common.trademark_table.trademark.application_number'),
            'value' => $trademark->application_number ?? '-',
        ];
        $this->trademark['created_at'] = [
            'label' => __('labels.common.trademark_table.trademark.created_at'),
            'value' => (!empty($trademark)) ? CommonHelper::formatTime($trademark->created_at) : '-',
        ];
        $this->trademark['application_date'] = [
            'label' => __('labels.common.trademark_table.app_trademark.created_at_v3'),
            'value' => (!empty($trademark)) ? CommonHelper::formatTime($trademark->application_date) : '-',
        ];
        $this->trademark['type_trademark'] = [
            'label' => __('labels.common.trademark_table.trademark.type_trademark'),
            'value' => $trademark->getTypeTrademark(),
        ];
        $this->trademark['name_trademark'] = [
            'label' => __('labels.common.trademark_table.trademark.name_trademark'),
            'value' => $trademark->name_trademark ?? '-',
        ];
        if (!empty($trademark->image_trademark)) {
            $this->trademark['image_trademark'] = [
                'show_type' => 'image',
                'label' => __('labels.common.trademark_table.trademark.image_trademark'),
                'value' => $trademark->getImageTradeMark(),
                'image_url_label' => __('labels.common.trademark_table.trademark.image_url_label'),
                'image_url' => $trademark->getImageTradeMark(),
            ];
        } else {
            $this->trademark['image_trademark'] = [
                'label' => __('labels.common.trademark_table.trademark.image_trademark'),
                'value' => '-',
            ];
        }
    }

    /**
     * Set App Trademark
     *
     * @param Model|null $appTrademark
     * @return void
     */
    public function setAppTrademark(?Model $appTrademark)
    {
        $this->appTrademark = [];
        $this->appTrademark['created_at'] = [
            'label' => __('labels.common.trademark_table.app_trademark.created_at'),
            'value' => (!empty($appTrademark)) ? CommonHelper::formatTime($appTrademark->created_at, 'Y/m/d') : '-',
        ];
        $this->appTrademark['created_at_2'] = [
            'label' => __('labels.common.trademark_table.app_trademark.created_at_v3'),
            'value' => (!empty($appTrademark)) ? CommonHelper::formatTime($appTrademark->created_at, 'Y/m/d') : '-',
        ];
        $this->appTrademark['pack'] = [
            'label' => __('labels.common.trademark_table.app_trademark.pack'),
            'value' => (!empty($appTrademark)) ? $appTrademark->getShortPackName() : '-',
        ];
    }

    /**
     * /**
     * Set Trademark Info
     *
     * @param Model|null $trademarkInfo
     * @param int $trademarkId
     * @return void
     */
    public function setTrademarkInfo(?Model $trademarkInfo, int $trademarkId)
    {
        $this->trademarkInfo = [];
        $this->trademarkInfo['name'] = [
            'label' => __('labels.common.trademark_table.trademark_info.name'),
            'value' => $trademarkInfo->name ?? '-',
            'links' => [
                [
                    'label' => __('labels.common.trademark_table.trademark_info.trademark_info_link'),
                    'url' => route('user.application-list.change-address.applicant', ['id' => $trademarkId]),
                ],
            ],
        ];
        $this->trademarkInfo['type_acc'] = [
            'label' => __('labels.common.trademark_table.trademark_info.type_acc'),
            'value' => (!empty($trademarkInfo)) ? $trademarkInfo->getTypeAcc() : '-',
        ];
    }

    /**
     * Set Trademark Info
     *
     * @param Model|null $registerTrademark
     * @param null $registerTrademarkFirst
     * @return void
     */
    public function setRegisterTrademark(?Model $registerTrademark, $registerTrademarkFirst = null)
    {
        $this->registerTrademark = [];
        $this->registerTrademark['trademark_info_name'] = [
            'label' => __('labels.common.trademark_table.register_trademark.trademark_info_name'),
            'value' => $registerTrademark->trademark_info_name ?? '-',
            'links' => [
                [
                    'label' => __('labels.common.trademark_table.register_trademark.trademark_info_link'),
                    'url' => (!empty($registerTrademark)) ? route('user.application-list.change-address.registered', ['id' => $registerTrademark->trademark_id]) : '',
                ],
            ],
        ];
        $this->registerTrademark['register_number'] = [
            'label' => __('labels.common.trademark_table.register_trademark.register_number'),
            'value' => $registerTrademark->register_number ?? '-',
        ];
        $this->registerTrademark['period_registration'] = [
            'label' => __('labels.common.trademark_table.register_trademark.period_registration'),
            'value' => (!empty($registerTrademark)) ? $registerTrademark->getPeriodRegistration() : '-',
        ];
        $this->registerTrademark['date_register'] = [
            'label' => __('labels.common.trademark_table.register_trademark.date_register'),
            'value' => (!empty($registerTrademark)) ? CommonHelper::formatTime($registerTrademarkFirst->date_register ?? '', 'Y/m/d') : '-',
        ];
        $this->registerTrademark['deadline_update'] = [
            'label' => __('labels.common.trademark_table.register_trademark.deadline_update'),
            'value' => (!empty($registerTrademark)) ? CommonHelper::formatTime($registerTrademark->deadline_update ?? '', 'Y/m/d') : '-',
        ];
        $this->registerTrademark['type_acc'] = [
            'label' => __('labels.common.trademark_table.trademark_info.type_acc'),
            'value' => (!empty($registerTrademark)) ? $registerTrademark->getTypeAcc() : '-',
        ];
    }

    /**
     * Set Distinction
     *
     * @param array $distinctionData
     * @return void
     */
    public function setDistinction(array $distinctionData, $trademark = null)
    {
        $this->distinction = [];

        $distinctionNameArray = collect($distinctionData)->sortBy('name')->pluck('name')->unique()->toArray();
        $total = count($distinctionNameArray);
        $name = '';
        foreach ($distinctionNameArray as $key => $distName) {
            $name = $name . __('labels.common.trademark_table.distinction.name_distinct', ['name' => $distName]) . '、';
        }
        if ($name != '') {
            $name = preg_replace("/、\s*$/", "", $name);
        }
        $this->distinction['name'] = [
            'label' => __('labels.common.trademark_table.distinction.name'),
            'value' => $total > 0 ? __('labels.common.trademark_table.distinction.name_desc', [
                'total' => $total,
                'name' => $name,
            ]) : '',
            'links' => [
                [
                    'label' => __('labels.common.trademark_table.distinction.name_link'),
                    'url' => route('user.app.product.list', ['id' => $trademark->id ?? 0]),
                ],
            ],
        ];
    }

    /**
     * Set Distinction
     *
     * @param Model|null $payment
     * @return void
     */
    public function setPayment(?Model $payment)
    {
        $this->payment = [];
        $this->payment['quote_number'] = [
            'label' => __('labels.common.trademark_table.payment.quote_number'),
            'value' => $payment->quote_number ?? '-',
            'link_value' => route('user.quote', $payment->id),
        ];
    }

    // COMMON FOR USER

    /**
     * Get config of type
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigType1(Model $trademark): array
    {
        $trademark = $trademark->load([
            'appTrademark.appTrademarkProd.mProduct.mDistinction',
            'appTrademark.trademarkInfo',
            'registerTrademarks' => function ($query) {
                $query->where('is_register', RegisterTrademark::IS_REGISTER);
            },
        ]);
        $appTrademark = $trademark->appTrademark;
        $registerTrademark = $trademark->registerTrademarks->last();
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $this->setAppTrademark($appTrademark);
        $this->setRegisterTrademark($registerTrademark, $registerTrademarkFirst);

        $data = [];

        // Set Data config
        // 1, 2, 4
        $data[] = array_merge($this->trademark['reference_number'], [
            'fields' => [
                $this->appTrademark['pack'],
            ],
        ]);

        // 3, 5
        $data[] = array_merge($this->trademark['user_trademark_number'], [
            'fields' => [
                $this->appTrademark['created_at'],
            ],
        ]);

        // 10, 10.2, 10.3
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $trademarkInfo = $appTrademark->trademarkInfo;
            $this->setTrademarkInfo($trademarkInfo->last(), $trademark->id);

            $trademarkInfoName = $this->trademarkInfo['name'];
            if (count($trademarkInfo) >= 2) {
                $trademarkInfoName['value'] = $trademarkInfoName['value'] . '　'
                    . __('labels.common.trademark_table.trademark_info.trademark_info_total', [
                        'total_trademark_info' => (count($trademarkInfo) - 1),
                    ]);
            }
            $data[] = $trademarkInfoName;
        } elseif (!empty($registerTrademark)) {
            $data[] = $this->registerTrademark['trademark_info_name'];
        }

        // 6
        if ($trademark->isTrademarkLetter() == true) {
            $data[] = $this->trademark['name_trademark'];
        }

        // 6.2
        $data[] = $this->trademark['type_trademark'];

        // 7, 8, 9
        if ($trademark->isTrademarkLetter() == false) {
            $data[] = $this->trademark['image_trademark'];
        }

        // 17, 18
        $appTrademarkProds = [];
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $appTrademark->load('appTrademarkProd.mProduct.mDistinction');
            $appTrademarkProds = $appTrademark->appTrademarkProd;
        } elseif (!empty($registerTrademark)) {
            $registerTrademark->load('registerTrademarkProds.appTrademarkProd.mProduct.mDistinction');
            $registerTrademarkProds = $registerTrademark->registerTrademarkProds;

            $appTrademarkProds = collect();
            foreach ($registerTrademarkProds as $registerTrademarkProd) {
                $appTrademarkProds->push($registerTrademarkProd->appTrademarkProd);
            }
        }
        $distinctionData = [];
        foreach ($appTrademarkProds as $appTrademarkProd) {
            $product = $appTrademarkProd->mProduct;
            $distinction = $product->mDistinction;

            $distinctionData[] = $distinction;
        }
        $this->setDistinction($distinctionData, $trademark);
        $data[] = $this->distinction['name'];

        // 11, 14
        $data[] = array_merge($this->trademark['application_number'], [
            'fields' => [
                $this->trademark['application_date'],
            ],
        ]);

        // 12, 15
        $data[] = array_merge($this->registerTrademark['register_number'], [
            'fields' => [
                $this->registerTrademark['date_register'],
            ],
        ]);

        // 13, 16
        $data[] = array_merge($this->registerTrademark['period_registration'], [
            'fields' => [
                $this->registerTrademark['deadline_update'],
            ],
        ]);

        return $data;
    }

    /**
     * Get config of type
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigType2(Model $trademark): array
    {
        $trademark = $trademark->load(['appTrademark']);
        $appTrademark = $trademark->appTrademark;

        $this->setAppTrademark($appTrademark);

        $data = [];

        // Set Data config

        // 1
        $data[] = $this->trademark['reference_number'];

        // 3, 5
        $data[] = array_merge($this->trademark['user_trademark_number'], [
            'fields' => [
                $this->appTrademark['created_at'],
            ],
        ]);

        // 6.2
        $data[] = $this->trademark['type_trademark'];

        // 6
        if (isset($this->options['input_name_trademark'])) {
            $this->trademark['name_trademark']['show_type'] = 'input';
            $this->trademark['name_trademark']['input_name'] = $this->options['input_name'];
        }
        $data[] = $this->trademark['name_trademark'];

        // 7, 8, 9
        if ($trademark->isTrademarkLetter() == false) {
            $data[] = $this->trademark['image_trademark'];
        }

        return $data;
    }

    /**
     * Get config of type
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigType3(Model $trademark): array
    {
        $trademark = $trademark->load(['appTrademark']);
        $appTrademark = $trademark->appTrademark;
        $this->setAppTrademark($appTrademark);

        $data = [];

        // Set Data config

        // 1, 2, 4
        $data[] = array_merge($this->trademark['reference_number'], [
            'fields' => [
                $this->appTrademark['pack'],
            ],
        ]);

        // 3, 5
        $data[] = array_merge($this->trademark['user_trademark_number'], [
            'fields' => [
                $this->appTrademark['created_at'],
            ],
        ]);

        // 6.2
        $data[] = $this->trademark['type_trademark'];

        // 6
        if ($trademark->isTrademarkLetter() == true) {
            $data[] = $this->trademark['name_trademark'];
        }

        // 7, 8, 9
        if ($trademark->isTrademarkLetter() == false) {
            $data[] = $this->trademark['image_trademark'];
        }

        return $data;
    }

    /**
     * Get config of type
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigType4(Model $trademark): array
    {
        $trademark = $trademark->load([
            'appTrademark',
            'registerTrademarks' => function ($query) {
                $query->where('is_register', RegisterTrademark::IS_REGISTER);
            },
        ]);
        $appTrademark = $trademark->appTrademark;
        $registerTrademark = $trademark->registerTrademarks->last();
        $registerTrademarkFirst = $trademark->registerTrademarks->first();

        $this->setAppTrademark($appTrademark);
        $this->setRegisterTrademark($registerTrademark, $registerTrademarkFirst);

        $data = [];

        // Set Data config

        if ((!empty($this->options[TRADEMARK_TABLE_KENRISHA]) && $this->options[TRADEMARK_TABLE_KENRISHA])
            || (!empty($this->options[U000_FREE]) && $this->options[U000_FREE])
            || (!empty($this->options[U201]) && $this->options[U201])
            || (!empty($this->options[U204]) && $this->options[U204])
            || (!empty($this->options[U203]) && $this->options[U203])
            || (!empty($this->options[U207Kyo]) && $this->options[U207Kyo])
            || (!empty($this->options[U204N]) && $this->options[U204N])
            || (!empty($this->options[U205]) && $this->options[U205])
            || (!empty($this->options[U210_ALERT_02]) && $this->options[U210_ALERT_02])
            || (!empty($this->options[U210_Encho]) && $this->options[U210_Encho])
            || (!empty($this->options[U302]) && $this->options[U302])
            || (!empty($this->options[U303]) && $this->options[U303])
            || (!empty($this->options[U302_402TSUINO_5YR_KOUKI]) && $this->options[U302_402TSUINO_5YR_KOUKI])
        ) {
            // 1, 2, 4
            $data[] = array_merge($this->trademark['reference_number'], [
                'fields' => [
                    $this->appTrademark['pack'],
                ],
            ]);
        } else {
            // 10
            $data[] = $this->trademark['reference_number'];
        }

        // 3, 5
        $data[] = array_merge($this->trademark['user_trademark_number'], [
            'fields' => [
                $this->appTrademark['created_at'],
            ],
        ]);

        // 6
        $data[] = $this->trademark['name_trademark'];

        // 7, 8, 9
        $data[] = $this->trademark['image_trademark'];

        if (!empty($this->options[TRADEMARK_TABLE_KENRISHA]) && $this->options[TRADEMARK_TABLE_KENRISHA]) {
            // 12, 15
            $data[] = array_merge($this->registerTrademark['register_number'], [
                'fields' => [
                    $this->registerTrademark['date_register'],
                ],
            ]);
        } else {
            // 11, 14
            $data[] = array_merge($this->trademark['application_number'], [
                'fields' => [
                    $this->trademark['application_date'],
                ],
            ]);
        }

        return $data;
    }

    /**
     * Get config of type
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigType5(Model $trademark): array
    {
        $trademark = $trademark->load([
            'appTrademark',
            'registerTrademarks' => function ($query) {
                $query->where('is_register', RegisterTrademark::IS_REGISTER);
            },
        ]);
        $appTrademark = $trademark->appTrademark;
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $registerTrademark = $trademark->registerTrademarks->last();

        $this->setAppTrademark($appTrademark);
        $this->setRegisterTrademark($registerTrademark, $registerTrademarkFirst);

        $data = [];

        // Set Data config

        // 1, 4
        $data[] = array_merge($this->trademark['reference_number'], [
            'fields' => [
                $this->appTrademark['pack'],
            ],
        ]);

        // 3, 5
        $data[] = array_merge($this->trademark['user_trademark_number'], [
            'fields' => [
                $this->appTrademark['created_at'],
            ],
        ]);

        // 6
        $data[] = $this->trademark['name_trademark'];

        // 7, 8, 9
        $data[] = $this->trademark['image_trademark'];

        // 12, 15
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $data[] = array_merge($this->registerTrademark['register_number'], [
            'fields' => [
                $this->registerTrademark['date_register'],
            ],
        ]);

        return $data;
    }

    /**
     * Get config of type
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigType6(Model $trademark): array
    {
        $trademark = $trademark->load([
            'appTrademark.appTrademarkProd.mProduct.mDistinction',
            'appTrademark.trademarkInfo',
            'registerTrademarks' => function ($query) {
                $query->where('is_register', RegisterTrademark::IS_REGISTER);
            },
        ]);
        $appTrademark = $trademark->appTrademark;
        $registerTrademark = $trademark->registerTrademarks->last();
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $this->setAppTrademark($appTrademark);
        $this->setRegisterTrademark($registerTrademark, $registerTrademarkFirst);

        $data = [];

        // Set Data config
        // 1, 2, 4
        $data[] = array_merge($this->trademark['reference_number'], [
            'fields' => [
                $this->appTrademark['pack'],
            ],
        ]);

        // 3, 5
        $data[] = array_merge($this->trademark['user_trademark_number'], [
            'fields' => [
                $this->appTrademark['created_at'],
            ],
        ]);

        // 10, 10.2, 10.3
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $trademarkInfo = $appTrademark->trademarkInfo;
            $this->setTrademarkInfo($trademarkInfo->last(), $trademark->id);

            $trademarkInfoName = $this->trademarkInfo['name'];
            if (count($trademarkInfo) >= 2) {
                $trademarkInfoName['value'] = $trademarkInfoName['value'] . '　'
                    . __('labels.common.trademark_table.trademark_info.trademark_info_total', [
                        'total_trademark_info' => (count($trademarkInfo) - 1),
                    ]);
            }
            $data[] = $trademarkInfoName;
        } elseif (!empty($registerTrademark)) {
            $data[] = $this->registerTrademark['trademark_info_name'];
        }

        // 6
        $data[] = $this->trademark['name_trademark'];

        // 6.2
        $data[] = $this->trademark['type_trademark'];

        // 7, 8, 9
        $data[] = $this->trademark['image_trademark'];


        // 11, 14
        $data[] = array_merge($this->trademark['application_number'], [
            'fields' => [
                $this->trademark['application_date'],
            ],
        ]);

        // 12, 15
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $data[] = array_merge($this->registerTrademark['register_number'], [
            'fields' => [
                $this->registerTrademark['date_register'],
            ],
        ]);

        // 13, 16
        $data[] = array_merge($this->registerTrademark['period_registration'], [
            'fields' => [
                $this->registerTrademark['deadline_update'],
            ],
        ]);

        return $data;
    }

    /**
     * Get config of type
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigType7(Model $trademark): array
    {
        $data = [];

        $payment = Payment::find($this->options['payment_id'] ?? 0);
        if (empty($payment)) {
            $paymentInfo = $this->options['payment_info'] ?? [];

            $data[] = [
                'label' => __('labels.common.trademark_table.payment.quote_number'),
                'value' => '-',
            ];

            $data[] = [
                'label' => __('labels.common.trademark_table.payment.trademark_info_name'),
                'value' => $paymentInfo['payer_name'] ?? '-',
            ];

            $trademarkInfoAddress = CommonHelper::getInfoAddress(
                $paymentInfo['m_nation_id'] ?? 0,
                $paymentInfo['m_prefecture_id'] ?? 0,
                $paymentInfo['address_second'] ?? '-',
                $paymentInfo['address_three'] ?? '-',
            );
            $trademarkInfoAddress = implode('<br>', $trademarkInfoAddress);

            $data[] = [
                'label' => __('labels.common.trademark_table.payment.trademark_info_address'),
                'value' => $trademarkInfoAddress ?? '-',
            ];

            // 6
            $data[] = $this->trademark['name_trademark'];

            // 7, 8, 9
            $data[] = $this->trademark['image_trademark'];
        } else {
            $trademark = $trademark->load([
                'appTrademark',
                'registerTrademarks' => function ($query) {
                    $query->where('is_register', RegisterTrademark::IS_REGISTER);
                },
            ]);
            $appTrademark = $trademark->appTrademark;
            $registerTrademark = $trademark->registerTrademarks->last();
            $registerTrademarkFirst = $trademark->registerTrademarks->first();
            $this->setAppTrademark($appTrademark);
            $this->setRegisterTrademark($registerTrademark, $registerTrademarkFirst);
            $this->setPayment($payment);

            $data[] = $this->payment['quote_number'];

            if (!empty($appTrademark) && empty($registerTrademark)) {
                $appTrademark = $appTrademark->load('trademarkInfo');

                $trademarkInfo = $appTrademark->trademarkInfo->last();

                $this->setTrademarkInfo($trademarkInfo, $trademark->id);

                $trademarkInfoName = $this->trademarkInfo['name'];
                $trademarkInfoName['label'] = __('labels.common.trademark_table.payment.trademark_info_name');
                $trademarkInfoName['links'] = [];
                $data[] = $trademarkInfoName;

                $trademarkInfoAddress = CommonHelper::getInfoAddress(
                    $trademarkInfo->m_nation_id ?? 0,
                    $trademarkInfo->m_prefecture_id ?? 0,
                    $trademarkInfo->address_second ?? '-',
                    $trademarkInfo->address_three ?? '-',
                );
                $trademarkInfoAddress = implode('<br>', $trademarkInfoAddress);

                $data[] = [
                    'label' => __('labels.common.trademark_table.payment.trademark_info_address'),
                    'value' => $trademarkInfoAddress ?? '-',
                ];
            } elseif (!empty($registerTrademark)) {
                $registerTrademarkInfoName = $this->registerTrademark['trademark_info_name'];
                $registerTrademarkInfoName['label'] = __('labels.common.trademark_table.payment.trademark_info_name');
                $registerTrademarkInfoName['links'] = [];
                $data[] = $registerTrademarkInfoName;

                $trademarkInfoAddress = CommonHelper::getInfoAddress(
                    $registerTrademark->trademark_info_nation_id ?? 0,
                    $registerTrademark->trademark_info_address_first ?? 0,
                    $registerTrademark->trademark_info_address_second ?? '-',
                    $registerTrademark->trademark_info_address_three ?? '-',
                );
                $trademarkInfoAddress = implode('<br>', $trademarkInfoAddress);

                $data[] = [
                    'label' => __('labels.common.trademark_table.payment.trademark_info_address'),
                    'value' => $trademarkInfoAddress ?? '-',
                ];
            } else {
                $data[] = [
                    'label' => __('labels.common.trademark_table.payment.trademark_info_name'),
                    'value' => '-',
                ];
                $data[] = [
                    'label' => __('labels.common.trademark_table.payment.trademark_info_address'),
                    'value' => '-',
                ];
            }

            // 6
            $data[] = $this->trademark['name_trademark'];

            // 7, 8, 9
            $data[] = $this->trademark['image_trademark'];
        }

        return $data;
    }

    /**
     * Get config of type
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigType8(Model $trademark): array
    {
        $trademark = $trademark->load([
            'appTrademark.appTrademarkProd.mProduct.mDistinction',
            'appTrademark.trademarkInfo',
            'registerTrademarks' => function ($query) {
                $query->where('is_register', RegisterTrademark::IS_REGISTER);
            },
        ]);
        $appTrademark = $trademark->appTrademark;
        $registerTrademark = $trademark->registerTrademarks->last();
        $registerTrademarkFirst = $trademark->registerTrademarks->first();

        $this->setAppTrademark($appTrademark);
        $this->setRegisterTrademark($registerTrademark, $registerTrademarkFirst);

        $data = [];

        // Set Data config
        // 1, 2, 4
        $data[] = array_merge($this->trademark['reference_number'], [
            'fields' => [
                $this->appTrademark['pack'],
            ],
        ]);

        // 3, 5
        $data[] = array_merge($this->trademark['user_trademark_number'], [
            'fields' => [
                $this->appTrademark['created_at'],
            ],
        ]);

        // 6
        $data[] = $this->trademark['name_trademark'];

        // 17, 18
        $appTrademarkProds = $appTrademark && $appTrademark->appTrademarkProd ? $appTrademark->appTrademarkProd : collect([]);
        $distinctionData = [];
        foreach ($appTrademarkProds as $appTrademarkProd) {
            $product = $appTrademarkProd->mProduct;
            $distinction = $product->mDistinction;

            $distinctionData[] = $distinction;
        }
        $this->setDistinction($distinctionData);
        $data[] = $this->distinction['name'];

        // 7, 8, 9
        $data[] = $this->trademark['image_trademark'];

        // 11, 14
        $data[] = array_merge($this->trademark['application_number'], [
            'fields' => [
                $this->trademark['application_date'],
            ],
        ]);

        return $data;
    }

    // COMMON FOR ADMIN

    /**
     * Set Trademark Admin User
     *
     * @param Model $user
     * @return void
     */
    public function setTrademarkAdminUser(?Model $user)
    {
        if (!empty($user)) {
            $this->user = [
                'info' => $user,
                'user_detail_link' => route('admin.question-answers.show', $user->id),
                'qa_link' => route('admin.question.answers.from.ams', $user->id),
            ];
        }
    }

    /**
     * Get config of type Admin
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigTypeAdmin1(Model $trademark): array
    {
        $trademark = $trademark->load([
            'appTrademark',
            'registerTrademarks' => function ($query) {
                $query->where('is_register', RegisterTrademark::IS_REGISTER);
            },
        ]);
        $appTrademark = $trademark->appTrademark;
        $registerTrademark = $trademark->registerTrademarks->last();
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $user = $trademark->user;

        $this->setTrademarkAdminUser($user);
        $this->setAppTrademark($appTrademark);
        $this->setRegisterTrademark($registerTrademark, $registerTrademarkFirst);

        $data = [];

        // 1, 2, 3, 24, 25
        $data['user'] = $this->user ?? [];

        // 4, 6
        $data['data'][] = array_merge($this->trademark['trademark_number'], [
            'fields' => [
                $this->trademark['created_at'],
            ],
        ]);

        // 7
        $appTrademarkPack = $this->appTrademark['pack'];
        $appTrademarkPack['label'] = __('labels.common.trademark_table.app_trademark.pack_v2');
        $data['data'][] = $appTrademarkPack;

        // 8, 9
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $trademarkInfo = $appTrademark->trademarkInfo;
            $this->setTrademarkInfo($trademarkInfo->last(), $trademark->id);

            // 8
            $trademarkInfoTypeAcc = $this->trademarkInfo['type_acc'];
            $data['data'][] = $trademarkInfoTypeAcc;

            // 9, 10
            if (count($trademarkInfo) >= 2) {
                $this->trademarkInfo['name']['value'] = $this->trademarkInfo['name']['value'] . '　'
                    . __('labels.common.trademark_table.trademark_info.trademark_info_total', [
                        'total_trademark_info' => (count($trademarkInfo) - 1),
                    ]);
            }
            $this->trademarkInfo['name']['links'] = [
                [
                    'label' => __('labels.common.trademark_table.trademark_info.trademark_info_link_v2'),
                    'url' => route('admin.question-answers.show', $trademark->user_id),
                ],
            ];
            $data['data'][] = $this->trademarkInfo['name'];
        } elseif (!empty($registerTrademark)) {
            // 8
            $data['data'][] = $this->registerTrademark['type_acc'];

            // 9, 10
            $this->registerTrademark['trademark_info_name']['links'] = [
                [
                    'label' => __('labels.common.trademark_table.register_trademark.trademark_info_link_v2'),
                    'url' => route('admin.question-answers.show', $trademark->user_id),
                ],
            ];
            $data['data'][] = $this->registerTrademark['trademark_info_name'];
        }

        // 11
        $data['data'][] = $this->trademark['type_trademark'];

        // 12
        $data['data'][] = $this->trademark['name_trademark'];

        // 13
        if ($trademark->isTrademarkLetter() == false) {
            $data['data'][] = $this->trademark['image_trademark'];
        }

        // 14, 15
        $this->trademark['application_date']['label'] = __('labels.common.trademark_table.app_trademark.created_at_v3');
        $data['data'][] = array_merge($this->trademark['application_number'], [
            'fields' => [
                $this->trademark['application_date'],
            ],
        ]);

        // 16, 17
        $data['data'][] = array_merge($this->registerTrademark['register_number'], [
            'fields' => [
                $this->registerTrademark['date_register'],
            ],
        ]);

        // 18
        $this->registerTrademark['deadline_update']['label'] = __('labels.common.trademark_table.register_trademark.deadline_update_v2');
        $data['data'][] = $this->registerTrademark['deadline_update'];

        // 19, 20
        $appTrademarkProds = [];
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $appTrademark->load('appTrademarkProd.mProduct.mDistinction');
            $appTrademarkProds = $appTrademark->appTrademarkProd;
        } elseif (!empty($registerTrademark)) {
            $registerTrademark->load('registerTrademarkProds.appTrademarkProd.mProduct.mDistinction');
            $registerTrademarkProds = $registerTrademark->registerTrademarkProds;

            $appTrademarkProds = collect();
            foreach ($registerTrademarkProds as $registerTrademarkProd) {
                $appTrademarkProds->push($registerTrademarkProd->appTrademarkProd);
            }
        }
        $distinctionData = [];
        foreach ($appTrademarkProds as $appTrademarkProd) {
            $product = $appTrademarkProd->mProduct;
            $distinction = $product->mDistinction ?? null;

            $distinctionData[] = $distinction;
        }

        $this->setDistinction($distinctionData);
        $this->distinction['name']['label'] = __('labels.common.trademark_table.distinction.name_admin');
        $this->distinction['name']['links'] = [
            [
                'label' => __('labels.common.trademark_table.distinction.name_link_v2'),
                'url' => route('user.app.product.list', $trademark->id),
            ],
        ];
        $data['data'][] = $this->distinction['name'];

        // 21
        //check exists support fist time
        $currentAdmin = Auth::guard(ADMIN_ROLE)->user();
        if ($trademark->supportFirstTime && in_array($currentAdmin->role, [ROLE_MANAGER, ROLE_SUPERVISOR])) {
            $data['sft'] = [
                'label' => __('labels.common.trademark_table.btn_support_first_time'),
                'url' => !empty($this->options[TRADEMARK_TABLE_A031])
                    || !empty($this->options[A202S])
                    || !empty($this->options[A203SASHI])
                    || !empty($this->options[A203S])
                    || !empty($this->options[A210Alert])
                    ? route('admin.support-first-time.create', $trademark->id) . '?type=view'
                    : route('admin.support-first-time.create', $trademark->id),
            ];
        } else {
            $data['sft'] = [];
        }

        // 22
        $data['precheck'] = [
            'label' => __('labels.common.trademark_table.btn_precheck'),
            'url' => route('admin.precheck.open-modal'),
            'trademark_id' => $trademark->id ?? 0,
        ];

        // 23
        $data['history'] = [
            'label' => __('labels.common.trademark_table.btn_history'),
            'url' => route('admin.application-detail.index', $trademark->id),
        ];

        return $data;
    }

    /**
     * Get config of type Admin
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigTypeAdmin2(Model $trademark): array
    {
        $trademark = $trademark->load([
            'appTrademark',
            'registerTrademarks' => function ($query) {
                $query->where('is_register', RegisterTrademark::IS_REGISTER);
            },
        ]);
        $appTrademark = $trademark->appTrademark;
        $registerTrademark = $trademark->registerTrademarks->last();
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $user = $trademark->user;

        $this->setTrademarkAdminUser($user);
        $this->setAppTrademark($appTrademark);
        $this->setRegisterTrademark($registerTrademark, $registerTrademarkFirst);

        $data = [];

        // 1, 2, 3, 24, 25
        $data['user'] = $this->user ?? [];

        // 4, 6
        $data['data'][] = array_merge($this->trademark['trademark_number'], [
            'fields' => [
                $this->trademark['created_at'],
            ],
        ]);

        // 8, 9
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $trademarkInfo = $appTrademark->trademarkInfo;
            $this->setTrademarkInfo($trademarkInfo->last(), $trademark->id);

            // 8
            $trademarkInfoTypeAcc = $this->trademarkInfo['type_acc'];
            $data['data'][] = $trademarkInfoTypeAcc;

            // 9, 10
            if (count($trademarkInfo) >= 2) {
                $this->trademarkInfo['name']['value'] = $this->trademarkInfo['name']['value'] . '　'
                    . __('labels.common.trademark_table.trademark_info.trademark_info_total', [
                        'total_trademark_info' => (count($trademarkInfo) - 1),
                    ]);
            }
            $this->trademarkInfo['name']['links'] = [
                [
                    'label' => __('labels.common.trademark_table.trademark_info.trademark_info_link_v2'),
                    'url' => route('admin.question-answers.show', $trademark->user_id),
                ],
            ];
            $data['data'][] = $this->trademarkInfo['name'];
        } elseif (!empty($registerTrademark)) {
            // 8
            $data['data'][] = $this->registerTrademark['type_acc'];

            // 9, 10
            $this->registerTrademark['trademark_info_name']['links'] = [
                [
                    'label' => __('labels.common.trademark_table.register_trademark.trademark_info_link_v2'),
                    'url' => route('admin.question-answers.show', $trademark->user_id),
                ],
            ];
            $data['data'][] = $this->registerTrademark['trademark_info_name'];
        }

        // 11
        $data['data'][] = $this->trademark['type_trademark'];

        // 12
        $data['data'][] = $this->trademark['name_trademark'];

        // 13
        if ($trademark->isTrademarkLetter() == false) {
            $data['data'][] = $this->trademark['image_trademark'];
        }

        // 14, 15
        $this->trademark['application_date']['label'] = __('labels.common.trademark_table.app_trademark.created_at_v3');
        $data['data'][] = array_merge($this->trademark['application_number'], [
            'fields' => [
                $this->trademark['application_date'],
            ],
        ]);
        // 16, 17
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $data['data'][] = array_merge($this->registerTrademark['register_number'], [
            'fields' => [
                $this->registerTrademark['date_register'],
            ],
        ]);

        // 19, 20
        $appTrademarkProds = [];
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $appTrademark->load('appTrademarkProd.mProduct.mDistinction');
            $appTrademarkProds = $appTrademark->appTrademarkProd;
        } elseif (!empty($registerTrademark)) {
            $registerTrademark->load('registerTrademarkProds.appTrademarkProd.mProduct.mDistinction');
            $registerTrademarkProds = $registerTrademark->registerTrademarkProds;

            $appTrademarkProds = collect();
            foreach ($registerTrademarkProds as $registerTrademarkProd) {
                $appTrademarkProds->push($registerTrademarkProd->appTrademarkProd);
            }
        }
        $distinctionData = [];
        foreach ($appTrademarkProds as $appTrademarkProd) {
            $product = $appTrademarkProd->mProduct;
            if ($product) {
                $distinction = $product->mDistinction;
                $distinctionData[] = $distinction;
            }
        }
        $this->setDistinction($distinctionData);
        $this->distinction['name']['label'] = __('labels.common.trademark_table.distinction.name_admin');
        $this->distinction['name']['links'] = [
            [
                'label' => __('labels.common.trademark_table.distinction.name_link_v2'),
                'url' => route('user.app.product.list', $trademark->id),
            ],
        ];
        $data['data'][] = $this->distinction['name'];

        // 13, 16
        $data['data'][] = array_merge($this->registerTrademark['period_registration'], [
            'fields' => [
                $this->registerTrademark['deadline_update'],
            ],
        ]);

        // 21
        $currentAdmin = Auth::guard(ADMIN_ROLE)->user();
        if ($trademark->supportFirstTime && in_array($currentAdmin->role, [ROLE_MANAGER, ROLE_SUPERVISOR])) {
            $data['sft'] = [
                'label' => __('labels.common.trademark_table.btn_support_first_time'),
                'url' => route('admin.support-first-time.create', $trademark->id),
            ];
        }

        // 22
        $data['precheck'] = [
            'label' => __('labels.common.trademark_table.btn_precheck'),
            'url' => route('admin.precheck.open-modal'),
            'trademark_id' => $trademark->id ?? 0,
        ];

        // 23
        $data['history'] = [
            'label' => __('labels.common.trademark_table.btn_history'),
            'url' => route('admin.application-detail.index', $trademark->id),
        ];

        return $data;
    }

    /**
     * Get config of type Admin
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigTypeAdmin3(Model $trademark): array
    {
        $trademark = $trademark->load([
            'appTrademark',
            'registerTrademarks' => function ($query) {
                $query->where('is_register', RegisterTrademark::IS_REGISTER);
            },
        ]);
        $appTrademark = $trademark->appTrademark;
        $registerTrademark = $trademark->registerTrademarks->last();
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $user = $trademark->user;

        $this->setTrademarkAdminUser($user);
        $this->setAppTrademark($appTrademark);
        $this->setRegisterTrademark($registerTrademark, $registerTrademarkFirst);

        $data = [];

        // 1, 2, 3, 24, 25
        $data['user'] = $this->user ?? [];

        // 4, 6
        $data['data'][] = array_merge($this->trademark['trademark_number'], [
            'fields' => [
                $this->trademark['created_at'],
            ],
        ]);

        // 7
        $appTrademarkPack = $this->appTrademark['pack'];
        $appTrademarkPack['label'] = __('labels.common.trademark_table.app_trademark.pack_v2');
        $data['data'][] = $appTrademarkPack;

        // 8
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $trademarkInfo = $appTrademark->trademarkInfo;
            $this->setTrademarkInfo($trademarkInfo->last(), $trademark->id);

            $trademarkInfoTypeAcc = $this->trademarkInfo['type_acc'];
            $data['data'][] = $trademarkInfoTypeAcc;
        } elseif (!empty($registerTrademark)) {
            $data['data'][] = $this->registerTrademark['type_acc'];
        }

        // 12
        $data['data'][] = $this->trademark['name_trademark'];

        // 13
        if ($trademark->isTrademarkLetter() == false) {
            $data['data'][] = $this->trademark['image_trademark'];
        }

        // 14, 15
        $this->trademark['application_date']['label'] = __('labels.common.trademark_table.app_trademark.created_at_v2');
        $data['data'][] = array_merge($this->trademark['application_number'], [
            'fields' => [
                $this->trademark['application_date'],
            ],
        ]);

        // 16, 17
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $data['data'][] = array_merge($this->registerTrademark['register_number'], [
            'fields' => [
                $this->registerTrademark['date_register'],
            ],
        ]);

        // 18
        $this->registerTrademark['deadline_update']['label'] = __('labels.common.trademark_table.register_trademark.deadline_update_v2');
        $data['data'][] = $this->registerTrademark['deadline_update'];

        // 19, 20
        $appTrademarkProds = [];
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $appTrademark->load('appTrademarkProd.mProduct.mDistinction');
            $appTrademarkProds = $appTrademark->appTrademarkProd;
        } elseif (!empty($registerTrademark)) {
            $registerTrademark->load('registerTrademarkProds.appTrademarkProd.mProduct.mDistinction');
            $registerTrademarkProds = $registerTrademark->registerTrademarkProds;

            $appTrademarkProds = collect();
            foreach ($registerTrademarkProds as $registerTrademarkProd) {
                $appTrademarkProds->push($registerTrademarkProd->appTrademarkProd);
            }
        }
        $distinctionData = [];
        foreach ($appTrademarkProds as $appTrademarkProd) {
            $product = $appTrademarkProd->mProduct;
            $distinction = $product->mDistinction;

            $distinctionData[] = $distinction;
        }
        $this->setDistinction($distinctionData);
        $this->distinction['name']['label'] = __('labels.common.trademark_table.distinction.name_admin');
        $this->distinction['name']['links'] = [
            [
                'label' => __('labels.common.trademark_table.distinction.name_link_v2'),
                'url' => route('user.app.product.list', $trademark->id),
            ],
        ];
        $data['data'][] = $this->distinction['name'];

        // 21
        $currentAdmin = Auth::guard(ADMIN_ROLE)->user();
        if ($trademark->supportFirstTime && in_array($currentAdmin->role, [ROLE_MANAGER, ROLE_SUPERVISOR])) {
            $data['sft'] = [
                'label' => __('labels.common.trademark_table.btn_support_first_time'),
                'url' => route('admin.support-first-time.create', $trademark->id),
            ];
        }

        // 22
        $data['precheck'] = [
            'label' => __('labels.common.trademark_table.btn_precheck'),
            'url' => route('admin.precheck.open-modal'),
            'trademark_id' => $trademark->id ?? 0,
        ];

        // 23
        $data['history'] = [
            'label' => __('labels.common.trademark_table.btn_history'),
            'url' => route('admin.application-detail.index', $trademark->id),
        ];

        return $data;
    }

    /**
     * Get config of type Admin
     *
     * @param Model $trademark
     * @return array
     */
    public function getConfigTypeAdmin4(Model $trademark): array
    {
        $trademark = $trademark->load([
            'appTrademark',
            'registerTrademarks' => function ($query) {
                $query->where('is_register', RegisterTrademark::IS_REGISTER);
            },
        ]);
        $appTrademark = $trademark->appTrademark;
        $registerTrademark = $trademark->registerTrademarks->last();
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $user = $trademark->user;

        $this->setTrademarkAdminUser($user);
        $this->setAppTrademark($appTrademark);
        $this->setRegisterTrademark($registerTrademark, $registerTrademarkFirst);

        $data = [];

        // 1, 2, 3, 24, 25
        $data['user'] = $this->user ?? [];

        // 4, 6
        $data['data'][] = array_merge($this->trademark['trademark_number'], [
            'fields' => [
                $this->trademark['created_at'],
            ],
        ]);

        // 7
        $appTrademarkPack = $this->appTrademark['pack'];
        $appTrademarkPack['label'] = __('labels.common.trademark_table.app_trademark.pack_v2');
        $data['data'][] = $appTrademarkPack;

        // 8, 9
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $trademarkInfo = $appTrademark->trademarkInfo;
            $this->setTrademarkInfo($trademarkInfo->last(), $trademark->id);

            // 8
            $trademarkInfoTypeAcc = $this->trademarkInfo['type_acc'];
            $data['data'][] = $trademarkInfoTypeAcc;

            // 9, 10
            if (count($trademarkInfo) >= 2) {
                $this->trademarkInfo['name']['value'] = $this->trademarkInfo['name']['value'] . '　'
                    . __('labels.common.trademark_table.trademark_info.trademark_info_total', [
                        'total_trademark_info' => (count($trademarkInfo) - 1),
                    ]);
            }
            $this->trademarkInfo['name']['links'] = [
                [
                    'label' => __('labels.common.trademark_table.trademark_info.trademark_info_link_v2'),
                    'url' => route('admin.question-answers.show', $trademark->user_id),
                ],
            ];
            $data['data'][] = $this->trademarkInfo['name'];
        } elseif (!empty($registerTrademark)) {
            // 8
            $data['data'][] = $this->registerTrademark['type_acc'];

            // 9, 10
            $this->registerTrademark['trademark_info_name']['links'] = [
                [
                    'label' => __('labels.common.trademark_table.register_trademark.trademark_info_link_v2'),
                    'url' => route('admin.question-answers.show', $trademark->user_id),
                ],
            ];
            $data['data'][] = $this->registerTrademark['trademark_info_name'];
        }

        // 11
        $data['data'][] = $this->trademark['type_trademark'];

        // 12
        $data['data'][] = $this->trademark['name_trademark'];

        // 13
        if ($trademark->isTrademarkLetter() == false) {
            $data['data'][] = $this->trademark['image_trademark'];
        }

        // 14, 15
        $this->trademark['application_date']['label'] = __('labels.common.trademark_table.app_trademark.created_at_v2');
        $data['data'][] = array_merge($this->trademark['application_number'], [
            'fields' => [
                $this->trademark['application_date'],
            ],
        ]);

        // 16, 17
        $registerTrademarkFirst = $trademark->registerTrademarks->first();
        $data['data'][] = array_merge($this->registerTrademark['register_number'], [
            'fields' => [
                $this->registerTrademark['date_register'],
            ],
        ]);

        // 18
        $this->registerTrademark['deadline_update']['label'] = __('labels.common.trademark_table.register_trademark.deadline_update_v2');
        $data['data'][] = $this->registerTrademark['deadline_update'];

        // 19, 20
        $appTrademarkProds = [];
        if (!empty($appTrademark) && empty($registerTrademark)) {
            $appTrademark->load('appTrademarkProd.mProduct.mDistinction');
            $appTrademarkProds = $appTrademark->appTrademarkProd;
        } elseif (!empty($registerTrademark)) {
            $registerTrademark->load('registerTrademarkProds.appTrademarkProd.mProduct.mDistinction');
            $registerTrademarkProds = $registerTrademark->registerTrademarkProds;

            $appTrademarkProds = collect();
            foreach ($registerTrademarkProds as $registerTrademarkProd) {
                $appTrademarkProds->push($registerTrademarkProd->appTrademarkProd);
            }
        }
        $distinctionData = [];
        foreach ($appTrademarkProds as $appTrademarkProd) {
            $product = $appTrademarkProd->mProduct;
            $distinction = $product->mDistinction;

            $distinctionData[] = $distinction;
        }
        $this->setDistinction($distinctionData);
        $this->distinction['name']['label'] = __('labels.common.trademark_table.distinction.name_admin');
        $this->distinction['name']['links'] = [
            [
                'label' => __('labels.common.trademark_table.distinction.name_link_v2'),
                'url' => route('user.app.product.list', $trademark->id),
            ],
        ];
        $data['data'][] = $this->distinction['name'];

        return $data;
    }
}
