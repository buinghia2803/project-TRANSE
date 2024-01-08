@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">

        @include('admin.components.includes.messages')

        <form action="{{ route('user.refusal.pre-question.reply.post', [
                'id' => $comparisonTrademarkResult->id,
                'reason_question_no' => $reasonQuestionNo->id
        ]) }}" id="form" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="from_page" class="from_page" value=""/>

            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <h2>{{ __('labels.u202.title_page') }}</h2>

            <div class="info mb10">
                <h3>{{ __('labels.form_trademark_information.title') }}</h3>
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable,
                ])
            </div><!-- /info -->

            <dl class="mb20 w20em clearfix middle wp-sending_noti_rejection_date">
                <dt class="dt-sending_noti_rejection_date">
                    <span>{{ __('labels.comparison_trademark_result.index.date_1') }} {{ ($comparisonTrademarkResult && $comparisonTrademarkResult->sending_noti_rejection_date) ? \App\Models\ComparisonTrademarkResult::showDateFormatJapanese($comparisonTrademarkResult->sending_noti_rejection_date) : '' }}</span><br>
                    <span>{{ __('labels.comparison_trademark_result.index.date_2') }} {{ ($comparisonTrademarkResult && $comparisonTrademarkResult->response_deadline) ? \App\Models\ComparisonTrademarkResult::showDateFormatJapanese($comparisonTrademarkResult->response_deadline) : '' }}</span>
                </dt>
                <dd class="dd-click_file_pdf"><input type="button"
                                                     value="{{ __('labels.comparison_trademark_result.index.btn') }}"
                                                     id="click_file_pdf" class="btn_b"/></dd>
            </dl>
            @foreach ($trademarkDocuments as $ele_a)
                <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
            @endforeach

            @if (!in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01, OVER_04, OVER_04B, OVER_05]))
                <p>{{ __('labels.u202.des_1') }}<br>{!! __('labels.u202.des_2') !!}</p>
                <p class="eol">
                    <input type="button" value="{{ __('labels.u202.redirect_u210alert02') }}"
                           onclick="window.location='{{ route('user.refusal.extension-period.alert', ['id' => $comparisonTrademarkResult->trademark_id]) }}'"
                           class="btn_b"/>
                </p>
            @endif

            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <dl class="w16em clearfix">
                <dt><h3><strong>{{ __('labels.u202.ams') }}</strong></h3></dt>
                <dd><h3>
                        <strong>{{ ($reasonQuestionNo && $reasonQuestionNo->user_response_deadline) ? \App\Models\ComparisonTrademarkResult::showDateFormatJapanese($reasonQuestionNo->user_response_deadline) : '' }}</strong>
                    </h3></dd>
            </dl>

            @include('user.modules.comparison-trademark-result.u202.includes._list_aswer_question', ['reasonQuestionDetails' => $reasonQuestionDetails])

            <ul class="footerBtn clearfix" style="margin-top: 3em">
                <li><input type="submit" value="{{ __('labels.support_first_times.save_return') }}"
                           class="btn_a saveDraftU202 mrb-8"/></li>
                <li><input type="submit" value="{{ __('labels.precheck.confirm') }}"
                           class="btn_b saveDraftRedirectToKakunin"/></li>
            </ul>
        </form>
    </div><!-- /contents -->
@endsection
@section('css')
    <style>
        .input-files {
            display: none;
        }

        .mrb-8 {
            margin-bottom: 8px;
        }

        .delete-file-icon {
            display: inline-block;
            position: relative;
            top: 10px;
            cursor: pointer;
            padding: 5px;
        }

        .jconfirm.jconfirm-white .jconfirm-box, .jconfirm.jconfirm-light .jconfirm-box {
            text-align: center;
        }

        .jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons {
            float: none !important;
        }

        @media screen and (max-width: 600px) {
            .wp-sending_noti_rejection_date {
                display: grid !important;
            }

            .dd-click_file_pdf {
                margin: 0 !important;
                margin-bottom: 5px !important;
            }
        }
    </style>
@endsection
@section('footerSection')
    <script>
        const errorMessageRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageMaxLength500 = '{{ __('messages.general.Common_E046') }}';
        const errorMessageMaxFileSize3MB = '{{ __('messages.general.Common_E060_1') }}'
        const errorMessageLimit20FileUpdate = '{{ __('messages.general.Import_A000_E001') }}'
        const Common_E049 = '{{ __('messages.general.Common_E049') }}';
        const U202_DRAFT = '{{ U202_DRAFT }}';
        const U202_DRAFT_TO_KAKUNIN = '{{ U202_DRAFT_TO_KAKUNIN }}';
        const routeDeleteFileAjax = '{{ route('user.refusal.pre-question.reply.delete-file-ajax') }}';
        const routeAnkenTop = '{{ route('user.top') }}';
        const labelTop = '{{ __('labels.to_anken_top') }}';
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/comparison_trademark_result/u202/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/comparison_trademark_result/u202/index.js') }}"></script>
    @if(!$flagEditPage)
        <script>disabledScreen()</script>
    @endif
@endsection
