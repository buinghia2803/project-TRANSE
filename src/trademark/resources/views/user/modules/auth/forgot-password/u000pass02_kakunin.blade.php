@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>パスワード再設定</h2>

        @include('admin.components.includes.messages')

        <form>
            <p class="eol">パスワードが変更されました。<br />
                新しいパスワードでログインしてください。</p>
            <p class="eol"><a href="{{ route('auth.login') }}">&gt;&gt; ログイン画面</a></p>
        </form>
    </div>
    <!-- /contents -->
@endsection
