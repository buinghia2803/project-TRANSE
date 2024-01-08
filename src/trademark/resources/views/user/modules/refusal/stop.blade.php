@extends('user.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents" class="normal">
    @include('compoments.messages')
    <h2>{{ __('labels.refusal_plans.u203stop.h2') }}</h2>

    <form id="form" action="{{ route('user.refusal.response-plan.post-stop') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $trademarkPlan->id }}">
        <p>
            {{ __('labels.refusal_plans.u203stop.p_1') }}<br />
            {{ __('labels.refusal_plans.u203stop.p_2') }}
        </p>
        <p>{{ __('labels.refusal_plans.u203stop.p_3') }}<a href="{{ route('user.refusal.plans.simple', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id] ) }}">{{ __('labels.refusal_plans.u203stop.a') }}</a>。</p>

        <p>{{ __('labels.refusal_plans.u203stop.p_4') }}</p>

        <textarea class="wide" name="reason_cancel">{{ $trademarkPlan->reason_cancel }}</textarea>
        <div class="mb20"></div>
        <ul class="footerBtn clearfix">
            <li><input
                type="button"
                value="{{ __('labels.refusal_plans.u203stop.input_1') }}"
                class="btn_a"
                onclick="window.location='{{ route('user.refusal.response-plan.refusal_response_plan', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id,'trademark_plan_id' => $trademarkPlan->id]) }}'"
            /></li>
        </ul>

        <ul class="footerBtn clearfix">
            <li><input type="submit" name="draft" value="{{ __('labels.refusal_plans.u203stop.input_2') }}" class="btn_a" /></li>
            <li><input type="submit" name="submit" value="{{ __('labels.refusal_plans.u203stop.input_3') }}" class="btn_b" /></li>
        </ul>
    </form>
</div>
<!-- /contents -->
@endsection
@section('script')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const Common_E001 = '{{ __('messages.general.Common_E001') }}';
        const Common_E031 = '{{ __('messages.general.Common_E031') }}';
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/refusal/stop.js') }}"></script>
@endsection
