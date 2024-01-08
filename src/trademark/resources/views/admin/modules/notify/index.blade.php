@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <div class="wide clearfix">
            <form id="form" action="{{route('admin.notify.sendComment')}}" method="POST">
                @csrf
                <h2>{{__('labels.a000_news_edit.title')}}</h2>
                <p class="eol">
                    <textarea class="middle_c" name="value">{{ $settingValue ?? '' }}</textarea>
                    <input type="hidden" name="key" value="{{A000NEWS_EDIT}}">
                </p>
                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{{ __('labels.a302.back') }}" class="btn_a"
                               onclick="window.location = '{{ $backUrl ?? route('admin.home') }}'">
                    </li>
                    <li><input type="submit" value="{{__('labels.precheck.confirm')}}" class="btn_a"></li>
                </ul>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const errorMessageMaxLength = '{{__('messages.common.errors.Common_E026')}}'
        const errorMessageRequired = '{{ __('messages.general.Common_E001') }}';
        validation('#form', {
            'value': {
                required: true,
                maxlength: 1000
            }
        }, {
            value: {
                required: errorMessageRequired,
                maxlength: errorMessageMaxLength
            }
        })
    </script>
@endsection
