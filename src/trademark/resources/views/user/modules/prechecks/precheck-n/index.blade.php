@extends('user.layouts.app')
@section('main-content')
<!-- contents -->
<!-- contents -->
<div id="contents" class="normal">

    <h2>{{ __('labels.precheck_n.title_h2') }}<br />
        {{ __('labels.precheck_n.title_h2_note') }}</h2>

    <form id="form" class="form-validate" action="{{ route('user.precheck.register-time-n', [
        'id' => $trademark->id,
        'precheck_id' => $precheckOld->id]) }}" method="POST">
        @csrf
        <input type="hidden" name="from_page" value="{{ U021N }}">
        <input type="hidden" name="code" id="param-code" value="">
        <h3>{{ __('labels.list_change_address.text_1') }}</h3>
        {{-- Trademark table --}}
        @include('user.components.trademark-table', [
            'table' => $trademarkTable
        ])

        <hr />
        <h3>{{ __('labels.precheck_n.check_service') }}</h3>
        <p>{{ __('labels.precheck_n.check_service_note') }}</p>

        @include('user.modules.prechecks.precheck-n._radio_type_precheck', [
            'data' => $dataPriceBasicTax,
            'precheck' => $precheckNext ?? $precheckOld
        ])

        @include('user.modules.prechecks.precheck-n._table_precheck_n', [
            'infoPrecheckTable' => $infoPrecheckTable,
            'precheck' => $precheckOld,
            'precheckBefore' => $precheckBefore,
            'precheckNext' => $precheckNext,
        ])

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
            <li><input type="submit" value="{{ __('labels.precheck_n.btn_go_to_payment') }}" class="btn_e big goToCommonPayment" /></li>
        </ul>

        <ul class="btn_left eol">
            <li><input type="submit" value="{{ __('labels.precheck_n.btn_go_to_anken') }}" class="btn_a saveGoToAnkenTop" /></li>
        </ul>

        @include('user.modules.prechecks._box_cart')

    </form>

</div><!-- /contents -->
@endsection
@section('script')
<script src="{{ asset('common/js/validate.js') }}"></script>
<script>
    const errorMessageIsValidRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
    const errorMessageIsValidRequiredRadioSelect = '{{ __('messages.profile_edit.validate.Common_E025') }}';
    const errorMessageIsValidEmailFormat = '{{ __('messages.profile_edit.validate.Common_E002') }}';
    const errorMessageIsValidInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
    const errorMessageIsValidInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
    const errorMessageIsValidatePayerName = '{{ __('messages.profile_edit.validate.Common_E016') }}';
    const errorMessageIsValidatePayerNameFurigana = '{{ __('messages.profile_edit.validate.Common_E018') }}';
    const nationJPId  = '{{ NATION_JAPAN_ID }}';
    const typePrecheckDetailedReport  = '{{ \App\Models\Precheck::TYPE_PRECHECK_DETAILED_REPORT }}';
    const routeGetInfoUserAjax = '{{ route('user.get-info-user-ajax') }}';
    const routeAjaxGetInfoPayment = '{{ route('user.precheck.ajax-get-info-payment') }}';
    const typePrecheckSimple = @json($typePrecheckSimple);
    const labelPrecheckSimple = '{{ __('labels.precheck_simple') }}';
    const labelPrecheckGender = '{{ __('labels.precheck_gender') }}';
    const _ANKEN = @json(_ANKEN);
    const _QUOTES = @json(_QUOTES);
    const _PAYMENT = @json(_PAYMENT);
</script>
<script src="{{ asset('end-user/prechecks/precheck-n/validate.js') }}"></script>
<script src="{{ asset('common/js/checkbox-table.js') }}"></script>
<script src="{{ asset('end-user/prechecks/precheck-n/index.js') }}"></script>
@if(!$flugEditPrecheck)
    <script src="{{ asset('end-user/common/js/disabled_page.js') }}"></script>
@endif
@endsection
@section('css')
    <style>
        @media screen and (max-width: 375px) {
            .copyInfoContactOfUser {
                margin-bottom: 15px;
            }

        }
    </style>
@endsection
