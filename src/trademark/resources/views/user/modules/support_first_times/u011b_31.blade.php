@extends('user.layouts.app')
@section('main-content')

    <!-- contents -->
    <div id="contents" class="normal">

        <h2>出願：お申込み</h2>

        <form id="form" action="{{ route('user.sft.proposal-ams.store') }}" methOd="POST">
            @csrf
            <input type="hidden" name="from_page" value="{{ U011B_31 }}" class="em30" />
            <h3>{{ __('labels.support_first_times.trademark.trademark_title') }}</h3>

            {{-- Trademark table --}}
            @include('user.components.trademark-table', [
                'table' => $trademarkTable
            ])

            <hr />

            <h3>{{ __('labels.support_first_times.category_product_name') }}</h3>

            <p>{{ __('labels.support_first_times.pls_select_product') }}</p>

            {{-- Start Proposal from AMS table  --}}
            @include('user.modules.support_first_times.partials._table_product_choose', [
                'products' => $products,
                'showColumnChoose' => true
            ])
            <div id="error-msg"></div>

            @if (session()->has('error'))
                <div class="notice mb15">
                    {{ session('error') }}
                </div>
            @endif
            {{-- End Proposal from AMS table  --}}

            <div id="u031pass-modal" class="modal fade" role="dialog">
                <div class="modal-dialog" style="min-width: 60%;min-height: 60%;">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                            <div class="content loaded">
                                <iframe src="{{route('user.apply-trademark.show-pass', [
                                 'id' => $id ?? 0,
                                 'from_page' => U011B_31
                                 ])}}" style="width: 100%; height: 70vh;" frameborder="0"></iframe></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="error-ajax" class="notice d-none"></div>

            <p>
                <input type="hidden" name="id" value="{{ $id }}">
                <button type="button" name="submitEntry" class="btn_f rollback_suggest_ai">
                    {{ __('labels.support_first_times.rollback_suggest_ai') }}
                </button>
            </p>

            <p>
                {{-- <input type="submit" value="過去に登録となった商品・サービス名を参照し追加を検討"
                onclick="window.open('u031past.html','subwin','width=640,height=640,scrollbars=yes');return false;" class="btn_f" /> --}}
                <button type="button" name="submitEntry" id="redirect_to_u031pass" class="btn_f show_product_list">
                    {{ __('labels.support_first_times.show_product_list') }}
                </button>
            </p>
                <p>
                    <a href="#!" data-route="{{ route('user.precheck.redirect-u031_edit_with_number') }}" class="btn_a" id="addProduct">{{ __('labels.support_first_times.add_product') }}</a>
                <br />
            <br />
            <span class="note">{{ __('labels.support_first_times.note_1') }}<br />
                {{ __('labels.support_first_times.note_2') }}<br />
                {{ __('labels.support_first_times.note_3') }}<br />
                {{ __('labels.support_first_times.note_4') }}<br />
                {{ __('labels.support_first_times.note_5') }}</span>
            </p>

            <p>{{ __('labels.support_first_times.note_6') }}<br />
                {{ __('labels.support_first_times.note_7') }}
            </p>

            <p>
                <button type="button" name="submitEntry" id="btn-redirect-u021c" class="btn_a redirectToU021c" data-route="{{ route('user.precheck.redirect-u021c')}}">
                  {{ __('labels.support_first_times.regis-by-trademark.title_h1') }}
                </button>
            </p>

            <hr />

            {{-- Choose pack --}}
            <h3>{{ __('labels.support_first_times.plan_selection') }}</h3>
            <table class="normal_b mb10">
                <tr>
                    <td style="width:34em;">
                        <input type="radio" name="pack" class="package_type" {{ $appTrademark && $appTrademark->pack == App\Models\AppTrademark::PACK_A ? 'checked' : '' }} value="1" id="package_a" />
                        <span id="name_package_a">{{ __('labels.support_first_times.pack_a') }}</span>{{ __('labels.support_first_times.up_to_3_prod') }}
                        <span id="price_package_a">{{ CommonHelper::formatPrice($pricePackage[0][0]['base_price'] ?? 0) }}</span>円
                        <br />
                        {{ __('labels.support_first_times.note_8') }}<br />
                        {{ __('labels.support_first_times.note_9') }}
                        <span id="price_product_add_pack_a">
                            {{ CommonHelper::formatPrice($pricePackage[1][0]['base_price'] ?? 0) }}
                        </span>
                        {{ __('labels.support_first_times.note_10') }}<br />
                        {{ __('labels.support_first_times.note_11') }}
                    </td>
                </tr>
                <tr>
                    <td style="width:34em;">
                        <input type="radio" name="pack" class="package_type" {{ $appTrademark && $appTrademark->pack == App\Models\AppTrademark::PACK_B ? 'checked' : '' }} value="2" id="package_b" />
                        <span id="name_package_b">{{ __('labels.support_first_times.pack_b') }}</span> {{ __('labels.support_first_times.up_to_3_prod') }}
                        <span id="price_package_b">{{ CommonHelper::formatPrice($pricePackage[0][1]['base_price'] ?? 0) }}</span>円
                        <br />
                        {{ __('labels.support_first_times.note_12') }}<br />
                        {{ __('labels.support_first_times.note_13') }}
                        <span id="price_product_add_pack_b">
                            {{ CommonHelper::formatPrice($pricePackage[1][1]['base_price'] ?? 0) }}
                        </span>
                        {{ __('labels.support_first_times.note_14') }}<br />
                        {{ __('labels.support_first_times.note_15') }}
                    </td>
                </tr>
                <tr>
                    <td style="width:34em;">
                        <input type="radio" name="pack" class="package_type" value="3" {{ $appTrademark && $appTrademark->pack == App\Models\AppTrademark::PACK_C ? 'checked' : ($appTrademark ? '' : 'checked') }} id="package_c" />
                        <span id="name_package_c">{{ __('labels.support_first_times.pack_c') }}</span> {{ __('labels.support_first_times.up_to_3_prod') }}
                        <span id="price_package_c">{{ CommonHelper::formatPrice($pricePackage[0][2]['base_price'] ?? 0) }}</span>円
                        <br />
                        {{ __('labels.support_first_times.note_16') }}<br />
                        {{ __('labels.support_first_times.note_17') }}
                        <span id="price_product_add_pack_c">
                            {{ CommonHelper::formatPrice($pricePackage[1][2]['base_price'] ?? 0) }}
                        </span>
                        {{ __('labels.support_first_times.note_18') }}<br />
                        {{ __('labels.support_first_times.note_19') }}
                    </td>
                </tr>
            </table>
            {{-- Choose pack --}}
            <hr />

            <h3>{{ __('labels.support_first_times.mailing_regis_cert.title') }}</h3>
            <p class="eol">
                <input type="checkbox" id="is_mailing_register_cert" {{ !empty($appTrademark) && $appTrademark->is_mailing_regis_cert ? 'checked': '' }} name="is_mailing_register_cert" />
                {{ __('labels.support_first_times.mailing_regis_cert.note_1') }}<br />
                <span class="note">{{ __('labels.support_first_times.mailing_regis_cert.title', [ 'attr' => CommonHelper::formatPrice($mailRegisterCert['cost_service_base'] ?? 0) ]) }}<br />
                    {{ __('labels.support_first_times.mailing_regis_cert.note_2') }}</span>
            </p>
            <hr />
            <h3>{{ __('labels.support_first_times.regis_period.title') }}</h3>

            <p class="eol">
                <input type="checkbox" name="period_registration" id="period_registration" {{ !empty($appTrademark) && $appTrademark->period_registration == 2 ? 'checked': '' }} />
                {{ __('labels.support_first_times.regis_period.regis_10_years') }}<br />
                <span class="note">
                    {{ __('labels.support_first_times.regis_period.note_1', [ 'attr' => CommonHelper::formatPrice(($registerTermChange->base_price  + $registerTermChange->base_price * $setting['value'] / 100) ?? 0)]) }}
                </span>
            </p>
            <hr />

            {{-- Start Trademark info --}}
            @include('user.components.trademark-info', [
                'nations' => $nations,
                'prefectures' => $prefectures,
                'trademarkInfos' => $trademarkInfos ?? null
            ])
            {{-- End Trademark info --}}

            <hr />

            {{-- Payer info --}}
            @include('user.components.payer-info', [
               'prefectures' => $prefectures ?? [],
               'nations' => $nations ?? [],
               'paymentFee' => $paymentFee ?? null,
               'payerInfo' => $payerInfo ?? null
            ])
            {{-- End Payer info --}}

            <hr />

            <ul class="footerBtn clearfix">
                <li>
                    <button name="submitEntry" type="submit" id="gonnaCommonPayment" value="GTCP" class="btn_e big redirect_to_common_payment">
                        {{ __('labels.support_first_times.apply_content') }}
                    </button>
                </li>
            </ul>
            @if(empty($appTrademark))
                <ul class="footerBtn clearfix">
                    <li>
                        <button id="register_precheck" type="button" value="" class="btn_b">
                            {{ __('labels.support_first_times.to_precheck') }}
                        </button>
                        <br>
                        {{ __('labels.support_first_times.note_20') }}
                    </li>
                </ul>
            @endif

            <ul class="btn_left eol">
                @if($routeCancel)
                    <li>
                        <a href="javascript:void(0)" id="stop_applying"
                            class="btn_a">{{ __('labels.support_first_times.stop_applying') }}</a>
                    </li>
                @endif
                <li>
                    <a href="javascript:void(0)" id="redirec_to_anken_top" class="btn_a">
                        {{ __('labels.precheck_n.btn_go_to_anken') }}
                    </a>
                </li>
            </ul>


            <!-- estimate box -->
            <div class="estimateBox">
            <input type="checkbox" id="cart" />
            <label class="button" for="cart">
                <span class="open">{{ __('labels.support_first_times.cart.see_est_amount') }}</span>
                <span class="close">{{ __('labels.support_first_times.cart.close_est_amount') }}</span>
            </label>

            <div class="estimateContents">
                <h3>{{ __('labels.support_first_times.cart.quoted_amount') }}</h3>
                <table class="normal_b">
                    <tr>
                        <td>
                            <span id="name_package"></span><br />
                            <span id="name_package_note"> {{ __('labels.support_first_times.cart.note_1') }}</span>
                        </td>
                        <td class="right"><span id="price_package">0</span>円</td>
                    </tr>
                    <tr>
                    <tr>
                        <td>{{ __('labels.support_first_times.addition') }} <span id="product_selected_count">0</span>{{ __('labels.support_first_times.cart.prod_name_3_prod') }} <span id="each_3_prod_pack"></span>円）</td>
                        <input type="hidden" name="reduce_number_distitions" value="">
                        <td class="right"><span id="price_product_add">0</span>円</td>
                    </tr>
                    <tr class="cost_bank_transfer_tr d-none">
                        <td class="em16">{{ __('labels.user_common_payment.bank_transfer_fee') }}</td>
                        <td class="right">
                            <span id="cost_bank_transfer_span">{{ CommonHelper::formatPrice($paymentFee['cost_service_base'] ?? 0 )}}</span>円
                        </td>
                    </tr>
                    <tr class="tr_change_5yrs_to_10yrs d-none">
                        <td class="em16">{{ __('labels.user_common_payment.change_5yrs_to_10yrs') }}</td>
                        <td class="right">
                            <span id="change_5yrs_to_10yrs"></span>円
                        </td>
                    </tr>
                    <tr class="d-none tr_mailing_regis_cert_el">
                        <td class="em16">{{ __('labels.support_first_times.cart.mailing_regis_cert') }}</td>
                        <td class="right">
                            <span id="mailing_regis_cert_el">{{ CommonHelper::formatPrice($mailRegisterCert['cost_service_base'] ?? 0) }}</span>円
                        </td>
                    </tr>
                <tr>
                        <th class="right">
                            <strong>
                                <span id="product_checked_count">0</span>
                                {{ __('labels.support_first_times.cart.10_prod_name_small_meter') }}
                            </strong>
                        </th>
                        <th class="right"><strong id="sub_total">0</strong>
                            <span style="font-size:1.2em; font-weight: bold;" >円</span>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="right">
                            <span class="breakdown-real-fee d-none">
                                {{ __('labels.support_first_times.cart.breakdown_real_fee') }}
                                <span id="commission">0</span>円
                                <br />
                                {{ __('labels.support_first_times.cart.consumption_tax') }}（{{ $setting->value ?? 10 }}％）
                                <span id="tax">0</span>円
                            </span>
                        </th>
                    </tr>
                    <tr>
                        <td>{{ __('labels.user_common_payment.app_fees') }}
                            <span id="sumDistintion">{{count($products)}}</span>区分<br />
                            <span id="one_division">0</span>区分
                            <span id="regis5Ys1">{{ CommonHelper::formatPrice($pricePackage[0][0]['pof_1st_distinction_5yrs'] ?? 0) }}</span>円+
                            <span id="regis5Ys2">{{ CommonHelper::formatPrice($pricePackage[0][0]['pof_2nd_distinction_5yrs'] ?? 0) }}</span>
                            x<span id="mDistintionPayment">{{ count($products) -1 }}</span>区分
                        </td>
                        <td class="right">
                            <span id="fee_submit_register">
                                {{ CommonHelper::formatPrice($pricePackage[0][2]['pof_1st_distinction_5yrs'] + $pricePackage[0][2]['pof_2nd_distinction_5yrs']) }}
                            </span>円
                            <input type="hidden" name="cost_print_application_one_distintion" value="">
                        </td>
                    </tr>
                    <tr id="tr_fee_submit_register_year">
                        <td>
                            {{ __('labels.support_first_times.cart.expense_patent_3_category') }}
                            <span id="sumDistintion2">{{count($products)}}</span>区分
                            <input type="hidden" name="sum_distintion" id="sum_distintion"
                                    value="{{count($products)}}">
                            <span
                                id="price_fee_submit_5_year">{{ CommonHelper::formatPrice($periodRegistration->pof_1st_distinction_5yrs) }}</span>
                            <input type="hidden" name="" id="price_fee_submit_5_year_old"
                                value="{{ $periodRegistration->pof_1st_distinction_5yrs }}">
                            <input type="hidden" name="" id="price_fee_submit_5_year_old2"
                                   value="{{ $periodRegistration->pof_2nd_distinction_5yrs }}">
                            <input type="hidden" disabled name="" id="price_fee_submit_10_year_old"
                                value="{{ $periodRegistration->pof_1st_distinction_10yrs }}">
                            円<br />
                            <input type="hidden" disabled name="" id="price_fee_submit_10_year_old2"
                                   value="{{ $periodRegistration->pof_2nd_distinction_10yrs }}">
                            <span id="text_5yrs_10yrs2">
                            {{ __('labels.support_first_times.cart.stamp_fee_5yrs') }}
                            </span>
                        </td>
                        <td class="right"><span
                                id="fee_submit_register_year">
                                {{ CommonHelper::formatPrice($countDistinct * $periodRegistration->pof_1st_distinction_5yrs) }}</span>円
                            <input type="text" name="" id="value_fee_submit_ole"
                                value="{{ $countDistinct * $periodRegistration->pof_1st_distinction_5yrs }}">
                        </td>
                    </tr>
                    <tr>
                        <th class="right">{{ __('labels.support_first_times.cart.total') }}</th>
                        <th class="right" nowrap><strong style="font-size:1.2em;"
                                id="total_amount">{{ CommonHelper::formatPrice($countDistinct * $periodRegistration->pof_1st_distinction_5yrs + ($pricePackage[0][2]['pof_1st_distinction_5yrs'] + $pricePackage[0][2]['pof_2nd_distinction_5yrs'])) }}</strong>
                                <strong>円</strong>
                        </th>
                    </tr>
                </table>
                <input type="hidden" name="trademark_id" id="trademark_id" value="{{ $trademark->id }}">
                <input type="hidden" name="cost_service_base" id="cost_service_base">
                <input type="hidden" name="cost_service_add_prod" id="cost_service_add_prod">
                <input type="hidden" name="subtotal" id="subtotal">
                <input type="hidden" name="commission" id="request_commission">
                <input type="hidden" name="tax" id="request_tax">
                <input type="hidden" name="cost_print_application_one_distintion" id="cost_print_application_one_distintion">
                <input type="hidden" name="cost_5_year_one_distintion" id="cost_5_year_one_distintion">
                <input type="hidden" name="cost_10_year_one_distintion" id="cost_10_year_one_distintion">
                <input type="hidden" name="total_amount" id="value_total_amount">
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="redirect_to">
                <p class="red mb10">{{ __('labels.support_first_times.cart.note_2') }}</p>

                <ul class="right list">
                    <li>
                        <button type="button" class="btn_a recalculation" >
                            {{ __('labels.support_first_times.recalculation') }}
                        </button>
                    </li>
                </ul>

                <ul class="right list">
                    <li>
                        <button type="submit" id="redirect_to_quote" name="submitEntry" class="btn_a" >
                            {{ __('labels.support_first_times.save_display_quotation') }}
                        </button>
                    </li>
                </ul>

                <ul class="footerBtn right clearfix">
                    <li>
                        <button type="submit" name="submitEntry" class="btn_e big redirect_to_common_payment" >
                            {{ __('labels.support_first_times.apply_content') }}
                        </button>
                    </li>
                </ul>

            </div><!-- /estimate contents -->

            </div><!-- /estimate box -->


        </form>

        </div><!-- /contents -->
        <style>
            .d-none {
                display: none;
            }
            .d-block {
                display: block;
            }
            .d-content {
                display: contents;
            }
            @media (max-width: 425px) {
            #register_precheck {
                padding: 0.5em 0.5em;
            }
        }
        </style>
