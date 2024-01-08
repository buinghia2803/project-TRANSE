@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        <p>{{ __('labels.apply_trademark.confirm_completed.text_1') }}</p><br>
        <a href="{{ route('user.top') }}">{{ __('labels.apply_trademark.confirm_completed.text_2') }} &gt;&gt;</a></p>
    </div>
@endsection
