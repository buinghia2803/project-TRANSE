@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">

        <h2>パスワード再設定</h2>

        @include('admin.components.includes.messages')

        <form id="form" action="{{ route('auth.forgot-password.send-mail') }}" method="post">
            @csrf
            <p>パスワードを忘れた方は、以下にIDとご登録のメールアドレスを入力して次へお進みください。</p>

            <dl class="w12em eol clearfix">

                <dt>ID <span class="red">*</span></dt>
                <dd><input type="text" name="info_member_id" value="" onkeypress="return RestrictSpace()" /></dd>
                @error('info_member_id')
                    <div class="error-valid">
                        {{ $message }}
                    </div>
                @enderror
                <dt>登録メールアドレス <span class="red">*</span></dt>
                <dd><input type="text" name="email" value="" class="em30"
                        onkeypress="return RestrictSpace()" /></dd>
                @error('email')
                    <div class="error-valid">
                        {{ $message }}
                    </div>
                @enderror
            </dl>

            <p><a href="{{ route('auth.forgot-password.no-email') }}">登録メールアドレスおよび連絡先メールアドレスのどちらも使えない方はこちらから
                    &gt;&gt;</a></p>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="submit" id="submitEntry" name="submitEntry" value="次へ" class="btn_b" />
                </li>
            </ul>
        </form>
    </div><!-- /contents -->
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageIsValidInfoMemberId = '{{ __('messages.general.Forgot_Password_U000_E008') }}';
        const errorMessageIsValidEmail = '{{ __('messages.common.errors.Common_E002') }}';
        const errorMessageMaxLength255 = '{{ __('messages.common.errors.Common_E021') }}';

        validation('#form', {
            'info_member_id': {
                required: true,
                minlength: 8,
                maxlength: 30,
                isValidInfoMemberId: true,
            },
            'email': {
                required: true,
                maxlength: 255,
                isValidEmail: true,
            },
        }, {
            'info_member_id': {
                required: errorMessageRequired,
                maxlength: errorMessageIsValidInfoMemberId,
                minlength: errorMessageIsValidInfoMemberId,
                isValidInfoMemberId: errorMessageIsValidInfoMemberId,
            },
            'email': {
                required: errorMessageRequired,
                maxlength: errorMessageMaxLength255,
                isValidEmail: errorMessageIsValidEmail,
            },
        });

        function RestrictSpace() {
            if (event.keyCode == 32) {
                return false;
            }
        }

        $('#form').on('submit', function() {
            $('#submitEntry').prop('disabled', true);
        })
    </script>
@endsection
