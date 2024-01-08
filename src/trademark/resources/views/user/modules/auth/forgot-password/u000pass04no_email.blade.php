@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>新しいパスワード・登録メールアドレス再設定</h2>

        @include('admin.components.includes.messages')

        <form id="form" action="{{ route('auth.forgot-password.no-email.verification.post', $authentication->token) }}" method="POST">
            @csrf
            <p>ID・ご登録のメールアドレスと、新しいメールアドレスに送られてきた認証コードを入力して次へお進みください。</p>
            <dl class="w14em eol clearfix">
                <dt>ID <span class="red">*</span></dt>
                <dd><input type="text" name="info_member_id" value="{{ old('info_member_id') }}" nospace/></dd>
                @error('info_member_id')
                    <div class="error-valid">
                        <dt>登録メールアドレス <span class="red">*</span></dt>
                        <dd>{{ $message }}</dd>
                    </div>
                @enderror
                <dt>登録メールアドレス <span class="red">*</span></dt>
                <dd><input type="text" name="email" value="{{ old('email') }}" class="em30" nospace/></dd>
                @error('email')
                    <div class="error-valid">
                        <dt>登録メールアドレス <span class="red">*</span></dt>
                        <dd>{{ $message }}</dd>
                    </div>
                @enderror
                <dt>新たな登録メールアドレス</dt>
                <dd>{{ $authentication->value }}</dd>

                <dt>メールに記載の認証コード <span class="red">*</span></dt>
                <dd><input type="text" name="code" value="{{ old('code') }}" nospace/></dd>

                @error('code')
                    <div class="error-valid">
                        <dt>登録メールアドレス <span class="red">*</span></dt>
                        <dd>{{ $message }}</dd>
                    </div>
                @enderror
            </dl>
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
        const errorMessageIsValidInfoMemberId = '{{ __('messages.common.errors.Common_E006') }}';
        const errorMessageIsValidEmail = '{{ __('messages.general.Common_E002') }}';
        const errorMessageIsValidCode = '{{ __('messages.common.errors.Common_E013') }}';
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
                isValidCode: errorMessageIsValidCode,
            },
        });

        $('#form').on('submit', function() {
            $('#submitEntry').prop('disabled', true);
        })
    </script>
@endsection
