@extends('admin.layouts.app')

@section('main-content')
    <div class="content-wrapper">

        @include('admin.components.includes.content-header', [
            'page_title' => __('labels.user_create'),
            'breadcrumbs' => [
                [ 'label' => __('labels.user_list'), 'url' => route('admin.user.index') ],
                [ 'label' => __('labels.user_create'), 'active' => true ],
            ],
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
                                <form action="{{ route('admin.user.store') }}" id="form" method="post" enctype="multipart/form-data">
                                    @csrf

                                    @include('admin.modules.user.partials.form')

                                    @include('admin.components.forms.action', [
                                        'type' => 'create',
                                        'backUrl' => route('admin.user.index')
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
        const errorMessageFirstNameRequired = '{{ __('validation.required', ['attribute' => __('labels.user_first_name')]) }}';
        const errorMessageFirstNameMaxLength = '{{ __('validation.max.string', ['attribute' => __('labels.user_first_name'), 'max' => 255]) }}';

        const errorMessageLastNameRequired = '{{ __('validation.required', ['attribute' => __('labels.user_last_name')]) }}';
        const errorMessageLastNameMaxLength = '{{ __('validation.max.string', ['attribute' => __('labels.user_last_name'), 'max' => 255]) }}';

        const errorMessageEmailRequired = '{{ __('validation.required', ['attribute' => __('labels.email')]) }}';
        const errorMessageIsValidEmail = '{{ __('validation.email', ['attribute' => __('labels.email')]) }}';
        const errorMessageEmailMaxLength = '{{ __('validation.max.string', ['attribute' => __('labels.email'), 'max' => 60]) }}';

        const errorMessagePasswordRequired = '{{ __('validation.required', ['attribute' => __('labels.password')]) }}';
        const errorMessageValidPassword = '{{ __('messages.valid_password') }}';
        const errorMessagePasswordMaxCharacter = '{{ __('validation.max.string', ['attribute' => __('labels.password'), 'max' => '32']) }}';
        const errorMessagePasswordMinCharacter = '{{ __('validation.min.string', ['attribute' => __('labels.password'), 'min' => '8']) }}';

        const errorMessagePasswordConfirmRequired = '{{ __('validation.required', ['attribute' => __('labels.password_confirm')]) }}';
        const errorMessagePasswordConfirmEqualTo = '{{ __('validation.same', ['attribute' => __('labels.password_confirm'), 'other' => __('labels.password')]) }}';

        const errorMessageStatusRequired = '{{ __('validation.required', ['attribute' => __('labels.status')]) }}';

        validation('#form', {
            'first_name': {
                required: true,
                maxlength: 255
            },
            'last_name': {
                required: true,
                maxlength: 255
            },
            'email': {
                required: true,
                email: true,
                isValidEmail: true
            },
            'password': {
                required: true,
                minlength : 8,
                maxlength : 32,
                isValidPassword: true,
            },
            'password_confirm': {
                required: true,
                equalTo: '#password'
            },
            'status': {
                required: true,
            },
        }, {
            'first_name': {
                required: errorMessageFirstNameRequired,
                maxlength: errorMessageFirstNameMaxLength
            },
            'last_name': {
                required: errorMessageLastNameRequired,
                maxlength: errorMessageLastNameMaxLength
            },
            'email': {
                required: errorMessageEmailRequired,
                email: errorMessageIsValidEmail,
                isValidEmail: errorMessageIsValidEmail,
            },
            'password': {
                required: errorMessagePasswordRequired,
                minlength : errorMessagePasswordMinCharacter,
                maxlength : errorMessagePasswordMaxCharacter,
                isValidPassword: errorMessageValidPassword,
            },
            'password_confirm': {
                required: errorMessagePasswordConfirmRequired,
                equalTo: errorMessagePasswordConfirmEqualTo
            },
            'status': {
                required: errorMessageStatusRequired,
            },
        });
    </script>
@endsection
