@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>パスワード・登録メールアドレス再設定</h2>

        @include('admin.components.includes.messages')

        <form id="form" action="{{ route('auth.forgot-password.no-email.other-email.post') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <p>ご連絡の取れる新しいメールアドレスを入力して次へお進みください。<br />
                新しいメールアドレスに、認証コードが送信されます。<br />
                この新しいメールアドレスは、新たな登録メールアドレスになります。</p>

            <dl class="w12em eol clearfix">

                <dt>新しいメールアドレス <span class="red">*</span></dt>
                <dd><input type="text" name="value" value="" class="em30"
                        onkeypress="return RestrictSpace()" /></dd>
                @error('value')
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
        const errorMessageMaxLength255 = '{{ __('messages.common.errors.Common_E021') }}';
        const errorMessageIsValidEmail = '{{ __('messages.general.Common_E002') }}';
        validation('#form', {
            'value': {
                required: true,
                maxlength: 255,
                isValidEmail: true,
            },
        }, {
            'value': {
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
