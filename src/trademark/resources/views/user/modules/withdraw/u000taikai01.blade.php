@extends('user.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents" class="normal">
    <h2>{{ __('labels.u000taikai01.title') }}</h2>
    <form method="POST">
        @csrf
        <p>{{ __('labels.u000taikai01.note_1') }}<br />
            {{ __('labels.u000taikai01.note_2') }}<br />
            {{ __('labels.u000taikai01.note_3') }}</p>
        <dl class="w08em eol clearfix">
            <dt>ID</dt>
            <dd>{{ $user->info_member_id ?? '' }}</dd>
            <dt>{{ __('labels.u000taikai01.name') }}</dt>
            <dd>{{ $user->info_name ?? ''}}</dd>
        </dl>
        <ul class="footerBtn clearfix">
            <li><button style="height: 39px; width: 112px;font-size: 1.3em;" type="button" name="submitEntry" onclick="history.back()" class="btn_b">{{ __('labels.u000taikai01.back') }}</button></li>
            <li><button style="height: 39px; width: 112px;font-size: 1.3em;" type="submit" name="submitEntry" class="btn_b">{{ __('labels.u000taikai01.next') }}</button>
        </ul>
    </form>
</div><!-- /contents -->
@endsection
@section('script')
@endsection