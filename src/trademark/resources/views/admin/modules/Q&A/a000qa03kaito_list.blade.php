@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form>
                <h2>{{ __('labels.qa.qa03_kaito_list.text_1') }}</h2>

                <h3>{{ __('labels.qa.qa03_kaito_list.text_2') }}</h3>
                <p>{{ __('labels.qa.qa03_kaito_list.text_3') }}</p>
                <div class="qa">
                    <p>{{ isset($firstQuestion) && $firstQuestion->question_type == 1 ? __('labels.qa.qa03_kaito_list.text_4') : __('labels.qa.qa03_kaito_list.text_11') }}
                        （{{ isset($firstQuestion) && $firstQuestion->question_date ? CommonHelper::formatTime($firstQuestion->question_date ?? '', 'Y/m/d') : __('labels.qa.qa03_kaito_list.text_9') }}）　<span
                            class="content_qa">{{ $firstQuestion->question_content ?? '' }}</span>
                    </p>
                    <p class="eol">
                        {{ isset($firstQuestion) && $firstQuestion->question_type == 1 ? __('labels.qa.qa03_kaito_list.text_5') : __('labels.qa.qa03_kaito_list.text_12') }}
                        （{{ isset($firstQuestion) && $firstQuestion->answer_date ? CommonHelper::formatTime($firstQuestion->answer_date ?? '', 'Y/m/d') : __('labels.qa.qa03_kaito_list.text_9') }}）　<span
                            class="content_qa">{{ $firstQuestion->answer_content ?? '' }}</span>
                    </p>
                </div>

                <table class="normal_b mb10 customer-info-tbl">
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

                <p class="eol"> <a href="{{ route('admin.search.application-list') }}"
                        class="btn_a">{{ __('labels.qa.admin.btn_submit_user') }}</a></p>
                <h3>{{ __('labels.qa.qa03_kaito_list.text_10') }}</h3>
                <br />
                <br />
                @foreach ($listQuestion as $key => $item)
                    <div class="qa">
                        <h5>{{ Request::get('page') > 0 ? $key + 1 + (Request::get('page') - 1) * $paginateNumber : $key + 1 }}
                        </h5>
                        <p>{{ $item->question_type == 1 ? 'お客様からの質問' : 'AMSからの質問' }}
                            @if ($item->question_date != null)
                                （{{ CommonHelper::formatTime($firstQuestion->question_date ?? '', 'Y/m/d') }}）<span
                                    class="content_qa">{{ $item->question_content }}</span>
                            @else
                                {{ __('labels.qa.qa03_kaito_list.text_6') }}
                            @endif
                        </p>
                        <p>
                            {{ $item->question_type == 1 ? 'AMSからの回答' : 'お客様からの回答' }}
                            @if ($item->answer_date != null)
                                （{{ CommonHelper::formatTime($firstQuestion->answer_date ?? '', 'Y/m/d') }}）<span
                                    class="content_qa">{{ $item->answer_content }}</span>
                            @else
                                {{ __('labels.qa.qa03_kaito_list.text_6') }}
                            @endif
                        </p>
                    </div>
                @endforeach
                {{ $listQuestion->links() }}
                <ul class="footerBtn2 clearfix">
                    <li>
                        <input type="submit" data-redirect_url="{{ route('admin.question.answers.from.ams', ['user_id' => $userInfo->id, 'qa_id' => $qAId]) }}" value="{{ __('labels.qa.qa03_kaito_list.text_7') }}" class="btn_b">
                    </li>
                    <li>
                        <input type="submit" data-back value="{{ __('labels.qa.qa03_kaito_list.text_8') }}" class="btn_a">
                    </li>
                </ul>
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
        .customer-info-tbl {
            font-size: 14px;
        }
    </style>
@endsection

@section('footerSection')
    <script>
        $('body').on('click', '[data-redirect_url]', function (e) {
            e.preventDefault();
            window.location = $(this).data('redirect_url');
        });
        $('body').on('click', '[data-back]', function (e) {
            e.preventDefault();
            window.location.href = '{{ $backPage }}'
        });
    </script>
@endsection
