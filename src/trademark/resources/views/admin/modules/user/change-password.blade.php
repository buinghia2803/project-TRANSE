@extends('admin.layouts.app')

@section('main-content')
    <div class="content-wrapper">

        @include('admin.components.includes.content-header', [
            'page_title' => 'labels.change_password',
            'breadcrumbs' => [['label' => 'labels.change_password', 'active' => true]],
        ])

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        @include('admin.components.includes.messages')
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.user.update-password') }}" id="form" method="post"
                                    enctype="multipart/form-data">
                                    @csrf

                                    @include('admin.components.forms.text', [
                                        'type' => 'password',
                                        'name' => 'old_password',
                                        'value' => '',
                                        'label' => __('labels.current_password'),
                                        'required' => true,
                                        'placeholder' => __('labels.current_password'),
                                    ])

                                    @include('admin.components.forms.text', [
                                        'type' => 'password',
                                        'name' => 'new_password',
                                        'value' => '',
                                        'label' => __('labels.new_password'),
                                        'required' => true,
                                        'placeholder' => __('labels.new_password'),
                                    ])

                                    @include('admin.components.forms.text', [
                                        'type' => 'password',
                                        'name' => 'password_confirm',
                                        'value' => '',
                                        'label' => __('labels.new_password_confirm'),
                                        'required' => true,
                                        'placeholder' => __('labels.new_password_confirm'),
                                    ])

                                    @include('admin.components.forms.action', [
                                        'type' => 'update-profile',
                                    ])
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('footerSection')
    <script src="{{ asset('admin_assets/js/form.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/validate.min.js') }}"></script>
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';

        const errorMessageValidPassword = '{{ __('messages.valid_password') }}';
        const errorMessagePasswordMaxCharacter =
            '{{ __('validation.max.string', ['attribute' => __('labels.new_password'), 'max' => '32']) }}';
        const errorMessagePasswordMinCharacter =
            '{{ __('validation.min.string', ['attribute' => __('labels.new_password'), 'min' => '8']) }}';
        const errorMessagePasswordConfirmEqualTo =
            '{{ __('validation.same', ['attribute' => __('labels.new_password_confirm'), 'other' => __('labels.new_password')]) }}';
        validation('#form', {
            'old_password': {
                required: true,
            },
            'new_password': {
                required: true,
                minlength: 8,
                maxlength: 32,
                isValidPassword: true,
            },
            'password_confirm': {
                required: true,
                equalTo: '#new-password'
            },
        }, {
            'old_password': {
                required: errorMessageRequired,
            },
            'new_password': {
                required: errorMessageRequired,
                minlength: errorMessagePasswordMinCharacter,
                maxlength: errorMessagePasswordMaxCharacter,
                isValidPassword: errorMessageValidPassword,
            },
            'password_confirm': {
                required: errorMessageRequired,
                equalTo: errorMessagePasswordConfirmEqualTo
            },
        });
    </script>
@endsection
