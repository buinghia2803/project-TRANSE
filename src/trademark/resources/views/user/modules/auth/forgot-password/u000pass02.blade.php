@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        <h2>新しいパスワードの設定</h2>

        @include('admin.components.includes.messages')

        <form id="form" action="{{ route('auth.forgot-password.reset.post') }}" method="post">
            @csrf
            <input type="hidden" name="token_authen" value="{{ $token }}">
            <p>ID・ご登録のメールアドレスとメールで送られた認証コード、新しいパスワードを入力して次へお進みください。</p>
            <h3>【ユーザー情報・連絡先】</h3>
            <dl class="w16em mb20 clearfix">
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
                <dt>認証コード <span class="red">*</span></dt>
                <dd><input type="text" name="code" value=""
                        onkeypress="return RestrictSpace()" /></dd>
                @error('code')
                    <div class="error-valid">
                        {{ $message }}
                    </div>
                @enderror
            </dl>
            <hr class="mb20" style="text-align:left;width:52em;">
            <dl class="w16em eol clearfix">

                <dt>新しいパスワード <span class="red">*</span></dt>
                <dd><input type="password" name="password" value="" id="password" onkeypress="return RestrictSpace()" /><br />
                    ※アルファベットと数字を混ぜた8文字以上16文字まで。</dd>
                @error('password')
                    <div class="error-valid">
                        {{ $message }}
                    </div>
                @enderror
                <dt>新しいパスワード（確認用）<span class="red">*</span></dt>
                <dd><input type="password" name="password_confirmation" value="" onkeypress="return RestrictSpace()" /></dd>
                @error('password_confirmation')
                    <div class="error-valid">
                        {{ $message }}
                    </div>
                @enderror
            </dl>
            <p>※30分経過してもパスワード再設定が完了されない場合、認証コードが無効となりますのでご注意ください</p>
            <p>無効になった場合は、最初から再設定をやり直してください。</p>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="submit" name="submitEntry" value="次へ" class="btn_b" />
                </li>
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <link href="{{ asset('common/css/forgot-password-validate.css') }}" rel="stylesheet" type="text/css" />
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageConfirmPasswordNotEqual = '{{ __('messages.common.errors.Register_U001_E002') }}';
        const errorMessagePasswordInvalid = '{{ __('messages.common.errors.Common_E005') }}'
        const errorMessageIsValidInfoMemberId = '{{ __('messages.general.Common_E007') }}';
        const errorMessageIsValidEmail = '{{ __('messages.common.errors.Common_E002') }}';
        const errorMessageIsValidNumber = '{{ __('messages.common.errors.Common_E013') }}';
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
            'code': {
                required: true,
                isValidCode: true,
            },
            'password': {
                required: true,
                minlength: 8,
                maxlength: 16,
                isValidInfoPassword: true
            },
            'password_confirmation': {
                equalTo: '#password',
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
            'code': {
                required: errorMessageRequired,
                isValidCode: errorMessageIsValidNumber,
            },
            'password': {
                required: errorMessageRequired,
                minlength: errorMessagePasswordInvalid,
                maxlength: errorMessagePasswordInvalid,
                isValidInfoPassword: errorMessagePasswordInvalid,
            },
            'password_confirmation': {
                equalTo: errorMessageConfirmPasswordNotEqual,
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
