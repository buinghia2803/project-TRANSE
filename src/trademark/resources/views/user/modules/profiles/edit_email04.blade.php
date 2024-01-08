@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.profile_edit.edit_email04_title') }}</h2>
        <p class="eol">{{ __('labels.profile_edit.edit_email04_note') }}</p>
        <ul class="footerBtn clearfix">
            <li><input type="submit" onclick="location.href = '{{ route('user.profile.edit') }}'"  name="submitEntry" value="{{ __('labels.profile_edit.edit_email04_button') }}" class="btn_b back-to-profile-edit" /></li>
        </ul>
    </div><!-- /contents -->

@endsection
