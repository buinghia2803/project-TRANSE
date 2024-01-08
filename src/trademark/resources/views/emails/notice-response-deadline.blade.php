@include('emails.partials.header')
特許庁の審査の結果、拒絶理由通知が来ました。期限内に、審査官の指摘に合わせて必要な書類を準備し、拒絶理由がなくなれば登録へと進んで行く可能性があります。<br>
速やかにログインして対応をご検討ください。<br>
このまま放置すると登録になりませんのでご注意ください。<br>
【ログインURL】<br/>
<a href="{{ route('auth.login') }}">{{ route('auth.login') }}</a><br/>
<br>
@include('emails.partials.footer_v2')
