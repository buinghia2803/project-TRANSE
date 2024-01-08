@extends('admin.layouts.app')
@section('main-content')
@php
    $admin = \Auth::user();
@endphp
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form" action="{{ route('admin.registration.change-address.update-info') }}" method="POST">
                @csrf
                <h3>{{ __('labels.change_address.title_confirm') }}</h3>
                <dl class="w16em eol clearfix">
                    <dt>{{ __('labels.change_address.name') }}</dt>
                    <dd><span class="blue">{{ __('labels.change_address.text_9') }}{{ $data['name'] ?? '' }}</span><br />
                        {{ __('labels.change_address.text_10') }}{{ $dataSession['name'] ?? '' }}</dd>
                    <dt>{{ __('labels.change_address.nation_name') }}</dt>
                    <dd>
                        <span
                            class="blue">{{ __('labels.change_address.text_11') }}{{ $data['nation_name'] ?? '' }}</span><br />
                        <span>
                            {{ __('labels.change_address.text_12') }}
                            @foreach ($nations as $key => $nation)
                                @if ($key == $dataSession['m_nation_id'])
                                    {{ $nation }}
                                @endif
                            @endforeach
                        </span>
                    </dd>
                    <dt>{{ __('labels.change_address.text_13') }}</dt>
                    <dd>
                        <span class="blue">{{ __('labels.change_address.text_14') }}{{ $data['old_address_info'] ?? '' }}</span>
                        <br />
                        <span>{{ __('labels.change_address.text_15') }}{{ $data['new_address_info'] ?? '' }}</span>
                    </dd>

                </dl>
                <ul class="footerBtn clearfix">
                    <li><input type="button" value="{{ __('labels.change_address.btn_back') }}" class="btn_a" onclick="history.back()"/></li>
                    <li><input type="submit" value="{{ __('labels.change_address.btn_confirm') }}" class="btn_b" /></li>
                </ul>
                <input type="hidden" name="name" value="{{ $dataSession['name'] ?? '' }}">
                <input type="hidden" name="m_nation_id" value="{{ $dataSession['m_nation_id'] ?? '' }}">
                <input type="hidden" name="m_prefecture_id" value="{{ $dataSession['m_prefecture_id'] ?? '' }}">
                <input type="hidden" name="address_second" value="{{ $dataSession['address_second'] ?? '' }}">
                <input type="hidden" name="address_three" value="{{ $dataSession['address_three'] ?? '' }}">
                <input type="hidden" name="trademark_info_id" value="{{ $dataSession['trademark_info_id'] ?? '' }}">
                <input type="hidden" name="register_trademark_id" value="{{ $dataSession['register_trademark_id'] ?? '' }}">
                <input type="hidden" name="change_info_register_id"
                    value="{{ $dataSession['change_info_register_id'] ?? '' }}">
                <input type="hidden" name="trademark_id" value="{{ $dataSession['trademark_id'] ?? '' }}">
                <input type="hidden" name="from_page" value="{{ $dataSession['from_page'] ?? '' }}">
                @if(isset($data['matching_result_id']))
                    <input type="hidden" name="matching_result_id" value="{{ $dataSession['matching_result_id'] ?? '' }}">
                @endif
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script>
        const admin = @json($admin);
        if(admin.role == 3) {
            $('#form').find('button, select, input').each(function (key, item) {
                $(item).attr('disabled', 'disabled')
                $(item).addClass('disabled')
            })
        }
    </script>
@endsection
