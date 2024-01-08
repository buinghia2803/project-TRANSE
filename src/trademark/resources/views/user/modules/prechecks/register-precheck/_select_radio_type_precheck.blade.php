@error('type_precheck') <div class="notice">{{ $message }}</div> @enderror
<div class="error_type_precheck"></div>
<ul class="eol">
    <li class="mb10"><label>
            <input type="radio" name="type_precheck" class="type_precheck type_precheck_simple"
                   {{ ($preheckOld && $preheckOld->type_precheck == $typePrecheckSimple) ? 'checked' : '' }}
                   value="{{ old('type_precheck', $typePrecheckSimple) }}" />
            ＜{{ __('labels.precheck.simple_report') }}＞</label><br />{{ __('labels.precheck.simple_report_note_1') }}<br />{{ __('labels.precheck.simple_report_note_2', ['value_1' => number_format($dataFeeDefaultPrecheck['precheck_simple']['cost_service_base']), 'value_2' => number_format($dataFeeDefaultPrecheck['precheck_simple']['cost_service_add_prod'])]) }}
    </li>
    <li><label>
            <input type="radio" name="type_precheck" class="type_precheck type_precheck_detail"
                   {{ ($preheckOld && $preheckOld->type_precheck == $typePrecheckSelect) ? 'checked' : '' }}
                   value="{{ old('type_precheck', $typePrecheckSelect) }}" />
            ＜{{ __('labels.precheck.detailed_report') }}＞</label><br />{{ __('labels.precheck.detailed_report_note_1') }}<br />{{ __('labels.precheck.detailed_report_note_2', ['value_1' => number_format($dataFeeDefaultPrecheck['precheck_detail']['cost_service_base']), 'value_2' => number_format($dataFeeDefaultPrecheck['precheck_detail']['cost_service_add_prod'])]) }}
    </li>
</ul>
