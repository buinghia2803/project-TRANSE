@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        @include('admin.components.includes.messages')
        <h2>{{ __('labels.u210.title') }}</h2>
        <form action="{{ route('user.save_data_extension_period', ['id' => $trademark->id]) }}" method="post" id="form">
            @csrf
            @if (isset($registerTrademarkRenewals) && $registerTrademarkRenewals->count())
                <p class="red">{{ __('labels.u210.error_register_trademark') }}</p>
                @include('user.modules.extension-period.include.checkConditionAlert', [
                    'trademarkTable' => $trademarkTable,
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                ])
            @elseif($comparisonTrademarkResult->checkRegistrationOverdue())
                <p class="red">{{ __('labels.u210.error_comparison') }}</p>
                @include('user.modules.extension-period.include.checkConditionAlert', [
                    'trademarkTable' => $trademarkTable,
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                ])
            @else
                @include('user.modules.extension-period.include.checkConditionAlert', [
                    'trademarkTable' => $trademarkTable,
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                ])
                <hr />
                <h3>{{ __('labels.u210.text_1') }}</h3>
                <p>{{ __('labels.u210.text_2') }}</p>
                <ul class="eol">
                    <li class="fs12">
                        {{ __('labels.u210.text_3') }}{{ $comparisonTrademarkResult->parseResponseDeadline() }}
                    </li>
                    <li class="fs12">
                        <strong>{{ __('labels.u210.text_4') }}{{ $comparisonTrademarkResult->parseResponseDeadline(1) }}</strong>
                    </li>
                </ul>
                <hr />
                @include('user.components.payer-info', [
                    'prefectures' => $prefectures ?? [],
                    'nations' => $nations ?? [],
                    'paymentFee' => [
                        'cost_service_base' => $dataAjax['cost_bank_transfer'] * (1 + $setting->value / 100) ?? '',
                    ],
                    'payerInfo' => $paymentDraft->payerInfo ?? null,
                ])
                <ul class="footerBtn2 clearfix">
                    <li>
                        <input type="submit" value="{{ __('labels.u210.btn_2') }}" class="btn_e"
                            data-submit="{{ SUBMIT }}" />
                    </li>
                </ul>
            @endif
            <ul class="footerBtn2 clearfix">
                <li>
                    <input type="button" value="{{ __('labels.u210.btn_3') }}" class="btn_a" onclick="history.back()" />
                </li>
            </ul>
            <!-- estimate box -->
            @include('user.modules.extension-period.include.estimate-box')
            <!-- /estimate box -->
            <input type="hidden" name="payer_info_id"
                value="{{ isset($paymentDraft) && $paymentDraft ? $paymentDraft->payerInfo->id : '' }}">
            <input type="hidden" name="payment_id"
                value="{{ isset($paymentDraft) && $paymentDraft ? $paymentDraft->id : '' }}">
            <input type="hidden" name="submit_type">
            <input type="hidden" name="package_type" value="{{ $packageTypeCostService }}">
            <input type="hidden" name="responde_date"
                value="{{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline ?? '', 'Y-m-d') }}">
            <input type="hidden" name="from_page" value="{{ U210_ALERT_02 }}">
            @if (isset($registerTrademarkRenewals))
                @foreach ($registerTrademarkRenewals as $registerTrademarkRenewal)
                    <input type="hidden" name="register_trademark_renewal_ids[]"
                        value="{{ $registerTrademarkRenewal->id }}">
                @endforeach
            @endif
        </form>
    </div>
    <!-- /contents -->
@endsection
<style>
    .footerBtn2 {
        margin-bottom: 0px !important;
    }
</style>
@section('footerSection')
    <script>
        const countRegisterTrademarkRenewals = @json(isset($registerTrademarkRenewals) && $registerTrademarkRenewals->count() ?? 0);
        const checkResponseDeadline = @json($comparisonTrademarkResult->checkRegistrationOverdue());
        if (countRegisterTrademarkRenewals && checkResponseDeadline || checkResponseDeadline) {
            const form = $('#form').not('#form-logout');
            $('#estimateBox').find('input, textarea, select , button').prop('disabled', true);
        }
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const trademarkDocument = @json($trademarkDocuments);
        const messageContentModal = '{{ __('labels.u205.content_modal') }}';
        const routeAjax = @json($routeAjax);
        const dataCost = @json($dataAjax);
        const redirectToQuote = '{{ QUOTE }}'
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/extension-period/extension_period.js') }}"></script>
@endsection
