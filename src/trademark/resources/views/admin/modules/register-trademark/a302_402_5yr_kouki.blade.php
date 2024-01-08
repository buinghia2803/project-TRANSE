@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            @include('admin.components.includes.messages')
            <form action="{{ route('admin.registration.procedure-latter-period.document-post', $registerTrademark->id) }}"  method="POST" id="form">
                @csrf
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])

                <h3>{{ $registerTrademark->showTitlePageByType(A302_402_5YR_KOUKI) }}</h3>
                <dl class="w16em clearfix">
                    <dt>{{ __('labels.a402for_submit.document_name') }}</dt>
                    <dd>{{ __('labels.a302.dd_1') }}</dd>

                    <dt>{{ __('labels.a402for_submit.reference_number') }}</dt>
                    <dd class="trademark_number">{{ $trademark ? $trademark->trademark_number : '' }}</dd>

                    <dt>{{ __('labels.a402for_submit.filing_date') }}</dt>
                    <dd class="filing_date">{{ $registerTrademark->filing_date ? App\Helpers\CommonHelper::formatTime($registerTrademark->filing_date, 'Y年m月d日') : '' }}</dd>

                    <dt>{{ __('labels.a402for_submit.address') }}</dt>
                    <dd>{{ __('labels.a402for_submit.commissioner') }}</dd>

                    <dt>{{ __('labels.a402for_submit.register_number') }}</dt>
                    <dd class="register_number">{{ $registerTrademark->register_number ? __('labels.a402for_submit.no').$registerTrademark->register_number.__('labels.a402for_submit.issue') : ''}}</dd>

                    <dt>{{ __('labels.a402for_submit.total_distinctions') }}</dt>
                    <dd class="m_distintions_count">{{ $totalDistinctions }}</dd>
                </dl>

                <p>{{ __('labels.a402for_submit.trademark_owner') }}</p>
                <dl class="w16em clearfix">

                    <dt>{{ __('labels.a402hosoku01.agent_name') }}</dt>
                    <dd class="trademark_info_name">{{ $registerTrademark->trademark_info_name }}</dd>
                </dl>

                <p>{{ __('labels.a302.h4_2') }}</p>
                <dl class="w16em clearfix">

                    <dt>{{ __('labels.a402hosoku01.identification_number') }}</dt>
                    <dd class="identification_number">{{ $agent ? $agent->identification_number : '' }}</dd>

                    <dt>{{ __('labels.a402hosoku01.agent_name') }}</dt>
                    <dd class="agent_name">{{ $agent ? $agent->name : '' }}</dd>
                </dl>

                <p>{{ __('labels.a302.h4_3') }}</p>
                <dl class="w16em clearfix">
                    @if($agent && $agent->deposit_type == $depositTypePayment)
                        <dt class="no_14">{{ __('labels.a402hosoku01.designated_advance_payment') }}</dt>
                        <dd>&nbsp;</dd>
                    @endif

                    @if($agent && $agent->deposit_type == $depositTypeAdvence)
                        <dt class="deposit_account_number no_14_a">{{ __('labels.u205.deposit_account_number') }}</dt>
                        <dd>{{ $agent ? $agent->deposit_account_number : '' }}</dd>
                    @endif
                    <dt>{{ __('labels.u205.total_amount') }}</dt>
                    <dd class="total_amount">{{ \App\Helpers\CommonHelper::convertNumberToFullwidth($totalAmount) }}<dd>
                    @if($registerTrademark->trademark_info_id || $registerTrademark->id_register_trademark_choice)
                        <dt>{{ __('labels.a302.dt_10') }}</dt>
                        <dd>
                            <ul>
                                <li><label><input type="checkbox" name="is_register_change_info" value="{{ $isRegisChangeInfo }}" {{ $registerTrademark->is_register_change_info == $isRegisChangeInfo ? 'checked' : '' }} />{{ __('labels.a402hosoku01.note_msg_checkbox') }}</label></li>
                            </ul>
                        </dd>
                    @endif
                </dl>

                <ul class="footerBtn clearfix">
                    <li><input type="button" onclick="window.history.back()" value="{{ __('labels.back') }}" class="btn_a btn-back" /></li>
                    <li><input type="submit" value="{{ __('labels.a302.submit') }}" class="btn_b" />
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
