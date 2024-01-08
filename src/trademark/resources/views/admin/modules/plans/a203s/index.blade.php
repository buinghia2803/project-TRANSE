@extends('admin.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents" class="admin_wide">
        <!-- contents inner -->
        <div class="wide clearfix">

            @include('compoments.messages')

            <form action="{{ route('admin.refusal.response-plan.supervisor.post', ['id' => $comparisonTrademarkResultId, 'trademark_plan_id' => $trademarkPlanId]) }}" method="POST" id="form">
                @csrf
                <input type="hidden" name="redirect_to" value="">

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])
                {{-- End Trademark table --}}

                @include('admin.modules.plans.common.common_a203', [
                    'dataCommon' => $dataCommon,
                    'isSubmit' => false,
                ])

                <p>
                    <input type="button" onclick="window.location='{{ route('admin.refusal.pre-question-re.supervisor.show', ['id' => $comparisonTrademarkResult->id, 'type' => VIEW]) }}'" value="{!! __('labels.view_question') !!}" class="btn_b redirectToA202N_S" />
                </p>

                <p class="eol"><button
                        type="button"
                        value="draft"
                        class="btn_b open-modal customer_a"
                        data-route="{{ A203CHECK }}"
                        data-src-iframe={{ route('admin.refusal.response-plan.modal-a203check', ['id' => $dataCommon['comparisonTrademarkResult']->id, 'trademark_plan_id' => $dataCommon['trademarkPlans']->id ?? null])}}
                    >{{ __('labels.view_203_check') }}</button></p>



                <p>{{ __('messages.a203s.note_text') }}</p>

                @include('admin.modules.plans.a203s.includes._table_plan', [
                   'trademarkPlan' => $trademarkPlan,
                   'listPossibilityResolution' => $listPossibilityResolution,
                   'listLeaveStatus' => $listLeaveStatus,
                   'isChoiceTrue' => $isChoiceTrue,
                   'isChoiceFalse' => $isChoiceFalse,
                   'roleAddOther' => $roleAddOther,
                   'roleAddPersonCharge' => $roleAddPersonCharge,
                   'roleAddResponsiblePerson' => $roleAddResponsiblePerson
                ])

                <!--comments-->
                <dl class="w08em eol clearfix">
                    @foreach($listPlanComments as $comment)
                        <dt>{{ __('labels.support_first_times.comment') }}</dt>
                        <dd class="white-space-cls">{{ $comment->created_at ? Carbon\Carbon::parse($comment->created_at)->format('Y/m/d') : '' }}　{{ $comment->content }}</dd>
                    @endforeach
                </dl>

                <ul class="footerBtn clearfix">
                    @if(!$isHideButtonReject)
                        <li><input type="submit" value="{{ __('labels.a203s.send_again') }}" data-redirect="{{ A203SASHI }}" class="btn_a" />
                    @endif
                    <li><input type="submit" value="{{ __('labels.a203s.edit') }}" data-redirect="{{ A203SHU }}" class="btn_c" /></li>
                </ul>

                <hr>

                <p class="eol">
                    {{ __('labels.support_first_times.comment') }}<br />
                    <textarea class="middle_c comment_content" name="content">{{ $planComment ? $planComment->content : ''}}</textarea>
                    @error("content") <br><span class="notice">{{ $message }}</span> @enderror
                </p>

                <ul class="footerBtn clearfix">
                    <li><input type="submit" value="{{ __('labels.a203s.accept_and_show') }}" class="btn_b" {{ $trademarkPlan->is_reject == $trademarkPlanIsRejectTrue ? 'disabled' : '' }} /></li>
                </ul>
                @if ($isModal)
                    <ul class="clearfix center fs12">
                        <li>
                            <input type="button" id="closeModal" data-dismiss="modal" value="{{ __('labels.a203c_rui.close_up') }}" class="btn_a">
                        </li>
                    </ul>
                @endif
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

        input[type="submit"]:disabled {
            cursor: not-allowed;
        }
    </style>
@endsection
@section('script')
    <script>
        const errorMessageMaxLength1000 = '{{ __('messages.general.Common_E026') }}';
        const isRejectCurrent = @json($trademarkPlan->is_reject);
        const trademarkPlanIsRejectFalse = @json(App\Models\TrademarkPlan::IS_REJECT_FALSE);
        const trademarkPlanIsRejectTrue = @json($trademarkPlanIsRejectTrue);
        const isConfirmCurrent = @json($trademarkPlan->is_confirm);
        const isConfirmTrue = @json($isConfirmTrue);
        const Common_E035 = '{{ __('messages.general.Common_E035') }}';
        const Common_S041 = '{{ __('messages.general.Common_S041') }}';
        const Hoshin_A203_S001 = '{{ __('messages.general.Hoshin_A203_S001') }}';
        const labelBack = '{{ __('labels.back') }}';
        const routeA00Top = '{{ route('admin.home') }}';
        const routeA203Check = '{{ route('admin.refusal.response-plan.modal-a203check', ['id' => $dataCommon['comparisonTrademarkResult']->id, 'trademark_plan_id' => $dataCommon['trademarkPlans']->id ?? null])}}';
    </script>
    <link rel="stylesheet" href="{{ asset('common/css/simple-modal.css') }}">
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/pages/plans/a203s/index.js') }}"></script>
    <script>
        $('#closeModal').click(function () {
            window.parent.closeModal('#open-modal-iframe')
            window.parent.$('body').removeClass('fixed-body')
        })
    </script>
    @if ($trademarkPlan->is_confirm == $isConfirmTrue
        || $trademarkPlan->is_reject == $trademarkPlanIsRejectTrue
        || $trademarkPlan->is_redirect == true
        || isset(request()->type))
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_SUPERVISOR] ])
@endsection
