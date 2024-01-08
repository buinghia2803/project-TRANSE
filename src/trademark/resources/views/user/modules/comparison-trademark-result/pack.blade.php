@extends('user.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents" class="normal">
    <form id="form">
        @csrf
        <input type="hidden" name="comparison_trademark_result_id" value="{{ $comparisonTrademarkResult->id }}">
        <input type="hidden" name="trademark_id" value="{{ $trademark->id }}">

        @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
            @include('user.modules.common.content', [
                'trademark' => $comparisonTrademarkResult->trademark,
            ])
        @endif

        <h2>{{ __('labels.comparison_trademark_result.pack.h2') }}</h2>

        <h3>{{ __('labels.comparison_trademark_result.pack.h3') }}</h3>
        {{-- Trademark table --}}
        @include('user.components.trademark-table', [
            'table' => $trademarkTable
        ])

        <dl class="w20em clearfix middle mt-2">
            <dt>{{ __('labels.comparison_trademark_result.pack.date_1') }}{{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date) }}</dt>
            <dd>
                <input type="button" value="{{ __('labels.comparison_trademark_result.pack.btn_1') }}" class="btn_b" id="click_file_pdf"/>
                @foreach ($trademarkDocuments as $ele_a)
                    <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
                @endforeach
            </dd>
        </dl>
        <p>{{ __('labels.comparison_trademark_result.pack.date_2') }}{{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline) }}</p>
        <p class="eol">
            {{ __('labels.comparison_trademark_result.pack.p_1') }}<br />
            {{ __('labels.comparison_trademark_result.pack.p_2') }}<br />
            {{ __('labels.comparison_trademark_result.pack.p_3') }}<br />
            {{ __('labels.comparison_trademark_result.pack.p_4') }}
        </p>
        @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
            @include('user.modules.common.content', [
                'trademark' => $comparisonTrademarkResult->trademark,
            ])
        @endif
        <h4>{{ __('labels.comparison_trademark_result.pack.h4_1') }}</h4>
        <p>
            {{ __('labels.comparison_trademark_result.pack.p_5') }}<br />
            {{ __('labels.comparison_trademark_result.pack.p_6') }}<br />
            {{ __('labels.comparison_trademark_result.pack.p_7') }}<br />
            {{ __('labels.comparison_trademark_result.pack.p_8') }}
        </p>
        <ul class="footerBtn clearfix">
            <li><input type="button" value="{{ __('labels.comparison_trademark_result.pack.btn_2') }}" class="btn_b submit_form" /></li>
        </ul>

        <h4>{{ __('labels.comparison_trademark_result.pack.h4_2') }}</h4>
        <p class="eol">
            {{ __('labels.comparison_trademark_result.pack.p_9') }}<br />
            {{ __('labels.comparison_trademark_result.pack.p_10') }}<br />
            {{ __('labels.comparison_trademark_result.pack.p_11') }}<br />
            {{ __('labels.comparison_trademark_result.pack.p_12') }}
        </p>

        <h4>{{ __('labels.comparison_trademark_result.pack.h4_3') }}</h4>
        <ul class="normal eol">
            <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.pack.li_1') }}</li>
            <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.pack.li_2') }}</li>
            <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.pack.li_3') }}</li>
            <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.pack.li_4') }}</li>
            <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.pack.li_5') }}</li>
        </ul>

        <ul class="footerBtn mb20 clearfix">
            <li><input type="button" value="{{ __('labels.comparison_trademark_result.pack.btn_2') }}" class="btn_b submit_form" /></li>
        </ul>

        <ul class="btn_left mb20">
            <li><a class="btn_a" style="display: unset" href="{{ !$checkSubmit ? route('user.application-detail.index', ['id' => $trademark->id ]) : 'javascript:void(0)' }}" >{{ __('labels.comparison_trademark_result.pack.btn_3') }}</a></li>
        </ul>
        <ul class="btn_left eol">
            <li><a class="btn_a" style="display: unset" href="{{ !$checkSubmit ? route('user.refusal.notification.cancel', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) : 'javascript:void(0)' }}">{{ __('labels.comparison_trademark_result.pack.btn_4') }}</a></li>
        </ul>

    </form>
</div>
<!-- /contents -->
@endsection
@section('footerSection')
    <script>
        const routeSubmit = @JSON(route('user.refusal.plans.create-pack'));
        const routeApplicationDetail = @JSON(route('user.application-detail.index', ['id' => $trademark->id]));
    </script>
    @if($checkSubmit)
        <script>disabledScreen()</script>
    @endif
    <script type="text/JavaScript" src="{{ asset('end-user/comparison_trademark_result/pack.js') }}"></script>
@endsection
