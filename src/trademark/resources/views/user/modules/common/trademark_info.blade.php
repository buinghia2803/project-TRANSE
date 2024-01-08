@section('headerSection')
<link rel="stylesheet" href="{{ asset('common/css/custom-css.css') }}">
@endsection
<h3>{{ __('labels.form_trademark_information.title') }}</h3>
<table class="info_table">
    <tr>
        <th>{{ __('labels.trademark_info.customer_reference_number') }}</th>
        <td>{{ $trademark->reference_number }}<input type="submit" value="編集" class="btn_a small" /></td>
        <th nowrap>{{ __('labels.trademark_info.application_plan') }}</th>
        <td>{{ $trademark->pack_name }}</td>
    </tr>
    <tr>
        <th>{{ __('labels.trademark_info.application_number') }}</th>
        <td><a href="{{ route('anken-top.index') }}">{{ $trademark->trademark_number }}</a></td>
        <th>{{ __('labels.trademark_info.application_date') }}</th>
        <td>{{ $trademark->created_at ? date_format($trademark->created_at, 'Y/m/d') : '' }}</td>
    </tr>
    <tr>
        <th>{{ __('labels.trademark_info.branch_name') }}</th>
        <td colspan="3">{{ $trademark->name_trademark }}</td>
    </tr>
    <tr>
        <th><a href="#">{{ __('labels.trademark_info.distinguishing_points') }}</a></th>
        <td colspan="3">
            <div class="display:flex">
                {{ $trademark->distinguishing_points }}{{ __('labels.trademark_info.distinguishing') }}（
                @if($trademark->data_register)
                @foreach($trademark->data_register as $value)
                <span class="distinction_name">{{ $value['distinction_name'] }}</span>
                @endforeach
                @endif
                ）　
            </div>
            <input type="button" onclick="window.location='{{ route("user.u003db") }}'" value="{{  __('labels.trademark_info.view_more_product') }}" class="btn_a btn" /><a href="{{ route('user.u003db') }}" target="_blank">→</a>
        </td>
    </tr>
    <tr>
        <th>{{ __('labels.trademark_info.number_of_division') }}</th>
        <td colspan="3">{{ $trademark->total_distinguishing_points }}{{ __('labels.trademark_info.distinguishing') }}</td>
    </tr>
    <tr>
        <th nowrap>{{ __('labels.trademark_info.logo') }}</th>
        <td colspan="3">
            @if($trademark->image_trademark)
            <a href="{{ asset($trademark->image_trademark) }}" target="_blank"><img src="{{ asset($trademark->image_trademark) }}" class="logo_trademark">{{ __('labels.trademark_info.click_to_enlarge') }} >></a>
            @endif
        </td>
    </tr>
    <tr>
        <th nowrap>{{ __('labels.trademark_info.trademark_owner') }}</th>
        <td colspan="3">
            <div class="trademark_owner">
                @if($trademark->appTrademark && $trademark->appTrademark->trademarkInfo)
                @foreach($trademark->appTrademark->trademarkInfo as $trademarkInfo)
                <span>{{ $trademarkInfo->name }}</span>
                @endforeach
                @endif
            </div>
        </td>
    </tr>
    <tr>
        <th>{{ __('labels.trademark_info.application_number') }}</th>
        <td>{{ $trademark->trademark_number }}</td>
        <th>{{ __('labels.trademark_info.filing_date') }}</th>
        <td>{{ $trademark->created_at ? date_format($trademark->created_at, 'Y/m/d') : '' }}</td>
    </tr>
    <tr>
        <th>{{ __('labels.trademark_info.registration_number') }}</th>
        <td>{{ $trademark->registerTrademark ? $trademark->registerTrademark->register_number : '' }}</td>
        <th>{{ __('labels.trademark_info.registration_date') }}</th>
        <td>{{ $trademark->registerTrademark ? date_format($trademark->registerTrademark->created_at, 'Y/m/d') : '' }}</td>
    </tr>
    <tr>
        <th>{{ __('labels.trademark_info.registration_period') }}</th>
        <td>{{
        $trademark->registerTrademark ?
        $trademark->registerTrademark->period_registration == PERIOD_REGISTRATION_FIVE_YEAR
        ? LABEL_FIVE_YEAR
        : LABEL_TEN_YEAR
        : '' }}</td>
        <th>{{ __('labels.trademark_info.renewal_period') }}</th>
        <td>{{ isset($trademark['renewal_period']) ? $trademark['renewal_period'] : '' }}</td>
    </tr>
    <input type="hidden" name="target_id" id="">
</table>
