@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            @include('admin.components.includes.messages')
            <form id="form"
                  action="{{ route('admin.refusal.pre-question-re.supervisor.save', ['id' => $comparisonTrademarkResult->id, 'reason_question_no' => $reasonQuestionNo->id ]) }}"
                  method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $comparisonTrademarkResult->id }}">
                <input type="hidden" name="reason_question_id" value="{{ $reasonQuestion->id ?? null }}">
                <input type="hidden" name="code" value="" class="code-submit" />
                <input type="hidden" name="type" class="type_view" value="{{ \Request::get('type') }}" />
                <input type="hidden" name="reason_question_no_draft" value="{{ $reasonQuestionNoDraft->id ?? null }}" />
                <input type="hidden" name="reason_question_no_id" value="{{ $reasonQuestionNo->id ?? null }}" />
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])
                {{-- End Trademark table --}}

                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->
                <table class="normal_a eol">
                    <caption>{{ __('labels.maching_results.caption') }}</caption>
                    <tbody>
                    <tr>
                        <th>{{ __('labels.pre_question.index.th_1') }}</th>
                        <td>{{ $comparisonTrademarkResult->sending_noti_rejection_date ? Carbon\Carbon::parse($comparisonTrademarkResult->sending_noti_rejection_date)->format('Y/m/d') : '' }}
                            <input type="button" value="{{ __('labels.comparison_trademark_result.index.btn') }}"
                                   id="click_file_pdf" class="btn_b mrg-10"/>
                            @foreach ($trademarkDocuments as $ele_a)
                                <a hidden href="{{ asset($ele_a) }}" class="click_ele_a"
                                   target="_blank">{{ $ele_a }}</a>
                            @endforeach

                            <a target="_blank" href="{{ route('admin.refusal.eval-report.create-examine', ['id' => $comparisonTrademarkResult->id, 'type' => 'view'])}}">
                                <input type="button" value="{{ __('labels.pre_question.index.btn_2') }}" class="btn_a mrg-10">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.refusal.eval_report_create_reason.time_limit_reply_notice') }}</th>
                        <td>
                            {{ $comparisonTrademarkResult->response_deadline ? Carbon\Carbon::parse($comparisonTrademarkResult->response_deadline)->format('Y/m/d') : '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h3>{{ __('labels.a202n_s.responsible_person_caption') }}</h3>

                @include('admin.modules.comparison_trademark_results.pre-question-re.includes._question_past', [
                   'reasonQuestion' => $reasonQuestion,
                   'reasonQuestionDetailsOld' => $reasonQuestionDetailsOld ?? []
               ])

                <hr>

                @include('admin.modules.comparison_trademark_results.pre-question-re.includes._add_question', [
                    'reasonQuestion' => $reasonQuestion,
                    'reasonQuestionDetails' => $reasonQuestionDetails ?? [],
                    'reasonQuestionDetailsOld' => $reasonQuestionDetailsOld ?? [],
                    'reasonCommentOld' => $reasonCommentOld
                ])

                <ul class="footerBtn clearfix">
                    <li><input type="submit" id="btn_keep" value="{{ __('labels.pre_question.index.btn_3') }}" class="btn_b saveDraft"></li>
                </ul>
                <ul class="footerBtn clearfix">
                    <li><input type="submit" class="btn_c submitSaveToEndUser" value="{{ __('labels.apply_trademark.btn_submit') }}"></li>
                </ul>
                <ul class="footerBtn clearfix">
                    <li><input type="submit" class="btn_b saveComplateQuestion" value="{{ __('labels.a202n_s.complate_of_preliminary_note') }}"></li>
                </ul>

                @if ($isModal)
                    <ul class="clearfix center fs12">
                        <li>
                            <input type="button" id="closeModal" data-dismiss="modal" value="{{ __('labels.a203c_rui.close_up') }}" class="btn_a">
                        </li>
                    </ul>
                @endif
            </form>
        </div><!-- /contents inner -->
    </div>
@endsection
@section('css')
    <style>
        .jconfirm-title {
            font-size: 16px!important;
            line-height: 24px!important;
        }
        .jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons {
            float: none!important;
            text-align:center;
        }

        /**
        Calender
         */
        #ui-datepicker-div {
            width: 322px;
        }
        .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active {
            width: 40px;
            height: 30px;
            text-align: center;
            line-height: 26px;
            font-size: 11px;
        }
        .th_no {
            min-width: 50px;
        }
        .th_question {
            min-width: 402px!important;
        }
        .white-space-cls {
            white-space: pre-line;
        }
    </style>
@endsection
@section('script')
    <script>
        const adminRole = @json($adminRole);
        const request = @json(request()->type);
        const ROLE_SUPERVISOR = @json(ROLE_SUPERVISOR);
        const ROLE_MANAGER = @json(ROLE_MANAGER);
        const type = @json(!empty(request()->type));
        const saveDraft = @json(SAVE_DRAFT);
        const viewConst = @json(VIEW);
        const saveToEndUser = @json(SAVE_TO_END_USER);
        const saveComplateQuestion = @json(SAVE_COMPLATE_QUESTION);
        const Common_E001 = '{{ __('messages.general.Common_E001') }}';
        const Common_E024 = '{{ __('messages.general.Common_E024') }}';
        const QA_U000_E001 = '{{ __('messages.general.QA_U000_E001') }}';
        const messageModalSeki = '{{ __('messages.general.Common_E035') }}';
        const messageBlockerPopupIsEnabled = '{{ __('messages.general.blocker_popup_is_enabled') }}';
        const back = '{{ __('messages.apply_trademark.back') }}';
        const regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/;
        const userResponsedeadline = @JSON($reasonQuestionNoDraft->user_response_deadline ?? $reasonQuestionNo->user_response_deadline ?? '');
        const isConfirmTrue = @json($isConfirmReasonQuestion);
        const isConfirmCurrent = @json($reasonQuestionNoDraft ? $reasonQuestionNoDraft->is_confirm : []);
        const questionStatusRequired = @json($questionStatusRequired);
        const questionStatusCurrent = @json($reasonQuestionNoDraft ? $reasonQuestionNoDraft->question_status : []);
        const routeAnkenTop = '{{ route('admin.home') }}';
        const commonE039 = '{{ __('messages.general.Common_E039') }}';
        const questionA202_E001 = '{{ __('messages.general.question_A202_E001') }}';
        const questionA202_E002 = '{{ __('messages.general.question_A202_E002') }}';
        const Common_E046 = '{{ __('messages.general.Common_E046') }}';
        const closeModalText = '{{ __('labels.comparison_trademark_result.plans.close_modal') }}';
        const messageSubmitToUser = '{{ __('messages.general.seki_submit_to_user') }}';
        const messageSaveComplateQuestion = '{{ __('messages.general.requested_a_draft_policy') }}';
        const lengthReasonQuestionDetailsOld = @json($reasonQuestionDetailsOld ? count($reasonQuestionDetailsOld) : 0);
        const checkIsConfirm = @json($checkIsConfirm);
    </script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/comparison-trademark-result/pre-question-re.js') }}"></script>
    <script>
        $('#closeModal').click(function () {
            window.parent.closeModal('#open-modal-iframe')
            window.parent.$('body').removeClass('fixed-body')
        })
    </script>
    @if($checkIsConfirm)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', ['only' => [ROLE_SUPERVISOR]])
@endsection


