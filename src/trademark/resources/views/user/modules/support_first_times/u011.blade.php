@extends('user.layouts.app')
@section('css')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
@endsection
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.support_first_times.sft_apply') }}<br />{{ __('labels.support_first_times.prod_service_name_selection') }}
        </h2>
        <form id="form" action="{{ route('user.sft.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <p class="eol">{{ __('labels.support_first_times.pls_enter_trademark_info') }}<br />
                {{ __('labels.support_first_times.sft_apply') }}<br />
                <span class="red">*</span>{{ __('labels.support_first_times.item_marked_required') }}
            </p>
            <input type="hidden" name="from_page" value="{{ U011 }}" class="em30" />
            <dl class="w16em clearfix" id="">
                @include('user.modules.common.form_trademark_info', [
                    'trademark' => $trademark ?? (isset($oldData['trademark']) && $oldData['trademark'] ? $oldData['trademark'] : []),
                ])
                <input type="hidden" name="trademark_id" value="{{ $oldData['trademark']->id ?? 0 }}">
                <input type="hidden" name="sft_id" value="{{ $oldData['sft']->id ?? 0 }}">
                <div id="product_name_form">
                    <dt>{{ __('labels.support_first_times.u_think_about') }} <span class="red">*</span></dt>
                    @if(isset($oldData['product_names']) && $oldData['product_names'])
                        @foreach ($oldData['product_names'] as $prod)
                            <dd class="product_name">
                                <input type="text" name="product_names[]" class="em30" value="{{ $prod }}" />
                            </dd>
                        @endforeach
                    @elseif (count($productsSession) <= 0)
                        <dd class="product_name">
                            <input type="text" name="product_names[]" class="em30" />
                        </dd>
                        <dd class="product_name">
                            <input type="text" name="product_names[]" class="em30" />
                        </dd>
                        <dd class="product_name">
                            <input type="text" name="product_names[]" class="em30" /><br />
                        </dd>
                    @else
                        @foreach ($productsSession as $item)
                            <dd class="product_name">
                                <input type="text" name="product_names[]" value="{{ $item['name'] }}" class="em30" />
                            </dd>
                        @endforeach
                    @endif
                </div>
                <dd>
                    <a id="add_product_name"
                       class="cursor-pointer">+{{ __('labels.support_first_times.addition_prod_service') }}</a>
                </dd>
            </dl>
            <dl class="w16em clearfix">
                <dd>
                    {{ __('labels.support_first_times.attention_1') }}<br />
                    {{ __('labels.support_first_times.attention_2') }}<br />
                    {{ __('labels.support_first_times.attention_3') }}<br />
                    {{ __('labels.support_first_times.attention_4') }}<br />
                    {{ __('labels.support_first_times.attention_5') }}
                </dd>
            </dl>
            <hr />

            {{-- Payer info --}}
            @include('user.components.payer-info', [
               'prefectures' => $prefectures ?? (  []),
               'nations' => $nations ?? [],
               'paymentFee' => $paymentFee ?? null,
               'payerInfo' => $payerInfo ?? (isset($oldData['payerInfo']) && $oldData['payerInfo'] ?$oldData['payerInfo'] : null )
           ])
            {{-- End Payer info --}}

            <hr />
            <ul class="footerBtn clearfix">
                <li>
                    <input type="button" value="この内容で申込む" class="btn_e big btn_submit_payment" />
                </li>
            </ul>
            <ul class="footerBtn clearfix">
                <li>
                    <button type="button"
                            class="btn_a btn_save_temp btn_go_to_ankentop fs13">{{ __('labels.support_first_times.save_return') }}</button>
                </li>
            </ul>
            <!-- estimate box -->
            <div class="estimateBox">
                <input hidden="true" type="checkbox" id="cart" /><label class="button" for="cart"><span
                        class="open">{{ __('labels.support_first_times.view_quote') }}</span><span
                        class="close">{{ __('labels.support_first_times.close_quote') }}</span></label>
                <div class="estimateContents">

                    <h3>{{ __('labels.support_first_times.est_total_amount') }}</h3>
                    <table class="normal_b">
                        <tr class="cost_service_base">
                            <td class="em16">{{ __('labels.support_first_times.sft') }}</td>
                            <td class="right">
                                <span id="price_package">
                                    {{ CommonHelper::formatPrice($fees['cost_service_base'] ?? 0) }}</span>円
                                </span>
                                <input type="hidden" name="cost_service_base"
                                    value="{{ $fees['cost_service_base'] ?? 0 }}" />
                            </td>
                        </tr>
                        <tr class="cost_bank_transfer_tr d-none">
                            <td class="em16">{{ __('labels.user_common_payment.bank_transfer_fee') }}</td>
                            <td class="right">
                                <span id="cost_bank_transfer_span" > {{ CommonHelper::formatPrice($paymentFee['cost_service_base'] ?? 0) }}</span>円
                            </td>
                        </tr>
                        <tr>
                            <th class="right">{{ __('labels.support_first_times.total') }}</th>
                            <th class="right subtotal" nowrap>
                                <strong style="font-size:1.2em;" id="sub_total" >{{ CommonHelper::formatPrice($fees['subtotal'] ?? 0) }}</strong>
                                    <span style="font-size:1.2em; font-weight: bold;" >円</span>
                                <input type="hidden" name="subtotal" value="{{ $fees['subtotal'] ?? 0 }}" />
                            </th>
                        </tr>
                        <tr>
                            <th class="right taxt" colspan="2">
                                {{ __('labels.support_first_times.breakdown') }}
                                {{ __('labels.support_first_times.actual_fee') }} <span id="commission">{{ CommonHelper::formatPrice($fees['commission'] ?? 0) }} </span>円<br />
                                {{ __('labels.support_first_times.consumption_tax') }} （{{ ($fees['tax'] / $fees['commission']) * 100 }}％）　<span id="tax">{{ CommonHelper::formatPrice($fees['tax'] ?? 0) }}</span>円
                                <input type="hidden" name="commission" id="request_commission" value="{{ $fees['commission'] ?? 0 }}" />
                                <input type="hidden" name="tax" id="request_tax" value="{{ $fees['tax'] ?? 0 }}" />
                                <input type="hidden" name="pof_1st_distinction_5yrs"
                                    value="{{ $fees['pof_1st_distinction_5yrs'] ?? 0 }}" />
                                <input type="hidden" name="pof_1st_distinction_10yrs"
                                    value="{{ $fees['pof_1st_distinction_10yrs'] ?? 0 }}" />
                                <input type="hidden" name="pof_2nd_distinction_5yrs"
                                    value="{{ $fees['pof_2nd_distinction_5yrs'] ?? 0 }}" />
                                <input type="hidden" name="pof_2nd_distinction_10yrs"
                                    value="{{ $fees['pof_2nd_distinction_10yrs'] ?? 0 }}" />
                                <input type="hidden" name="payment_fee_commision" value="{{ $paymentFee['commission'] ?? 0 }}">
                                <input type="hidden" name="payment_fee_tax" value="{{ $paymentFee['tax'] ?? 0 }}">
                            </th>
                        </tr>
                    </table>
                    <p class="red mb10">{{ __('labels.support_first_times.attention_6') }}</p>
                    <ul class="right list">
                        <li>
                            <button type="button" class="btn_a recalculation">
                                {{ __('labels.support_first_times.recalculation') }}</button>
                        </li>
                    </ul>
                    <ul class="right list">
                        <li><button type="button"
                                class="btn_a btn_save_quote">{{ __('labels.support_first_times.save_display_quotation') }}</button>
                        </li>
                    </ul>
                    <ul class="footerBtn right clearfix">
                        <li>
                            <input type="button" value="{{ __('labels.support_first_times.apply_content') }}"
                                class="btn_e big btn_submit_payment" />
                        </li>
                    </ul>
                </div><!-- /estimate contents -->
            </div><!-- /estimate box -->
        </form>
    </div><!-- /contents -->
