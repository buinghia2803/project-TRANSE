<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('labels.dashboard') }}</a></li>
    @foreach($breadcrumbs ?? [] as $item)
        <li class="breadcrumb-item {{ (isset($item['active']) && $item['active'] == true) ? 'active' : '' }}">
            @if(!empty($item['url']))
                <a href="{{ $item['url'] ?? '' }}">{{ __($item['label'] ?? '') }}</a>
            @else
                {{ __($item['label'] ?? '') }}
            @endif
        </li>
    @endforeach
</ol>
