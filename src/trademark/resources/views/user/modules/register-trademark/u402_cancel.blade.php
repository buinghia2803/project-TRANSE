@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">

        <h2>{{ __('labels.u402_cancel.renewal_procedure') }}</h2>
        <form action="{{ route('user.register-trademark.cancel-trademark.post', $registerTrademark->id) }}" method="POST" id="form" class="form-cancel-trademark">
            @csrf
            {!! __('labels.u402_cancel.description') !!}

            <ul class="footerBtn clearfix">
                <li><input type="button" value="{{ __('labels.back') }}" class="btn_a back-page" onclick="history.back()" /></li>
                <li><input type="submit" value="{{ __('labels.confirm') }}" class="btn_b" /></li>
            </ul>
        </form>
    </div><!-- /contents -->
@endsection
@section('script')
    @if($registerTrademark && $registerTrademark->is_cancel == $isCancel)
        <script>
            const form = $('.form-cancel-trademark');
            form.find('button[type=submit], input[type=submit]').prop('disabled', true).css('cursor', 'not-allowed');
        </script>
    @endif
@endsection
