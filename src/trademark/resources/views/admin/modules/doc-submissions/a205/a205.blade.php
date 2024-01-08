@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="code" id="code-submit" value="{{ SAVE_DRAFT }}"/>

                @include('admin.components.includes.messages')

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])

                @include('admin.modules.doc-submissions.a205.common.reply_reason_refusal', [
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                ])

                <h3>{{ __('labels.a205_common.comment.title') }}</h3>

                @include('admin.modules.doc-submissions.a205.common.comment', [
                    'docSubmissionCmts' => $docSubmission ? $docSubmission->docSubmissionCmts : collect([])
                ])

                {{-- Trademark Info--}}
                @include('admin.modules.doc-submissions.a205.common.trademark-info', [
                    'data' => $trademakInfo,
                    'docSubmission' => $docSubmission,
                ])

                @if($trademarkPlan->docSubmissions()->count() > 0)
                    <p>
                        <input type="submit" class="btn_a" value="{{ __('labels.common_a203.common_reply.a_3') }}">
                    </p>
                @endif
                <div class="d-flex">
                    <div class="iframe-common">
                        @include('admin.modules.doc-submissions.a205.common.a205hosei01window', [
                            'dataCommonA205Hosei01' => $dataCommonA205Hosei01
                        ])
                    </div>
                    <div class="iframe-common">
                        @include('admin.modules.doc-submissions.a205.common.a205shu02window', [
                            'oldDataCommonShu02Window' => $oldDataCommonShu02Window,
                            'dataProductCommonA205Shu02' => $dataProductCommonA205Shu02,
                            'planDetailDescription' => $planDetailDescription,
                            'type' => ACTION
                        ])
                    </div>
                </div>
                <div class="clearfix">
                    <div class="col2">&nbsp;</div>
                    <div class="col2 center"><input type="submit" value="{{ __('labels.pre_question.index.btn_3') }}"
                                                    class="btn_b submitSaveDraft"/></div>
                </div>
                <p class="eol">
                    {{ __('labels.a205.content') }}<br/>
                    <textarea class="middle_c"
                              name="content">{{ $commentInternalComment ? $commentInternalComment->content : '' }}</textarea>
                </p>
                <ul class="footerBtn clearfix">
                    <li><input type="button" value="{{ __('labels.import_01.text_4') }}" class="btn_a"
                               onclick="history.back()"/></li>
                    <li><input type="submit" value="{{ __('labels.pre_question.index.btn_3') }}"
                               class="btn_b submitSaveDraft"/></li>
                    <li><input type="submit" value="{{ __('labels.precheck.confirm') }}" class="btn_b"/></li>
                </ul>
            </form>
        </div>
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
        const flagRoleDocSubmission = @json($docSubmission ? $docSubmission->flag_role : null);
        const flagRole2 = @json($flagRole2);
        const isConfirmDocSubmission = @json($docSubmission ? $docSubmission->is_confirm : null);
        const isConfirmTrue = @json($isConfirmTrue);
        const isRejectDocSubmission = @json($docSubmission ? $docSubmission->is_reject : null);
        const isRejectFalse =  @json($isRejectFalse);
        const isReject =  @json($isReject);
        const Common_E026 = "{{ __('messages.general.Common_E026') }}";
        const Common_E055 = "{{ __('messages.general.Common_E055') }}";
        const Common_E034 = "{{ __('messages.general.Common_E034') }}";
        const Common_E035 = "{{ __('messages.general.Common_E035') }}";
        const back = "{{ __('labels.back') }}";
        const routeA000top = "{{ route('admin.home') }}";
        const saveDraft = "{{ SAVE_DRAFT }}";
        const saveSubmit = "{{ SAVE_SUBMIT }}";
        const isRoleTanto = @json($isRoleTanto);
        const isRoleSeki = @json($isRoleSeki);
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/doc-submissions/a205.js') }}"></script>
    @include('compoments.readonly', ['only' => [ROLE_MANAGER]])
@endsection
