@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>会員情報の変更</h2>
        <form>
            <p class="eol">会員情報が変更されました。</p>

            <ul class="footerBtn clearfix">
                <li><input type="submit" name="submitEntry" value="トップ画面へ戻る" class="btn_b" /><a href="u001edit.html">→</a></li>
            </ul>
        </form>
    </div><!-- /contents -->
@endsection

