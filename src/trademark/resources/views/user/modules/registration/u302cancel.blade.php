@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.u302_cancel.title') }}</h2>
        <form id="form" action="{{ route('user.registration.update.cancel', ['id' => $registerTrademark->id]) }}" method="POST">
            @csrf
            <h3 class="eol">{{ __('labels.u302_cancel.text_1') }}<br />
                {{ __('labels.u302_cancel.text_2') }}<br />
                {{ __('labels.u302_cancel.text_3') }}<br />
                <br />
                {{ __('labels.u302_cancel.text_4') }}
            </h3>
            <ul class="footerBtn clearfix">
                <li><input type="button" value="{{ __('labels.u302_cancel.back') }}" class="btn_a" onclick="history.back()" /></li>
                <li><input type="submit" value="{{ __('labels.u302_cancel.submit') }}" class="btn_b" /></li>
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    @if ($isBlockScreen)
        <script>
            const form = $('#form').not('#form-logout');
            form.find('input, textarea, select , button ,a').css('pointer-events', 'none');
            form.find('input, textarea, select , button ,a').addClass('disabled');
        </script>
    @endif
@endsection
