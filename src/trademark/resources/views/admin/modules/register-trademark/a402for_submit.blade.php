@if($registerTrademark)
<div id="a402for_submit">
    <span>{{ __('labels.a402for_submit.document_name') }}</span>{{ __('labels.a402for_submit.document_name_text') }}<br>
    <span>{{ __('labels.a402for_submit.reference_number') }}</span>{{ $registerTrademark->trademark ? $registerTrademark->trademark->trademark_number : '' }}<br>
    <span>{{ __('labels.a402for_submit.filing_date') }}</span><span class="filing_date">{{ $registerTrademark->filing_date ? App\Helpers\CommonHelper::formatTime($registerTrademark->filing_date, 'Y年m月d日') : '' }}</span><br>
    <span>{{ __('labels.a402for_submit.address') }}</span>{{ __('labels.a402for_submit.commissioner') }}<br>
    <span>{{ __('labels.a402for_submit.register_number') }}</span>{{ $registerTrademark->register_number ? __('labels.a402for_submit.no').$registerTrademark->register_number.__('labels.a402for_submit.issue') : ''}}<br>
    <span>{{ __('labels.a402for_submit.renewal_registration') }}</span><br>
    <span>{{ __('labels.a402for_submit.address_or_whereabouts') }}</span>{{ $registerTrademark->showInfoAddress() }}<br>
    <span>{{ __('labels.a402for_submit.trademark_info_name') }}</span>{{ $registerTrademark->trademark_info_name }}<br>
    <span>{{ __('labels.a402for_submit.agent') }}</span><br>
    <span>{{ __('labels.a402for_submit.identification_number') }}</span>{{ $agent ? $agent->identification_number : '' }}<br>
    <span>{{ __('labels.a402for_submit.patent_attorney') }}</span><br>
    <span>{{ __('labels.a402for_submit.agent_name') }}</span>{{ $agent ? $agent->name : '' }}<br>

    <span>{{ __('labels.a402for_submit.registration_fee') }}</span><br>
    @if($agent && $agent->deposit_type == \App\Models\Agent::DEPOSIT_TYPE_CREDIT)
        <span>{{ __('labels.a402for_submit.advance_payment') }}</span><br>
        <span class="15">{{ __('labels.a402for_submit.total_amount') }}</span>{{ \App\Helpers\CommonHelper::convertNumberToFullwidth($totalAmount) }}<br>
    @elseif($agent && $agent->deposit_type == \App\Models\Agent::DEPOSIT_TYPE_ADVENCE)
        <span>{{ __('labels.a402for_submit.deposit_account_number') }}</span>{{ $agent ? $agent->deposit_account_number : '' }}<br>
        <span class="15">{{ __('labels.a402for_submit.total_amount') }}</span>{{ \App\Helpers\CommonHelper::convertNumberToFullwidth($totalAmount) }}<br>
    @endif
</div>
    <script>
        document.querySelector('.filing_date').innerHTML = convertToFull(document.querySelector('.filing_date').outerText)
        function convertToFull(str) {
            return str.replace(/[!-~]/g, fullwidthChar => String.fromCharCode(fullwidthChar.charCodeAt(0) + 0xfee0));
        }
    </script>
@endif

