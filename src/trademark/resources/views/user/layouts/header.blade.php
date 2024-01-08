<!-- header -->
<div id="header">
    <div id="headerInner" class="clearfix">
        <h1>
            <span class="lead">{{ __('labels.hf_user.title') }}</span>
            <img src="{{asset('common/images/logo.png')}}" alt="AMS" />
        </h1>
    </div><!-- /headerInner -->
    @php
        $user = auth()->user();
    @endphp
    @if(Auth::guard('web')->check())
        <div id="navigation-user">
            @if (!Route::is('auth.signup-success'))
                <div id="toggle">
                    <div>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <span class="text">{{ __('labels.hf_user.menu') }}</span>
                </div>
            @endif
            <nav>
                <ul>
                    <li><a href="{{route('user.top')}}">{{ __('labels.hf_user.a1') }}</a></li>
                    <li><a href="{{route('user.menu-new-apply')}}">{{ __('labels.hf_user.a2') }}</a></li>
                    <li><a href="{{route('user.search-ai')}}">{{ __('labels.hf_user.a3') }}</a></li>
                    <li><a href="{{route('user.application-list')}}">{{ __('labels.hf_user.a4') }}</a></li>
                    <li><a href="{{route('user.application-list.change-address')}}">{{ __('labels.hf_user.a5') }}</a></li>
                    <li><a href="{{route('user.application-list.change-address')}}?scroll=registered">{{ __('labels.hf_user.a6') }}</a></li>
                    <li><a href="{{route('user.profile.edit')}}">{{ __('labels.hf_user.a7') }}</a></li>
                    <li><a href="{{route('user.qa.01.faq')}}">{{ __('labels.hf_user.a8') }}</a></li>
                    <li>
                        <form action="{{ route('auth.logout') }}" method="POST" id="form-logout">
                            @csrf
                            <input class="logout" type="submit" value="{{ __('labels.hf_user.logout') }}">
                        </form>
                    </li>
                </ul>
            </nav>
        </div><!-- /navigation -->
    @endif
</div><!-- /header -->

{{-- login user name --}}
@if(Auth::guard('web')->check())
    @if (!Route::is('auth.signup-success', 'auth.login'))
        <div id="username">
            <ul>
                <li class="login">
                    @if (Auth::user())
                        {{ Auth::user()->info_name }}{{ __('messages.user_login') }}
                    @endif
                </li>
            </ul>
        </div>
    @endif
@endif
{{-- login user name --}}
