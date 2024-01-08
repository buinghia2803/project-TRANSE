@if (\Request::route()->getName() == 'admin.payment-check.all'||\Request::route()->getName() == 'admin.payment-check.bank-transfer')
    <!-- header -->
    <div id="header">
        <div id="headerInner" class="clearfix">
            <h1>{{ __('labels.hf_admin.title_payment_all') }}</h1>
            <ul id="navigation" class="clearfix">
                <li><a href="{{ route('admin.home') }}">{{ __('labels.hf_admin.back') }}</a></li>
                <li><a href="{{ route('admin.search.application-list') }}">{{ __('labels.hf_admin.list_project') }}</a></li>
                <li>
                    <a href="{{ route('admin.logout') }}"  class="logout">{{ __('labels.hf_admin.logout') }}</a>
                </li>

            </ul>
        </div><!-- /headerInner -->
    </div><!-- /header -->
@else
    <!-- header -->
    <div id="header">
        <div id="headerInner" class="clearfix">
            <h1>{{ __('labels.hf_admin.title') }}</h1>
            <ul id="navigation" class="clearfix">
                <li>
                    <a href="{{ route('admin.logout') }}"  class="logout">{{ __('labels.hf_admin.logout') }}</a>
                </li>
                <li><a href="{{ route('admin.home') }}" class="home-page">{{ __('labels.hf_admin.back') }}</a></li>
            </ul>
        </div><!-- /headerInner -->
    </div><!-- /header -->
@endif
