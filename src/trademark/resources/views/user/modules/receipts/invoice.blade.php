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
                            <th>{{ __('labels.invoice.payment_number') }}</th>
                            <td class="num">{{ $payment->invoice_number ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.invoice.payment_date') }}</th>
                            <td>{{ CommonHelper::formatTime($payment->payment_date, 'Y年m月d日') }}</td>
                        </tr>
                    </table>
                </div>
                <!-- /number & date -->
                <h1>{{ __('labels.invoice.title') }}</h1>
            </div>
            <!-- /header -->

            <div class="client_add">
                {{ $payerInfo->prefecture->name ?? '' }}
                {{ $payerInfo->address_second ?? '' }}
                {{ $payerInfo->address_three ?? '' }}
            </div>
            <div class="client_name">{{ $payerInfo->payer_name ?? '' }}</div>
            <div class="client_country">{{ __('labels.invoice.nation_name', [ 'attribute' => $payerInfo->nation->name ?? '' ]) }}</div>

            <div class="clearfix">
                <!-- info -->
                <table class="info">
                    <tr>
                        <th style="width:10em;">{{ __('labels.invoice.reference_number') }}</th>
                        <td nowrap>{{ $trademark->reference_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.invoice.trademark_number') }}</th>
                        <td nowrap>{{ $trademark->trademark_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.invoice.application_number') }}</th>
                        <td nowrap>@if(!empty($trademark->application_number)){{ __('labels.invoice.application_number_value', [ 'attribute' => $trademark->application_number ?? '' ]) }}@endif </td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.invoice.name_trademark') }}</th>
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

            <p>{{ __('labels.invoice.request') }}</p>

            {{-- Common Payment Table --}}
{{--            @include('user.components.payment-table', [--}}
{{--                'payment' => $payment,--}}
{{--            ])--}}
            @include('user.modules.receipts.partials.form_payment', [
             'data' => $data ?? [],
             'contentPay' => $contentPay ?? [],
             'payerInfo' => $payerInfo ?? null,
            ])

            <p class="eol">{{ __('labels.invoice.tax_note') }}</p>

            @if($payerInfo->isPaymentBankTransfer())
                <p>
                    <strong style="border-bottom:2px solid #000000;font-size:1.1em;">
                        {{ __('labels.invoice.invoice_deadline', [ 'attribute' => CommonHelper::formatTime($invoiceDeadline, 'Y年m月d日') ]) }}
                    </strong>
                    <br/>
                    {{ __('labels.invoice.invoice_deadline_desc') }}
                </p>
            @endif

            <!-- footer -->
            <div id="footer" class="clearfix">
                @if($payerInfo->isPaymentBankTransfer())
                    <h3>{{ __('labels.invoice.note.title') }}</h3>
                    <ul class="note">
                        <li>{!! __('labels.invoice.note.desc_1') !!}</li>
                        <li>{!! __('labels.invoice.note.desc_2') !!}</li>
                        <li>{!! __('labels.invoice.note.desc_3') !!}</li>
                    </ul>

                    <p>{{ __('labels.invoice.confirm') }}</p>

                    <!-- payee -->
                    <div class="payee">
                        {{--<p>＊＊＊＊＊＊銀行　＊＊＊＊支店　普通　　*******</p>--}}
{{--                        <p>{{ __('labels.invoice.payee.number') }}</p>--}}
{{--                        <dl>--}}
{{--                            <dt>{{ __('labels.invoice.payee.title') }}</dt>--}}
{{--                            <dd>{!! __('labels.invoice.payee.desc') !!}</dd>--}}
{{--                        </dl>--}}
                        @if(count($setting) > 0)
                            @foreach($setting as $item)
                                @if($item->key == SETTING_BANK_INFORMATION){!! $item->value !!} @endif
                            @endforeach
                        @endif
                    </div>
                    <!-- /payee -->
                @endif
                @if($payment->type == PAYMENT_TYPE_1)
                <div class="page_feed">
                    <h2>{{ __('labels.invoice.schedule') }}</h2>

                    <h2>{{ __('labels.invoice.trademark_info.title') }}</h2>
                    <table class="tm_info">
                        <tr>
                            <th>{{ __('labels.invoice.trademark_info.type_trademark') }}</th>
                            <td>{{ $trademark->isTrademarkLetter() ? __('labels.invoice.trademark_info.type_trademark_text') : __('labels.invoice.trademark_info.type_trademark_image') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.invoice.trademark_info.name_trademark') }}</th>
                            <td>
                                @if($trademark->isTrademarkLetter())
                                    {{ $trademark->name_trademark ?? '' }}
                                @else
                                    <img src="{{ $trademark->getImageTradeMark() ?? '' }}" height="50">
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.invoice.trademark_info.reference_number') }}</th>
                            <td>{{ $trademark->reference_number ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.invoice.trademark_info.trademark_number') }}</th>
                            <td>{{ $trademark->trademark_number ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.invoice.trademark_info.application_number') }}</th>
                            <td>@if($trademark->application_number){{ __('labels.invoice.application_number_value', [ 'attribute' => $trademark->application_number ?? '' ]) }}@endif</td>
                        </tr>
                    </table>

                    <h2>{{ __('labels.invoice.product_info.title') }}</h2>
                    <table class="detail2 eol">
                        <tr>
                            <th class="w10">{{ __('labels.invoice.product_info.distinction') }}</th>
                            <th>{{ __('labels.invoice.product_info.product_name') }}</th>
                        </tr>

                        @php $totalProduct = 0; @endphp
                        @foreach($productGroup as $item)
                            <tr>
                                <td rowspan="{{ count($item['products'])+1 }}" class="center">第{{ $item['distinction_name'] ?? '' }}類</td>
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
                            <td colspan="2" class="right">{{ __('labels.invoice.product_info.distinction_total') }}：{{ count($productGroup) }}　{{ __('labels.invoice.product_info.product_total') }}：{{ $totalProduct }}</td>
                        </tr>
                    </table>
                </div><!-- feed -->
                @endif
            </div>
            <!-- /footer -->
        </div>
        <!-- /main -->
    </div>
    <!-- /wrapper -->
@endsection
