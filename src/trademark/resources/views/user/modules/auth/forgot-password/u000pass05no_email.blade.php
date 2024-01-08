@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">

        <h2>新しいパスワード・登録メールアドレス再設定</h2>

        @include('admin.components.includes.messages')

        <form id="form" action="{{ route('auth.forgot-password.no-email.reset.post', $authentication->token) }}"
            method="POST">
            @csrf
            <p>以下の内容で登録されます。新しいパスワードを入力して次へお進みください。</p>

            <dl class="w16em eol clearfix">

                <dt>新たな登録メールアドレス</dt>
                <dd>{{ $authentication->value }}</dd>

                <dt>新しいパスワード <span class="red">*</span></dt>
                <dd><input id="password" type="password" name="password" value="" nospace/></dd>
                @error('password')
                    <div class="error-valid">
                        <dt>登録メールアドレス <span class="red">*</span></dt>
                        <dd>{{ $message }}</dd>
                    </div>
                @enderror
                <dt>新しいパスワード（確認用）<span class="red">*</span></dt>
                <dd><input type="password" name="password_confirmation" value="" nospace/></dd>
                @error('password_confirmation')
                    <div class="error-valid">
                        <dt>登録メールアドレス <span class="red">*</span></dt>
                        <dd>{{ $message }}</dd>
                    </div>
                @enderror
            </dl>
            <p>※30分経過してもパスワード再設定が完了されない場合、認証コードが無効となりますのでご注意ください</p>
            <p>無効になった場合は、最初から再設定をやり直してください。</p>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="submit" name="submitEntry" value="送信" class="btn_b" />
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
        const errorMessagePasswordInvalid = '{{ __('messages.general.Common_E005') }}'
        validation('#form', {
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

        $('#form').on('submit', function() {
            $('#submitEntry').prop('disabled', true);
        })
    </script>
@endsection
