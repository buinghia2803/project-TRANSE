@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2> {{ __('labels.recover_id.id_inquiry')}} </h2>
        <form>
            <p>{{ __('labels.recover_id.member_id') }}</p>
            <h3>
                <strong>{{ $user->info_member_id ?? '' }}</strong>
            </h3>
            <p>{{ __('messages.recover_id.pls_using_id_login') }}<br />
                {{ __('messages.recover_id.not_contact_after_receive') }}</p>
            <p><a href="{{ route('auth.login') }}">{{ __('labels.recover_id.gonna_login')}} &gt;&gt;</a></p>
        </form>
    </div><!-- /contents -->
@endsection
