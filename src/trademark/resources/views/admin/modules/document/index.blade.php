@extends('admin.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents">
    <!-- contents inner -->
    <div class="wide clearfix">
        @include('compoments.messages')

        <form id="form" action="{{ route('admin.registration.document.post', ['id' => $machingResult->id, 'register_trademark_id' => $registerTrademark->id]) }}" method="POST">
            @csrf
            {{-- Trademark table --}}
            @include('admin.components.includes.trademark-table', [
                'table' => $trademarkTable,
            ])

            <h3>{{ __('labels.a302.h3') }}</h3>

            <dl class="w16em clearfix">
                <dt>{{ __('labels.a302.dt_1') }}</dt>
                <dd>{{ __('labels.a302.dd_2') }}</dd>

                <dt>{{ __('labels.a302.dt_2') }}</dt>
                <dd>{{ $trademark->trademark_number ? mb_convert_kana($trademark->trademark_number, 'ASV') : null }}</dd>

                <dt>{{ __('labels.a302.dt_3') }}</dt>
                <dd>{{ __('labels.a302.dd_2') }}</dd>

                <dt>{{ __('labels.a302.dt_4') }}</dt>
                <dd>商願{{ $trademark->application_number ? mb_convert_kana(substr($trademark->application_number, 0, 4) . '-' . substr($trademark->application_number, 4), 'ASV') : null }}</dd>

                <dt>{{ __('labels.a302.dt_5') }}</dt>
                <dd>{{ $countDistinctionIsApply ? mb_convert_kana($countDistinctionIsApply, 'ASV') : 0 }}</dd>
            </dl>

            <h4>{{ __('labels.a302.h4_1') }}</h4>
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a302.dt_6') }}</dt>
                <dd>{{ $registerTrademark->trademark_info_name ?? null }}</dd>
            </dl>

            <h4>{{ __('labels.a302.h4_2') }}</h4>
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a302.dt_7') }}</dt>
                <dd>{{ $agent->identification_number ?? null }}</dd>

                <dt>{{ __('labels.a302.dt_6') }}</dt>
                <dd>{{ $agent->name ?? null }}</dd>
            </dl>

            <h4>{{ __('labels.a302.h4_3') }}</h4>
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a302.dt_8') }}</dt>
                <dd>{{ $agent->deposit_account_number ?? null }}</dd>

                <dt>{{ __('labels.a302.dt_9') }}</dt>
                <dd>{{ $printDistinction ? mb_convert_kana($printDistinction, 'ASV') : 0 }}</dd>

                <dt>{{ __('labels.a302.dt_10') }}</dt>
                <dd>
                    <ul>
                        <li><input type="checkbox" name="display_info_status[]" value="{{ LAW }}" {{ $law == true ? 'checked' : '' }}/>{{ __('labels.a302.checkbox_1') }}</li>
                        <li><input type="checkbox" name="display_info_status[]" value="{{ CHANGE_NAME }}" {{ $changeName == true ? 'checked' : '' }}/>{{ __('labels.a302.checkbox_2') }}</li>
                        <li><input type="checkbox" name="display_info_status[]" value="{{ CHANGE_ADDRESS }}" {{ $changeAddress == true ? 'checked' : '' }}/>{{ __('labels.a302.checkbox_3') }}</li>
                    </ul>
                </dd>
            </dl>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="button" value="{{ __('labels.a302.back') }}" class="btn_a" onclick="window.location = '{{ $backUrl ?? route('admin.home') }}'">
                </li>
                <li>
                    <input type="submit" value="{{ __('labels.a302.submit') }}" class="btn_b" />
                </li>
            </ul>
        </form>
    </div>
    <!-- /contents inner -->
</div>
<!-- /contents -->
@endsection
@section('footerSection')
    <script>
        function disableInput () {
            const form = $('form')
            form.find('a, input, button, textarea, select').prop('disabled', true)
            form.find('a, input, button, textarea, select').addClass('disabled')
            form.find('a').attr('href', 'javascript:void(0)')
            form.find('a').attr('target', '')
            $('[type=submit]').remove()
            $('#cart').prop('disabled', false);
        }
    </script>
    @if($checkIsSend)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', ['only' => [ ROLE_OFFICE_MANAGER ]])
@endsection
