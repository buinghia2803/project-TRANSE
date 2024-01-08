@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        <h2>{{ __('labels.signup.success_page.title') }}</h2>
        <p>{{ __('labels.signup.success_page.text_1') }}</p>
        <p>{{ __('labels.signup.success_page.text_2') }}</p>
    </div>
@endsection
