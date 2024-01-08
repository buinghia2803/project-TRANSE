@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        @include('compoments.messages')
        <div class="wide clearfix">
            <form id="form" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" name="code" id="code-submit" value="{{ SAVE_DRAFT }}"/>
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->
                @include('admin.modules.doc-submissions.a205.common.reply_reason_refusal', [
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                ])

                @if(!empty($docSubmission->data_a205))
                    <p class="eol">
                        <input type="button" value="{{ __('labels.a205hiki.opinion_written_by') }}" class="btn_a" onclick="openModal('#a205iken02window-modal');">
                    </p>

                    <div id="a205iken02window-modal" class="modal fade" role="dialog">
                        <div class="modal-dialog" style="min-width: 60%;min-height: 60%;">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-body">
                                    <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                                    <div class="content">
                                        @include('admin.modules.doc-submissions.a205.common.a205iken02window', [
                                            'dataCommonA205Iken02' => $docSubmission->formatDataA205()
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <h3>{{ __('labels.a205shu.title') }}</h3>

                @include('admin.modules.doc-submissions.a205.common.comment', [
                    'docSubmissionCmts' => $docSubmission->docSubmissionCmts ?? [],
                ])

                <div class="d-flex">
                    <div class="iframe-common">
                        @include('admin.modules.doc-submissions.a205.common.a205hosei01window', [
                            'dataCommonA205Hosei01' => $dataCommonA205Hosei01
                        ])
                    </div>
                    <div class="iframe-common">
                        @include('admin.modules.doc-submissions.a205.common.a205shu02window', [
                            'type' => ACTION
                        ])
                    </div>
                </div>
                <br>
                <dl class="w16em eol clearfix">
                    <dt>{{ __('labels.a205shu.internal_comments') }}</dt>
                    <dd>
                        <textarea maxlength="10000" name="content" class="middle_c">{{ $commentInternalComment ? $commentInternalComment->content : '' }}</textarea>
                    </dd>
                </dl>
                <ul class="footerBtn clearfix">
                    <li>
                        <a style="width:112px;height:38px;font-size:1.3em;padding:0;text-align:center;line-height:38px;"
                            class="btn_a"
                            href="{{ route('admin.refusal.documents.supervisor', [
                                'comparison_trademark_result_id' => request()->__get('id'),
                                'trademark_plan_id' => request()->__get('trademark_plan_id'),
                                'doc_submission_id' => request()->__get('doc_submission_id')
                            ]) }}"
                        >
                            {{ __('labels.a205shu.back') }}
                        </a>
                    </li>
                    <li><input type="submit" value="{{ __('labels.a205shu.save_draft') }}" class="btn_c submitSaveDraft" /></li>
                    <li><input type="submit" value="{{ __('labels.a205shu.submit') }}" class="btn_b" /></li>
                </ul>
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('css')
    <link href="{{ asset('common/css/iframe-custom.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('script')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript">
        const Common_E026 = "{{ __('messages.general.Common_E026') }}";
        const messageRequireChecked = '{{ __('messages.general.correspondence_A205s_E001') }}';
        const messageMaxLength = '{{ __('messages.general.Common_E026') }}';
        const Common_E035 = "{{ __('messages.general.Common_E035') }}";
        const routeA000top = "{{ route('admin.home') }}";
        const saveDraft = "{{ SAVE_DRAFT }}";
        const saveSubmit = "{{ SAVE_SUBMIT }}";
        const docSubmission = @json($docSubmission);
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/doc-submissions/a205shu.js') }}"></script>
    @if (Request::get('type') == 'view')
        <script>disabledScreen();</script>
    @endif
@endsection
