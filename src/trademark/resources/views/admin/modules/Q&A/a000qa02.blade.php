@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        @include('compoments.messages')

        <!-- contents inner -->
        <div class="wide clearfix">

            <form id="form_validate" action="{{ route('admin.question-answers.store', $userInfo->id) }}" method="POST">
                @csrf
                <h2>{{ __('labels.qa.user_info.text_1') }}　{{ __('labels.qa.user_info.text_11') }}</h2>
                <input type="hidden" name="qa_id" value="{{ $qaId }}">
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
                    {{-- <input type="submit" value="" class="btn_a" /> --}}
                    <a href="{{ route('admin.question-answers.search', ['user_id' => $userInfo->id]) }}"
                       onclick="openNewWindow(this.href, 1300, 900); return false;"
                       class="btn_a">{{ __('labels.qa.admin.btn_submit_user') }}</a>
                </p>
                @foreach ($questionAnswersInput as $key => $item)
                    <hr>
                    <p>
                        {{ __('labels.qa.qa02.text_1') }}<br />
                        <span style="white-space: pre-line;">{{ $item->question_content }} </span>
                        <br>
                        <br>

                        @if ($item->question_attaching_file)
                            {{ __('labels.qa.qa02.text_4') }} ＞＞ <br>
                            @foreach (json_decode($item->question_attaching_file) as $questionAttachFile)
                                @php
                                    $convertItem = explode('/', $questionAttachFile);
                                @endphp
                                <a href="{{ $questionAttachFile ?? '' }}"
                                    target="_blank">{{ isset($questionAnswersInput) ? $convertItem[count($convertItem) - 1] : '' }}</a>
                                <br>
                            @endforeach
                        @endif
                    </p>

                    <table class="normal_b mb05">
                        <tr>
                            <th>No.</th>
                            <th>{{ __('labels.qa.qa02.text_5') }}</th>
                        </tr>
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <textarea class="middle_b" name="answer_content_{{ $item->id }}">{{ isset($item) ? $item->answer_content : '' }}</textarea>
                            </td>
                        </tr>
                    </table>

                    <p class="eol">
                        {{ __('labels.qa.qa02.text_6') }}<br />
                        <textarea class="middle_c" name="office_comment_{{ $item->id }}">{{ isset($item) ? $item->office_comments : '' }}</textarea>
                    </p>
                @endforeach
                @foreach ($questionAnswers as $key => $item)
                    <div class="qa">
                        <h5>{{ $key + 1 }}</h5>
                        <p>
                            {{ $item->question_type == QUESTION_TYPE_CUSTOMER ? 'お客様からの質問' : 'AMSからの質問' }}
                            （{{ date('Y/m/d', strtotime($item->question_date)) }}）<span style="white-space: pre-line;">{{ $item->question_content }}</span>
                        </p>
                        <p>
                            {{ $item->question_type == QUESTION_TYPE_CUSTOMER ? 'AMSからの回答' : 'お客様からの回答' }}
                            @if ($item->answer_content != null)
                                （{{ date('Y/m/d', strtotime($item->answer_date)) }}）<span style="white-space: pre-line;">{{ $item->answer_content }}</span>
                            @else
                                {{ __('labels.qa.qa02.text_7') }}
                            @endif
                        </p>
                    </div>
                @endforeach
                <p class="eol"><a
                        href="{{ route('admin.question.answers.show.kaito.list', [
                            'user_id' => $userInfo->id,
                            'qa_id' => Request::get('qa_id'),
                        ]) }}">{{ __('labels.qa.qa02.text_8') }}
                        ＞＞</a>
                <p>
                <ul class="footerBtn2 clearfix">
                    <li><input type="submit" id="save" name="submitSave" value="{{ __('labels.qa.admin.btn_save') }}"
                            class="btn_b" /></li>
                    <li><input type="submit" id="confirm" name="submitConfirm"
                            value="{{ __('labels.qa.admin.btn_confirm') }}" class="btn_c" /></li>
                    <li><input type="button" name="back" value="{{ __('labels.qa.admin.btn_back') }}" class="btn_a" />
                    </li>
                </ul>
                <input type="hidden" name="submitEntry" id="type_submit">
            </form>
        </div><!-- /contents inner -->

    </div><!-- /contents -->
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageMaxLength = '{{ __('messages.general.QA_U000_E001') }}';
        const draft = '{{ DRAFT_QA }}'
        const confirm = '{{ CONFIRM_QA }}'
        let listIdItem = @json($questionAnswersInput);
        const rules = {}
        const messages = {}
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

        jQuery.map(listIdItem, function(item) {
            rules[`answer_content_${item.id}`] = {
                required: () => {
                    if (flug == true) {
                        return true
                    }
                    return false
                },
                maxlength: 500,
                checkEnter: true
            }
            rules[`office_comment_${item.id}`] = {
                maxlength: 1000,
                checkEnter: true
            }
            messages[`answer_content_${item.id}`] = {
                required: errorMessageRequired,
                maxlength: errorMessageMaxLength,
                checkEnter: errorMessageMaxLength
            }
            messages[`office_comment_${item.id}`] = {
                maxlength: errorMessageMaxLength,
                checkEnter: errorMessageMaxLength,
            }
        })

        validation('#form_validate', rules, messages);

        $("#files").change(function() {
            const [file] = this.files
            if (file.size > 3000000) {
                $('.error_file').empty().append(errorMessageMaxSizeImage);
            } else {
                $('.error_file').empty();
            }
        });
    </script>
    @include('compoments.readonly', ['only' => [ ROLE_MANAGER ]])
@endsection
