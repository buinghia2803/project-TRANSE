@extends('user.layouts.app')

@section('headerSection')
<link rel="stylesheet" href="{{ asset('common/css/custom-css.css') }}">
<style>
    .close {
        top: unset;
    }
</style>
@endsection

@section('main-content')
<div id="contents" class="normal login-page">

<h2>{{ __('labels.login') }}</h2>
@include('admin.components.includes.messages')
<form action="{{ route('auth.post-login') }}" id="form" method="post">
    {{ csrf_field() }}
    <p>{{ __('labels.login_user.title') }}</p>

    <dl class="w10em eol clearfix">
        <dt style="width:13em;">{{ __('labels.login_user.ID_user') }}<span class="red">*</span></dt>
        <dd>
            <input type="text" name="info_member_id" value="{{ old('info_member_id') }}" class="em30" id="info_member_id" nospace/>
            @error('info_member_id') <div class="notice">{{ $message ?? '' }}</div> @enderror
        </dd>
        <dt style="width:13em;">
        {{ __('labels.password') }} <span class="red">*</span>
        </dt>
        <dd>
            <input type="password" maxlength="128" name="password" value="" class="input-password-login" id="password"/>
            @error('password') <div class="notice">{{ $message ?? '' }}</div> @enderror
        </dd>
    </dl>

    <ul class="footerBtn clearfix">
        <li>
            <input type="reset" name="submitEntry" value="{{ __('labels.clear') }}" class="btn_b btn-clear" id="btn-clear"/>
        </li>
        <li>
            <input type="submit" name="submitEntry" value="{{ __('labels.login') }}" class="btn_b" />
        </li>
    </ul>

    <p>
        <a href="{{ route('auth.show-recover-id') }}" target="_blank">{{ __('labels.login_user.forgot_ID') }}</a>
    </p>
    <p>
        <a href="{{ route('auth.forgot-password.index') }}">{{ __('labels.login_user.forgot_password') }}</a>
    </p>
    <p>
        <a href="{{ route('auth.signup') }}">{{ __('labels.login_user.sign_up') }}</a>
    </p>

</form>

</div>
@endsection

@section('footerSection')
    <script type="text/javascript">
        $('#btn-clear').click(function(e) {
            e.preventDefault();

            $('#password').val('');
            $('#info_member_id').val('');
        })
    </script>
@endsection
