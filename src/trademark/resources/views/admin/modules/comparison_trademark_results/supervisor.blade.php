@extends('admin.layouts.app')
@section('main-content')
<style>
    .ui-datepicker {
        width: 212px; /*what ever width you want*/
    }
</style>
<!-- contents -->
<div id="contents">
    <!-- contents inner -->
    <div class="wide clearfix">
        @include('compoments.messages')

        <form id="form" action="{{ route('admin.refusal.pre-question.post-supervisor') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $comparisonTrademarkResult->id }}">
            <input type="hidden" name="reason_question_no" value="{{ $reasonQuestionNoId }}">
            {{-- Trademark table --}}
            @include('admin.components.includes.trademark-table', [
                'table' => $trademarkTable,
            ])
            {{-- End Trademark table --}}

            <table class="normal_a eol">
                <caption>
                    {{ __('labels.comparison_trademark_result.supervisor.caption') }}
                </caption>
                <tr>
                    <th>{{ __('labels.comparison_trademark_result.supervisor.th') }}</th>
                    <td colspan="3">
                        {{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date, 'Y/m/d') }}
                        <input type="button" value="{{ __('labels.comparison_trademark_result.supervisor.btn') }}" class="btn_b" id="click_file_pdf"/>
                        @foreach ($trademarkDocuments as $ele_a)
                            <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
                        @endforeach
                        <a class="btn_a" target="_blank" href="{{ route('admin.refusal.eval-report.create-examine', ['id' => $comparisonTrademarkResult->id]) }}?type=view" style="display: unset">{{ __('labels.comparison_trademark_result.supervisor.a') }}</a>
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.comparison_trademark_result.supervisor.th_1') }}</th>
                    <td colspan="3">{{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline, 'Y/m/d') }}</td>
                </tr>
            </table>

            <h3>{{ __('labels.comparison_trademark_result.supervisor.h3') }}</h3>

            <dl class="w10em eol clearfix js-scrollable">
                <dt>{{ __('labels.comparison_trademark_result.supervisor.dt') }}</dt>
                <dd><input type="text" name="user_response_deadline" id="datepicker" /></dd>
            </dl>

            <table class="normal_b mb05 append_html">
                <tr>
                    <th style="min-width: 58px">No.</th>
                    <th style="min-width: 460px">
                        {{ __('labels.comparison_trademark_result.supervisor.th_2') }}<br />
                        <button type="button" id="copyAllQuestionToFix" class="btn_a mb05" >
                            {{ __('labels.comparison_trademark_result.supervisor.btn_1') }}
                        </button>
                        <button type="button" id="copyAllQuestionToDecision" class="btn_b" >
                            {{ __('labels.comparison_trademark_result.supervisor.btn_2') }}
                        </button>
                    </th>
                    <th style="min-width: 460px">
                        {{ __('labels.comparison_trademark_result.supervisor.th_3') }}<br />
                        <button type="button" id="copyAllQuestionEditToDecision" class="btn_b" >
                            {{ __('labels.comparison_trademark_result.supervisor.btn_3') }}
                        </button>
                    </th>
                    <th class="center" style="min-width: 110px"></th>
                    <th style="min-width: 460px">{{ __('labels.comparison_trademark_result.supervisor.th_4') }}</th>
                    <th>
                        {{ __('labels.comparison_trademark_result.supervisor.check_all_1') }}<br />
                        {{ __('labels.comparison_trademark_result.supervisor.check_all_2') }} <input type="checkbox" name="checkAll" id="checkAllQuestion" />
                    </th>
                </tr>
                @foreach ($reasonQuestionDetails as $key => $item)
                    <tr class="tr_reason_question_detail">
                        <td class="center">
                            <span class="No">{{ $key + 1 }}</span>.<br />
                            <button type="button" data-question-detail-id="{{ $item->id }}" class="small btn_d delete_question_detail" >{{ __('labels.comparison_trademark_result.supervisor.btn_4') }}</button>
                        </td>
                        <td class="td_item_question">
                            <span class="item_question" style="white-space: pre-line;">{{ $item->question }}</span><br />
                            <input type="hidden" name="data[{{ $key }}][id]" value="{{ $item->id ?? '' }}">
                            <input type="button" value="{{ __('labels.comparison_trademark_result.supervisor.btn_5') }}" class="btn_a mb05 copyQuestionToEdit" /><br />
                            <input type="button" value="{{ __('labels.comparison_trademark_result.supervisor.btn_6') }}" class="btn_b copyQuestionToDecision" />
                        </td>
                        <td>
                            <textarea name="data[{{ $key }}][question_edit]" class="middle_b question_edit w-100">{{ $item->question_edit ?? '' }}</textarea>
                            <input type="hidden" name="data[{{ $key }}][question_edit_hidden]" class="question_edit_hidden" value="{{ $item->question_edit ?? '' }}">
                        </td>
                        <td class="center">
                            <input type="button" value="決定" class="btn_b copyEditToDecision" />
                        </td>
                        <td class="td_question_decision">
                            <span class="question_decision" style="white-space: pre-line;">{{ $item->question_decision }}</span>
                            <input type="hidden" name="data[{{ $key }}][question_decision]" class="input_question_decision" value="{{ $item->question_decision ?? '' }}">
                        </td>
                        <td class="center">
                            <input type="checkbox" name="data[{{ $key }}][is_confirm]" value="1" {{ $item->is_confirm ? 'checked' : '' }} class="checkQuestion"/>
                            {{ __('labels.comparison_trademark_result.supervisor.check_single') }}
                        </td>
                    </tr>
                @endforeach
            </table>

            <p class="eol"><a class="click_append" href="javascript:void(0)">{{ __('labels.comparison_trademark_result.supervisor.append') }}</a></p>

            <dl class="w10em eol clearfix checkbox_question">
                <dt>{{ __('labels.comparison_trademark_result.supervisor.dt_1') }}</dt>
                <dd>
                    <input
                        type="checkbox"
                        name="question_status"
                        {{ $reasonQuestionNo && isset($reasonQuestionNo->question_status) && $reasonQuestionNo->question_status == App\Models\ReasonQuestion::QUESTION_STATUS_REQUIRED ? 'checked' : '' }}
                        value="{{ App\Models\ReasonQuestion::QUESTION_STATUS_REQUIRED }}"
                    />{{ __('labels.comparison_trademark_result.supervisor.text') }}
                </dd>
            </dl>

            <p class="eol">
                {{ __('labels.comparison_trademark_result.supervisor.p') }}<br />
                <textarea class="middle_c" name="content">{{ $reasonComment->content ?? '' }}</textarea>
            </p>

            <ul class="footerBtn clearfix">
                <li><input type="submit" name="draft" value="{{ __('labels.comparison_trademark_result.supervisor.btn_7') }}" class="btn_b" /></li>
                <li><input type="submit" name="submit" value="{{ __('labels.comparison_trademark_result.supervisor.btn_8') }}" class="btn_c" /></li>
            </ul>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="submit" name="submit_no_question" value="{{ __('labels.comparison_trademark_result.supervisor.btn_9') }}" class="btn_b" />
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
        const Common_E001 = '{{ __('messages.general.Common_E001') }}';
        const Common_E024 = '{{ __('messages.general.Common_E024') }}';
        const Common_E025 = '{{ __('messages.general.Common_E025') }}';
        const Common_E046 = '{{ __('messages.general.Common_E046') }}';
        const Common_E038 = '{{ __('messages.general.Common_E038') }}';
        const question_A202_E001 = '{{ __('messages.general.question_A202_E001') }}';
        const userResponseDeadline = @JSON($reasonQuestionNo->user_response_deadline ?? null);
        const comparisonTrademarkResultResponseDeadline = @JSON($comparisonTrademarkResult->response_deadline ?? null);
        const routeDeleteQuestion = '{{ route('admin.refusal.pre-question.delete-question-detail', ['id' => '[id]'])}}';
        const questionStatus = @JSON($reasonQuestionNo->question_status ?? null);
        const checkIsConfirm = @JSON($checkIsConfirm ?? false);
    </script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/comparison-trademark-result/supervisor.js') }}"></script>
    @if($checkIsConfirm)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_SUPERVISOR] ])
@endsection
