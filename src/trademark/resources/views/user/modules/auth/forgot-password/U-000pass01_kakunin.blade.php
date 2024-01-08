@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">

        <h2>新しいパスワードの設定</h2>

        @include('admin.components.includes.messages')
        
        <form>
            <p>登録メールアドレスと、連絡先メールアドレスにメールが送信されました。<br />
                30分以内に、メールに記載の認証コードを使って新しいパスワードの設定を行ってください。</p>

            <p>登録メールアドレス宛てのメールを受信できない場合、登録メールアドレスが使用できない可能性があります。<br />
                <a href="{{route('auth.forgot-password.no-email')}}" >こちら</a>から変更をお願い致します。
            </p>
        </form>
    </div>
    <!-- /contents -->
@endsection

