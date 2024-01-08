@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">

        <h2>{{ __('labels.regis-by-trademark.title_h1') }}</h2>

        <form>
            <p class="eol">{{ __('labels.regis-by-trademark.note_1') }}<br />
                {{ __('labels.regis-by-trademark.note_2') }}</p>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="button" onclick="window.location='{{ route('user.apply-trademark-with-product-copied', ['s' => $key]) }}'" value="{{ __('labels.regis-by-trademark.redirectToScreenU031C') }}" class="btn_a redirectToScreenU031c" />
                    <br />
                    {{ __('labels.regis-by-trademark.note_3') }}<br />
                    {{ __('labels.regis-by-trademark.note_4') }}</li>
            </ul>

            <ul class="footerBtn clearfix">
                <li><input type="button" onclick="window.location='{{ route('user.precheck.register-precheck', ['id' => $trademark_id, 's' => $key]) }}'" value="{{ __('labels.regis-by-trademark.redirectToScreenU021') }}" class="btn_a redirectToScreenU021" /><br />
                    {{ __('labels.regis-by-trademark.note_5') }}<br />
                    {{ __('labels.regis-by-trademark.note_6') }}</li>
            </ul>

            <ul class="footerBtn clearfix">
                <li><input type="button" onclick="window.location='{{ route('user.precheck.redirect-to-search-ai', ['s' => $key]) }}'" value="{{ __('labels.regis-by-trademark.redirectToScreenU020b') }}" class="btn_a redirectToScreenU020b" />
                    <br />{{ __('labels.regis-by-trademark.note_7') }}</li>
            </ul>

            <ul class="footerBtn clearfix">
                <li><input type="button" onclick="window.location='{{ route('user.precheck.redirect-to-u011', $trademark_id) }}'" value="{{ __('labels.regis-by-trademark.redirectToScreenU011') }}" class="btn_a redirectToScreenU011" /><br />
                    {{ __('labels.regis-by-trademark.note_8') }}</li>
            </ul>
        </form>
    </div><!-- /contents -->
@endsection
