@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.recover_id.id_inquiry') }}</h2>

        @include('admin.components.includes.messages')

        <form id="recover_id_no_email_form" method="POST" action="{{ route('auth.recover-id-no-email') }}">
            @csrf
            @method('POST')
            <p> {{ __('messages.recover_id.require_email_password') }} </p>
            <br>
            <dl class="w12em eol clearfix">
                <dt> {{ __('labels.recover_id.registered_email') }} <span class="red">*</span></dt>
                <dd>
                    <input type="text" name="email" value="{{ old('email', '') }}" class="em30" nospace/>
                    @error('email')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>

                <dt> {{ __('labels.recover_id.password') }} <span class="red">*</span></dt>
                <dd>
                    <input type="password" name="password" nospace/>
                    @error('password')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>
            </dl>
            <ul class="footerBtn clearfix">
                <li><input type="submit" name="submitEntry" value="次へ" class="btn_b" /></li>
            </ul>
        </form>
    </div><!-- /contents -->
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageEmail = '{{ __('messages.common.errors.Common_E003') }}';
        const errorMessagePassword = '{{ __('messages.common.errors.Common_E005') }}';
        const errorMessageEmailMaxLength = '{{ __('messages.common.errors.Common_E021') }}';
        validation('#recover_id_no_email_form', {
            'email': {
                required: true,
                isValidEmail: true,
                maxlength: 255
            },
            'password': {
                required: true,
                isValidInfoPassword: true,
            },
        }, {
            'email': {
                required: errorMessageRequired,
                isValidEmail: errorMessageEmail,
                maxlength: errorMessageEmailMaxLength
            },
            'password': {
                required: errorMessageRequired,
                isValidInfoPassword: errorMessagePassword,
            },
        });
    </script>
@endsection
