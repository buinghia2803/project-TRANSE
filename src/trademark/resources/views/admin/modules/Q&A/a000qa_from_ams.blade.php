@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        @include('compoments.messages')

        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form_validate" action="{{ route('admin.create.question', $userInfo->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <h2>{{ __('labels.qa.user_info.text_1') }} {{ __('labels.qa.user_info.text_2') }}</h2>
                <table class="normal_b mb10">
                    <caption>{{ __('labels.qa.user_info.text_3') }}</caption>
                    <tr>
                        <th>{{ __('labels.qa.user_info.text_4') }}</th>
                        <td>{{ $userInfo->info_name }}</td>
                        <th>{{ __('labels.qa.user_info.text_5') }}</th>
                        <td>{{ isset($userInfo->info_type_acc) == 1 ? '法人' : '個人' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.qa.user_info.text_6') }}</th>
                        <td>{{ $userInfo->contact_name }}</td>
                        <th>{{ __('labels.qa.user_info.text_7') }}</th>
                        <td>{{ isset($userInfo->contact_type_acc) == 1 ? '法人' : '個人' }}</td>
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
                <p class="eol">
                    <a href="{{ route('admin.search.application-list') }}" class="btn_a">{{ __('labels.qa.admin.btn_submit_user') }}</a></p>
                <table class="normal_b mb10">
                    <tr>
                        <th>No.</th>
                        <th>{{ __('labels.qa.from_ams.text_1') }}</th>
                    </tr>
                    <tr>
                        <td>1. </td>
                        <td>
                            <textarea class="middle_b" name="question_content">{{ $questionAnswers->question_content ?? '' }}</textarea>
                        </td>
                    </tr>
                </table>
                <p>{{ __('labels.qa.from_ams.text_4') }}<br />
                    <input type="file" id="files" name="question_attaching_file[]" multiple />
                    <br>
                    @if ($arrQuestionFile)
                        @foreach ($arrQuestionFile as $item)
                            @php
                                if ($arrQuestionFile) {
                                    $convertItem = explode('/', $item);
                                }
                            @endphp
                            <a href="{{ $item ?? '' }}"
                                target="_blank">{{ isset($arrQuestionFile) ? $convertItem[count($convertItem) - 1] : '' }}</a>
                            <br>
                        @endforeach
                    @endif
                </p>
                <dd class="error_file" style="color: red"></dd>
                </p>
                <p>{{ __('labels.qa.from_ams.text_5') }}<input type="date" name="response_deadline_user"
                        value="{{ isset($questionAnswers) ? $questionAnswers->getResponseDeadlineUser() : '' }}" />
                </p>
                <p>{{ __('labels.qa.from_ams.text_6') }}<input type="date" name="response_deadline_admin"
                        value="{{ isset($questionAnswers) ? $questionAnswers->getResponseDeadlineAdmin() : '' }}" />
                </p>

                <p class="eol">
                    {{ __('labels.qa.from_ams.text_7') }}<br />
                    <textarea class="middle_c" name="office_comments">{{ $questionAnswers->office_comments ?? '' }}</textarea>
                </p>
                @foreach ($questionAnswersExist as $key => $item)
                    <div class="qa">
                        <h5>{{ $key + 1 }}</h5>
                        <p>{{ $item->question_type == 1 ? 'お客様からの質問' : 'AMSからの質問' }}
                            （{{ date('Y/m/d', strtotime($item->question_date)) }}）{{ $item->question_content }}</p>
                        <p>
                            {{ $item->question_type == 1 ? 'AMSからの回答' : 'お客様からの回答' }}
                            @if ($item->answer_content != null)
                                （{{ date('Y/m/d', strtotime($item->answer_date)) }}）{{ $item->answer_content }}
                            @else
                                {{ __('labels.qa.from_ams.text_8') }}
                            @endif
                        </p>
                    </div>
                @endforeach
                <p class="eol"><a
                        href="{{ route('admin.question.answers.show.kaito.list', ['user_id' => $userInfo->id]) }}">{{ __('labels.qa.from_ams.text_9') }}
                        ＞＞</a>
                <p>

                <ul class="footerBtn2 clearfix">
                    <li><input type="submit" id="save" name="submitEntry"
                            value="{{ __('labels.qa.admin.btn_save') }}" class="btn_b" /></li>
                    <li><input type="submit" id="confirm" name="submitEntry"
                            value="{{ __('labels.qa.admin.btn_confirm') }}" class="btn_c" /></li>
                    <li><a href="#" onclick="history.back()" class="btn_a"
                            style="font-size: 1.3em;">{{ __('labels.qa.admin.btn_back') }}</a></li>
                </ul>
                <input type="hidden" name="submitEntry" id="type_submit">
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageMaxLength = '{{ __('messages.general.QA_U000_E001') }}';
        const errorMessageMaxLengthOfficeComment = '{{ __('messages.general.Common_E026') }}';
        const errorMessageInvalidFormatFile = '{{ __('messages.general.Common_E023') }}';
        const errorMessageInvalidMaxFile20 = '{{ __('messages.general.Import_A000_E001') }}';
        const draft = '{{ DRAFT_QA }}'
        const confirm = '{{ CONFIRM_QA }}'
        let flug = false

        $('#save').on('click', function(e) {
            e.preventDefault();

            $('#type_submit').val(draft)
            flug = false
            $('#form_validate').submit();
        })

        $('#confirm').on('click', function(e) {
            e.preventDefault();

            $('#type_submit').val(confirm)
            flug = true
            $('#form_validate').submit();
        })

        validation('#form_validate',
            {
                question_content: {
                    required: () => {
                        if (flug == true) {
                            return true
                        }
                        return false
                    },
                    maxlength: 500,
                    checkEnter: true
                },
                "answer_attaching_file[]": {
                    formatFile: true,
                    formatFileSize: 3,
                    maxfiles: 20
                },
                response_deadline_user: {
                    required: () => {
                        if (flug == true) {
                            return true
                        }
                        return false
                    },
                },
                response_deadline_admin: {
                    required: () => {
                        if (flug == true) {
                            return true
                        }
                        return false
                    },
                },
                office_comments: {
                    maxlength: 1000,
                },
            },
            {
                question_content: {
                    required: errorMessageRequired,
                    maxlength: errorMessageMaxLength,
                    checkEnter: errorMessageMaxLength
                },
                "answer_attaching_file[]": {
                    formatFile: errorMessageInvalidFormatFile,
                    formatFileSize: errorMessageInvalidFormatFile,
                    maxfiles: errorMessageInvalidMaxFile20
                },
                response_deadline_user: {
                    required: errorMessageRequired,
                },
                response_deadline_admin: {
                    required: errorMessageRequired,
                },
                office_comments: {
                    maxlength: errorMessageMaxLengthOfficeComment
                },
            },
        )
    </script>
    @include('compoments.readonly', ['only' => [ROLE_MANAGER]])
@endsection
