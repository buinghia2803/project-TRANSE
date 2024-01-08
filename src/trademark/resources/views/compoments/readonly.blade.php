@php
    // @include('compoments.readonly', [ 'only' => [ ROLE_OFFICE_MANAGER, ROLE_MANAGER, ROLE_SUPERVISOR ] ])
    $user = \Auth::guard('admin')->user();
    $role = $user->role;

    $hasRemoveSubmit = $hasRemoveSubmit ?? true;
@endphp

@if(!in_array($user->role, $only))
    <script>disabledScreen({{ $hasRemoveSubmit }});</script>
    {!! $script ?? null !!}
@endif
