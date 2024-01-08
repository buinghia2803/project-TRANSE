@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        <h2>{{ __('labels.signup.verify_code_page.title') }}</h2>

        <form action="{{ route('auth.post-verify-code', [ 'token' => $authentication->token ?? '' ]) }}" id="form" method="POST" enctype="multipart/form-data">
            @csrf

            <p>{{ __('labels.signup.verify_code_page.text_1') }}</p>

            <dl class="w06em clearfix">
                <dt>{{ __('labels.signup.form.code') }}</dt>
                <dd>
                    <input type="text" name="code" value="" class="trimSpace"/><br/>
                    @error('code') <span class="notice">{{ $message }}</span> @enderror
                </dd>
            </dl>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.signup.form.submit_code') }}" class="btn_b"/></li>
            </ul>
        </form>
    </div>
@endsection

@section('footerSection')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageCodeRequired = '{{ __('messages.signup.form.required') }}';
        const errorMessageCodeRegex = '{{ __('messages.signup.form.Common_E013') }}';

        validation('#form', {
            'code': {
                required: true,
                isValidCode: true,
            },
        }, {
            'code': {
                required: errorMessageCodeRequired,
                isValidCode: errorMessageCodeRegex,
            },
        });

        $(".trimSpace").keyup(function() {
            const val = $(this).val().trim()
            $(this).val(val)
        });
    </script>
@endsection
