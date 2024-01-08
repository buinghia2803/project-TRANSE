@include('emails.partials.header')
<br>
メールアドレスの確認が完了致しました。<br>
<br>
30分以内に、下記アドレスから会員登録をお願い致します。<br>
<br>
【本登録用URL】<br>
<a href="{{ $url ?? '' }}">{{ $url ?? '' }}</a><br>
<br>
【認証コード】<br>
{{ $code ?? '' }}<br>
<br>
※URLの有効期限はお申し込みをいただいてから30分です。<br>
<br>
※URLをクリックしてもうまくいかない場合には、URLを直接コピーし、<br>
ブラウザのアドレス欄に貼り付けてアクセスしてください。<br>
<br>
※このメールにお心当たりがない場合、破棄くださいますようお願いいたします。<br>
@include('emails.partials.footer_v2')
