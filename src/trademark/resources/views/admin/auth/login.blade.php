<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('labels.login') }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    {{-- Core JS Footer --}}
    <link rel="stylesheet" href="{{ asset('common/css/admin.css') }}">
    <link href="{{ asset('common/css/alert.css') }}" rel="stylesheet" type="text/css" />
</head>

<body id="pagetop" class="admin">


<!-- wrapper -->
<div id="wrapper">


    <!-- header -->
    <div id="header">
        <div id="headerInner" class="clearfix">
            <h1>{{__('labels.hf_admin.title')}}</h1>


        </div><!-- /headerInner -->
    </div><!-- /header -->


    <!-- contents -->
    <div id="contents">


        <h2>{{__('labels.login')}}</h2>

        <!-- contents inner -->
        <div class="wide clearfix">
            @include('admin.components.includes.messages')

            <form action="{{ route('admin.login') }}" id="form" method="post">
                @csrf
                <p>{{__('labels.login_user.title')}}</p>

                <dl class="w10em eol clearfix">

                    <dt style="width:13em;">{{__('labels.login_user.ID_user')}} <span class="red">*</span></dt>
                    <dd>
                        <input type="text" name="admin_number" value="{{ old('admin_number') }}" class="em30" id="admin_number" nospace/>
                         @error('admin_number') <div class="error">{{ $message ?? '' }}</div> @enderror
                    </dd>
                    <dt style="width:13em;">{{__('labels.password')}} <span class="red">*</span></dt>
                    <dd>
                        <input type="password" class="" name="password" placeholder="" maxlength="128" id="password"/>
                        @error('password') <div class="error">{{ $message ?? '' }}</div> @enderror
                    </dd>
                </dl>

                <ul class="footerBtn clearfix">
                    <li><input type="reset" name="submitEntry" value="{{ __('labels.clear') }}" class="btn_b btn-clear" id="btn-clear" style="font-size: 1.3em"/></li>
                    <li><input type="submit" value="{{__('labels.login')}}" class="btn_b" /></li>
                </ul>


            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->


</div><!-- /wrapper -->


<!-- footer -->
<div id="footer">
    <div id="footerInner" class="clearfix">
        Copyright&copy; AMS Patent & Trademark Office. All rights reserved.
    </div><!-- /footerInner -->
</div><!-- /footer -->


<div class="pagetop"><a href="#pagetop" title="ページトップへ">Page Top</a></div>


<!-- jQuery 2.2.3 -->
<script type="text/JavaScript" src="{{asset('common/js/jquery-3.6.0.min.js')}}"></script>
<script type="text/JavaScript" src="{{asset('common/js/scroll-hint.min.js')}}"></script>
<script type="text/JavaScript" src="{{asset('common/libs/jquery-validation/jquery.validate.min.js')}}"></script>
{{-- jquery-confirm --}}
<link href="{{ asset('common/libs/jquery-confirm/jquery-confirm.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('common/libs/jquery-confirm/jquery-confirm.min.js') }}"></script>
{{-- multiple-select --}}
<link href="{{ asset('common/libs/multiple-select/multiple-select.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('common/libs/multiple-select/jquery.multiple.select.js') }}"></script>
<script src="{{ asset('common/js/functions.js') }}"></script>
<script type="text/JavaScript" src="{{asset('common/js/common.js')}}"></script>
<script src="{{ asset('common/js/validate.js') }}"></script>
<script>
    const errorMessageFormatRequired = '{{ __('messages.common.errors.Common_E001') }}';
    const errorMessageFormatMemberId = '{{ __('messages.common.errors.Common_E006') }}';
    const errorMessageFormatPassword = '{{ __('messages.common.errors.Common_E005') }}';
</script>
</body>
</html>
