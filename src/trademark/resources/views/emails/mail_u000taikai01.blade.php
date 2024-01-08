@include('emails.partials.header')
<br />
以下のURLにて、退会手続きを行います。<br />
<br>
【退会用URL】 <a href="{{ $link ?? '' }}">{{ $link ?? '' }}</a>
【認証コード】 {{ $code ?? '' }}<br>
<br>
※URLの有効期限は退会のお申し込みをいただいてから1時間です。<br>
※URLをクリックしてもうまくいかない場合には、URLを直接コピーし、ブラウザのアドレス欄に貼り付けてアクセスしてください。<br>
※このメールにお心当たりがない場合、破棄くださいますようお願いいたします。<br>
@include('emails.partials.footer_v2')
