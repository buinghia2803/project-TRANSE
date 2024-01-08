@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        @include('admin.components.includes.messages')
        <div class="wide clearfix">
            <form id="form"
                action="{{ route('admin.refusal.documents.redirect_page', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}"
                method="post">
                @csrf
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->
                @include('admin.modules.doc-submissions.a205.common.reply_reason_refusal', [
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                ])

                <h3>{!! __('labels.a205shashi.title_comment') !!}</h3>
                @include('admin.modules.doc-submissions.a205.common.comment', [
                    'docSubmissionCmts' => $docSubmission->docSubmissionCmts ?? [],
                ])

                <div class="eol d-flex">
                    <div class="iframe-common">
                        @include('admin.modules.doc-submissions.a205.common.a205hosei01window', [
                            'dataCommonA205Hosei01' => $dataCommonA205Hosei01,
                        ])
                    </div>
                    <div class="iframe-common">
                        @include('admin.modules.doc-submissions.a205.common.a205shu02window', [
                            'oldDataCommonShu02Window' => $oldDataCommonShu02Window,
                            'dataProductCommonA205Shu02' => $dataProductCommonA205Shu02,
                            'type' => VIEW,
                        ])
                    </div>
                </div>
                <dl class="w16em eol clearfix">

                    <dt>{!! __('labels.a205shashi.content') !!}</dt>
                    <dd>
                        <textarea class="middle_c" name="content">{{ isset($docSubmissionCmt) && $docSubmissionCmt ? $docSubmissionCmt->content : '' }}</textarea>
                    </dd>

                </dl>
                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{!! __('labels.a205shashi.btn_redirect_a205s') !!}" class="btn_a clear_validate"
                            data-submit="{{ A205S }}" />
                    </li>
                    <li>
                        <input type="submit" value="{!! __('labels.a205shashi.btn_save_draft') !!}" class="btn_c"
                            data-submit="{{ DRAFT }}" />
                    </li>
                </ul>

                <ul class="footerBtn clearfix">
                    <li>
                        <input type="submit" value="{!! __('labels.a203shu.decided') !!}" class="btn_b"
                            data-submit="{{ CREATE_A205SHASHI }}" />
                    </li>
                </ul>
                {{-- Input Hidden --}}
                <input type="hidden" name="submit_type">
                <input type="hidden" name="trademark_plan_id" value="{{ Request::get('trademark_plan_id') }}">
                <input type="hidden" name="doc_submission_id" value="{{ Request::get('doc_submission_id') }}">
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('css')
    <link href="{{ asset('common/css/iframe-custom.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('script')
    @if (Request::get('type') == 'view')
        <script>
            const form = $('#form').not('#form-logout');
            form.find('input, textarea, select , button').prop('disabled', true);
            form.find('button[type=submit], input[type=submit]').prop('disabled', true).css('display', 'none').remove();
            form.find('a').css('pointer-events', 'none');
        </script>
    @endif
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const messageMaxLength = '{{ __('messages.general.Common_E055') }}';
        const messageRequired = '{{ __('messages.general.Common_E001') }}';
        const messageRequireChecked = '{{ __('messages.general.correspondence_A205s_E001') }}';
        const messagePopup = '{{ __('messages.general.Common_S041') }}';
        const messageIsConfirm = '{{ __('messages.general.Common_E035') }}';
        const docSubmission = @json($docSubmission);
        const urlA000top = @json($routeAdmin);
        const flag = @json(A205_SASHI);
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/doc-submissions/a205s.js') }}"></script>
@endsection
