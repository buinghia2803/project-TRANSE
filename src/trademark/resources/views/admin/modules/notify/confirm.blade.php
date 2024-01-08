@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form" action="{{route('admin.notify.postConfirm')}}" method="POST">
                @csrf
                <h2>{{__('labels.a000_news_edit.title')}}</h2>
                <p class="eol">
                    <div>
                        <p class="white-space-pre-line">{!! $data['value'] !!}</p>
                    </div>
                    <input type="hidden" class="middle_c" name="value" value="{{ $data['value'] }}"></input>
                    <input type="hidden" name="key" value="{{A000NEWS_EDIT}}">
                </p>
                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{{ __('labels.a302.back') }}" class="btn_a" onclick="window.location = '{{ route('admin.notify.index', ['s' => $data['s']]) }}'">
                    </li>
                    <li><input type="submit" value="{{__('labels.a000_news_edit.send_to_user')}}" class="btn_b"></li>
                </ul>
            </form>

        </div><!-- /contents inner -->

    </div>
@endsection
