@extends('admin.layouts.app')

@section('main-content')
    <div class="content-wrapper">

        @include('admin.components.includes.content-header', [
            'page_title' => 'labels.account_information',
            'breadcrumbs' => [['label' => 'labels.account_information', 'active' => true]],
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
                                <form action="{{ route('admin.user.update-profile') }}" id="form" method="post"
                                    enctype="multipart/form-data">
                                    @csrf

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
                                        'name' => '',
                                        'value' => $user->email ?? '',
                                        'label' => __('labels.email'),
                                        'placeholder' => __('labels.email'),
                                        'required' => false,
                                        'disabled' => true,
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
        validation('#form', {
            'first_name': {
                required: true,
            },
            'last_name': {
                required: true,
            },
        }, {
            'first_name': {
                required: errorMessageRequired,
            },
            'last_name': {
                required: errorMessageRequired,
            },
        });
    </script>
@endsection
