@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <form id="form" action="{{ route('user.free-history.create') }}" method="POST">
            @csrf
            <input type="hidden" name="submit_type" value="">
            <input type="hidden" name="free_history_id" value="{{$freeHistory->id}}">

            @if($freeHistory->type !== TYPE_CUSTOMER_FEEDBACK_REQUIRED)
                <h2>{{__('labels.u000top.title')}}</h2>
            @else
                <h2>{{__('labels.free_history.labels_1')}}</h2>
            @endif

            <h3>{{__('labels.list_change_address.text_1')}}</h3>
            @include('user.components.trademark-table', [
              'table' => $trademarkTable
            ])

            <div class="js-scrollable w780 eol mt-5">
                <table class="normal_b">
                    <tr>
                        <th style="width:6em;">{{__('labels.application_detail_user.table.text_1')}}</th>
                        <th style="width:20em;">{{__('labels.admin_top.todo.redirect_page')}}</th>
                        <th style="width:6em;">{{__('labels.admin_top.todo.response_deadline')}}</th>
                        <th>{{__('labels.free_history.delivery_document')}}</th>
                        <th style="width:6em;">{{__('labels.payment_table.amount')}}</th>
                    </tr>
                    <tr>
                        <td>{{$freeHistory ? App\Helpers\CommonHelper::formatTime($freeHistory->XML_delivery_date, 'Y/m/d') : '' }}</td>
                        <td>{{ $freeHistory->status_name }}</td>
                        <td>{{$freeHistory ? App\Helpers\CommonHelper::formatTime($freeHistory->patent_response_deadline, 'Y/m/d') : '' }}</td>
                        <td>
                            @if(!empty($freeHistory->attachment) && count($freeHistory->attachment) > 0)
                                @foreach($freeHistory->attachment as $value)
                                    @if(!empty($value))
                                        @php $valueArray = explode('/', $value); @endphp
                                        <a href="{{ $value }}" target="_blank">{{ $valueArray[count($valueArray) - 1] ?? '' }}</a>
                                    @endif
                                @endforeach
                            @else

                            @endif
                        </td>
                        <td>
                            @if($freeHistory->is_check_amount == IS_CHECK_AMOUNT)
                                @if($freeHistory->amount_type != 3)
                                    {{ \CommonHelper::formatPrice($priceData['priceService'] ?? 0, '円') }}
                                @else
                                    0円
                                @endif
                            @else
                                0円
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            @if($freeHistory->type == TYPE_CUSTOMER_FEEDBACK_REQUIRED)
                <p class="eol">
                    {{__('labels.create_support_first_time.comment_AMS')}}：<br/>
                    <span class="white-space-pre-line">{{ $freeHistory->comment }}</span>
                </p>
            @endif

            <dl class="w16em clearfix">
                @if($freeHistory->type == TYPE_CUSTOMER_FEEDBACK_REQUIRED || $freeHistory->type == TYPE_REPORT_CUSTOMER_NO_AGENT_PROCEDURES)
                    <dt><h3><strong>{{__('labels.admin_top.notice_user.notice_response_deadline')}}</strong></h3></dt>
                    <dd>
                        <h3>
                            <strong>{{$freeHistory ? \App\Helpers\CommonHelper::formatTime($freeHistory->user_response_deadline) : ''}}</strong>
                        </h3>
                    </dd>
                @endif
            </dl>

            @if($freeHistory->type == TYPE_CUSTOMER_FEEDBACK_REQUIRED)
                <h4>{{__('labels.free_history.answer')}}</h4>
                <p class="eol content_answer_parent">
                    <textarea
                        class="content_answer middle_c @if($freeHistory->is_cancel == IS_CANCEL_TRUE) disabled @endif"
                        name="content_answer" @if($freeHistory->is_cancel == IS_CANCEL_TRUE) readonly @endif>{!! $freeHistory ? $freeHistory->content_answer : '' !!}</textarea>
                </p>
            @endif

            @if($freeHistory->is_check_amount == IS_CHECK_AMOUNT)
                @include('user.components.payer-info', [
                   'prefectures' => $prefectures ?? (  []),
                   'nations' => $nations ?? [],
                   'paymentFee' => $priceData['paymentFee'] ?? null,
                   'payerInfo' => $payerInfo ?? (isset($oldData['payerInfo']) && $oldData['payerInfo'] ? $oldData['payerInfo'] : null )
               ])
            @endif

            @if($freeHistory->is_cancel != IS_CANCEL_TRUE || $freeHistory->is_answer != IS_ANSWER_TRUE)
                @if($freeHistory->is_check_amount == IS_CHECK_AMOUNT)
                    <ul class="footerBtn clearfix">
                        <li>
                            <button type="submit" data-submit="{{ REDIRECT_TO_COMMON_PAYMENT }}" class="btn_b">{{__('labels.suggest_ai.button.next')}}</button>
                        </li>
                    </ul>
                @endif

                <ul class="footerBtn clearfix">
                    <li>
                        <button type="submit" data-submit="{{ REDIRECT_TO_ANKEN_TOP }}"
                            @if($freeHistory->is_cancel == IS_CANCEL_TRUE) disabled @endif
                            class="btn_b @if($freeHistory->is_cancel == IS_CANCEL_TRUE) disabled @endif"
                        >{{__('labels.apply-trademark-with-number.btn5')}}</button>
                    </li>
                </ul>

                <ul class="btn_left eol">
                    <li class="m-0"><a class="btn_a @if($freeHistory->is_cancel == IS_CANCEL_TRUE) disabled @endif" style="padding: 5px 2em !important;"
                           href="{{route('user.free-history.show-cancel', $freeHistory->id)}}">{{__('labels.free_history.back_to_u000_cancel')}}</a>
                    </li>
                </ul>
            @endif

            @if($freeHistory->is_check_amount == IS_CHECK_AMOUNT)
                @if($freeHistory->is_answer != IS_ANSWER_TRUE || $freeHistory->is_cancel != IS_CANCEL_TRUE)
                    @include('user.modules.free-history.partials.estimate-box',[
                        'priceData' => $priceData,
                    ])
                @endif
            @endif
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('script')
    @if($freeHistory->is_answer == IS_ANSWER_TRUE || $freeHistory->is_cancel == IS_CANCEL_TRUE)
        <script>disabledScreen();</script>
    @endif
    <script>
        const idNationJP = @json(NATION_JAPAN_ID);
        const setting = @json($setting);
        const priceData = @json($priceData);
        const BANK_TRANSFER = '{{ \App\Models\Payment::BANK_TRANSFER }}';
        const CREDIT_CARD = '{{ \App\Models\Payment::CREDIT_CARD }}';

        const errorMessageIsValidInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
        const errorMessageIsValidInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
        const errorMessageInvalidCharacter = '{{ __('messages.common.errors.Register_U001_E006') }}';
        const errorMessageInvalidFormatFile = '{{ __('messages.common.errors.Common_E023') }}';
        const errorMessageInvalidCharacterRefer = '{{ __('messages.common.errors.support_U011_E002') }}';
        const errorMessageInvalidCharacterPayer = '{{ __('messages.common.errors.Common_E016') }}';
        const errorMessageInvalidCharacterPayerFurigana = '{{ __('messages.common.errors.Common_E018') }}';
        const errorMessageContentMaxLength255 = '{{ __('messages.general.Common_E029') }}';
        const routeGetInfoUserAjax = '{{ route('user.get-info-user-ajax') }}';
        const errorMessageMaxLength = '{{__('messages.common.errors.Common_E026')}}';
        const redirectToQuote = '{{ REDIRECT_TO_COMMON_QUOTE }}'
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/free_history/index.js') }}"></script>
@endsection
