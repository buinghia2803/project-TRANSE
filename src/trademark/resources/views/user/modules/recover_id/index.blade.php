@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.recover_id.id_resend') }}</h2>

        @include('admin.components.includes.messages')

        <form id="recover_id_form" method="POST" action="{{ route('auth.recover-id') }}">
            @csrf
            @method('POST')
            <p>{{ __('messages.recover_id.first_attention') }}<br />
                {{ __('messages.recover_id.second_attention') }}</p>
            <br>
            <dl class="w12em eol clearfix">
                <dt>{{ __('labels.recover_id.email_address') }}</dt>
                <dd>
                    <input type="text" name="email" value="{{ old('email', '') }}" class="em30" nospace/>
                    @error('email')
                        <div class="error-valid">
                            {{ $message }}
                        </div>
                    @enderror
                </dd>
            </dl>
            <p><a href="{{ route('auth.show-recover-id-no-email') }}" target="_blank">{{ __('labels.recover_id._no_email') }} &gt;&gt;</a>
            </p>
            <ul class="footerBtn clearfix">
                <input type="submit" name="submitEntry" value="送信" class="btn_b" />
            </ul>
        </form>
    </div><!-- /contents -->
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageEmail = '{{ __('messages.common.errors.Common_E002') }}';
        const errorMessageEmailMaxLength = '{{ __('messages.common.errors.Common_E021') }}';
        validation('#recover_id_form', {
            'email': {
                required: true,
                isValidEmail: true,
                maxlength: 255
            },
        }, {
            'email': {
                required: errorMessageRequired,
                isValidEmail: errorMessageEmail,
                maxlength: errorMessageEmailMaxLength
            },
        });
    </script>
@endsection
