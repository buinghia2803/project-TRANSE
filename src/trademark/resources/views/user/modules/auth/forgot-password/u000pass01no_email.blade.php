@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        <h2>新しいパスワード・登録メールアドレス再設定</h2>

        @include('admin.components.includes.messages')

        <form id="form" action="{{ route('auth.forgot-password.no-email.post') }}" method="post">
            @csrf

            <p>パスワードを忘れた方で、ご登録のメールアドレスが使えない場合、<br />
                以下にIDとメールアドレスを入力して次へお進みください。</p>

            <dl class="w12em eol clearfix">

                <dt>ID <span class="red">*</span></dt>
                <dd><input type="text" name="info_member_id" value="" onkeydown="return RestrictSpace()" /></dd>
                <dt>登録メールアドレス <span class="red">*</span></dt>
                <dd><input type="text" name="email" value="" class="em30" onkeydown="return RestrictSpace()" />
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
        const errorMessageIsValidInfoMemberId = '{{ __('messages.common.errors.Common_E006') }}';
        const errorMessageIsValidEmail = '{{ __('messages.general.Common_E002') }}';
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
