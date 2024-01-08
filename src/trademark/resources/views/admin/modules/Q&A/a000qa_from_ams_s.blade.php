@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        @include('compoments.messages')

        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form_validate" action="{{ route('admin.modify.question', $userInfo->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <h2>{{ __('labels.qa.user_info.title_seki') }} {{ __('labels.qa.user_info.text_2') }}</h2>
                <table class="normal_b mb10">
                    <caption>{{ __('labels.qa.user_info.text_3') }}</caption>
                    <tr>
                        <th>{{ __('labels.qa.user_info.text_4') }}</th>
                        <td>{{ $userInfo->info_name }}</td>
                        <th>{{ __('labels.qa.user_info.text_5') }}</th>
                        <td>{{ isset($userInfo->info_type_acc) == INFO_TYPE_ACC_GROUP ? '法人' : '個人' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.qa.user_info.text_6') }}</th>
                        <td>{{ $userInfo->contact_name }}</td>
                        <th>{{ __('labels.qa.user_info.text_7') }}</th>
                        <td>{{ isset($userInfo->contact_type_acc) == CONTACT_TYPE_ACC_GROUP ? '法人' : '個人' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.qa.user_info.text_8') }}</th>
                        <td colspan="3">{{ $userInfo->contact_name_department }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.qa.user_info.text_9') }}</th>
                        <td>{{ $userInfo->contact_name_manager }}</td>
                        <td colspan="3" class="center">
                            <a href="{{ route('admin.question-answers.show', $userInfo->id) }}" class="btn_a"
                                target="_blank">{{ __('labels.qa.user_info.text_10') }}</a>
                        </td>
                    </tr>
                </table>
                <p class="eol"><a href="{{ route('admin.search.application-list') }}"
                        class="btn_a">{{ __('labels.qa.admin.btn_submit_user') }}</a></p>
                <table class="normal_b mb10">
                    <tr>
                        <th>No.</th>
                        <th>{{ __('labels.qa.from_ams_s.text_1') }}</th>
                        <th class="center"></th>
                        <th>{{ __('labels.qa.from_ams_s.text_2') }}</th>
                        <th class="center"></th>
                        <th>{{ __('labels.qa.from_ams_s.text_3') }}</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="middle_b">
                            <span id="question_content_old" class="question_content">{{ $questionAnswer->question_content ?? '' }}</span>
                        </td>
                        <td class="center">
                            <input type="button" value="修正" class="btn_a mb05 disabled_input"
                                id="get_value_question_content_old" /><br />
                            <input type="button" value="決定" class="btn_b disabled_input"
                                id="decision_question_content" /><br />
                        </td>
                        <td>
                            <textarea class="middle_b disabled_input" id="modify_question_content_old" name="question_content_edit">{{ $questionAnswer->question_content_edit ?? '' }}</textarea>
                        </td>
                        <td class="center"><input type="button" value="決定" class="btn_b disabled_input"
                                id="push_value_modify_question_content_old" /></td>
                        <td class="middle_b">
                            <span id="question_content_new"
                                class="question_content"
                                style="display: block; min-height: 5em;white-space: pre-line">{{ $questionAnswer->question_content_decision ?? '' }}</span>
                            <textarea name="question_content_decision" id="question_content" class="hiddenInput disabled_input">{{ $questionAnswer->question_content_decision ?? '' }}</textarea>
                        </td>
                    </tr>
                </table>

                <p>{{ __('labels.qa.from_ams_s.text_4') }}<br />
                    <input type="file" id="file" name="question_attaching_file[]" multiple
                        class="disabled_input" /><br>
                    @if (isset($questionAnswer->question_attaching_file))
                        @foreach (json_decode($questionAnswer->question_attaching_file) as $questionAttachFile)
                            @php
                                $convertItem = explode('/', $questionAttachFile);
                            @endphp
                            <a href="{{ $questionAttachFile ?? '' }}"
                                target="_blank">{{ isset($questionAnswer) ? $convertItem[count($convertItem) - 1] : '' }}</a>
                            <br>
                        @endforeach
                    @endif
                </p>
                <p>{{ __('labels.qa.from_ams_s.text_5') }}<input type="date" name="response_deadline_user"
                        class="disabled_input"
                        value="{{ isset($questionAnswer) ? $questionAnswer->getResponseDeadlineUser() : '' }}" />
                </p>
                <p>{{ __('labels.qa.from_ams_s.text_6') }}<input type="date" name="response_deadline_admin"
                        class="disabled_input"
                        value="{{ isset($questionAnswer) ? $questionAnswer->getResponseDeadlineAdmin() : '' }}" />
                </p>

                <p class="eol">
                    {{ __('labels.qa.from_ams_s.text_7') }}<br />
                    <textarea class="middle_c disabled_input" name="office_comments">{{ $questionAnswer->office_comments ?? '' }}</textarea>
                </p>
                @foreach ($listQuestionAnswers as $key => $item)
                    <div class="qa">
                        <h5>{{ $key + 1 }}</h5>
                        <p>{{ $item->question_type == QUESTION_TYPE_CUSTOMER ? 'お客様からの質問' : 'AMSからの質問' }}
                            （{{ date('Y/m/d', strtotime($item->question_date)) }}）{{ $item->question_content }}</p>
                        <p>
                            {{ $item->question_type == QUESTION_TYPE_CUSTOMER ? 'AMSからの回答' : 'お客様からの回答' }}
                            @if ($item->answer_date)
                                （{{ date('Y/m/d', strtotime($item->answer_date)) }}）{{ $item->answer_content }}
                            @else
                                {{ __('labels.qa.from_ams_s.text_8') }}
                            @endif
                        </p>
                    </div>
                @endforeach
                <p class="eol"><a
                        href="{{ route('admin.question.answers.show.kaito.list', ['user_id' => $userInfo->id, 'qa_id' => $questionAnswer->id]) }}">{{ __('labels.qa.from_ams_s.text_9') }}
                        ＞＞</a>
                <p>

                <ul class="footerBtn2 clearfix">
                    <li><input type="submit" name="submitSave" value="{{ __('labels.qa.admin.btn_save') }}"
                            class="btn_b btnSave disabled_input" /></li>
                    <li><input type="submit" name="submitConfirm"
                            value="{{ __('labels.qa.admin.btn_display_to_customer') }}"
                            class="btn_c btnConfirm disabled_input" /></li>
                    <li><a href="#" onclick="history.back()" class="btn_a"
                            style="font-size: 1.3em;">{{ __('labels.qa.admin.btn_back') }}</a></li>
                </ul>
                <input type="hidden" name="qa_id" value="{{ $questionAnswer->id ?? '' }}">
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection

@section('css')
    <style>
        .question_content {
            white-space: pre-line;
        }
    </style>
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        let firstQuestionAnswer = @json($questionAnswer);
        if (firstQuestionAnswer == null) {
            $('.disabled_input').attr('disabled', true);
        }
        const rules = {}
        const messages = {}

        let flug = false
        $('.btnConfirm').click(function() {
            flug = true
        })

        $('.btnSave').click(function() {
            flug = false
        })


        const questionContentOld = $('#question_content_old').text()
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}'
        const errorMessageMaxLength = '{{ __('messages.general.QA_U000_E001') }}'
        const errorMessageInvalidFormatFile = '{{ __('messages.general.Common_E023') }}';

        $('#get_value_question_content_old').on('click', function() {
            $('#modify_question_content_old').val(questionContentOld)
        })

        $('#decision_question_content').on('click', function() {
            $('#question_content_new').text(questionContentOld)

            $('#question_content').val(questionContentOld)
        })

        $('#push_value_modify_question_content_old').on('click', function() {
            $('#question_content_new').text($(
                '#modify_question_content_old').val())

            $('#question_content').val($(
                '#modify_question_content_old').val())
        })

        rules[`question_content_edit`] = {
            maxlength: 500,
        }
        rules[`question_content_decision`] = {
            required: () => {
                if (flug == true) {
                    return true
                }
                return false
            },
            maxlength: 500,
        }
        rules[`question_attaching_file`] = {
            formatFile: () => {
                if (flug == true) {
                    return true
                }
                return false
            },
            formatFileSize: 3
        }
        rules[`office_comments`] = {
            maxlength: 1000,
        }
        messages[`question_content_edit`] = {
            required: errorMessageRequired,
            maxlength: errorMessageMaxLength,
        }
        messages[`question_content_decision`] = {
            required: errorMessageRequired,
            maxlength: errorMessageMaxLength,
        }
        messages[`question_attaching_file`] = {
            formatFile: errorMessageInvalidFormatFile,
            formatFileSize: errorMessageInvalidFormatFile
        }
        messages[`office_comments`] = {
            maxlength: errorMessageMaxLength,
        }

        validation('#form_validate', rules, messages);
    </script>
    @include('compoments.readonly', ['only' => [ROLE_SUPERVISOR]])
@endsection
