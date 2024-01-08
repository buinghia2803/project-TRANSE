@extends('user.layouts.app')

@section('main-content')
<!-- contents -->
<div id="contents" class="normal">
    <h2>{{ __('labels.u000taikai01kakunin.title') }}</h2>
    <form action="{{ route('user.withdraw.confirm.send_mail') }}" method="POST">
        @csrf
        @include('admin.components.includes.messages')
        <p>{{ __('labels.u000taikai01kakunin.note_1') }}</p>
        <p>{{ __('labels.u000taikai01kakunin.note_2') }}</p>
        <p class="eol"><a href="{{ route('user.withdraw.anken-list') }}" target="_blank">{{ __('labels.u000taikai01kakunin.go_to_anken_list') }} &gt;&gt;</a></p>
        <ul class="footerBtn clearfix">
            <li>
                <a style="width:112px;height:38px;padding:0;text-align:center;line-height:38px" class="btn_a" href="{{ route('user.withdraw.index') }}">
                    {{ __('labels.u000taikai01kakunin.back') }}
                </a>
            </li>
            <li>
                <button style="width:112px;height:38px;padding:0;text-align:center;line-height:38px" type="submit" class="btn_b" >
                    {{ __('labels.u000taikai01kakunin.next') }}
                </button>
            </li>
        </ul>
    </form>
</div>
<!-- /contents -->
@endsection