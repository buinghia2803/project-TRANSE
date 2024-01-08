@include('admin.components.forms.text', [
    'name' => 'name',
    'value' => $role->name ?? '',
    'label' => __('labels.role_name'),
    'placeholder' => __('labels.role_name'),
    'required' => true,
])

<div class="form-group row fText">
    <label class="col-12 col-lg-3 col-xl-2 col-form-label">
        {{ __('labels.permission') }}
    </label>
    <div class="col-12 col-lg-9 col-xl-10">
        @php
            $rolePermission = (!empty($role)) ? $role->permissions->pluck('name')->toArray() : [];
        @endphp
        @foreach($permissions as $moduleName => $data)
            <div class="card card-info role-item">
                <div class="card-header p-2 px-3">
                    <h2 class="card-title">{{ __($data['labels'] ?? '') }}</h2>
                </div>
                <div class="card-body p-2 px-3">
                    <div class="icheck-primary icheck-inline">
                        <input type="checkbox" class="check-all" id="{{ $moduleName . '-all' }}"/>
                        <label for="{{ $moduleName . '-all' }}">{{ __('labels.all') }}</label>
                    </div>

                    @foreach($data['permissions'] as $routeName => $permission)
                        @php $prefix = $moduleName . '.' .$routeName; @endphp
                        <div class="icheck-primary icheck-inline">
                            <input
                                type="checkbox"
                                name="permissions[]"
                                id="{{ $moduleName . '_' .$routeName }}"
                                value="{{ $prefix }}"
                                @if(in_array($prefix, $rolePermission)) checked @endif
                            />
                            <label for="{{ $moduleName . '_' .$routeName }}">{{ __($permission['labels']) }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
