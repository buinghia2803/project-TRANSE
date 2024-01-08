@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">

        <h2>{!! __('labels.register_precheck.h2_register_precheck') !!} </h2>

        <form action="" method="POST" id="form" class="form-validate">
            @csrf
            <input type="hidden" name="from_page" value="{{ U021 }}">
            <input type="hidden" name="code" id="param-code" value="">
            <input type="hidden" name="id" value="{{ $preheckOld->id ?? '' }}">
            <h3>{{ __('labels.register_precheck.title_info_trademark') }}</h3>

            {{-- Trademark table --}}
            @include('user.components.trademark-table', [
                'table' => $trademarkTable
            ])

            <hr />
            <h3>{!! __('labels.register_precheck.check_service') !!}</h3>

            <p>{{ __('labels.register_precheck.please_check_service') }}</p>

            @include('user.modules.prechecks.register-precheck._select_radio_type_precheck', [
                'preheckOld' => $preheckOld,
                'typePrecheckSimple' => $typePrecheckSimple,
                'typePrecheckSelect' => $typePrecheckSelect,
                'dataFeeDefaultPrecheck' => $dataFeeDefaultPrecheck
            ])

            <p>{{ __('labels.precheck.detailed_report_note_3') }}<br /></p>

            @include('user.modules.prechecks.register-precheck._table_product_choose', [
                'mProductChoose' => $mProductChoose,
                'preheckOld' => $preheckOld,
                'precheckProductIdsOld' => $precheckProductIdsOld
            ])

            <hr />

            {{-- Payer info --}}
            @include('user.components.payer-info', [
               'prefectures' => $prefectures ?? [],
               'nations' => $nations ?? [],
               'paymentFee' => $paymentFee ?? null,
               'payerInfo' => $payerInfo ?? null
           ])
            {{-- End Payer info --}}

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.register_precheck.submit_form') }}"
                        class="btn_e big goToCommonPayment" data-code="{{ PAYMENT }}" /></li>
            </ul>
            <ul class="footerBtn clearfix">
                <li><input type="submit"
                        value="{{ __('labels.register_precheck.back_to') }}" data-code="{{ ANKEN_TOP }}"
                        class="btn_a saveGoToAnkenTop" /></li>
            </ul>
            <!-- estimate box -->
            @include('user.modules.prechecks._box_cart')

        </form>

    </div><!-- /contents -->
@endsection
@section('script')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const statusRegister = @json($statusRegister);
        const statusRegisterTrue = @json($statusRegisterTrue);
        const errorMessageIsValidRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessageIsValidRequiredRadioSelect = '{{ __('messages.profile_edit.validate.Common_E025') }}';
        const errorMessageIsValidEmailFormat = '{{ __('messages.profile_edit.validate.Common_E002') }}';
        const errorMessageIsValidInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
        const errorMessageIsValidInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
        const errorMessageIsValidatePayerName = '{{ __('messages.profile_edit.validate.Common_E016') }}';
        const messagePrecheckReportSuccess = '{{ __('messages.profile_edit.precheck_report_success') }}';
        const errorMessageIsValidatePayerNameFurigana = '{{ __('messages.profile_edit.validate.Common_E018') }}';
        const errorMessageIsFullWidthNameTrademark = '{{ __('messages.general.Register_U001_E006') }}';
        const nationJPId = '{{ NATION_JAPAN_ID }}';
        const typePrecheckDetailedReport = '{{ \App\Models\Precheck::TYPE_PRECHECK_DETAILED_REPORT }}';
        const routeAjaxGetInfoPayment = '{{ route('user.precheck.ajax-get-info-payment') }}';
        const routeGetInfoUserAjax = '{{ route('user.get-info-user-ajax') }}';
        const routeTop = '{{ route('user.top') }}';
        const typePrecheckSimple = @json($typePrecheckSimple);
        const labelPrecheckSimple = '{{ __('labels.precheck_simple') }}';
        const labelPrecheckGender = '{{ __('labels.precheck_gender') }}';
        const okLabel = '{{ __('labels.btn_ok') }}';
        const _ANKEN = @json(_ANKEN);
        const _QUOTES = @json(_QUOTES);
        const _PAYMENT = @json(_PAYMENT);
    </script>
    <script src="{{ asset('end-user/prechecks/register-precheck/validate.js') }}"></script>
    <script src="{{ asset('end-user/prechecks/register-precheck/index.js') }}"></script>
@endsection
