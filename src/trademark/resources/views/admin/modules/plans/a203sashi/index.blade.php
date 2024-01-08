@extends('admin.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents" class="admin_wide">

        <!-- contents inner -->
        <div class="wide clearfix">
            @include('compoments.messages')

            {{-- Trademark table --}}
            @include('admin.components.includes.trademark-table', [
                'table' => $trademarkTable,
            ])

            <form action="{{ route('admin.refusal.response-plan.supervisor-reject.post', ['id' => $comparisonTrademarkResultId, 'trademark_plan_id' => $trademarkPlanId]) }}" method="POST" id="form">
                @csrf

                {{-- End Trademark table --}}
                @include('admin.modules.plans.common.common_a203', [
                    'dataCommon' => $dataCommon,
                    'isSubmit' => false,
                ])

                <p>
                    <input type="button" onclick="window.location='{{ route('admin.refusal.pre-question-re.supervisor.show', ['id' => $comparisonTrademarkResult->id, 'type' => 'view']) }}'" value="{!! __('labels.view_question') !!}" class="btn_b redirectToA202N_S" />
                </p>

                <p class="eol"><input type="button" value="{{ __('labels.view_203_check') }}" onclick="openModal('#open-modal-iframe');"
                                      class="btn_b" /></p>

                <p>{{ __('messages.a203s.note_text') }}</p>

                @include('admin.modules.plans.a203sashi.includes._table_plan', [
                  'trademarkPlan' => $trademarkPlan,
                  'listPossibilityResolution' => $listPossibilityResolution,
                  'listLeaveStatus' => $listLeaveStatus,
                  'isChoiceTrue' => $isChoiceTrue,
                  'isChoiceFalse' => $isChoiceFalse,
                  'roleAddOther' => $roleAddOther,
                  'roleAddPersonCharge' => $roleAddPersonCharge,
                  'roleAddResponsiblePerson' => $roleAddResponsiblePerson
               ])
                <hr />

                <dl class="w16em eol clearfix">
                    <dt>{{ __('labels.support_first_times.comment') }}</dt>
                    <dd><textarea class="middle_c comment_content" name="content">{{ $planComment ? $planComment->content : ''}}</textarea></dd>
                    @error("content") <br><span class="notice">{{ $message }}</span> @enderror
                </dl>
                <!--comment-->
                <dl class="w08em eol clearfix">
                    @foreach($listPlanComments as $comment)
                        <dt>{{ __('labels.support_first_times.comment') }}</dt>
                        <dd class="white-space-cls">{{ $comment->created_at ? Carbon\Carbon::parse($comment->created_at)->format('Y/m/d') : '' }}　{{ $comment->content }}</dd>
                    @endforeach
                </dl>

                <ul class="footerBtn clearfix">
                    <li><input type="button" onclick="window.location='{{ route('admin.refusal.response-plan.supervisor', ['id' => $comparisonTrademarkResultId, 'trademark_plan_id' => $trademarkPlanId]) }}'" value="{!! __('labels.a203sashi.return') !!}" class="btn_a" /></li>
                    <li><input type="submit" value="{{ __('labels.a203sashi.decision') }}" class="btn_b" /></li>
                </ul>

            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->
@endsection
@section('css')
    <style>
        .show-more-codes {
            display: none;
        }
        .jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons {
            float: none;
            text-align: center;
        }
        .bg-fff9ab {
            background-color: #fff9ab;
        }
        .bg-e5d9ed {
            background-color: #e5d9ed;
        }
        .white-space-cls {
            white-space: pre-line;
        }
    </style>
@endsection
@section('script')
    <script>
        const errorMessageMaxLength1000 = '{{ __('messages.general.Common_E026') }}';
        const isRejectCurrent = @json($trademarkPlan ? $trademarkPlan->is_reject : []);
        const trademarkPlanIsRejectFalse = @json($trademarkPlanIsRejectFalse);
        const trademarkPlanIsRejectTrue = @json($trademarkPlanIsRejectTrue);
        const isConfirmCurrent = @json($trademarkPlan ? $trademarkPlan->is_confirm : []);
        const isConfirmTrue = @json($isConfirmTrue);
        const Common_E035 = '{{ __('messages.general.Common_E035') }}';
        const Common_S041 = '{{ __('messages.general.Common_S041') }}';
        const labelBack = '{{ __('labels.back') }}';
        const routeA00Top = '{{ route('admin.home') }}';
        const routeA203Check = '{{ route('admin.refusal.response-plan.modal-a203check', ['id' => $dataCommon['comparisonTrademarkResult']->id, 'trademark_plan_id' => $dataCommon['trademarkPlans']->id ?? null])}}';
    </script>
    <link rel="stylesheet" href="{{ asset('common/css/simple-modal.css') }}">
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/pages/plans/a203sashi/index.js') }}"></script>
    @if($isBlockScreen)
        <script>disabledScreen()</script>
    @endif
    @include('compoments.readonly', ['only' => [ROLE_SUPERVISOR]])
@endsection
