@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>新しいパスワード・登録メールアドレス再設定</h2>

        @include('admin.components.includes.messages')

        <form id="form" action="{{ route('auth.forgot-password.no-email.secret-answer.post') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <p>以下の回答を入力し、次へお進みください。</p>

            <dl class="w10em eol clearfix">

                <dt>秘密の質問</dt>
                <dd>{{ $user->info_question }}</dd>
                <input type="hidden" name="info_question" value="{{ $user->info_question }}">
                <dt>回答 <span class="red">*</span></dt>
                <dd><input type="text" name="info_answer" value="" /></dd>

            </dl>

            <ul class="footerBtn clearfix">
                <li><input type="submit" name="submitEntry" id="submitEntry" value="次へ" class="btn_b" /></li>
            </ul>


        </form>

    </div>
    <!-- /contents -->
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageIsValidInfoAnswer = '{{ __('messages.forgot_password.Register_U001_E007') }}';
        validation('#form', {
            'info_answer': {
                required: true,
                isValidInfoAnswer: true,
            },
        }, {
            'info_answer': {
                required: errorMessageRequired,
                isValidInfoAnswer: errorMessageIsValidInfoAnswer,
            },
        });
    </script>
@endsection
