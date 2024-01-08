@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            @include('admin.components.includes.messages')
            <form action="" method="POST">
                @csrf
                <input type="hidden" name="from_page" id="from-page" value="{{ $fromPage }}"/>

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->
                @include('admin.modules.doc-submissions.a205.common.reply_reason_refusal', [
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                ])
                <h3>{{ __('labels.a205kakunin.title_h3')}}</h3>
                @include('admin.modules.doc-submissions.a205.common.comment', [
                    'docSubmissionCmts' => $docSubmission ? $docSubmission->docSubmissionCmts : collect([])
                ])
                {{-- Trademark Info --}}
                @include('admin.modules.doc-submissions.a205.common.trademark-info', [
                    'data' => $trademakInfo,
                ])

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
                                'type' => VIEW
                        ])
                    </div>
                </div>
                <ul class="footerBtn clearfix mt-4">
                    <li>
                        <input type="button"
                            value="{{ __('labels.back') }}"
                            class="btn_a"
                            onclick="window.location='{{ route("admin.refusal.documents.create", ['id' => $comparisonTrademarkResultId, 'trademark_plan_id' => $trademarkPlanId]) }}'" 
                        />
                    </li>
                    <!-- only tanto: from-a205-->
                    @if($btnSubmitTanTo && $fromPage == A205)
                        <li><input type="submit" value="{{ __('labels.a205kakunin.confirm_title_tanto')}}" class="btn_c"></li>
                    @endif

                    <!-- only seki: from-a205shu & a205hiki-->
                    @if($btnSubmitSeki && in_array($fromPage, [A205_HIKI, A205_SHU]))
                        <li><input type="submit" value="{{ __('labels.a205kakunin.confirm_title_seki')}}" class="btn_c"></li>
                    @endif

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
        const Common_E055 = "{{ __('messages.general.Common_E055') }}";
        const Common_E034 = "{{ __('messages.general.Common_E034') }}";
        const Common_E035 = "{{ __('messages.general.Common_E035') }}";
        const routeA000top = "{{ route('admin.home') }}";
        const saveDraft = "{{ SAVE_DRAFT }}";
        const saveSubmit = "{{ SAVE_SUBMIT }}";
        const flagRoleDocSubmission = @json($docSubmission ? $docSubmission->flag_role : null);
        const flagRole2 = @json($flagRole2);
        const isConfirmDocSubmission = @json($docSubmission ? $docSubmission->is_confirm : null);
        const isConfirmTrue = @json($isConfirmTrue);
        const labelBack = '{{ __('labels.a205kakunin.back')}}';
        const isRoleTanto = @json($btnSubmitTanTo);
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/doc-submissions/a205kakunin.js') }}"></script>
@endsection
