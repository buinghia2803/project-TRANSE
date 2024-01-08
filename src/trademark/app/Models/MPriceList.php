<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPriceList extends Model
{
    protected $table = 'm_price_lists';

    // constant for service type
    const BEFORE_FILING = 1;
    const APPLICATION = 2;
    const REASONS_REFUSAL = 3;
    const REGISTRATION = 4;
    const LATE_PAYMENT_REGISTRATION = 5;
    const EACH_PAYMENT = 6;
    const AT_REGISTRATION = 7;
    const UPDATE = 8;
    const LATE_PAYMENT_RENEWAL = 9;
    const CHANGE_PROCEDURE = 10;
    const FREE_HISTORY = 11;
    const OTHER = 12;

    // Constant for package type
    const SFT_SELECT_SUPPORT  = '1.1';
    const PRECHECK1_SERVICE_UP_3_PRODS  = '1.2';
    const PRECHECK1_SERVICE_EACH_3_PRODS = '1.3';
    const PRECHECK2_SERVICE_UP_3_PRODS  = '1.4';
    const PRECHECK2_SERVICE_EACH_3_PRODS = '1.5';
    const PACK_A_UP_3_ITEMS = '2.1';
    const PACK_A_EACH_3_ITEMS = '2.2';
    const PACK_B_UP_3_ITEMS = '2.3';
    const PACK_B_EACH_3_ITEMS = '2.4';
    const PACK_C_UP_3_ITEMS = '2.5';
    const PACK_C_EACH_3_ITEMS = '2.6';
    const SIMPLE_PLAN_BASIC = '3.1';
    const SIMPLE_PLAN_ADD_3_PRODS = '3.2';
    const SELECT_PLAN_REGISTRATION_REPORT_BASIC = '3.3';
    const SELECT_PLAN_REGISTRATION_REPORT_EACH_PROD = '3.4';
    const SELECT_PLAN_A_RATING = '3.5';
    const SELECT_PLAN_B_C_D_E = '3.6';
    const ADD_OPTION_EACH_PROD = '3.7';
    const REGISTER_BEFORE_DEADLINE = '3.8';
    const PRIOR_DEADLINE = '3.9';
    const EXTENDED_SERVICE_OUTSIDE_PERIOD = '3.10';
    const AMENDMENT_SERVICE = '3.11';
    const REGISTRATION_UP_3_PRODS = '4.1';
    const REGISTRATION_EACH_3_PRODS = '4.2';
    const REGISTRATION_REDUCTION_PROCEDURE = '4.3';
    const SENDING_CERT_REGIS = '4.4';
    const APP_ADDRESS_CHANGE_PROCEDURES = '4.5';
    const APP_NAME_CHANGE_PROCEDURE = '4.6';
    const REGISTRATION_TERM_CHANGE = '4.7';
    const CERT_SELECTION = '4.8';
    const PAYMENT_UP_3_PRODS = '5.1';
    const PAYMENT_EACH_1_CATEGORY = '5.2';
    const BANK_TRANSFER_HANDLING = '6.1';
    const MAILING_CERTIFICATE_REGISTRATION = '7.1';
    const UPDATE_SERVICE_UP_3_CATEGORY = '8.1';
    const UPDATE_SERVICE_EACH_CATEGORY = '8.2';
    const PAYMENT_SERVICE_UP_3_CATEGORY = '9.1';
    const PAYMENT_SERVICE_EACH_CATEGORY = '9.2';
    const CHANGE_ADDRESS_PROCEDURE = '10.1';
    const CHANGE_NAME_PROCEDURE = '10.2';
    const HISTORY_FREE_DEFAULT = '11.1';
    const REGIS_CERT_REISSUANCE_PROCEDURE = '12.1';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_type',
        'package_type',
        'base_price',
        'pof_1st_distinction_5yrs',
        'pof_1st_distinction_10yrs',
        'pof_2nd_distinction_5yrs',
        'pof_2nd_distinction_10yrs',
    ];
}
