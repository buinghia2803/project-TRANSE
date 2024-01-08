@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    @php
        $errors = [];
        $errorSession = Session::get('errors');
        if ($errorSession && $errorSession->getBag('default') && $errorSession->getBag('default')->messages()) {
            $errors = $errorSession->getBag('default')->messages();
        }
    @endphp
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form" action="{{ route('admin.send-session') }}" enctype="multipart/form-data" method="post">
                @csrf
                <h2>{{ __('labels.import_01.text_1') }}</h2>
                @if ($errors && count($errors))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close-alert" data-dismiss="alert" aria-label="close">&times;</button>
                        @foreach ($errors as $key => $item)
                            {{ (isset($item['filename']) ? $item['filename'] : '') . ' . ' . $item['message'] }} <br/>
                        @endforeach
                    </div>
                @endif
                @include('admin.components.includes.messages')
                <dl class="w16em eol clearfix">
                    <dt>{{ __('labels.import_01.text_2') }}</dt>
                    <dd><input type="file" id="file" name="file[]" multiple accept="text/xml" /><br />
                        <ul id="filenames" class="file-name-text"></ul>
                        {{ __('labels.import_01.text_3') }}
                    </dd>
                    <dd>
                        <div id="error_file"></div>
                    </dd>
                </dl>
                <ul class="footerBtn clearfix">
                    <li>
                        <a href="{{ route('admin.home') }}" class="btn_a" style="padding: 5px 2em; display:inline;">{{ __('labels.import_01.text_4') }}</a>
                    </li>
                    <li><input type="button" id="btn_submit" value="{{ __('labels.import_01.text_5') }}" class="btn_a" />
                    </li>
                </ul>
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <style>
        .file-name-text {
            font-size: 0.7em;
            color: #444444b3;
        }
    </style>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    @include('compoments.readonly', ['only' => [ROLE_OFFICE_MANAGER]])
    <script>
        const errorMaxFileLength = '{{ __('messages.general.Import_A000_E001_v2') }}';
        const errorMaxFileSize = '{{ __('messages.general.Common_E028') }}';
        const errorFormat = '{{ __('messages.general.Common_E040') }}';
        const errorRequired = '{{ __('messages.general.Common_E025') }}';

        $('#file').on('change', function(e) {
            const listFile = $(this)[0].files
            const fileLength = $(this).get(0).files.length;
            if (listFile.length >= 1) {
                $('#filenames').empty()
                for (const key in listFile) {
                    if (Object.hasOwnProperty.call(listFile, key)) {
                        const element = listFile[key];
                        if (element.type != 'text/xml') {
                            $('#error_file').empty().append(`<p style="color:red">${errorFormat}</p>`)
                        } else if (fileLength > 100) {
                            $('#error_file').empty().append(`<p style="color:red">${errorMaxFileLength}</p>`)
                        } else if (element.size > '3145728') {
                            $('#error_file').empty().append(`<p style="color:red">${errorMaxFileSize}</p>`)
                        } else {
                            $('#filenames').append(`
                              <li>${element.name}</li>
                            `)
                            $('#error_file').empty()
                        }
                    }
                }
            }
        })

        $('#btn_submit').on('click', function() {
            let form = $('#form')
            if ($('#file').get(0).files.length == 0) {
                $('#error_file').empty().append(`<p style="color:red">${errorRequired}</p>`)
            }

            if (!$('#error_file').text()) {
                loadingBox('open');
                form.submit();
            }

        })
    </script>
@endsection
