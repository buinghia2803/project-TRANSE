@extends('admin.layouts.app')
@section('main-content')
<style>
    .error {
        font-size: unset;
    }
</style>
<!-- contents -->
<div id="contents">
    <!-- contents inner -->
    <div class="wide clearfix">
        @include('compoments.messages')

        <form id="form" action="{{ route('admin.refusal.pre-question.create') }}" autocomplete="off" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $comparisonTrademarkResult->id }}">
            {{-- Trademark table --}}
            @include('admin.components.includes.trademark-table', [
                'table' => $trademarkTable,
            ])
            {{-- End Trademark table --}}

            <table class="normal_a eol">
                <caption>
                    {{ __('labels.pre_question.index.caption') }}
                </caption>
                <tr>
                    <th>{{ __('labels.pre_question.index.th_1') }}</th>
                    <td colspan="3">
                        {{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date, 'Y/m/d') }}
                        <input type="button" value="{{ __('labels.pre_question.index.btn_1') }}" class="btn_b" id="click_file_pdf"/>
                        @foreach ($trademarkDocuments as $ele_a)
                            <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
                        @endforeach
                        <a class="btn_a" target="_blank" href="{{ route('admin.refusal.eval-report.create-examine', ['id' => $comparisonTrademarkResult->id]) }}?type=view" style="display: unset">{{ __('labels.pre_question.index.btn_2') }}</a>
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.pre_question.index.th_2') }}</th>
                    <td colspan="3">{{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline, 'Y/m/d') }}</td>
                </tr>
            </table>

            <h3>{{ __('labels.pre_question.index.h3') }}</h3>

            <dl class="w10em eol clearfix js-scrollable change_datepicker">
                <dt>{{ __('labels.pre_question.index.dt_1') }}</dt>
                <dd><input type="text" name="user_response_deadline" id="datepicker" /></dd>
            </dl>

            <br />
            <br />

            <table class="normal_b mb05 append_html">
                <tr>
                    <th style="min-width: 57.4px">No.</th>
                    <th style="min-width: 458.6px">{{ __('labels.pre_question.index.th_3') }}</th>
                </tr>
                @foreach ($reasonQuestionDetails as $key => $item)
                    <tr class="pre_question">
                        <td class="center">
                            <span class="No">{{ $key + 1 }}</span>.<br />
                            <input type="hidden" name="data[{{ $key }}][id]" value="{{ $item->id ?? '' }}">
                            <input type="button" value="{{ __('labels.pre_question.index.delete') }}" class="small btn_d delete_question" />
                        </td>
                        <td><textarea class="middle_b data-question w-100" name="data[{{ $key }}][question]">{!! $item->question ?? '' !!}</textarea></td>
                    </tr>
                @endforeach
            </table>

            <p class="eol"><a class="click_append" href="javascript:void(0)">{{ __('labels.pre_question.index.append') }}</a></p>

            <dl class="w10em eol clearfix checkbox_question">
                <dt>{{ __('labels.pre_question.index.dt_2') }}</dt>
                <dd>
                    <input
                        type="checkbox"
                        name="question_status"
                        {{ $reasonQuestionNo && isset($reasonQuestionNo->question_status) && $reasonQuestionNo->question_status == App\Models\ReasonQuestion::QUESTION_STATUS_REQUIRED ? 'checked' : '' }}
                        value="{{ App\Models\ReasonQuestion::QUESTION_STATUS_REQUIRED }}"
                    />{{ __('labels.pre_question.index.checkbox') }}
                </dd>
            </dl>

            <p class="eol">
                {{ __('labels.pre_question.index.p') }}<br />
                <textarea class="middle_c" name="content">{!! $reasonComment->content ?? '' !!}</textarea>
            </p>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.pre_question.index.btn_3') }}" name="submit" class="btn_b" /></li>
                <li><input type="submit" value="{{ __('labels.pre_question.index.btn_4') }}" name="submitRedirect" class="btn_c" /></li>
            </ul>
        </form>
    </div>
    <!-- /contents inner -->
</div>
<!-- /contents -->
@endsection
@section('footerSection')
    <script>
        const Common_E001 = '{{ __('messages.general.Common_E001') }}';
        const Common_E024 = '{{ __('messages.general.Common_E024') }}';
        const Common_E025 = '{{ __('messages.general.Common_E025') }}';
        const Common_E038 = '{{ __('messages.general.Common_E038') }}';
        const Common_E046 = '{{ __('messages.general.Common_E046') }}';
        const question_A202_E001 = '{{ __('messages.general.question_A202_E001') }}';
        const close = '{{ __('labels.close') }}';
        const userResponsedeadline = @JSON($reasonQuestionNo->user_response_deadline ?? null);
        const routeDeleteQuestion = @JSON(route('admin.refusal.pre-question.ajax-delete-question'));
        const responseDeadline = @JSON($comparisonTrademarkResult->response_deadline);

        const planCorrespondence = @JSON($planCorrespondence ?? null);
        const TYPE_SIMPLE = '{{ \App\Models\PlanCorrespondence::TYPE_1 }}';
        const TYPE_SELECT = '{{ \App\Models\PlanCorrespondence::TYPE_2 }}';
        const TYPE_PACK = '{{ \App\Models\PlanCorrespondence::TYPE_3 }}';
    </script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/comparison-trademark-result/pre_question.js') }}"></script>
    @if(!empty(request()->type) || $checkFlagRole)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_MANAGER] ])
@endsection
