@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        @include('compoments.messages')
        <h2>{{ __('labels.apply-trademark-with-number.title') }}</h2>
        <form id="form" action="{{ route('user.apply-trademark-register-create') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="trademark_id" value="{{ $id }}">
            <input type="hidden" name="cost_service_base" value="{{ $pricePackageA['base_price_multiplication_tax'] }}">
            <input type="hidden" name="cost_service_add_prod">
            <input type="hidden" name="cost_bank_transfer">
            <input type="hidden" name="subtotal">
            <input type="hidden" name="commission">
            <input type="hidden" name="tax">
            <input type="hidden" name="cost_print_application_one_distintion">
            <input type="hidden" name="cost_5_year_one_distintion">
            <input type="hidden" name="total_amount">
            <input type="hidden" name="total_distinction">
            <input type="hidden" name="sum_distintion">
            <p class="eol">{{ __('labels.apply-trademark-with-number.sub') }}</p>

            @include('user.modules.common.form_trademark_info', [
                'trademark' => $trademark ?? [],
            ])
            <hr />

            <h3>{{ __('labels.apply-trademark-with-number.title_table') }}</h3>
            <p><button type="button" class="btn_f" id="redirect_to_u031pass">{{ __('labels.apply-trademark-with-number.btn1') }}</button></p>
            <p>{{ __('labels.apply-trademark-with-number.title_table3') }}<br />
                {{ __('labels.apply-trademark-with-number.title_table4') }}</p>

            {{-- Table product_choose --}}
            @include('user.modules.prechecks.u031._table_product_choose', [
                'mProductChoose' => $mProductChoose ?? [],
                'distinctions' => $distinctions,
                'fromPage' => $fromPage,
                'appTrademarkProds' => $appTrademarkProds ?? []
            ])
            {{-- Table product_choose --}}
            <p class="eol"><input type="button" value="保存" class="btn_a save_draft redirec_to_anken_top"></p>

            <div id="u031pass-modal" class="modal fade" role="dialog">
                <div class="modal-dialog" style="min-width: 60%;min-height: 60%;">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                            <div class="content loaded">
                                <iframe src="{{route('user.apply-trademark.show-pass', [
                                 'id' => $id ?? 0,
                                 'from_page' => U031
                                 ])}}" style="width: 100%; height: 70vh;" frameborder="0"></iframe></div>
                        </div>
                    </div>
                </div>
            </div>

            <hr />

            {{-- Form trademark-info --}}
            @include('user.components.trademark-info', [
                'nations' => $nations,
                'prefectures' => $prefectures,
            ])
            {{-- End Form trademark-info --}}

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
                <li><input type="submit" value="{{ __('labels.apply-trademark-with-number.btn3') }}" class="btn_e big redirect_to_common_payment" /></li>
            </ul>

            <ul class="btn_left eol">
                <input type="button" value="{{ __('labels.apply-trademark-with-number.btn5') }}" class="btn_a redirec_to_anken_top" style="font-size: 1.3em;"/></li>
            </ul>

            <!-- estimate box -->
            <div class="estimateBox">
                <input type="checkbox" id="cart" /><label class="button" for="cart"><span
                        class="open">{{ __('labels.apply-trademark-with-number.open') }}</span><span class="close">{{ __('labels.apply-trademark-with-number.close') }}</span></label>

                <div class="estimateContents">
                    <h3>{{ __('labels.apply-trademark-with-number.cart') }}</h3>
                    <table class="normal_b">
                        <tr>
                            <td>{{ __('labels.apply-trademark-with-number.packA') }}</td>
                            <td class="right">{{ CommonHelper::formatPrice($pricePackageA['base_price_multiplication_tax']) }}円</td>
                        </tr>
                        <tr id="cost_prod_grow_four" class="d-none">
                            <td style="width: 34em;">{{ __('labels.apply-trademark-with-number.product_selected_count') }}<span class="product_selected_count"></span>
                                {{ __('labels.apply-trademark-with-number.product_selected_count1') }}{{ CommonHelper::formatPrice($pricePackageEachA['base_price_multiplication_tax']) }}円）</td>
                            <td class="right">
                                <span class="price_product_add">0</span>円<br />
                                <span class="price_product_add_not_tax d-none"></span>
                            </td>
                        </tr>
                        <tr class="cost_bank_transfer_tr d-none">
                            <td class="em16">{{ __('labels.user_common_payment.bank_transfer_fee') }}</td>
                            <td class="right">
                                <span id="cost_bank_transfer_span">0</span>円
                                <span id="cost_bank_transfer_span_not_tax" class="d-none">0</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="right"><strong><span class="totalChecked"></span>{{ __('labels.apply-trademark-with-number.total_checked') }}</strong></th>
                            <th class="right"><strong><span id="totalSub"></span>円</strong></th>
                        </tr>
                        <tr class="consumption_tax d-none">
                            <th class="right" colspan="2">
                                {{ __('labels.apply-trademark-with-number.total_sub_not_tax') }}<span id="totalSubNotTax">0</span>円<br />
                                <span>{{ __('labels.apply-trademark-with-number.tax') }}{{ $setting->value }}{{ __('labels.apply-trademark-with-number.tax1') }}<span id="priceTax"></span>円</span>
                            </th>
                        </tr>
                        <tr>
                            <td style="width: 34em;">
                                {{ __('labels.apply-trademark-with-number.total_dis') }}<span class="total-dis"></span>{{ __('labels.apply-trademark-with-number.total_dis1') }}<br />
                                <span id="one_division">0</span>{{ __('labels.apply-trademark-with-number.total_dis1') }}{{ CommonHelper::formatPrice($pricePackageA['pof_1st_distinction_5yrs']) }}円+{{ CommonHelper::formatPrice($pricePackageA['pof_2nd_distinction_5yrs']) }}円x<span class="total-dis-minus">0</span>{{ __('labels.apply-trademark-with-number.total_dis1') }}<br />
                                {{ __('labels.apply-trademark-with-number.pof_1st_distinction_5yr2') }}
                            </td>
                            <td class="right"><span class="totalDis5Yrs"></span>円<br /></td>
                        </tr>
                        {{-- <tr>
                            <td style="width: 34em;">
                                {{ __('labels.apply-trademark-with-number.pof_1st_distinction_5yr3') }}<span class="total-dis"></span>{{ __('labels.apply-trademark-with-number.pof_1st_distinction_5yr1') }}<br />{{ __('labels.apply-trademark-with-number.pof_1st_distinction_5yrs') }}{{ CommonHelper::formatPrice($print5yrs['pof_1st_distinction_5yrs']) }}円ｘ<span class="total-dis">{{ __('labels.apply-trademark-with-number.pof_1st_distinction_5yr1') }}
                            </td>
                            <td class="right"><span class="pricePrint">0</span>円<br /></td>
                        </tr> --}}
                        <tr>
                            <th class="right">{{ __('labels.apply-trademark-with-number.price_print') }}</th>
                            <th class="right" nowrap><strong class="fs12"><span id="total"></span>円</strong></th>
                        </tr>
                    </table>
                    <p class="red mb10">{{ __('labels.apply-trademark-with-number.price_print1') }}</p>

                    <ul class="right list">
                        <li><input type="button" value="{{ __('labels.apply-trademark-with-number.btn6') }}" class="btn_a" /></li>
                    </ul>

                    <ul class="right list">
                        <li><input type="submit" id="redirect_to_quote" value="{{ __('labels.apply-trademark-with-number.btn7') }}" class="btn_a" /></li>
                        <input type="hidden" name="redirect_to" value="">
                    </ul>

                    <ul class="footerBtn right clearfix">
                        <li><input type="submit" value="{{ __('labels.apply-trademark-with-number.btn3') }}" class="btn_e big redirect_to_common_payment" /></li>
                    </ul>
                </div>
                <!-- /estimate contents -->
            </div>
            <!-- /estimate box -->
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('css')
    <style>
        .boxes {
            position: relative;
        }
        .search-suggest {
            position: absolute;
            top: 35px;
            left: 0;
            right: 0;
            z-index: 1001;
            background: #fff;
            box-shadow: 0 10px 25px -5px rgb(31 31 31 / 50%);
            opacity: 1;
            visibility: visible;
            transition: .2s;
        }
        .item {
            margin: 2px 10px;
            cursor: pointer;
        }
        .item:hover {
            background-color: #ddd;
        }
        .no_item {
            display: flex;
            justify-content: center;
            margin: 10px;
        }
        .search-suggest {
            overflow: auto;
            max-height: 200px;
        }
        .prod_code {
            margin-top: 5px;
        }
        .prod_code:last-child {
            margin-bottom: 5px;
        }
        .customer_boxes {
            width: 100%;
            margin:0 0% 3px !important;
        }
    </style>
