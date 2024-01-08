<dl class="w16em clearfix">
    <dt>{{ __('labels.a205_common.trademark_info.content') }}</dt>
    <dd>{{ __('labels.a205_common.trademark_info.content_1') }}</dd>

    <dt>{{ __('labels.a205_common.trademark_info.trademark_number') }}</dt>
    <dd>{{ $data['trademark_number'] }}</dd>

    <dt>{{ __('labels.a205_common.trademark_info.application_date') }}</dt>
    <dd>{{ __('labels.a205_common.trademark_info.html_auto_date') }}</dd>

    <dt>{{ __('labels.a205_common.trademark_info.content_2') }}</dt>
    <dd>{{ __('labels.a205_common.trademark_info.content_3') }}</dd>
</dl>
<p>{{ __('labels.a205_common.trademark_info.content_4') }}</p>
<dl class="w16em clearfix">
    <dt>{{ __('labels.a205_common.trademark_info.application_number') }}</dt>
    <dd>{{ $data['application_number'] }}</dd>
</dl>
<p>{{ __('labels.a205_common.trademark_info.content_5_v2') }}</p>
<dl class="w16em clearfix">
    <dt>{{ __('labels.a205_common.trademark_info.address') }}</dt>
    <dd>{{ $data['prefecture_name'] }}{{ $data['address_second'] }}{{ $data['address_three'] }}</dd>

    <dt>{{ __('labels.a205_common.trademark_info.trademark_info_name') }}</dt>
    <dd>{{ $data['trademark_info_name'] }}</dd>
</dl>
<p>{{ __('labels.a205_common.trademark_info.agent_title') }}</p>
<dl class="w16em clearfix">
    <dt>{{ __('labels.a205_common.trademark_info.identification_number') }}</dt>
    <dd>{{ $data['identification_number'] }}</dd>

    <dt>{{ __('labels.a205_common.trademark_info.content_6') }}</dt>
    <dd>&nbsp;</dd>

    <dt>{{ __('labels.a205_common.trademark_info.agent_name') }}</dt>
    <dd>{{ $data['agent_name'] }}</dd>
    <br>

    <dt>{{ __('labels.a205_common.trademark_info.pi_dispatch_number') }}</dt>
    <dd>{{ $data['pi_dispatch_number'] }}</dd>
</dl>
