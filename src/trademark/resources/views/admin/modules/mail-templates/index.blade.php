@extends('admin.layouts.app')

@section('main-content')
    <div class="content-wrapper">

        @include('admin.components.includes.content-header', [
            'page_title' => 'labels.mail_template',
            'breadcrumbs' => [
                [ 'label' => 'labels.mail_template', 'active' => true ],
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
                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" role="tablist">
                                    @foreach($templateTypes as $type)
                                        <li class="nav-item">
                                            <a
                                                href="{{ route('admin.mail-templates.index', ['type' => $type['type']]) }}"
                                                class="nav-link {{ ($currentType == $type['type']) ? 'active' : '' }}"
                                            >{{ __($type['label'] ?? '') }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.mail-templates.update', $currentType) }}" id="form" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="type" value="{{ $currentType }}">

                                    @include('admin.modules.mail-templates.partials.form')

                                    @if(in_array('mail-template.update', $authPermissions))
                                        @include('admin.components.forms.action', [
                                            'type' => 'update-mail-template',
                                            'fixed' => true,
                                        ])
                                    @endif
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
    <script src="{{ asset('admin_assets/js/validate.min.js') }}"></script>
    <script type="text/javascript">
        const errorMessageSubjectRequired = '{{ __('validation.required', ['attribute' => __('labels.mail_template_subject')]) }}';
        const errorMessageSubjectMaxLength = '{{ __('validation.max.string', ['attribute' => __('labels.mail_template_subject'), 'max' => 255]) }}';

        const errorMessageContentRequired = '{{ __('validation.required', ['attribute' => __('labels.mail_template_content')]) }}';

        validation('#form', {
            'subject': {
                required: true,
                maxlength: 255,
            },
            'content': {
                requiredEditor: true,
            },
        }, {
            'subject': {
                required: errorMessageSubjectRequired,
                maxlength: errorMessageSubjectMaxLength,
            },
            'content': {
                requiredEditor: errorMessageContentRequired,
            },
        });

        const errorMessageFilesizeFile = '{{ __('messages.max_filesize', [ 'attr' => '20MB' ]) }}';
        filePreview('#attachment', {
            'filesize': errorMessageFilesizeFile,
        });
    </script>
    {{-- Tinymce --}}
    <script src="{{ asset('admin_assets/themes/plugins/tinymce/tinymce.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            addTinyMCE('#content', 400, false);

            $('body').on('change', 'input[name=lang]', function (e) {
                e.preventDefault();
                let url = new URL(window.location.href);
                url.searchParams.set('lang', $(this).val());
                loadingBox('open');
                window.location = url;
            })
        })
    </script>
    {{-- Select2 change --}}
    <script type="text/javascript">
        $('body').on('keyup change', '#cc,#bcc', function() {
            $(this).closest('.tTag').find('.error').remove();
            $(this).parent().find('.select2-selection').removeClass('border-error').addClass('border-normal');
        });
    </script>
@endsection
