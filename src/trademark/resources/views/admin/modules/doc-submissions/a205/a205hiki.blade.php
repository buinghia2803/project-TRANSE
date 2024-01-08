@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            @include('admin.components.includes.messages')
            <form id="form"
                action="{{ route('admin.refusal.documents.redirect_page', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}"
                method="post" enctype="multipart/form-data">
                @csrf
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->
                @include('admin.modules.doc-submissions.a205.common.reply_reason_refusal', [
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                ])
                <p class="eol"><input type="submit" value="{{ __('labels.a205hiki.opinion_written_by') }}" class="btn_a" /></p>
                <h3>{!! __('labels.a205hiki.title_iframe') !!}</h3>
                <div class="d-flex mb-3">
                    <div class="iframe-common">
                        @include('admin.modules.doc-submissions.a205.common.a205hosei01window', [
                            'dataCommonA205Hosei01' => $dataCommonA205Hosei01,
                        ])
                    </div>
                    <div class="iframe-common">
                        @include('admin.modules.doc-submissions.a205.common.a205shu02window', [
                            'oldDataCommonShu02Window' => $oldDataCommonShu02Window,
                            'dataProductCommonA205Shu02' => $dataProductCommonA205Shu02,
                            'type' => ACTION
                        ])
                    </div>
                </div>
                <dl class="w16em eol clearfix">
                    <dt>{!! __('labels.a205hiki.title_content') !!}</dt>
                    <dd>
                        <textarea class="middle_c" name="content">{{ isset($docSubmissionCmtDraft) && $docSubmissionCmtDraft ? $docSubmissionCmtDraft->content : '' }}</textarea>
                    </dd>
                    @include('admin.modules.doc-submissions.a205.common.comment', [
                        'docSubmissionCmts' => $docSubmission->docSubmissionCmts ?? collect([]),
                    ])
                </dl>
                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{!! __('labels.a205hiki.btn_a205s') !!}" class="btn_a clear_validate"
                            data-submit="{{ A205S }}"/>
                    </li>
                    <li><input type="submit" value="{!! __('labels.a205hiki.btn_a205hiki_draft') !!}" class="btn_c"
                            data-submit="{{ SAVE_DRAFT_A205Hiki }}" /></li>
                    <li><input type="submit" value="{!! __('labels.a205hiki.btn_a205s_submit') !!}" class="btn_b"
                            data-submit="{{ SUBMIT_A205Hiki }}" /></li>
                </ul>
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
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const saveDraft = "{{ SAVE_DRAFT }}";
        const saveSubmit = "{{ SAVE_SUBMIT }}";
        const messageMaxLength = '{{ __('messages.general.Common_E026') }}';
        const messageRequireChecked = '{{ __('messages.general.correspondence_A205s_E001') }}';
        const messageIsConfirm = '{{ __('messages.general.Common_E035') }}';
        const docSubmission = @json($docSubmission);
        const messagePopup = '{{ __('messages.general.Common_S041') }}';
        const urlA000top = @json($routeAdmin);
        const flag = @json(A205_HIKI);
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/doc-submissions/a205s.js') }}"></script>
    @if (Request::get('type') == 'view')
        <script>
            const form = $('#form').not('#form-logout');
            form.find('input, textarea, select , button ,a').addClass('disabled');
            form.find('input, textarea, select , button ,a').css('pointer-events', 'none');
            $('#btn_content').removeClass('disabled');
            $('#btn_content').css('pointer-events', '');
        </script>
    @endif
@endsection
