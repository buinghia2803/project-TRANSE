@include('emails.partials.header')
<br />
パスワードリセット画面でメールアドレスと下記認証コードを入力して、<br />
新しいパスワードを設定してください。<br />
【パスワードリセット用URL】 <a href="{{ $link ?? '' }}">{{ $link ?? '' }}</a><br />
【認証コード】 {{ $code }}<br />
<br />
※URLの有効期限は30分です。<br />
<br />
@include('emails.partials.footer_v2')
