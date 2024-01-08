@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.u031b.title_page') }}</h2>
        @include('admin.components.includes.messages')

        <form
            action="{{ route('user.register-apply-trademark-after-search-post', $tradeMarkOld ? $tradeMarkOld->id : null) }}"
            method="POST" id="form" enctype="multipart/form-data">
            @csrf
            <p class="eol">{{ __('labels.u031b.description_page') }}</p>

            <input type="hidden" name="redirect_to" class="redirect_to" value=""/>

            @include('user.modules.common.form_trademark_info', [
                    'trademark' => $tradeMarkOld ?? (isset($oldData['trademark']) && $oldData['trademark'] ? $oldData['trademark'] : []),
                ])
            <hr/>

            {{-- Start Proposal from AMS table  --}}
            <h3>{{ __('labels.u031b.title_table_product_choose') }}</h3>
            @include('user.modules.app-trademark.u031b.includes._table_product_choose', [
               'products' => $products,
               'showColumnChoose' => true,
               'appTradeMarkProdsIsApplyIds' => $appTradeMarkProdsIsApplyIds ?? null
           ])

            <p>
                <input type="button" value="{{ __('labels.u031b.title_redirect_to_search_ai') }}"
                       class="btn_f redirectToU020b"/>
            </p>

            <p>
                <input type="button" name="submitEntry" id="redirect_to_u031pass"
                       value="{{ __('labels.support_first_times.show_product_list') }}" class="btn_f show_product_list">
            </p>

            <p><input type="button" value="{{ __('labels.u031b.title_redirect_to_u031_edit') }}"
                      class="btn_a redirectToU031Edit"/><br/>
                {!! __('labels.u031b.des_redirect_to_u031_edit') !!}
            </p>
            {!! __('labels.u031b.des_redirect_to_u031_edit_2') !!}
            <hr/>

            <!--choose pack-->
            @include('user.modules.app-trademark.u031b.includes._choose_pack', [
                'appTrademark' => $appTrademark ?? NULL,
                'pricePackage' => $pricePackage,
                'dataDefaultPackA' => $dataDefaultPackA,
                'dataDefaultPackB' => $dataDefaultPackB,
                'dataDefaultPackC' => $dataDefaultPackC,
                'packA' => $packA,
                'packB' => $packB,
                'packC' => $packC,
                'packSession' => $packSession
            ])

            <hr/>

            @include('user.modules.app-trademark.u031b.includes._certificate', [
                'appTrademark' => $appTrademark ?? NULL,
                'mailRegisterCert' => $mailRegisterCert,
                'setting' => $setting
            ])
            <hr/>

            @include('user.modules.app-trademark.u031b.includes._registration_time', [
                'appTrademark' => $appTrademark ?? NULL,
                'periodRegistration' => $periodRegistration,
                'setting' => $setting
            ])
            <hr/>

            {{-- Form trademark-info --}}
            @include('user.components.trademark-info', [
//                'information' => $information,
                'nations' => $nations,
                'prefectures' => $prefectures,
                'trademarkInfos' => $tradeMarkInfosOld ?? collect(session('oldRequest'))
            ])
            {{-- End Form trademark-info --}}
            {{-- Payer info --}}
            @include('user.components.payer-info', [
                'prefectures' => $prefectures ?? [],
                'nations' => $nations ?? [],
                'paymentFee' => $paymentFee ?? null,
                'payerInfo' => $payerInfoOld ?? collect(session('payerOldData'))
            ])
            {{-- End Payer info --}}
            <hr/>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.u031b.submit_to_payment') }}"
                           class="btn_e big submitRedirectToCommonPayment"/>
                </li>
            </ul>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.u031b.submit_to_u021') }}"
                           class="btn_b submitRedirectToU021"/><br/>{{ __('labels.u031b.des_submit_to_u021') }}
                </li>
            </ul>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.u031b.des_submit_to_ankentop') }}"
                           class="btn_a submitRedirectToU000AnkenTop"/>
                </li>
            </ul>

            @include('user.modules.app-trademark.u031b.includes._cart', [
                'pricePackage' => $pricePackage,
                'periodRegistration' => $periodRegistration,
                'paymentFee' => $paymentFee,
                'fees' => $fees
            ])

            @include('user.modules.app-trademark.u031b.includes.modal_u031past', ['id' => $tradeMarkOld->id ?? null])

            <!-- estimate box -->
        </form>
    </div><!-- /contents -->

@endsection
@section('css')
    <style>
        .jconfirm-title {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .jconfirm .jconfirm-content div {
            display: none;
        }
        #error-table-choose-prod {
            display: none;
        }
        .td-distinction {
            min-width: 60px;
            text-align: center!important;
        }
    </style>
@endsection
@section('script')
    <script type="text/JavaScript">
        const tradeMarkId = @json($id ?? '');
        const folderId = @json($myFolder ? $myFolder->id : '');
        let japanId = @json(NATION_JAPAN_ID);
        let setting = @json($setting);
        let feeSubmit = @json($periodRegistration);
        let pricePackage = @json($pricePackage);
        const routeGetInfoUserAjax = '{{ route('user.get-info-user-ajax') }}';
        const routeAjaxGetInfoPaymentU031b = '{{ route('user.get-info-payment-u031b-ajax') }}';
        const routeAjaxRedirectToU021FromU031b = '{{ route('user.apply-trademark-after-search.redirect-to-regis-precheck') }}';
        const routeAjaxRedirectToU031EditFromU031b = '{{ route('user.redirect-to-u031edit') }}';
        const routeAjaxRedirectToU020bEditFromU031b = '{{ route('user.apply-trademark-after-search.redirect-to-search-ai') }}';
        const packA = @json($packA);
        const packB = @json($packB);
        const packC = @json($packC);
        const labelPackA = '{{ __('labels.u031b.cost_service_base_packA') }}';
        const labelPackB = '{{ __('labels.u031b.cost_service_base_packB') }}';
        const labelPackC = '{{ __('labels.u031b.cost_service_base_packC') }}';
        const redirectToQuote = @json($redirectToQuote);
        const redirectToAnkenTop = @json($redirectToAnkenTop);
        const redirectToCommonPayment = @json($redirectToCommonPayment);
        const redirectToU021 = @json($redirectToU021);
        const Common_E025 = '{{ __('messages.common.errors.Common_E025') }}'

        const support_U011_E007 = '{{ __('messages.general.support_U011_E007') }}'
    </script>
    <link rel="stylesheet" href="{{ asset('common/css/simple-modal.css') }}">
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script src="{{ asset('end-user/app_trademark/u031b/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/app_trademark/u031b/index.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/app_trademark/u031b/redirect_page.js') }}"></script>
@endsection

