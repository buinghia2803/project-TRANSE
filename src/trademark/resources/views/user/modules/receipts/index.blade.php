@extends('user.layouts.print')

@section('main-content')
    <!-- wrapper -->
    <div id="wrapper">

        <!-- main -->
        <div id="main">

            <!-- header -->
            <div id="header">
                <h1>{{ ($payerInfo->isPaymentCreditCard() ? __('labels.receipt.title_credit_card') : __('labels.receipt.title_bank_transfer')) }}</h1>
            </div>
            <!-- /header -->

            <div class="client_add">
                <span>{{ $payerInfo->prefecture->name ?? '' }} </span>
                <span>{{ $payerInfo->address_second ?? '' }} </span>
                <span>{{ $payerInfo->address_three ?? '' }} </span>
            </div>
            <div class="client_name">{{ $payerInfo->payer_name ?? '' }}</div>
            <div class="client_country">{{ __('labels.receipt.nation_name', [ 'attribute' => $payerInfo->nation->name ?? '' ]) }}</div>

            <div class="clearfix" style="margin-bottom: 3em;">

                <!-- info -->
                <table class="info">
                    <tr>
                        <th>{{ __('labels.receipt.reference_number') }}</th>
                        <td>{{ $trademark->reference_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.receipt.trademark_number') }}</th>
                        <td>{{ $trademark->trademark_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.receipt.name_trademark') }}</th>
                        <td>
                            @if($trademark->isTrademarkLetter())
                                {{ $trademark->name_trademark ?? '' }}
                            @else
                                <img src="{{ $trademark->getImageTradeMark() ?? '' }}" height="50" alt="">
                            @endif
                        </td>
                    </tr>
                </table>
                <!-- /info -->

                <!-- address -->
                <div class="address">
{{--                    {!! __('labels.receipt.ams_info') !!}--}}
                    @if(count($setting) > 0)
                        AMS
                        @foreach($setting as $item)
                            @if($item->key == SETTING_PUBLISHER_POSTAL_CODE) <p>{{$item->value}}</p> @endif
                            @if($item->key == SETTING_PUBLISHER_ADDRESS_FIRST)  <p>{{$item->value}}</p> @endif
                            @if($item->key == SETTING_PUBLISHER_ADDRESS_SECOND) <p>{{$item->value}}</p> @endif
                            @if($item->key == SETTING_PUBLISHER_TELL) <span>TEL: {{$item->value}} </span> @endif
                            @if($item->key == SETTING_PUBLISHER_TAX) <span>FAX: {{$item->value}}</span> @endif
                            @if($item->key == SETTING_PUBLISHER_REGISTRATION_NUMBER) <p>{{__('labels.trademark_enduser.register_number')}}: {{$item->value}}</p> @endif
                            <div class="stamp">
                                @if($item->key == SETTING_STAMP) <img src="{{ asset($item->value) }}"/> @endif
                            </div>
                        @endforeach
                    @endif
                </div>
                <!-- /address -->

            </div>

            <p>{{ __('labels.receipt.receipt_confirm') }}</p>

            <!-- receipt -->
            <table class="receipt">
                <tr>
                    <th>{{ __('labels.receipt.payment_amount') }}</th>
                    <td>&yen; {{ CommonHelper::formatPrice($payment->payment_amount ?? $payment->total_amount ?? 0) }}</td>
                </tr>
            </table>
            <!-- /receipt -->

            <!-- number & date -->
            <table class="receipt_date">
                <tr>
                    <th colspan="2">{{ __('labels.receipt.payment_number', [ 'attribute' => $payment->receipt_number ?? '' ]) }}</th>
                </tr>
                <tr>
                    <th>{{ __('labels.receipt.payment_date') }}</th>
                    <td>{{ CommonHelper::formatTime($payment->payment_date, 'Y年m月d日') }}</td>
                </tr>
                <tr>
                    <th>{{ __('labels.receipt.payment_type') }}</th>
                    <td>
                        {{ ($payerInfo->isPaymentCreditCard() ? __('labels.receipt.payment_type_credit') : __('labels.receipt.payment_type_bank')) }}<br/>
                        <span>
                            {{ __('labels.receipt.breakdown') }}<br/>
                            {{ __('labels.receipt.commission') }}&yen;{{ CommonHelper::formatPrice($payment->commission ?? 0) }}<br/>
                            @if($payment->commission == 0)
                                {{ __('labels.receipt.tax', ['attr' => $payment->tax ?? 0]) }}&yen;{{ CommonHelper::formatPrice($payment->tax ?? 0) }}
                            @else
                                {{ __('labels.receipt.tax', ['attr' => (number_format((float)$payment->tax/$payment->commission*100, 2, '.', ''))]) }}
                                &yen;
                                {{ CommonHelper::formatPrice($payment->tax ?? 0) }}
                            @endif
                        </span>
                    </td>
                </tr>
            </table>
            <!-- /number & date -->

        </div>
        <!-- /main -->

    </div>
    <!-- /wrapper -->
@endsection
