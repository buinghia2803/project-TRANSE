@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.qa.user.qa03_kaito.title') }}</h2>
        <form>
            <p>{{ __('labels.qa.user.qa03_kaito.sub_title') }}</p>
            @if (isset($firstQA))
                <div class="qa">
                    <p>
                        {{ $firstQA->question_type == QUESTION_TYPE_CUSTOMER ? 'お客様からの質問' : 'AMSからの質問' }}
                        （{{ CommonHelper::formatTime($firstQA->question_date ?? '', 'Y/m/d') }}）
                        <span class="content_qa">{{ $firstQA->question_content }}</span>
                    </p>
                    <p>
                        {{ $firstQA->question_type == QUESTION_TYPE_CUSTOMER ? 'AMSからの回答' : 'お客様からの回答' }}
                        @if ($firstQA->answer_date != null)
                            （{{ CommonHelper::formatTime($firstQA->answer_date ?? '', 'Y/m/d') }}）
                            <span class="content_qa">{{ $firstQA->answer_content }}</span>
                        @else
                            {{ __('labels.qa.qa03_kaito_list.text_6') }}
                        @endif
                    </p>
                </div>
                @foreach ($listQA as $key => $item)
                    <div class="qa">
                        <p>{{ $key + 1 }}.</p>
                        <p>
                            {{ $item->question_type == QUESTION_TYPE_CUSTOMER ? 'お客様からの質問' : 'AMSからの質問' }}
                            （{{ CommonHelper::formatTime($item->question_date ?? '', 'Y/m/d') }}）
                            <span class="content_qa">{{ $item->question_content }}</span>
                        </p>
                        <p>
                            {{ $item->question_type == QUESTION_TYPE_CUSTOMER ? 'AMSからの回答' : 'お客様からの回答' }}
                            @if ($item->answer_date != null)
                                （{{ CommonHelper::formatTime($item->answer_date ?? '', 'Y/m/d') }}）<span class="content_qa">{{ $item->answer_content }}</span>
                            @else
                                {{ __('labels.qa.qa03_kaito_list.text_6') }}
                            @endif
                        </p>
                    </div>
                @endforeach
            @else
                @foreach ($listQA as $key => $item)
                    <div class="qa">
                        <p>{{ $key + 1 }}.</p>
                        <p>
                            {{ $item->question_type == QUESTION_TYPE_CUSTOMER ? 'お客様からの質問' : 'AMSからの質問' }}
                            （{{ CommonHelper::formatTime($item->question_date ?? '', 'Y/m/d') }}）
                            <span class="content_qa">{{ $item->question_content }}</span>
                        </p>
                        <p>
                            {{ $item->question_type == QUESTION_TYPE_CUSTOMER ? 'AMSからの回答' : 'お客様からの回答' }}
                            @if ($item->answer_date != null)
                                （{{ CommonHelper::formatTime($item->answer_date ?? '', 'Y/m/d') }}）<span class="content_qa">{{ $item->answer_content }}</span>
                            @else
                                {{ __('labels.qa.qa03_kaito_list.text_6') }}
                            @endif
                        </p>
                    </div>
                @endforeach
            @endif
            <ul class="footerBtn2 clearfix">
                <li><a href="{{ route('user.qa.02.qa') }}" class="btn_b">{{ __('labels.qa.user.qa03_kaito.btn1') }}</a>
                </li>
                <li><a href="{{ route('user.top') }}" class="btn_a">トップへ戻る</a>
                </li>
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection
<style>
    .content_qa{
        white-space: pre-line;
    }
</style>
