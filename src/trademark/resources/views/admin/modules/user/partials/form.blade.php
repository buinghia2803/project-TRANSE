@include('admin.components.forms.text', [
    'name' => 'first_name',
    'value' => $user->first_name ?? '',
    'label' => __('labels.user_first_name'),
    'placeholder' => __('labels.user_first_name'),
    'required' => true,
])

@include('admin.components.forms.text', [
    'name' => 'last_name',
    'value' => $user->last_name ?? '',
    'label' => __('labels.user_last_name'),
    'placeholder' => __('labels.user_last_name'),
    'required' => true,
])

@include('admin.components.forms.text', [
    'type' => 'email',
    'name' => 'email',
    'value' => $user->email ?? '',
    'label' => __('labels.email'),
    'placeholder' => __('labels.email'),
    'required' => true,
])

@if(!isset($user))
    @include('admin.components.forms.text', [
        'type' => 'password',
        'name' => 'password',
        'value' => '',
        'label' => __('labels.password'),
        'required' => true,
        'placeholder' => __('labels.password'),
    ])

    @include('admin.components.forms.text', [
        'type' => 'password',
        'name' => 'password_confirm',
        'value' => '',
        'label' => __('labels.password_confirm'),
        'required' => true,
        'placeholder' => __('labels.password_confirm'),
    ])
@endif

@include('admin.components.forms.checkbox', [
    'name' => 'role',
    'value' => (!empty($user)) ? $user->roles->pluck('id')->toArray() : [],
    'label' => __('labels.role'),
    'required' => false,
    'options' => $roles->pluck('name', 'id')->toArray(),
    'inline' => false
])

@include('admin.components.forms.radio', [
    'name' => 'status',
    'value' => $user->status ?? 2,
    'label' => __('labels.status'),
    'required' => true,
    'options' => $status,
    'inline' => true
])
