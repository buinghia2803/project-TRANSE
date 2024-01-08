@extends('user.layouts.print')

@section('main-content')
    <!-- wrapper -->
    <div id="wrapper">
        <!-- main -->
        <div id="main">
            <!-- header -->
            <div id="header">
                <!-- number & date -->
                <div class="clearfix">
                    <table class="date">
                        <tr>
                            <th>{{ __('labels.quote.payment.payment_number') }}</th>
                            <td class="num">{{ $payment->quote_number ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.quote.payment.payment_date') }}：</th>
                            <td>{{ CommonHelper::formatTime($payment->created_at ?? '', 'Y年m月d日') }}</td>
                        </tr>
                    </table>
                </div>
                <!-- /number & date -->
                <h1>{{ __('labels.quote.title') }}</h1>
            </div>
            <!-- /header -->

            <div class="client_add">
                {{ $payerInfo->prefecture->name ?? '' }}
                {{ $payerInfo->address_second ?? '' }}
                {{ $payerInfo->address_three ?? '' }}
            </div>
            <div class="client_name">{{ $payerInfo->payer_name }}</div>
            <div class="client_country">（{{ __('labels.quote.payment.payment_info_nation') }}：{{ $payerInfo->nation->name }}）</div>

            <div class="clearfix">
                <!-- info -->
                <table class="info">
                    <tr>
                        <th style="width:10em;">{{__('labels.quote.trademark.reference_number')}}</th>
                        <td nowrap>{{ $trademark->reference_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('labels.quote.trademark.trademark_number')}}</th>
                        <td nowrap>{{ $trademark->trademark_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('labels.quote.trademark.application_number')}}</th>
                        <td nowrap>{{ $trademark->application_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.quote.trademark.name_trademark') }}<br/>
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


            <p>{{ __('labels.quote.title_estimate_as_follows') }}</p>

            {{-- Common Payment Table --}}
{{--            @include('user.components.payment-table', [--}}
{{--                'payment' => $payment,--}}
{{--            ])--}}
            @include('user.modules.receipts.partials.form_payment', [
               'data' => $data,
               'contentPay' => $contentPay ?? [],
               'payerInfo' => $payerInfo ?? null,
           ])

            <p class="eol">{{ __('labels.quote.note_tax') }}</p>

            <p>
                <strong style="border-bottom:2px solid #000000;font-size:1.1em;">
                    {{ __('labels.quote.payment.quote_deadline', ['attribute' => CommonHelper::formatTime($quoteDeadline, 'Y年m月d日') ]) }}
                </strong>
            </p>

            @if($payment->type == PAYMENT_TYPE_1)
            <p>{{ __('labels.quote.note_service') }}</p>
            <div class="page_feed">
                <h2>{{ __('labels.quote.schedule') }}：</h2>

                <h2>{{ __('labels.quote.trademark.trademark_title') }}</h2>
                <table class="tm_info">
                    <tr>
                        <th>{{ __('labels.quote.trademark.form_register_trademark') }}</th>
                        <td>
                            @if($trademark->type_trademark == TYPE_TRADEMARK_CHARACTERS)
                                {{__('labels.form_trademark_information.type_letter')}}
                            @else
                                {{__('labels.form_trademark_information.type_other')}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.quote.trademark.name_trademark') }}</th>
                        <td>
                            @if($trademark->isTrademarkLetter())
                                {{ $trademark->name_trademark ?? '' }}
                            @else
                                <img src="{{ $trademark->getImageTradeMark() ?? '' }}" height="50">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.quote.trademark.reference_number') }}</th>
                        <td>{{ $trademark->reference_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.quote.trademark.trademark_number') }}</th>
                        <td>{{ $trademark->trademark_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.quote.trademark.application_number') }}</th>
                        <td>{{ $trademark->application_number ?? '' }}</td>
                    </tr>
                </table>

                <h2>【{{ __('labels.quote.product_service_name') }}】</h2>
                <table class="detail2 eol">
                    <tr>
                        <th class="w10">{{ __('labels.quote.difference') }}</th>
                        <th>{{ __('labels.quote.product_service_name') }}</th>
                    </tr>

                    @php $totalProduct = 0; @endphp
                    @foreach($productGroup as $item)
                        <tr>
                            <td rowspan="{{ count($item['products'])+1 }}" class="center">
                                {{__('labels.support_first_times.No')}}{{ $item['distinction_name'] ?? '' }}{{__('labels.support_first_times.kind')}}
                            </td>
                            <td hidden></td>
                        </tr>
                        @foreach($item['products'] as $product)
                            @php $totalProduct++; @endphp
                            <tr>
                                <td class="item">{{ $product->name ?? '' }}</td>
                            </tr>
                        @endforeach
                    @endforeach

                    <tr>
                        <td colspan="2" class="right">{{ __('labels.invoice.product_info.distinction_total') }}：{{ count($productGroup) }} {{ __('labels.invoice.product_info.product_total') }}：{{ $totalProduct }}</td>
                    </tr>
                </table>
            </div><!-- feed -->
            @endif
        </div>
        <!-- /main -->
    </div>
    <!-- /wrapper -->
@endsection
