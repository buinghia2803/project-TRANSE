@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.profile_edit.edit_email.change_email_title') }}</h2>

        @include('admin.components.includes.messages')

        <form action="{{ route('user.profile.change-email.index') }}" method="POST" id="edit-email-form">
            @csrf
            <p class="eol">{{ __('labels.profile_edit.edit_email.change_email_note_1') }}<br />
                {{ __('labels.profile_edit.edit_email.change_email_note_2') }}<br />
                {{ __('labels.profile_edit.edit_email.change_email_note_3') }}
            </p>

            <dl class="w16em eol clearfix">
                <dt>{{ __('labels.profile_edit.edit_email.email_new') }}<span class="red">*</span></dt>
                <dd>
                    <input type="text" name="email" value="{{ old('email') }}" class="em30 remove_space_input" />
                    @error('email')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>
            </dl>
            <ul class="footerBtn clearfix">
                <li>
                    <input type="submit" name="sendForm" value="{{ __('labels.profile_edit.edit_email.send') }}"
                        class="btn_b disabled-btn-submit" />
                </li>
            </ul>
        </form>
    </div><!-- /contents -->
@endsection
@section('script')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const errorMessageIsValidRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageIsValidMaxLength255 = '{{ __('messages.general.Common_E021') }}';
        const errorMessageIsValidEmailFormat = '{{ __('messages.general.Common_E002') }}';
    </script>
@endsection
