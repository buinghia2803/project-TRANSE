@include('emails.partials.header')
<br />
AMSからの回答が届きました。<br />
ログインをしてご確認ください。<br />
<br />
【ログインURL】<br />
<a href="{{ route('auth.login') }}">{{ route('auth.login') }}</a><br />
<br />
<br />
@include('emails.partials.footer_v2')
