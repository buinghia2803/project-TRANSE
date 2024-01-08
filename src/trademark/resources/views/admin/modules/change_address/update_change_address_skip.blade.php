@extends('admin.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents">
    <!-- contents inner -->
    <div class="wide clearfix">
        <form>
            <h3>{{ __('labels.a700kenrisha01skip.h3') }}</h3>

            <h4 class="eol">{{ __('labels.a700kenrisha01skip.h4') }}</h4>

            <ul class="footerBtn clearfix">
                <li><input type="button" value="{{ __('labels.a700kenrisha01skip.button') }}" class="btn_b" style="color: white" onclick="window.location = '{{ $redirectPage }}'"/></li>
            </ul>
        </form>
    </div>
    <!-- /contents inner -->
</div>
<!-- /contents -->
@endsection
@section('footerSection')
    @include('compoments.readonly', [ 'only' => [ROLE_OFFICE_MANAGER] ])
@endsection
