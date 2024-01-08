@extends('user.layouts.app')
@section('main-content')
    <style>
        .customer_b {
            padding: 5px 2em !important;
            color: white !important;
            display: unset !important;
        }

        .customer_b:hover {
            color: black !important
        }
    </style>
    <!-- contents -->
    <div id="contents" class="normal">
        @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
            @include('user.modules.common.content', [
                'trademark' => $comparisonTrademarkResult->trademark,
            ])
        @endif

        <h2>{{ __('labels.comparison_trademark_result.index.h2') }}</h2>

        <form>
            <h3>{{ __('labels.precheck.precheck_report.title_table_info') }}</h3>
            {{-- Trademark table --}}
            @include('user.components.trademark-table', [
                'table' => $trademarkTable,
            ])

            <dl class="w20em clearfix middle" style="margin-top: 10px">
                <dt>{{ __('labels.comparison_trademark_result.index.date_1') }}{{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date) }}
                </dt>
                <dd>
                    <input type="button" value="{{ __('labels.comparison_trademark_result.index.btn') }}" class="btn_b"
                        id="click_file_pdf" />
                    @foreach ($trademarkDocuments as $ele_a)
                        <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
                    @endforeach
                </dd>
            </dl>
            <p>{{ __('labels.comparison_trademark_result.index.date_2') }}{{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline) }}</p>

            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <p>
                {{ __('labels.comparison_trademark_result.index.text_1') }}<br />
                {{ __('labels.comparison_trademark_result.index.text_2') }}<br />
                {{ __('labels.comparison_trademark_result.index.text_3') }}
            </p>

            <h5>{{ __('labels.comparison_trademark_result.index.h5') }}</h5>
            <p>
                {{ __('labels.comparison_trademark_result.index.text_4') }}<br />
                {{ __('labels.comparison_trademark_result.index.text_5') }}<br />
                {{ __('labels.comparison_trademark_result.index.text_6') }}<br />
                {{ __('labels.comparison_trademark_result.index.text_7') }}
            </p>

            <h5>{{ __('labels.comparison_trademark_result.index.h5_2') }}</h5>
            <ul class="normal mb20">
                <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.index.underline_1') }}
                </li>
                <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.index.underline_2') }}
                </li>
                <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.index.underline_3') }}
                </li>
                <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.index.underline_4') }}
                </li>
                <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.index.underline_5') }}
                </li>
            </ul>

            <h5>{{ __('labels.comparison_trademark_result.index.h5_3') }}</h5>
            <p>{{ __('labels.comparison_trademark_result.index.text_8') }}</p>

            <p>{{ __('labels.comparison_trademark_result.index.text_9') }}</p>

            <p class="eol notice">
                {{ __('labels.comparison_trademark_result.index.notice_1') }}<br />
                {{ __('labels.comparison_trademark_result.index.notice_2') }}<br />
                {{ __('labels.comparison_trademark_result.index.notice_3') }}
                <br />
            </p>

            <dl class="w16em eol clearfix">
                <dt>
                    <h3><strong>{{ __('labels.comparison_trademark_result.index.AMS') }}</strong></h3>
                </dt>
                <dd>
                    <h3><strong>{{ CommonHelper::formatTime($comparisonTrademarkResult->user_response_deadline) }}</strong>
                    </h3>
                </dd>
            </dl>

            <ul class="footerBtn clearfix">
                <li><a id="plan"
                        href="{{ !$checkDataPlanCorrespondence ? route('user.refusal.plans.index', ['id' => $comparisonTrademarkResult->id]) : 'javascript:void(0)' }}"
                        class="btn_b customer_b">{{ __('labels.comparison_trademark_result.index.a_1') }}</a></li>
            </ul>
            <ul class="btn_left eol">
                <li><a href="{{ !$checkDataPlanCorrespondence ? route('user.application-detail.index', ['id' => $trademark->id]) : 'javascript:void(0)' }}"
                        class="btn_a" style="display: unset">{{ __('labels.comparison_trademark_result.index.a_2') }}</a>
                </li>
            </ul>
            <ul class="btn_left eol">
                <li>
                    <input type="button" class="redirect-cancel btn_a {{ $isCancel ? 'disabled' : '' }}"
                        onclick="window.location = '{{ route('user.refusal.notification.cancel', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}'"
                        {{ $isCancel ? 'disabled' : '' }}
                        value="{{ __('labels.comparison_trademark_result.over.back') }}">
                </li>
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script>
        const arrayFilePDF = @JSON($trademarkDocuments);
        $('#click_file_pdf').click(function() {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })
    </script>
    @if ($checkDataPlanCorrespondence)
        <script>
            disabledScreen();
            document.getElementById('plan').style.cssText = 'color: #777 !important';
        </script>
    @endif
@endsection
