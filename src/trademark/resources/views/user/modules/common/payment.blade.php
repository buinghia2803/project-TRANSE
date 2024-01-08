@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.user_common_payment.title') }} </h2>
        {!! \App\Helpers\FlashMessageHelper::getMessage(request()) !!}
        @if ($message = Session::get('success'))
            <div class="alert alert-success message-booking">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <p class="">{{ $message }}</p>
            </div>
        @endif
        @include('compoments.messages')
        @if($message = Session::get('message'))
            <div id="message_modal" class="modal fade show" role="dialog">
                <div class="modal-dialog" style="min-width: 80%;">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                            <div class="content loaded">
                                <span class="close">&times;</span>
                                <p>{{ $message }}</p>
                                <div class="d-flex justify-content-center">
                                    <button id="btn_ok" > <a href="{{ route('user.top')}}"> {{ __('labels.back') }} </a> </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <form id="payment_form" action="{{ route('user.payment.payment.store')}}" method="POST">
            @csrf

            <p>{{ __('messages.user_common_payment.attention') }}</p>
            @include('user.components.trademark-table', [
                'table' => $trademarkTable
            ])
            <!-- /info -->
            @if ($isShowTblProd)
                <h3>{{ __('labels.user_common_payment.select_service') }}</h3>
                @include('user.modules.support_first_times.partials._table_product_choose', [
                    'products' => $products,
                    'showColumnChoose' => false
                ])
            @else
                <h3>{{ __('labels.user_common_payment.product_service_think_about') }}</h3>
                @if (count($products))
                    <ul class="list_prod">
                        @foreach ($products as $prod)
                            @if ($prod)
                                <li class="default_li">{{ $prod ?? '' }}</li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            @endif

            <h3>{{ __('labels.user_common_payment.billed_amount') }}</h3>

            @include('user.modules.common.partials.form_payment', [
                'data' => $data,
                'contentPay' => $contentPay ?? [],
                'payerInfo' => $payerInfo ?? null,
            ])

            <input type="hidden" name="payment_type" value="{{ $data['payment_type'] ?? '' }}">
            <input type="hidden" name="from_page" value="{{ $data['from_page'] ?? '' }}">
            <input type="hidden" name="type" value="{{ $data['type'] ?? '' }}">
            <input type="hidden" name="target_id" value="{{ $data['target_id'] ?? '' }}">
            <input type="hidden" name="trademark_id" value="{{ $data['trademark_id'] ?? '' }}">
            <input type="hidden" name="payment_id" value="{{ $data['id'] ?? '' }}">
            <input type="hidden" name="support_first_time_id" value="{{ $data['sft']->id ?? '' }}">
            <input type="hidden" name="payer_info_id" value="{{ $data['payer_info_id'] ?? '' }}">
            <input type="hidden" name="type_trademark" value="{{ $data['type_trademark'] ?? '' }}">
            <input type="hidden" name="secret" value="{{ request()->__get('s') }}">

            <p>{{ __('messages.user_common_payment.attention_2') }}</p>
            <p class="red">{{ __('messages.user_common_payment.attention_3') }}</p>
            <p class="eol">
                <label>{{ __('labels.user_common_payment.confirmed') }}<span class="red">*</span>
                    <input type="checkbox" name="is_confirm"/>
                </label>
            </p>
            <ul class="footerBtn clearfix">
            <li><button type="button" id="back_to_previous" class="btn_a fs13" >{{ __('labels.back') }}</button></li>
            <li><button type="submit" class="btn_b" >{{ __('labels.decision') }}</button></li>
            </ul>
        </form>
    </div>
<!-- /contents -->
@endsection

@section('footerSection')
    <style>
        .default_li {
            list-style: disc;
        }
        .list_prod {
            margin-left: 1.75rem;
            margin-bottom: 1rem;
        }
    </style>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const routeTop = '{{ route('user.top') }}';
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E025') }}';
        const messagePaymentSuccess = '{!! __('messages.payment_success') !!}';
        const products = @json($products);
        const messageServer = @json($message);
        const routeReturnBack = '{{ URL::previous() }}'
        const routeBack = @json($routeBack);
        let idxQuestMark = routeReturnBack.indexOf('?');
        if(idxQuestMark < 0) {
            idxQuestMark = routeReturnBack.length
        }
        $("#back_to_previous").on("click", function (){
            // const urlSearchParams = new URLSearchParams(window.location.search);
            // const params = Object.fromEntries(urlSearchParams.entries());
            window.location.href =  routeBack
        });
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script src="{{ asset('end-user/common/js/common-payment.js') }}"></script>
@endsection