@endsection

@section('script')
    @if(isset($appTrademark) && $appTrademark->status != App\Models\AppTrademark::STATUS_UNREGISTERED_SAVE)
        <script>
            $('#contents').find('input, textarea, select , button').prop('disabled', true);
            $('#contents').find('input, textarea, select , button').addClass('disabled');
            $('#contents').find('a').css('pointer-events' , 'none');
            $('#cart').prop('disabled', false);
        </script>
    @endif
    <script type="text/JavaScript">
        // Constant prepare data.
        const REGISTER_TRADEMARK_TEXT = 1
        let setting = @json($setting);
        let countDistinct = @json($countDistinct);
        let feeSubmit = @json($periodRegistration);
        let trademark_id = @json($trademark->id);
        let registerTermChange = @json($registerTermChange);
        let trademark = @json($trademark);
        let routeRegisterPrecheck = @json($routeRegisterPrecheck);
        let routeCancel = @json($routeCancel);
        let sft_id = @json($id);
        const pricePackage = @json($pricePackage);
        const redirectEntry = @json($redirectEntry);
        let products = @JSON($products);
        let arrayProductSelect = []
        let isChoiUser = []

        // Constants url
        const routeGetInfoUserAjax = "{{ route('user.get-info-user-ajax') }}";
        const urlSubmit = '{{ route('user.sft.create.session') }}';
        const support_U011_E007 = '{{ __('messages.general.support_U011_E007') }}';
        const nameRollback = '{{ __('labels.support_first_times.rollback_suggest_ai') }}'
        const confirmRedirectToApplyTradermark = '{{ __('labels.support_first_times.yes') }}'
        const redirectToPrecheck = '{{ __('labels.support_first_times.regis-by-trademark.title_h1') }}'
        const redirectToQuote = '{{ __('labels.support_first_times.save_display_quotation') }}'

        const NO = '{{ __('labels.support_first_times.no') }}';
        const YES = '{{ __('labels.support_first_times.yes') }}';
        const labelModal = '{{ __('labels.support_first_times.answer_1') }}';
        const stampFee5yrs = '{{ __('labels.support_first_times.cart.stamp_fee_5yrs') }}'
        const stampFee10yrs = '{{ __('labels.support_first_times.cart.stamp_fee_10yrs') }}'

        // Constants message.
        const errorMessageIsValidRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessageRequired = '{{ __('messages.registrant_information.Common_E001') }}';
        const errorMessageNameRegex = '{{ __('messages.registrant_information.Common_E016') }}';
        const errorMessageAddressRegex = '{{ __('messages.registrant_information.Common_E020') }}';
        const errorMessageIsValidInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
        const errorMessageIsValidInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
        const errorMessageInvalidCharacter = '{{ __('messages.common.errors.Register_U001_E006') }}';
        const errorMessageInvalidFormatFile = '{{ __('messages.common.errors.Common_E023') }}';
        const errorMessageInvalidCharacterRefer = '{{ __('messages.common.errors.support_U011_E002') }}';
        const errorMessageInvalidCharacterPayer = '{{ __('messages.common.errors.Common_E016') }}';
        const errorMessageInvalidCharacterPayerFurigana = '{{ __('messages.common.errors.Common_E018') }}';
        const errorMessageContentMaxLength = '{{ __('messages.common.errors.Common_E031') }}';
        const errorMessageTrademarkNameInvalid = '{{ __('messages.trademark_form_information.errors.trademark_name_invalid') }}';
        const errorMessageRegisterPrecheck = '{{ __('messages.support_first_time.support_U011_E008') }}'
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/support_first_times/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/support_first_times/js/sft-cart-product.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/support_first_times/js/u011b_31.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/common/js/precheck-redirect-to-u021c.js') }}"></script>
@endsection
