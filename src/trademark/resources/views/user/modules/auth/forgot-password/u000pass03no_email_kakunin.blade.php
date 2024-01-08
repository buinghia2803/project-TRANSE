@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>パスワード・登録メールアドレス再設定</h2>


        <form>
            <p class="eol">メールが送信されました。<br />
                認証コードを使って、パスワード再設定してください。</p>
        </form>

    </div>
    <!-- /contents -->
@endsection

@section('footerSection')
    <script src="{{ asset('admin_assets/js/delete-all.min.js') }}"></script>
@endsection
