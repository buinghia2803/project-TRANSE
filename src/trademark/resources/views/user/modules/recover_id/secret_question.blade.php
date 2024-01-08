@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.recover_id.id_inquiry') }}</h2>
        @if (isset($error))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close-alert" data-dismiss="alert" aria-label="close-alert">&times;</button>
                    {{ $error }}
            </div>
        @endif

        <form id="secret_question_form" method="POST" action="{{ route('auth.recover-id-secret-answer') }}">
            @csrf
            @method('POST')
            <p> {{ __('messages.recover_id.please_choose_answer') }} </p>
            <dl class="w10em eol clearfix">
                <input type="hidden" class="em20" id="user_id" name="user_id" value="{{ old('user_id', $user->id) }}"/>
                <dt>{{ __('labels.recover_id.secret_question') }}</dt>
                <dd>{{ $user->info_question ?? '' }}</dd>
                <dt> {{ __('labels.recover_id.reply') }} <span class="red">*</span></dt>
                <dd>
                    <input type="text" maxlength="255" class="em20" id="answer" name="answer"/>
                    @error('answer')
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
        const errorMessageMaxLength = '{{ __('messages.common.errors.Register_U001_E007') }}';
        const errorMessageIsFullWidth = '{{ __('messages.common.errors.Register_U001_E007') }}';
        validation('#secret_question_form', {
            'answer': {
                required: true,
                maxlength: 50,
                isFullwidth: true,
            }
        }, {
            'answer': {
                required: errorMessageRequired,
                maxlength: errorMessageMaxLength,
                isFullwidth: errorMessageIsFullWidth
            }
        });
    </script>
@endsection
