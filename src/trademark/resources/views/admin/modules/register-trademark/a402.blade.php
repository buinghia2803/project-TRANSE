@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">
            @include('admin.components.includes.messages')
            <form action="{{ route('admin.update.procedure.document.post', $id) }}" method="POST" id="form">
                @csrf

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])

                <h3>{{ $registerTrademark->showTitlePageByType(A402) }}</h3>

                <dl class="w16em clearfix">
                    <dt>{{ __('labels.a402.document_name') }}</dt>
                    <dd>{{ __('labels.a402.document_name_des') }}</dd>

                    <dt>{{ __('labels.a402.trademark_number') }}</dt>
                    <dd class="trademark_number">{{ $registerTrademark->trademark->trademark_number ?? '' }}</dd>

                    <dt>{{ __('labels.a402for_submit.filing_date') }}</dt>
                    <dd class="filing_date">{{ $registerTrademark->filing_date ? App\Helpers\CommonHelper::formatTime($registerTrademark->filing_date, 'Y年m月d日') : '' }}</dd>

                    <dt>{{ __('labels.a402for_submit.address') }}</dt>
                    <dd>{{ __('labels.a402for_submit.commissioner') }}</dd>

                    <dt>{{ __('labels.a402for_submit.register_number') }}</dt>
                    <dd class="register_number">{{ $registerTrademark->register_number ? __('labels.a402for_submit.no').$registerTrademark->register_number.__('labels.a402for_submit.issue') : ''}}</dd>

                    <dt>{{ __('labels.a402.name_distinct') }}</dt>
                    @php
                        $strDistinc = $distinctions->map(function($collection, $key) {
                            return __('labels.name_distinct', ['attr' => \App\Helpers\CommonHelper::convertNumberToFullwidth($key)]);
                        });
                    @endphp
                    <dd>{{ $strDistinc->implode('、') }}</dd>
                </dl>

                <p>{{ __('labels.a402for_submit.renewal_registration') }}</p>
                <dl class="w16em clearfix">

                    <dt>{{ __('labels.a402.address_or_whereabouts') }}</dt>
                    <dd>{{ $registerTrademark->showInfoAddress() }}</dd>

                    <dt>{{ __('labels.apply_trademark.trademark_info_name') }}</dt>
                    <dd class="trademark_info_name">{{ $registerTrademark->trademark_info_name }}</dd>

                    <dt>{{ __('labels.a402.agent') }}</dt>
                    <dd>&nbsp;</dd>

                    <dt>{{ __('labels.a402hosoku01.identification_number') }}</dt>
                    <dd class="identification_number">{{ $agent ? $agent->identification_number : '' }}</dd>
                </dl>

                <p>{{ __('labels.a402.agent_name') }}</p>
                <dl class="w16em clearfix">
                    <dt>{{ __('labels.a402.agent_name_2') }}</dt>
                    <dd class="agent_name">{{ $agent ? $agent->name : '' }}</dd>
                </dl>

                @if($flagChangeTenYearToFiveYear)
                    <dl class="w16em clearfix">
                        <dt class="no-15">{{ __('labels.a402.indication_of_payment') }}</dt>
                        <dd>{{ __('labels.a402.payment_type') }}<dd>
                    </dl>
                @endif

                <p class="no-16">{{ __('labels.a402.indication_of_registration_fee') }}</p>
                <dl class="w16em clearfix">
                    @if($agent->deposit_type == $depositTypePayment)
                        <dt class="no-17">{{ __('labels.a402.advance_payment') }}</dt>
                        <dd>&nbsp;</dd>
                        <dt class="no-18">{{ __('labels.u205.total_amount') }}</dt>
                        <dd class="total_mount">{{ \App\Helpers\CommonHelper::convertNumberToFullwidth($totalAmount) }}<dd>
                    @endif
                </dl>

                @if($agent->deposit_type == $depositTypeAdvence)
                    <dl class="w16em clearfix">
                        <dt class="deposit_account_number no-20">{{ __('labels.u205.deposit_account_number') }}</dt>
                        <dd>{{ $agent ? $agent->deposit_account_number : '' }}</dd>
                        <dt class="no-18">{{ __('labels.u205.total_amount') }}</dt>
                        <dd class="total_mount">{{ \App\Helpers\CommonHelper::convertNumberToFullwidth($totalAmount) }}<dd>
                    </dl>
                @endif

                @if($flagHasDocumentEdit)
                    <p class="no-22">{{ __('labels.a402.list_of_properties') }}</p>
                @endif
                <dl class="w16em eol clearfix">
                    @if($flagHasDocumentEdit)
                        <dt class="no-23">{{ __('labels.a402.property_name') }}</dt>
                        <dd>{{ __('labels.a402.power') }}</dd>

                        <dt class="no-24">{{ __('labels.a402.submitted_properties') }}</dt>
                        <dd>{{ __('labels.a402.follow_up') }}</dd>
                    @endif
                    @if($registerTrademark->trademark_info_id || $registerTrademark->id_register_trademark_choice)
                        <dt>{{ __('labels.a302.dt_10') }}</dt>
                        <dd>
                            <ul>
                                <li><label><input type="checkbox" name="is_register_change_info" value="{{ $isRegisChangeInfo }}" checked />{{ __('labels.a402hosoku01.note_msg_checkbox') }}</label></li>
                            </ul>
                        </dd>
                    @endif
                </dl>

                <ul class="footerBtn clearfix">
                    <li><input type="button" onclick="window.history.back()" value="{{ __('labels.back') }}" class="btn_a btn-back" /></li>
                    <li><input type="submit" value="{{ __('labels.a402.btn-submit') }}" class="btn_b" />
                    </li>
                </ul>
            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->
@endsection
@section('script')
    <script>
        $('.filing_date').text(convertToFull($('.filing_date').text()))
        function convertToFull(str) {
            return str.replace(/[!-~]/g, fullwidthChar => String.fromCharCode(fullwidthChar.charCodeAt(0) + 0xfee0));
        }
    </script>
    @if($registerTrademark->is_send == $isSendTrue)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_OFFICE_MANAGER] ])
@endsection
