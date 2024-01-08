@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        @include('compoments.messages')
        <h2>{{ __('labels.qa.title_question_f_customer') }}</h2>
        <form id="form" action="{{ route('user.qa.02.qa.create') }}" method="post" enctype="multipart/form-data">
            @csrf
            <p>{{ __('labels.qa.attention_1') }}</p>
            <dl class="w10em eol clearfix">
                <dt>{{ __('labels.qa.content_question') }}</dt>
                <dd>
                    <textarea class="middle_b" name="question_content">{{ $questionDraft->question_content ?? '' }}</textarea>
                </dd>
                <dt>{{ __('labels.qa.attaching_file') }}</dt>
                <dd><input id="files" type="file" name="question_attaching_file[]" multiple
                        accept="image/png, image/gif, image/jpeg , image/jpg"></dd>
                <dt></dt>
                <dd>
                    @if ($convertQuestionAttachFile)
                        @foreach ($convertQuestionAttachFile as $item)
                            @php
                                if ($convertQuestionAttachFile) {
                                    $convertItem = explode('/', $item);
                                }
                            @endphp
                            <a href="{{ asset($item ?? '') }}"
                                target="_blank">{{ isset($convertQuestionAttachFile) ? $convertItem[count($convertItem) - 1] : '' }}</a>
                            <br>
                        @endforeach
                    @endif
                </dd>
            </dl>
            @foreach ($questionAnswers as $key => $item)
                <div class="qa">
                    <h5>{{ $key + 1 }}.</h5>
                    <p>{{ __('labels.qa.c_question_customer') }}（{{ \CommonHelper::formatTime($item->question_date ?? '', 'Y/m/d') }}）
                        <span class="content_qa">{{ $item->question_content }}</span></p>
                    <p>{{ __('labels.qa.answer_ams') }}
                        @if ($item->answer_date != null)
                            （{{ \CommonHelper::formatTime($item->answer_date ?? '', 'Y/m/d') }}）<span class="content_qa">{{ $item->answer_content }}</span>
                        @else
                            {{ __('labels.qa.qa03_kaito_list.text_6') }}
                        @endif
                    </p>
                </div>
            @endforeach
            <p class="eol"><a href="{{ route('user.qa.03.kaito.list') }}">{{ __('labels.qa.kaito_list') }}</a></p>

            <ul class="footerBtn2 clearfix">
                <li><input type="submit" name="submitSave" value="{{ __('labels.qa.btn_entry') }}" class="btn_b" /></li>
                <li><input type="submit" name="submitConfirm" value="{{ __('labels.qa.btn_next') }}" class="btn_b" />
                </li>
                <li><button type="button" type="btn_a" class="btn_a" style="font-size: 1.3em;"
                        onclick="history.back()">{{ __('labels.qa.btn_back') }}</button></li>
            </ul>
            <input type="hidden" name="question_answer_draft_id" value="{{ $questionDraft->id ?? '' }}">
        </form>
    </div>
    <!-- /contents -->
    <style>
        #question_content-error {
            margin: 0 !important;
        }
        .content_qa{
        white-space: pre-line;
        }
    </style>
@endsection

@section('footerSection')
    <link href="{{ asset('common/css/question-answes.css') }}" rel="stylesheet" type="text/css" />
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageConfirmPasswordNotEqual = '{{ __('messages.forgot-password.password_confirm') }}';
        const errorMessageMaxLength = '{{ __('messages.general.QA_U000_E001') }}'
        const errorMessageMaxSizeImage = '{{ __('messages.question_answers.max_size_image') }}'
        const errorMessageInvalidFormatFile = '{{ __('messages.general.Common_E023') }}';
        validation('#form', {
            'question_content': {
                required: true,
                maxlength: 500,
                checkEnter: true
            },
            'question_attaching_file': {
                formatFile: true,
                formatFileSize: 3
            }
        }, {
            'question_content': {
                required: errorMessageRequired,
                maxlength: errorMessageMaxLength,
                checkEnter: errorMessageMaxLength
            },
            'question_attaching_file': {
                formatFile: errorMessageInvalidFormatFile,
                formatFileSize: errorMessageInvalidFormatFile
            }
        });
    </script>
@endsection
