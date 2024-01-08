@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">

            <h3>{{ __('labels.payment_all.title_page') }}</h3>

            <form action="{{ route('admin.payment-check.all.search-condition') }}" method="POST" id="form">
                @csrf
                <!--table filters -->
                @include('admin.modules.payments.includes._filters_table', [
                    'dataSession' => $dataSession,
                    'conditionsAll' => $conditionsAll,
                    'conditions' => $conditions,
                    'conditionDate' => $conditionDate,
                    'searchFields' => $searchFields,
                ])

                @include('admin.modules.payments.includes._table_precheck_all', [
                        'payments' => $payments,
                        'typeCreditCard' => $typeCreditCard
                    ])
                <!-- table 支払い状況 -->

                <p class="eol">
                    <input type="submit" value="{{ __('labels.payment_all.export_csv') }}" class="btn_a {{ $payments->count() == 0 ? 'disabled-btn' : '' }}" id="btn-download-csv" {{ $payments->count() == 0 ? 'disabled' : '' }}/>
                    <input type="checkbox" name="csv" class="checkbox-download-csv" style="display: none"/>
                </p>
                <ul class="footerBtn clearfix">
                    @if(in_array(auth()->user()->role, [ROLE_OFFICE_MANAGER, ROLE_SUPERVISOR]))
                        <li><input type="button" onclick="window.location='{{ route('admin.payment-check.bank-transfer')}}'" value="{{ __('labels.payment_all.view_application') }}" class="btn_a btn-back"/></li>
                    @else
                        <li><input type="submit" onclick="window.location='{{ route('admin.payment-check.bank-transfer')}}'" value="{{ __('labels.payment_all.view_application') }}" class="btn_a btn-back"/></li>
                    @endif
                </ul>

            </form>

        </div><!-- /contents inner -->
    </div><!-- /contents -->
@endsection
@section('footerSection')
    <script>
        const params = @json($params);
        const searchFields = @json($searchFields);
        const conditionsAll = @json($conditionsAll);
        const conditions = @json($conditions);
        const conditionDate = @json($conditionDate);
        const routeDeletePayment = '{{ route('admin.payment-check.update-payment-ajax') }}';
        const URL_LIST_ANKEN = '{{ route('admin.search.application-list', [ 'filter' => 1 ]) }}';
        const errorMessageFormatDatetime = '{{ __('messages.general.Common_E006') }}';
        const errorMessageMaxLength = '{{ __('messages.general.Common_E031') }}';
        const RETURNED_UNPROCESSED = '{{ __('messages.general.returned_unprocessed') }}';
    </script>
    <script src="{{ asset('admin_assets/pages/payments/payment-all/filter.js') }}"></script>
    <script src="{{ asset('admin_assets/pages/payments/payment-all/index.js') }}"></script>
    @include('compoments.readonly', [ 'only' => [ROLE_OFFICE_MANAGER, ROLE_SUPERVISOR] ])
@endsection
@section('css')
    <style>
        .btn-back {
            display: inline-block;
            background: #cccccc;
            padding: 5px 2em;
            border: 1px solid #999999;
            border-radius: 5px;
            text-decoration: none;
            color: #000000;
            cursor: pointer;
            width: 202px;
        }
        .disabled-btn {
            cursor: not-allowed!important;
        }
        .disabled {
            pointer-events: none;
            cursor: not-allowed;
            opacity: 0.65;
            filter: alpha(opacity=65);
            -webkit-box-shadow: none;
            box-shadow: none;
            background: #d3cece;
            border: 1px solid;
        }
    </style>
@endsection

