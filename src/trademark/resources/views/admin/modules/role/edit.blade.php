@extends('admin.layouts.app')

@section('main-content')
    <div class="content-wrapper">

        @include('admin.components.includes.content-header', [
            'page_title' => 'labels.role_edit',
            'breadcrumbs' => [
                ['label' => 'labels.role_management', 'url' => route('admin.role.index')],
                ['label' => 'labels.role_edit', 'active' => true],
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
                                <form action="{{ route('admin.role.update', $role->id) }}" id="form" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('put')

                                    @include('admin.modules.role.partials.form')

                                    @include('admin.components.forms.action', [
                                        'type' => 'create',
                                        'backUrl' => route('admin.role.index'),
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
        const errorMessageNameMaxLength = '{{ __('validation.max.string', ['attribute' => 'Name', 'max' => 30]) }}';
        validation('#form', {
            'name': {
                required: true,
                maxlength: 30,
            },
        }, {
            'name': {
                required: errorMessageRequired,
                maxlength: errorMessageNameMaxLength,
            },
        });
    </script>
    <script>
        $('.role-item').each(function() {
            let roleItem = $(this).find('input[name^=permissions]');
            let itemChecked = $(this).find('input[name^=permissions]:checked');
            if (itemChecked.length === roleItem.length) {
                $(this).find('.check-all').prop('checked', true);
            }
        })
        $('body').on('click', '.check-all', function() {
            let roleItem = $(this).closest('.role-item').find('input[name^=permissions]');
            let itemChecked = $(this).closest('.role-item').find('input[name^=permissions]:checked');
            if (itemChecked.length === roleItem.length) {
                roleItem.prop('checked', false);
                $(this).prop('checked', false);
            } else {
                roleItem.prop('checked', true);
                $(this).prop('checked', true);
            }
        });
        $('body').on('click', 'input[name^=permissions]', function() {
            let roleItem = $(this).closest('.role-item').find('input[name^=permissions]');
            let itemChecked = $(this).closest('.role-item').find('input[name^=permissions]:checked');
            if (itemChecked.length === roleItem.length) {
                $(this).closest('.role-item').find('.check-all').prop('checked', true);
            } else {
                $(this).closest('.role-item').find('.check-all').prop('checked', false);
            }
        });
    </script>
@endsection