@endsection
@section('css')
    <style>
        .d-none {
            display: none;
        }

        .d-block {
            display: block;
        }
    </style>
@endsection
@section('script')
    <script type="text/JavaScript">
        // let callClear = true
        const feeSubmit = {}
        const pricePackage = []
        const action = '{{ route('user.sft.store') }}'
        const Common_E001 = '{{ __('messages.general.Common_E001') }}';
        const errorMessageIsValidInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
        const errorMessageIsValidInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
        const errorMessageInvalidCharacter = '{{ __('messages.common.errors.Register_U001_E006') }}';
        const errorMessageInvalidFormatFile = '{{ __('messages.common.errors.Common_E023') }}';
        const errorMessageInvalidCharacterRefer = '{{ __('messages.common.errors.support_U011_E002') }}';
        const errorMessageInvalidCharacterPayer = '{{ __('messages.common.errors.Common_E016') }}';
        const errorMessageInvalidCharacterPayerFurigana = '{{ __('messages.common.errors.Common_E018') }}';
        const errorMessageContentMaxLength255 = '{{ __('messages.general.max_length_255') }}';
        const errorMessageContentMaxLength25 = '{{ __('messages.general.max_length_25') }}';
        const routeGetInfoUserAjax = '{{ route('user.get-info-user-ajax') }}';
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/support_first_times/js/sft-cart-product.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/support_first_times/js/index.js') }}"></script>
@endsection
@yield('common-payer-info-script')