@endsection
@section('script')
    <script>
        const trademark_id = @json($id ?? 0);
        const sft_id = @json($sft->id ?? 0);
        const DISTINCTIONS = @json($distinctions);
        const SuggestURL = '{{ route('user.precheck.search-recommend') }}';
        const SuggestURLItem = '{{ route('user.precheck.search-recommend-item') }}';
        const pricePackageA = @json($pricePackageA);
        const products = @json($products);
        const pricePackageEachA = @json($pricePackageEachA);
        const priceCostBank = @json($priceCostBank);
        const print5yrs = @json($print5yrs);
        const BANK_TRANSFER = 2;
        const NATION_JAPAN_ID = @json(NATION_JAPAN_ID);
        const routePost = '{{ route('user.apply-trademark-register-create') }}';
        const Common_E025 = '{{ __('messages.general.Common_E025') }}'
        const Common_E001 = '{{ __('messages.general.Common_E001') }}'
        const support_U011_E001 = '{{ __('messages.general.support_U011_E001') }}'
        const duplicateProductName = '{{ __('messages.general.duplicate_product_name') }}'
        let id = @json($id);
        const SAVE_DRAFT = @json(SAVE_DRAFT);
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/prechecks/apply_trademark_register/index.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/prechecks/apply_trademark_register/redirect_to.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/common/js/precheck-redirect-to-u021c.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/prechecks/precheck/validate.js') }}"></script>
@endsection
