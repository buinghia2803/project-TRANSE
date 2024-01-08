
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>AMS オンライン出願サービス</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />

    <link href="{{ asset('common/css/contents.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{asset('common/css/scroll-hint.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('common/css/alert.css')}}" rel="stylesheet" type="text/css" />
    @yield('css')
    <script>
        const SET_SESSION_AJAX_URL = '{{ route('user.ajax.set-session') }}';
    </script>
    <script type="text/JavaScript" src="{{asset('common/js/jquery-3.6.0.min.js')}}"></script>
    <script type="text/JavaScript" src="{{asset('common/js/functions.js')}}"></script>
    <script type="text/JavaScript" src="{{asset('common/js/common.js')}}"></script>
    <script type="text/JavaScript" src="{{asset('common/js/scroll-hint.min.js')}}"></script>
    <script type="text/JavaScript" src="{{asset('common/js/loadingoverlay.min.js')}}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/bs3-modal.js') }}"></script>
    <script type="text/JavaScript" src="{{asset('common/libs/jquery-validation/jquery.validate.min.js')}}"></script>
    <script type="text/JavaScript" src="{{asset('common/libs/jquery-validation/additional-methods.min.js')}}"></script>

    <link href="{{ asset('common/libs/jquery-confirm/jquery-confirm.min.css') }}" rel="stylesheet" type="text/css" />
    <script type="text/JavaScript" src="{{asset('common/libs/jquery-confirm/jquery-confirm.min.js')}}"></script>

</head>

<body id="pagetop" class="user">


<!-- wrapper -->
<div id="wrapper">


    <!-- header -->
    <div id="header">
        <div id="headerInner" class="clearfix">
            <h1><span class="lead">AMSは、オンラインでの商標出願・登録をお手伝いします。</span><img src="images/logo.png" alt="AMS" /></h1>

        </div><!-- /headerInner -->

        <div id="navigation-user">
            <div id="toggle">
                <div>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span class="text">MENU</span>
            </div>
            <nav>
                <ul>
                    <li><a href="u000top.html">トップページ</a></li>
                    <li><a href="u000new_apply.html">新規お申し込み</a></li>
                    <li><a href="u020a.html">AI検索</a></li>
                    <li><a href="u000list.html">案件一覧</a></li>
                    <li><a href="u000list_change_address.html#applicant">出願人情報変更</a></li>
                    <li><a href="u000list_change_address.html#registered">権利者情報変更</a></li>
                    <li><a href="u001edit.html">会員情報編集</a></li>
                    <li><a href="u000qa01.html">FAQ</a></li>
                    <li><a href="#" class="logout">ログアウト</a></li>
                </ul>
            </nav>
        </div><!-- /navigation -->

    </div><!-- /header -->


    <!-- login user name -->
    <div id="username">
        <ul>
            <li class="login">ｘｘｘｘでログイン中</li>
        </ul>
    </div>
    <!-- /login user name -->


    <!-- contents -->
    <div id="contents" class="normal">


        <h2>出願：中止</h2>


        <form>

            <h3 class="eol">ご依頼に基づき、本件のお手続きを中止致します。<br />
                中止の場合も、返金はございません。<br />
                よろしければ、「確認」ボタンを押してください。<br />
                <br />
                中止せずに、お手続きされたい場合は、「戻る」ボタンを押してください。</h3>


            <ul class="footerBtn clearfix">
                <li><input type="submit" value="戻る" class="btn_a" /></li>
                <li><input type="submit" value="確認" class="btn_b" /></li>
            </ul>


        </form>

    </div><!-- /contents -->


</div><!-- /wrapper -->


<!-- footer -->
<div id="footer">
    <div id="footerInner" class="clearfix">
        Copyright&copy; AMS Patent & Trademark Office. All rights reserved.
    </div><!-- /footerInner -->
</div><!-- /footer -->


<div class="pagetop"><a href="#pagetop" title="ページトップへ">Page Top</a></div>


</body>
</html>
