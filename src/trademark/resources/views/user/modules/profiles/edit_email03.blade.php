@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.profile_edit.edit_email03_title') }}</h2>

        @include('admin.components.includes.messages')

        <form action="{{ route('user.profile.change-email.verification.post') }}" method="POST" id="form">
            @csrf
            <p>{{ __('labels.profile_edit.edit_email03_note') }}</p>

            <dl class="w06em clearfix">
                <dt>{{ __('labels.profile_edit.edit_email03_email_new') }}</dt>
                <dd>
                    <input type="text" name="code" class="remove_space_input" value="{{ old('code') }}" />
                    @error('email')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>
                <input type="hidden" name="token_verify" value="{{ \Request::get('token') }}" />

            </dl>
            <ul class="footerBtn clearfix">
                <li><input type="submit" name="submitEntry" class="btn_b disabled-btn-submit"
                        value="{{ __('labels.profile_edit.edit_email03_button') }}" />
                </li>
            </ul>
        </form>
    </div><!-- /contents -->
@endsection
@section('script')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const errorMessageIsValidRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessageIsValidCodeFormat = '{{ __('messages.profile_edit.validate.Common_E013') }}';
    </script>
    <script src="{{ asset('end-user/profiles/js/edit_email03.js') }}"></script>
    <script></script>
@endsection
