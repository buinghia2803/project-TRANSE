@include('emails.partials.header')
<br />
登録可能な期限日を超えたため、登録されませんでした。<br />
再度出願を試みる方は以下よりお申し込みください。<br />
<a href="{{ $link }}">{{ $link }}</a><br />
<br />
@include('emails.partials.footer_v2')
