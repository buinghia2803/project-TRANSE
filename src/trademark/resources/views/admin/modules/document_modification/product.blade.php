@extends('admin.layouts.app')
@section('main-content')
    <div id="contents">
        <div class="wide clearfix">
            <form action="{{ route('admin.registration.document.modification.product.post', [
                'id' => $matchingResult->id,
                'register_trademark_id' => $registerTrademark->id,
            ]) }}" method="post">
                @csrf
                <input type="hidden" name="from_page" value="{{ A302_HOSEI02 }}">

                <div class="info eol">
                    {{-- Trademark table --}}
                    @include('admin.components.includes.trademark-table', [
                        'table' => $trademarkTable,
                    ])
                </div>

                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->
                <h3>{{ __('labels.a302hosei02.text_1') }}</h3>
                <dl class="w16em clearfix">
                    <dt>{{ __('labels.a302hosei02.text_2') }}</dt>
                    <dd>{{ __('labels.a302hosei02.text_3') }}</dd>

                    <dt>{{ __('labels.a302hosei02.trademark_number') }}</dt>
                    <dd>{{ $data['trademark_number'] }}
                    </dd>

                    <dt>{{ __('labels.a302hosei02.text_4') }}</dt>
                    <dd>{{ __('labels.a302hosei02.text_5') }}
                    </dd>
                </dl>

                <h4>{{ __('labels.a302hosei02.text_6') }}</h4>
                <dl class="w16em clearfix">

                    <dt>{{ __('labels.a302hosei02.application_number') }}</dt>
                    <dd>{{ $data['application_number'] }}
                    </dd>

                </dl>

                <h4>{{ __('labels.a302hosei02.text_7') }}</h4>
                <dl class="w16em clearfix">
                    <dt>{{ __('labels.a302hosei02.address') }}</dt>
                    <dd>{{ $data['address'] }}</dd>
                    <dt>{{ __('labels.a302hosei02.trademark_info_name') }}</dt>
                    <dd>{{ $data['trademark_info_name'] }}</dd>
                </dl>

                <h4>{{ __('labels.a302hosei02.text_8') }}</h4>
                <dl class="w16em clearfix">
                    <dt>{{ __('labels.a302hosei02.identification_number') }}</dt>
                    <dd>{{ $data['identification_number'] }}</dd>

                    <dt>{{ __('labels.a302hosei02.text_9') }}</dt>
                    <dd>&nbsp;</dd>

                    <dt>{{ __('labels.a302hosei02.agent_name') }}</dt>
                    <dd> {{ $data['agent_name'] }}</dd>
                </dl>

                @foreach ($data['m_distincts'] as $key => $mDistinctName)
                    <h4>{{ __('labels.a302hosei02.key', ['attr' => $key + 1]) }}</h4>
                    <dl class="w14em clearfix">
                        <dt>{{ __('labels.a302hosei02.text_10') }}</dt>
                        <dd>{{ __('labels.a302hosei02.text_11') }}</dd>

                        <dt>{{ __('labels.a302hosei02.text_12') }}</dt>
                        <dd>
                            {{ __('labels.a302hosei02.m_distinct_name', ['attr' => $mDistinctName]) }}
                        </dd>
                        <dt>{{ __('labels.a302hosei02.text_13') }}</dt>
                        <dd>{{ __('labels.a302hosei02.text_14') }}</dd>
                    </dl>
                @endforeach

                <ul class="footerBtn clearfix">
                    <li><input type="button" class="btn_a custom_a" value="{{ __('labels.a302hosei02.btn_back') }}"
                        onclick="window.location = '{{ route('admin.registration.document', [
                            'id' => $id,
                            'register_trademark_id' => $data['register_trademark_id'],
                        ]) }}'"></li>
                    <li><input type="submit" class="btn_b custom_a" value="{{ __('labels.a302hosei02.btn_submit') }}"></li>
                </ul>
            </form>
        </div>
    </div>
    <style>
        .custom_a {
            padding: 5px 2em !important;
        }
    </style>
@endsection
@section('footerSection')
    @if($hasSubmit)
        <script>
            disabledScreen();
        </script>
    @endif
    @include('compoments.readonly', [
        'only' => [ROLE_OFFICE_MANAGER],
        'hasRemoveSubmit' => false,
    ])
@endsection
