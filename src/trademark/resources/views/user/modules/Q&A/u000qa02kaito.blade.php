@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">

        @include('compoments.messages')

        <form id="form_validate" action="{{ route('user.qa.02.qa.kaito.create.answer') }}" enctype="multipart/form-data"
            method="POST">
            @csrf
            <h2>{{ __('labels.qa.user.qa02_kaito.title') }}</h2>
            <p>{{ __('labels.qa.user.qa02_kaito.sub_title') }}</p>
            {{-- @foreach ($questionsInput as $key => $item) --}}
            <dl class="w16em clearfix">
                <dt>
                    <h3><strong> {{ __('labels.qa.user.qa02_kaito.strong_title') }}</strong></h3>
                </dt>
                <dd>
                    <h3><strong>{{ CommonHelper::formatTime($questions->response_deadline_admin ?? '', 'Y年m月d日') }}</strong>
                    </h3>
                </dd>
            </dl>
            <p>{{ __('labels.qa.user.qa02_kaito.ams_q') }}（{{ CommonHelper::formatTime($questions->created_at ?? '', 'Y/m/d') }}）{{ $questions->question_content ?? '' }}
            </p>

            <dl class="w10em eol clearfix">

                <dt>{{ __('labels.qa.user.qa02_kaito.textarea') }}</dt>
                <dd>
                    <textarea class="middle_b custom_btn" name="answer_content">{{ $questions->answer_content ?? '' }}</textarea>
                </dd>
                <dt>{{ __('labels.qa.user.qa02_kaito.input') }}</dt>
                <dd><input type="file" id="file" name="answer_attaching_file[]" multiple class="custom_btn"></dd>
                @if (isset($questions) && $questions->answer_attaching_file)
                    @foreach (json_decode($questions->answer_attaching_file) as $answerUser)
                        @php
                            $convertItem = explode('/', $answerUser);
                        @endphp
                        <dd><a href="{{ $answerUser ?? '' }}"
                                target="_blank">{{ isset($questionsInput) ? $convertItem[count($convertItem) - 1] : '' }}</a>
                        </dd>
                    @endforeach
                @endif
                <dd class="error_file" style="color: red"></dd>
            </dl>
            {{-- @endforeach --}}
            @foreach ($questionsInput as $key => $item)
                <div class="qa">
                    <h5>{{ $key + 1 }}</h5>
                    <p>{{ $item->question_type == QUESTION_TYPE_CUSTOMER ? 'お客様からの質問' : 'AMSからの質問' }}
                        （{{ $item->question_date ? date('Y/m/d', strtotime($item->question_date)) : '' }}）<span class="content_qa">{{ $item->question_content ?? '' }}</span>
                    </p>
                    <p>{{ $item->question_type == QUESTION_TYPE_CUSTOMER ? 'AMSからの回答' : 'お客様からの回答' }}
                        （{{ $item->answer_date != null ? date('Y/m/d', strtotime($item->answer_date)) : '' }}）<span class="content_qa">{{ $item->answer_content ?? '' }}</span>
                    </p>
                </div>
            @endforeach

            <p class="eol"><a href="{{ route('user.qa.03.kaito.list', ['id' => $id]) }}">全てのQ&Aを見る ＞＞</a></p>
            <ul class="footerBtn2 clearfix">
                <li><input type="submit" name="draft" value="{{ __('labels.qa.admin.btn_save') }}"
                        class="btn_b custom_btn" />
                </li>
                <li><input type="submit" name="submit" value="{{ __('labels.qa.admin.btn_submit') }}"
                        class="btn_b custom_btn" />
                </li>
                <li><button onclick="history.back()" type="button" class="btn_a"
                        style="font-size: 1.3em !important;">{{ __('labels.qa.admin.btn_back') }}</button></li>
            </ul>
            <input type="hidden" name="question_answer_id" value="{{ $id }}">
        </form>
    </div>
    <!-- /contents -->
@endsection
<style>
    .content_qa{
        white-space: pre-line;
    }
</style>
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        let questions = @json($questions);
        if (questions == null || questions != null && questions.answer_date != null) {
            const form = $('#form_validate').not('#form-logout');
            form.find('input, textarea, select , button ').addClass('disabled');
            form.find('input, textarea, select , button ').css('pointer-events', 'none');
        }
        const rules = {}
        const messages = {}
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}'
        const errorMessageMaxLength = '{{ __('messages.general.QA_U000_E001') }}'
        const errorMessageInvalidFormatFile = '{{ __('messages.general.QA_U000_E005') }}'

        rules[`answer_content`] = {
            required: true,
            maxlength: 500,
            isFullwidthEnter: true
        }
        rules[`answer_attaching_file`] = {
            formatFile: true,
            formatFileSize: 3
        }
        messages[`answer_content`] = {
            required: errorMessageRequired,
            maxlength: errorMessageMaxLength,
            isFullwidthEnter: errorMessageMaxLength
        }
        messages[`answer_attaching_file`] = {
            formatFile: errorMessageInvalidFormatFile,
            formatFileSize: errorMessageInvalidFormatFile
        }

        validation('#form_validate', rules, messages);
    </script>
@endsection
