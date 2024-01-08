@extends('user.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents" class="normal">
    <h2>{{ __('labels.update_profile.register_finish_title') }}</h2>
    <form>
        <p>{{ __('labels.update_profile.register_finish_sub_title') }}</p>

        <ul class=" clearfix">
            <li><a href="{{ route('auth.login') }}" class="btn_b hoverCB">{{ __('labels.update_profile.form.value_register_finish') }}</a></li>
        </ul>
    </form>
</div>
<!-- /contents -->
@endsection
@section('footerSection')
@endsection
