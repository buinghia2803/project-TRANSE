<ul class="eol">
    <li class="mb10">
        <label>
            <input type="radio" name="type_precheck" class="type_precheck type_precheck_1"
                   @if($precheck && $precheck->type_precheck == \App\Models\Precheck::TYPE_PRECHECK_SIMPLE_REPORT) checked @endif
                   @if(old('type_precheck') == \App\Models\Precheck::TYPE_PRECHECK_SIMPLE_REPORT) checked @endif
                   value="{{ \App\Models\Precheck::TYPE_PRECHECK_SIMPLE_REPORT }}" /> {{ __('labels.precheck.simple_report_text') }}
        </label>
        <br />{{ __('messages.precheck.precheck_u021n_note_1') }}<br />{{ __('messages.precheck.precheck_u021n_note_2') }} {{ number_format($data[0]['cost_service_base']) }}{{ __('messages.precheck.precheck_u021n_note_3') }}{{ number_format($data[1]['cost_service_base']) }} {{ __('labels.円') }}
    </li>
    <li>
        <label>
            <input type="radio" name="type_precheck" class="type_precheck type_precheck_2"
                   @if($precheck && $precheck->type_precheck == \App\Models\Precheck::TYPE_PRECHECK_DETAILED_REPORT) checked @endif
                   @if(old('type_precheck') == \App\Models\Precheck::TYPE_PRECHECK_DETAILED_REPORT) checked @endif
                   value="{{ \App\Models\Precheck::TYPE_PRECHECK_DETAILED_REPORT }}" /> {{ __('labels.precheck.detail_report_text') }}
        </label>
        <br />{{ __('messages.precheck.precheck_u021n_note_4') }}<br />{{ __('messages.precheck.precheck_u021n_note_5') }} {{ number_format($data[2]['cost_service_base']) }}{{ __('messages.precheck.precheck_u021n_note_6') }}{{ number_format($data[3]['cost_service_base']) }} {{ __('labels.円') }}
    </li>
    <div class="error_type_precheck"></div>
    @error('type_precheck') <div class="notice">{{ $message }}</div> @enderror
</ul>
