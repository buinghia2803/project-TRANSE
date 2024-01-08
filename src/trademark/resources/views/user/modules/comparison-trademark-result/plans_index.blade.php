@extends('user.layouts.app')
@section('main-content')
<style>
    .customer_a {
        display: unset !important;
    }
    .customer_b,
    .customer_c {
        padding: 5px 2em !important;
        color: white !important;
        display: unset !important;
    }
    .customer_b:hover,
    .customer_c:hover {
        color: black !important
    }
    .text-center {
        text-align: center;
    }
    h1 {
        margin-bottom: 1em;
        font-size: 1.6em;
    }
    a:hover {
        color: #359ce0 !important;
    }
</style>
<!-- contents -->
<div id="contents" class="normal">
    <h2>{{ __('labels.comparison_trademark_result.plans.h2') }}</h2>
    <form>
        @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
            @include('user.modules.common.content', [
                'trademark' => $comparisonTrademarkResult->trademark,
            ])
        @endif
        <h3>{{ __('labels.comparison_trademark_result.plans.h3') }}</h3>
        {{-- Trademark table --}}
        @include('user.components.trademark-table', [
            'table' => $trademarkTable
        ])
        {{-- Trademark table --}}
        <dl class="w20em clearfix middle mt-2">
            <dt>{{ __('labels.comparison_trademark_result.plans.date_1') }}{{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date) }}</dt>
            <dd>
                <input type="button" value="{{ __('labels.comparison_trademark_result.plans.btn_1') }}" class="btn_b" id="click_file_pdf"/>
                @foreach ($trademarkDocuments as $ele_a)
                    <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
                @endforeach
            </dd>
        </dl>
        <p>{{ __('labels.comparison_trademark_result.plans.date_2') }}{{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline) }}</p>
        <p>
            {{ __('labels.comparison_trademark_result.plans.text_1') }}<br />
            {{ __('labels.comparison_trademark_result.plans.text_2') }}
        </p>
        @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
            @include('user.modules.common.content', [
                'trademark' => $comparisonTrademarkResult->trademark,
            ])
        @endif
        <p><a href="{{ route('user.refusal.plans.simple', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}" class="btn_c customer_c">{{ __('labels.comparison_trademark_result.plans.btn_2') }}</a> {{ __('labels.comparison_trademark_result.plans.text_3') }}</p>
        <p class="eol mt-4">
            <a href="{{ route('user.refusal.plans.select', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}" class="btn_b customer_b">{{ __('labels.comparison_trademark_result.plans.btn_3') }}</a> {{ __('labels.comparison_trademark_result.plans.text_4') }}
            <br />
            <strong class="fs11 blue2 d-block mt-2">{{ __('labels.comparison_trademark_result.plans.strong') }}</strong>
        </p>
        <p>
            {{ __('labels.comparison_trademark_result.plans.text_5') }} <a href="javascript:void(0)" id="redirect_to_u201a_window">{{ __('labels.comparison_trademark_result.plans.text_6') }}</a>
            <br />
            <img src="{{ asset('common/images/flow_oa_plans.png') }}" alt="flow_oa_plans" style="max-width:800px; height:500px; width: 100%; height: auto;"/>
        </p>
        <div id="u201a_window-modal" class="modal fade" role="dialog">
            <div class="modal-dialog" style="min-width: 50%;">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" style="height: 52px">
                        <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <h1 class="text-center">{{ __('labels.comparison_trademark_result.plans.modal') }}</h1>
                        <div class="content loaded text-center">
                            <iframe src="{{ asset('common/images/flow_plans.pdf') }}" style="max-width:800px; height:500px; width: 100%;" frameborder="0"></iframe>
                        </div>
                    </div>
                    <p class="center eol fs12">[<a href="#" data-dismiss="modal">{{ __('labels.comparison_trademark_result.plans.close_modal') }}</a>]</p>
                </div>
            </div>
        </div>
        <p class="eol notice">
            {{ __('labels.comparison_trademark_result.plans.text_7') }}<br />
            {{ __('labels.comparison_trademark_result.plans.text_8') }}<br />
            {{ __('labels.comparison_trademark_result.plans.text_9') }}
            <br />
        </p>
        <dl class="w16em eol clearfix">
            <dt>
                <h3><strong>{{ __('labels.comparison_trademark_result.plans.AMS') }}</strong></h3>
            </dt>
            <dd>
                <h3><strong>{{ CommonHelper::formatTime($comparisonTrademarkResult->user_response_deadline) }}</strong></h3>
            </dd>
        </dl>
        <ul class="footerBtn2 clearfix">
            <li><a href="{{ route('user.refusal.plans.simple', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}" class="btn_c" style="font-size: 1.3em">{{ __('labels.comparison_trademark_result.plans.btn_2') }}</a></li>
            <li><a href="{{ route('user.refusal.plans.select', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}" class="btn_b" style="font-size: 1.3em">{{ __('labels.comparison_trademark_result.plans.btn_3') }}</a></li>
        </ul>
        <ul class="btn_left eol">
            <li><a href="{{ route('user.refusal.notification.cancel', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}" class="btn_a customer_a">{{ __('labels.comparison_trademark_result.plans.btn_4') }}</a></li>
            <li><a href="{{ route('user.application-detail.index', ['id' => $trademark->id]) }}" class="btn_a customer_a">{{ __('labels.comparison_trademark_result.plans.btn_5') }}</a></li>
        </ul>
    </form>
</div>
<!-- /contents -->
@endsection
@section('footerSection')
    <script>
        const arrayFilePDF = @JSON($trademarkDocuments);
        $('#click_file_pdf').click(function () {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })
        $('#redirect_to_u201a_window').on('click', function () {
            arrayProductSelect = []
            isChoiUser = []
            openModal('#u201a_window-modal');
        })
    </script>
@endsection
