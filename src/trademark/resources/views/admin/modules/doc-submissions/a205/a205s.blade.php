@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
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

                <h3>{!! __('labels.a205s.text_1') !!}</h3>
                @include('admin.modules.doc-submissions.a205.common.comment', [
                    'docSubmissionCmts' => $docSubmission->docSubmissionCmts ?? [],
                ])
                <div class="d-flex">
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
                <ul class="footerBtn clearfix">
                    @if (empty($docSubmissionBefore) || $docSubmissionBefore && $docSubmissionBefore->is_reject == IS_NOT_REJECT)
                        <li>
                            <input type="submit" value="{!! __('labels.a205s.btn_redirect_a205_shashi') !!}" class="btn_a clear_validate"
                                data-submit="{{ A205_SASHI }}" />
                        </li>
                    @endif
                    <li>
                        <input type="submit" value="{!! __('labels.a205s.btn_redirect_a205_shu') !!}" class="btn_c clear_validate"
                            data-submit="{{ A205_SHU }}" />
                    </li>
                </ul>
                <hr />
                <p class="eol">
                    {!! __('labels.a205s.text_2') !!}<br />
                    <textarea class="middle_c" name="content">{{ isset($docSubmissionCmt) && $docSubmissionCmt ? $docSubmissionCmt->content : '' }}</textarea>
                </p>
                <dl class="w10em clearfix">
                    {{-- To do --}}
                    <dt>{!! __('labels.a205s.text_4') !!}</dt>
                    <dd>{{ CommonHelper::formatTime($payment->updated_at ?? '', 'Y/m/d') }}</dd>

                    <dt>{!! __('labels.a205s.text_5') !!}</dt>
                    <dd>{{ CommonHelper::formatTime($noticeDetailBtn->updated_at ?? '', 'Y/m/d') }}</dd>

                    <dt>{!! __('labels.a205s.text_3') !!}</dt>
                    <dd><input type="checkbox" name="check_submit" /></dd>
                </dl>
                <ul class="footerBtn clearfix">
                    <li>
                        <input type="submit" value="{!! __('labels.a205s.btn_create') !!}" class="btn_b"
                            data-submit="{{ CREATE_A205S }}" />
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
        const flag = @json(A205S);
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/doc-submissions/a205s.js') }}"></script>
    @if (Request::get('type') == 'view'
        || $docSubmission && ($docSubmission->is_reject == DOC_SUBMISSION_IS_REJECT || $docSubmission->is_confirm == DOC_SUBMISSION_IS_CONFIRM))
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_SUPERVISOR] ])
@endsection
