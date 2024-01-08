@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        @include('compoments.messages')

        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form_validate" action="{{ route('admin.question-answers.update', $userInfo->id) }}" method="POST">
                @csrf
                <h2>{{ __('labels.qa.user_info.text_12') }}　{{ __('labels.qa.user_info.text_11') }}</h2>
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
                    <a href="{{ route('admin.question-answers.search', ['user_id' => $userInfo->id]) }}"
                       onclick="openNewWindow(this.href, 1300, 900); return false;"
                       class="btn_a">{{ __('labels.qa.admin.btn_submit_user') }}</a>
                </p>
                @foreach ($questionAnswersInput as $key => $item)
                    <p>
                        {{ __('labels.qa.qa02_s.text_1') }}<br />
                        <span class="answer_content">{{ $item->question_content }}</span>
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
                            <th>{{ __('labels.qa.qa02_s.text_5') }}</th>
                            <th class="center"></th>
                            <th>{{ __('labels.qa.qa02_s.text_6') }}</th>
                            <th class="center"></th>
                            <th>{{ __('labels.qa.qa02_s.text_7') }}</th>
                        </tr>
                        <tr>
                            <td>{{ $key + 1 }} </td>
                            <td class="middle_b">
                                {{-- <textarea class="middle_b" disabled id="answers_content_old_{{ $item->id }}">{{ $item->answer_content ?? '' }}</textarea> --}}
                                <span
                                    class="answer_content" id="answers_content_old_{{ $item->id }}">{{ $item->answer_content ?? '' }}</span>
                            </td>
                            <td class="center">
                                <button type="button" class="btn_a mb05"
                                    id="get_value_answers_content_old_{{ $item->id }}">修正</button><br />
                                <input type="button" id="clickDecision_{{ $item->id }}" value="決定"
                                    class="btn_b btn-click-decision" /><br />
                            </td>
                            <td>
                                <textarea class="middle_b" id="modify_answers_content_old_{{ $item->id }}"
                                    name="answer_content_edit_{{ $item->id }}">{{ $item->answer_content_edit ?? '' }}</textarea>
                            </td>
                            <td class="center">
                                <button type="button" class="btn_b"
                                    id="push_value_modify_answers_content_old_{{ $item->id }}">{{ __('labels.qa.qa02_s.text_9') }}</button>
                            </td>
                            <td class="middle_b">
                                <div id="answers_content_news_{{ $item->id }}" class="answer_content" style="display: block; min-height: 5em;">{{ $item->answer_content_decision ?? '' }}</div>
                                <textarea class="hiddenInput" name="answer_content_decision_{{ $item->id }}" id="value_answers_content_news_{{ $item->id }}">{{ $item->answer_content_decision ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>

                    <p class="eol">
                        {{ __('labels.qa.qa02_s.text_10') }}<br />
                        <textarea class="middle_c" name="office_comments_{{ $item->id }}">{{ $item->office_comments ?? '' }}</textarea>
                    </p>
                @endforeach
                @foreach ($questionAnswers as $key => $item)
                    <div class="qa">
                        <h5>{{ $key + 1 }}</h5>
                        <p>{{ $item->question_type == 1 ? 'お客様からの質問' : 'AMSからの質問' }}
                            （{{ date('Y/m/d', strtotime($item->question_date)) }}）<span class="content_qa">{{ $item->question_content }}</span></p>
                        <p>
                            {{ $item->question_type == 1 ? 'AMSからの回答' : 'お客様からの回答' }}
                            @if ($item->answer_content != null)
                                （{{ date('Y/m/d', strtotime($item->answer_date)) }}）<span class="content_qa">{{ $item->answer_content }}</span>
                            @else
                                {{ __('labels.qa.qa02_s.text_11') }}
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
                            value="{{ __('labels.qa.admin.btn_display_to_customer') }}" class="btn_c" /></li>
                    <li><input type="button" onclick="history.back()" value="{{ __('labels.qa.admin.btn_back') }}"
                            class="btn_a" /></li>
                </ul>
                <input type="hidden" name="submitEntry" id="type_submit">
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('css')
    <style>
        .content_qa {
            white-space: pre-line;
        }
        .answer_content {
            white-space: pre-line;
        }
        .btn-click-decision {
            font-size: revert;
        }
    </style>
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const draft = '{{ DRAFT_QA }}'
        const confirm = '{{ CONFIRM_QA }}'
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageMaxLength = '{{ __('messages.general.QA_U000_E001') }}'
        const errorMessageMaxLength1000 = '{{ __('messages.general.Common_E026') }}'
        const rules = {}
        const messages = {}
        let listIdItem = @json($questionAnswersInput);
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
            const answersContentOld = $('#answers_content_old_' + item.id).text()
            rules[`answer_content_decision_${item.id}`] = {
                required: () => {
                    if (flug == true) {
                        return true
                    }
                    return false
                },
                maxlength: 500,
            }
            rules[`answer_content_edit_${item.id}`] = {
                maxlength: 500,
            }
            rules[`office_comments_${item.id}`] = {
                maxlength: 1000,
            }
            messages[`answer_content_decision_${item.id}`] = {
                required: errorMessageRequired,
                maxlength: errorMessageMaxLength,
            }
            messages[`answer_content_edit_${item.id}`] = {
                required: errorMessageRequired,
                maxlength: errorMessageMaxLength,
            }
            messages[`office_comments_${item.id}`] = {
                maxlength: errorMessageMaxLength1000,
            }

            $('#get_value_answers_content_old_' + item.id).on('click', function() {
                let modifyAnswersContentOld = $('#modify_answers_content_old_' + item.id).val(
                    answersContentOld)
            })

            $('#clickDecision_' + item.id).on('click', function() {
                let answersContentNews = $('#answers_content_news_' + item.id).text($(
                    '#answers_content_old_' + item.id).text())
                let valueAnswersContentNews = $('#value_answers_content_news_' + item.id).val($(
                    '#answers_content_old_' + item.id).text())
            })

            $('#push_value_modify_answers_content_old_' + item.id).on('click', function() {
                if ($('#modify_answers_content_old_' + item.id).val().length <= 500) {
                    let answersContentNews = $('#answers_content_news_' + item.id).text($(
                        '#modify_answers_content_old_' + item.id).val())
                }

                let valueAnswersContentNews = $('#value_answers_content_news_' + item.id).val($(
                    '#modify_answers_content_old_' + item.id).val())
            })

        })
        validation('#form_validate', rules, messages);
    </script>
    @include('compoments.readonly', ['only' => [ ROLE_SUPERVISOR ]])
@endsection
