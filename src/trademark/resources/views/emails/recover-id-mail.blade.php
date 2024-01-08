@include('emails.partials.header')
<br />
IDを送信する操作が行われました。<br />
<br />
【お客様ID】 {{ $member_id ?? '' }}<br />
【ログインURL】 <a href="{{ route('auth.login') }}">{{ route('auth.login') }}</a><br />
<br />
本メールにお心当たりがない場合、第三者により操作された可能性があります。<br />
念のために、パスワードを変更する等、ご注意ください。<br />
<br />
@include('emails.partials.footer_v2')
