@extends('user.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents" class="normal">
    <p class="alertBox">
        <strong>
            {{ __('labels.comparison_trademark_result.over.strong_1') }}<br />
            {{ __('labels.comparison_trademark_result.over.strong_2') }}<br />
            {{ __('labels.comparison_trademark_result.over.strong_3') }}<br />
            {{ __('labels.comparison_trademark_result.over.strong_4') }}
        </strong>
        <br />
        <a href="{{ route('user.refusal.extension-period.over', ['id' => $comparisonTrademarkResult->trademark_id]) }}" class="btn_b">{{ __('labels.comparison_trademark_result.over.btn_1') }}</a>
    </p>

    <h2>{{ __('labels.comparison_trademark_result.over.h2') }}</h2>

    <form id="form">
        <h3>{{ __('labels.precheck.precheck_report.title_table_info') }}</h3>
        {{-- Trademark table --}}
        @include('user.components.trademark-table', [
            'table' => $trademarkTable
        ])
        {{-- Trademark table --}}

        <dl class="w20em clearfix middle" style="margin-top: 10px">
            <dt>{{ __('labels.comparison_trademark_result.over.date_1') }}{{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date) }}</dt>
            <dd>
                <input type="button" value="{{ __('labels.comparison_trademark_result.over.btn_2') }}" class="btn_b" id="click_file_pdf"/>
                @foreach ($trademarkDocuments as $ele_a)
                    <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
                @endforeach
            </dd>
        </dl>
        <p>{{ __('labels.comparison_trademark_result.over.date_2') }}{{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline) }}</p>

        <p>{{ __('labels.comparison_trademark_result.over.text_1') }}</p>

        <p>
            {{ __('labels.comparison_trademark_result.over.text_2') }}<br />
            {{ __('labels.comparison_trademark_result.over.text_3') }}
        </p>

        <p>
            {{ __('labels.comparison_trademark_result.over.text_4') }}<br />
            {{ __('labels.comparison_trademark_result.over.text_5') }}
            {{ __('labels.comparison_trademark_result.over.text_6') }}
        </p>
        {{ __('labels.comparison_trademark_result.over.text_7') }}
        <ul class="normal eol">
            <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.over.underline_1') }}</li>
            <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.over.underline_2') }}</li>
            <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.over.underline_3') }}</li>
            <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.over.underline_4') }}</li>
            <li style="text-decoration: underline;">{{ __('labels.comparison_trademark_result.over.underline_5') }}</li>
        </ul>

        <p>
            {{ __('labels.comparison_trademark_result.over.text_8') }}<br />
            {{ __('labels.comparison_trademark_result.over.text_9') }}
        </p>

        <p>
            {{ __('labels.comparison_trademark_result.over.text_10') }}<br />
            {{ __('labels.comparison_trademark_result.over.text_11') }}
        </p>

        <p>{{ __('labels.comparison_trademark_result.over.text_12') }}</p>

        <p class="eol notice">
            {{ __('labels.comparison_trademark_result.over.notice_1') }}<br />
            {{ __('labels.comparison_trademark_result.over.notice_2') }}<br />
            {{ __('labels.comparison_trademark_result.over.notice_3') }}
            <br />
        </p>

        <dl class="w16em eol clearfix">
            <dt>
                <h3><strong>{{ __('labels.comparison_trademark_result.over.AMS') }}</strong></h3>
            </dt>
            <dd>
                <h3><strong>{{ CommonHelper::formatTime($comparisonTrademarkResult->user_response_deadline) }}</strong></h3>
            </dd>
        </dl>

        <p>{{ __('labels.comparison_trademark_result.over.text_13') }}</p>

        <ul class="btn_left eol">
            <li>
                <input
                    type="button"
                    class="redirect-cancel btn_a {{ $isCancel ? 'disabled' : '' }}"
                    onclick="window.location = '{{ route('user.refusal.notification.cancel', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}'"
                    {{ $isCancel ? 'disabled' : '' }}
                    value="{{ __('labels.comparison_trademark_result.over.back') }}"
                >
            </li>
        </ul>
        <ul class="footerBtn clearfix">
            <li><input type="button" value="戻る" class="btn_a" onclick="window.location = '{{ route('user.top') }}'"></li>
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
    </script>
    <script>
        function disableInput() {
            const form = $('form');
            form.find('a, input, button, textarea, select').prop('disabled', true)
            form.find('a, input, button, textarea, select').addClass('disabled')
        }
    </script>
    @if($checkDataPlanCorrespondence)
        <script>
            disableInput();
            $('[type=submit]').remove();
        </script>
    @endif
@endsection
