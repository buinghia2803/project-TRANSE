@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        @include('admin.components.includes.messages')

        <form action="{{ route('user.refusal.response-plan.post-product', ['s' => request()->s]) }}" method="post" id="form">
            @csrf
            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <h2> {{ __('labels.refusal_plans.u203c_n.title') }} </h2>

            <h3>{{ __('labels.refusal_plans.trademark_info') }}</h3>
            <div class="info mb10">
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable,
                ])
            </div>
            <!-- /info -->

            <dl class="w20em clearfix middle">
                <dt>{{ __('labels.refusal_plans.sending_noti_rejecttion_date') }}{{ $comparisonTrademarkResult->parseSendingNotiRejecttionDate() }}
                </dt>
                <dd>
                    @if ($trademarkDocuments->count())
                        <input type="button" value="{{ __('labels.refusal_plans.u203.btn.btn_5') }}" class="btn_b"
                            id="openAllFileAttach" />
                    @endif
                </dd>
            </dl>
            <p>{{ __('labels.refusal_plans.response_deadline') }}{{ $comparisonTrademarkResult->parseResponseDeadline() }}
            </p>
            <p class="blue">{{ __('labels.refusal_plans.u203.content_1') }}</p>

            @if (!in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01, OVER_04, OVER_04B, OVER_05]))
                <p class="red">{{ __('labels.refusal_plans.u203.content_2') }}<br />
                    {{ __('labels.refusal_plans.u203.content_3') }}</p>
                <p class="eol">
                    <a href="{{ route('user.refusal.extension-period.alert', ['id' => $comparisonTrademarkResult->planCorrespondence->id]) }}"
                       class="btn_b">{{ __('labels.refusal_plans.u203.btn.btn_4') }}</a>
                </p>
            @endif

            <dl class="w16em clearfix">
                <dt>
                    <h3><strong>{{ __('labels.refusal_plans.u203.content_4') }}</strong></h3>
                </dt>
                <dd>
                    <h3><strong>{{ $trademarkPlan->parseResponseDeadline() ?? '' }}</strong></h3>
                </dd>
            </dl>

            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <div class="js-scrollable parent_table_product mb-2">
                <table class="normal_b planCorrespondenceTblProduct mb-2">
                    <tr>
                        <td colspan="12" class="right"><input type="button"
                                value="{{ __('labels.refusal_plans.u203.btn.btn_11') }}" class="btn_a"
                                id="clear_checked" /></td>
                    </tr>
                    {{-- plan name --}}
                    <tr>
                        <th colspan="3" rowspan="2"></th>
                        @foreach ($trademarkPlan->plans as $plan)
                            <th colspan="{{ $plan->planDetails->count() }}">
                                {{ __('labels.refusal_plans.u203c.key_plan', [
                                    'attr' => $loop->iteration,
                                ]) }}
                                <span>
                                    @foreach ($plan->planReasons as $key => $planReason)
                                        @foreach ($planReason->reasons as $reason)
                                            {{ $plan->planReasons->count() > 1 ? $reason->reason_name . ($key < $plan->planReasons->count() - 1 ? ',' : '') : $reason->reason_name }}
                                        @endforeach
                                    @endforeach
                                </span>
                            </th>
                        @endforeach
                    </tr>
                    {{-- plan detail name --}}
                    <tr>
                        @php
                            $white = 0;
                        @endphp
                        @foreach ($trademarkPlan->plans as $keyPlan => $plan)
                            @foreach ($plan->planDetails as $planDetail)
                                @php
                                    $planDetailDistincts = $planDetail->planDetailDistincts->filter(function ($item) {
                                        $planDetailProducts = $item->planDetailProducts;
                                        $planDetailProductNotAdd = $planDetailProducts->whereIn('leave_status', [
                                            \App\Models\PlanDetailProduct::LEAVE_STATUS_7,
                                            \App\Models\PlanDetailProduct::LEAVE_STATUS_3,
                                        ]);
                                        $planDetailProductIsDeleted = $planDetailProducts->where('is_deleted', true);
                                        return $planDetailProductIsDeleted->count() == 0 && $planDetailProductNotAdd->count() == 0;
                                    });

                                    $planDetailDistinctUnique = $planDetailDistincts
                                        ->where('is_add', IS_ADD_TRUE)
                                        ->pluck('mDistinction.name')
                                        ->unique();
                                    $distinctionsAddOn = $planDetailDistincts
                                        ->where('is_distinct_settlement', IS_DISTINCT_SETTLEMENT)
                                        ->where('is_add', IS_ADD_TRUE)
                                        ->pluck('mDistinction.name')
                                        ->unique();
                                @endphp
                                <th>
                                    <input name="is_choice[{{ $plan->id }}]" type="radio"
                                        class="is_choice plan_{{ $plan->id }} choose_plan_detail"
                                        value="{{ $planDetail->id }}"
                                        {{ $planDetail->is_choice == IS_CHOICE ? 'checked' : '' }}
                                        class="white-{{ $white++ }}" data-white="white-plan-{{ $plan->id }}"
                                        data-plan_nth="{{ $keyPlan + 1 }}" data-plan_id="{{ $plan->id }}"
                                        data-plan_detail_id="{{ $planDetail->id }}"
                                        @if($keyPlan == 0)
                                           data-distincion_add_on="{{ $distinctionsAddOn->count() }}"
                                        @endif
                                    />
                                    {{ __('labels.a203c.plan_table.plan_detail') }}({{ $loop->iteration }})
                                </th>
                                <input type="hidden" name="" id="count_m_distinct_{{ $plan->id }}"
                                    value="{{ $planDetail->planDetailDistincts->count() }}">
                            @endforeach
                            <input type="hidden" name="plan_ids[]" value="{{ $plan->id }}" class="plan_ids">
                        @endforeach
                        @php
                            $white = 0;
                        @endphp
                    </tr>
                    {{-- plan_detail text --}}
                    <tr>
                        @php
                            $white = 0;
                        @endphp
                        <th colspan="3" class="right">{{ __('labels.a203c.revolution') }}</th>
                        @foreach ($trademarkPlan->plans as $plan)
                            @foreach ($plan->planDetails as $planDetail)
                                <th class="white-{{ $white++ }} white-plan-{{ $planDetail->id }}-revol">
                                    {{ $planDetail->getStrRevolution() }}
                                </th>
                            @endforeach
                        @endforeach
                        @php
                            $white = 0;
                        @endphp
                    </tr>
                    {{-- M type doc --}}
                    <tr>
                        <th colspan="3" class="right">{{ __('labels.refusal_plans.u203c.m_type_plan_doc') }}</th>
                        @php
                            $white = 0;
                        @endphp
                        @foreach ($trademarkPlan->plans as $plan)
                            @foreach ($plan->planDetails as $planDetail)
                                <th class="white-{{ $white++ }} white-plan-{{ $planDetail->id }}">
                                    @if (in_array($planDetail->mTypePlan->id, [1, 3, 6]))
                                        {{ __('labels.refusal_plans.u203c.none') }}
                                    @else
                                        @foreach ($planDetail->mTypePlan->mTypePlanDocs ?? [] as $mTypePlanDoc)
                                            @if ($mTypePlanDoc->url && isset($mTypePlanDoc->url))
                                                <span class="doc_name" title="{{ $mTypePlanDoc->name }}">
                                                    {{ $mTypePlanDoc->name }}
                                                </span><br>
                                            @else
                                                <span class="doc_name" title="{{ $mTypePlanDoc->name }}">
                                                    {{ $mTypePlanDoc->name }}
                                                </span> <br>
                                            @endif
                                        @endforeach
                                    @endif
                                </th>
                            @endforeach
                        @endforeach
                        @php
                            $white = 0;
                        @endphp
                    </tr>
                    <tr>
                        <th>{{ __('labels.a203c.distinction') }}</th>
                        <th style="width:18%;">{{ __('labels.a203c.product_name') }}</th>
                        <th class="middle bg_pink">
                            {{ __('labels.refusal_plans.u203c.text_2') }}<br />{{ __('labels.refusal_plans.u203c.text_3') }}
                        </th>
                        @php
                            $white = 0;
                        @endphp
                        @foreach ($trademarkPlan->plans as $plan)
                            @foreach ($plan->planDetails as $planDetail)
                                <th class="white-{{ $white++ }} white-plan-{{ $planDetail->id }}-revol">

                                </th>
                            @endforeach
                        @endforeach
                        @php
                            $white = 0;
                        @endphp
                    </tr>

                    @foreach ($mDistincts as $nameMDistinct => $mProducts)
                        @foreach ($mProducts as $mProduct)
                            @php
                                $roleAddTwo = $mProduct->planDetailProducts->whereIn('role_add', [ROLE_MANAGER, ROLE_SUPERVISOR])->count();
                                $overallRating = null;
                                $maxPossibilityResolution = [];
                                foreach ($mProduct->planDetailProducts as $planDetailProduct) {
                                    array_push($maxPossibilityResolution, $planDetailProduct->planDetail->possibility_resolution);
                                    if ($planDetailProduct->getLeaveStatusProdPossility()) {
                                        $overallRating = $planDetailProduct->getLeaveStatusProdPossility();
                                    } else {
                                        $overallRating = \App\Models\PlanDetail::listPossibilityResolution()[max($maxPossibilityResolution)] ?? null;
                                    }
                                }
                            @endphp
                            <tr class="product-item {{ $roleAddTwo == 0 ? 'is_user' : '' }}">
                                <td class="right">
                                    @if ($roleAddTwo)
                                        <span class="icon_add">{{ __('labels.refusal_plans.u203c.addition') }}</span><br />
                                    @endif
                                    {{ $nameMDistinct ?? '' }}
                                </td>
                                <td>
                                    @if ($roleAddTwo)
                                        <span class="icon_add">{{ __('labels.refusal_plans.u203c.addition') }}</span>
                                    @endif
                                    {{ $mProduct->name ?? '' }}
                                    <br>
                                </td>
                                <td class="center bg_pink rating">{{ $overallRating ?? '' }}</td>
                                @php
                                    $white = 0;
                                @endphp
                                @foreach ($mProduct->planDetailProducts as $planDetailProduct)
                                    <td class="white-{{ $white++ }} center bg_gray white-plan-{{ $planDetailProduct->planDetail->plan_id }}-revol plan_detail_{{ $planDetailProduct->planDetail->id }}"
                                        data-role_add="{{ $planDetailProduct->role_add }}"
                                        data-leave_status="{{ $planDetailProduct->leave_status }}"
                                        data-plan_detail_id="{{ $planDetailProduct->planDetail->id }}"
                                        data-leave_status_other="{{ $planDetailProduct->leave_status_other }}"
                                        data-child_plan_id="{{ $planDetailProduct->planDetail->plan_id }}"
                                        data-revolution_value=" {{ $planDetailProduct->planDetail->possibility_resolution }}"
                                        data-revolution=" {{ $planDetailProduct->planDetail->getTextRevolution() }}">
                                        @if ($planDetailProduct->role_add == ROLE_OFFICE_MANAGER && $planDetailProduct->leave_status == LEAVE_STATUS_2)
                                            {{ __('labels.refusal_plans.u203c.delete') }}
                                        @elseif ($planDetailProduct->role_add == ROLE_OFFICE_MANAGER && $planDetailProduct->leave_status != LEAVE_STATUS_2)
                                            {{ $planDetailProduct->planDetail->getTextRevolution() ?? '' }}
                                        @elseif (in_array($planDetailProduct->role_add, [ROLE_SUPERVISOR, ROLE_MANAGER]))
                                            <span class="leave_status_{{ $planDetailProduct->planDetail->id }}">
                                                {{ LEAVE_STATUS_TYPES[$planDetailProduct->leave_status] ?? '' }}
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                                @php
                                    $white = 0;
                                @endphp
                            </tr>
                            <input type="hidden" name="productIds[]" value="{{ $mProduct->id }}">
                        @endforeach
                    @endforeach
                </table>
                @error('is_choice')
                    <div class="red">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <!-- /scroll wrap -->
            <table class="normal_b mb30">
                <tr>
                    <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_1') }}</th>
                    <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_1') }}</td>
                </tr>
                <tr>
                    <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_2') }}</th>
                    <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_2') }}</td>
                </tr>
                <tr>
                    <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_3') }}</th>
                    <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_3') }}</td>
                </tr>
                <tr>
                    <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_4') }}</th>
                    <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_4') }}</td>
                </tr>
                <tr>
                    <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_5') }}</th>
                    <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_5') }}</td>
                </tr>
                <tr>
                    <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_6') }}</th>
                    <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_6') }}</td>
                </tr>
                <tr>
                    <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_7') }}</th>
                    <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_7') }}</td>
                </tr>
                <tr>
                    <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_8') }}</th>
                    <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_8') }}</td>
                </tr>
            </table>
            <p>{{ __('labels.refusal_plans.u203c.text_4') }}</p>
            @foreach ($mDistinctsNotRegister->values() as $key => $mDistinctName)
                @foreach ($mDistinctName as $index => $mProduct)
                    <span style="font-weight: bold">
                        {{ $mDistinctsNotRegister->count() > 1 ? $mProduct->mDistinction->name . '類' . $mProduct->name . ($key < $mDistinctsNotRegister->count() - 1 ? ',' : '') : $mProduct->mDistinction->name . '類' . $mProduct->name }}
                    </span>
                @endforeach
            @endforeach
            <p>{{ __('labels.refusal_plans.u203c.text_5') }}</p>
            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.refusal_plans.u203.btn.btn_1') }}" class="btn_e"
                        data-submit="{{ U203B02 }}" /></li>
            </ul>

            <ul class="btn_left eol">
                <li><input type="submit" value="{{ __('labels.refusal_plans.u203.btn.btn_6') }}" class="btn_a"
                        data-submit="{{ U203N }}" /></li>
            </ul>

            <!-- estimate box -->
            <div class="hidden_common">
                @include('user.modules.refusal.include.estimate-box', [
                    'mDistinctCart' => $mDistinctCart ?? [],
                    'costBankTransfer' => $costBankTransfer,
                    'flag' => 'u203re',
                    'redirect_to' => route('user.refusal.response-plan.refusal_product_re', [
                        'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => Request::get('trademark_plan_id'),
                    ]),
                ])
            </div>
            <!-- /estimate box -->
            {{-- Input Hidden --}}
            <input type="hidden" name="trademark_plan_id" value="{{ $trademarkPlan->id }}">
            <input type="hidden" name="comparison_trademark_result_id" value="{{ $comparisonTrademarkResult->id }}">
            <input type="hidden" name="cost_bank_transfer" id="cost_bank_transfer-input">
            <input type="hidden" name="subtotal" id="subtotal-input">
            <input type="hidden" name="commission" id="commission-input">
            <input type="hidden" name="tax" id="tax-input">
            <input type="hidden" name="cost_one_distintion" id="cost_one_distintion-input">
            <input type="hidden" name="total_amount" id="total_amount-input">
            <input type="hidden" name="cost_prod_add" id="cost_prod_add-input">
            <input type="hidden" name="submit_type">
            <input type="hidden" name="trademark_id" value="{{ $planCorrespondence->comparisonTrademarkResult->trademark->id }}">
            <input type="hidden" name="trademark_number" value="{{ $planCorrespondence->comparisonTrademarkResult->trademark->trademark_number }}">
            <input type="hidden" name="payer_info_id" value="{{ $paymentDraft->payerInfo->id ?? '' }}">
            <input type="hidden" name="from_page" value="{{ U203C_N }}">
            <input type="hidden" name="redirect_to" value="{{ U203N }}">
            <input type="hidden" name="total" value="" id="total_input">
            <input type="hidden" name="plan_corresspondence_id" value="{{ $planCorrespondence->id }}">
        </form>
    </div>
    <!-- /contents -->
    <style>
        .doc_name {
            display: inline-block;
            width: 180px;
            white-space: nowrap;
            overflow: hidden !important;
            text-overflow: ellipsis;
        }
    </style>
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const messagePopup = '{{ __('messages.general.Sentaku_U203_E001') }}';
        const messageError = '{{ __('messages.general.Common_E025') }}';
        const dataCost = @json($data);
        const routeAjax = '{{ route('user.refusal.response-plan.ajax-choose-plan') }}';
        const trademarkDocument = @json($trademarkDocuments);
        const plans = @json($plans);
        const trademarkPlan = @json($trademarkPlan);
        const numberProductDetails = 0;
        const localRules = {};
        const localMessages = {};
        const flag = @json(U203C);
        const ROLE_MANAGER = @json(ROLE_MANAGER);
        const ROLE_SUPERVISOR = @json(ROLE_SUPERVISOR);
        const labelClose = '{{ __('labels.close') }}'
        const messsagesErrorChooseNG = '{{ __('messages.general.Common_E058') }}'
        const planIdFirst = @json($plans->first()->id);
        const LEAVE_STATUS_TYPES = @json(LEAVE_STATUS_TYPES);
        const LEAVE_STATUS_2 = @json(LEAVE_STATUS_2);
        const LEAVE_STATUS_3 = @json(LEAVE_STATUS_3);
        const LEAVE_STATUS_4 = @json(LEAVE_STATUS_4);
        const LEAVE_STATUS_5 = @json(LEAVE_STATUS_5);
        const LEAVE_STATUS_6 = @json(LEAVE_STATUS_6);
        const LEAVE_STATUS_7 = @json(LEAVE_STATUS_7);
        var firstPlan = plans[0];
        const revolutionTypes = @json(App\Models\PlanDetail::getRevolutionTypes());
        const listPossibilityResolution = @json(App\Models\PlanDetail::listPossibilityResolution());

        function updateRating() {
            let checkedPlan = [];
            $.each($('.choose_plan_detail:checked'), function () {
                let val = $(this).val();
                checkedPlan.push(val);
            });

            $.each($('.product-item.is_user'), function () {
                $(this).find('.rating').text('－');

                let rating = [];
                $.each($(this).find('[data-role_add]'), function () {
                    let leaveStatus = $(this).data('leave_status');
                    let planDetailID = $(this).data('plan_detail_id');

                    if(inArray(planDetailID, checkedPlan)) {
                        if(leaveStatus == LEAVE_STATUS_2) {
                            rating.push(99);
                        } else {
                            rating.push(parseInt($(this).data('revolution_value')));
                        }
                    }
                });

                let overallRating = Math.max(...rating);
                let overallRatingText = '';
                if(overallRating == 99) {
                    overallRatingText = LEAVE_STATUS_TYPES[LEAVE_STATUS_2];
                } else {
                    overallRatingText = listPossibilityResolution[overallRating];
                }

                $(this).find('.rating').text(overallRatingText);
            });
        }

        function onChangeChoosePlanDetail() {
            $.each($('.choose_plan_detail'), function () {
                let val = $(this).val();
                let isChecked = $(this).prop('checked');
                if(isChecked) {
                    $(`[data-plan_detail_id=${val}]`).removeClass('bg_gray');
                } else {
                    $(`[data-plan_detail_id=${val}]`).addClass('bg_gray');
                }
            });

            updateRating();
        }
        onChangeChoosePlanDetail();

        function checkNGData() {
            let selectInputHasNG = [];

            $.each($('.choose_plan_detail'), function () {
                const selectedInput = $(this)
                const planDetailId = $(this).data('plan_detail_id')
                let isChecked = $(this).prop('checked');

                const checkedData = $(`td[data-plan_detail_id][data-plan_detail_id=${planDetailId}]`)
                for (const item of checkedData) {
                    if ($(item).text() == LEAVE_STATUS_TYPES[LEAVE_STATUS_5]) {
                        if(isChecked) {
                            selectInputHasNG.push(selectedInput);
                        }
                    }
                }
            });

            if(selectInputHasNG.length > 0) {
                $.confirm({
                    title: '',
                    content: messsagesErrorChooseNG,
                    buttons: {
                        ok: {
                            text: labelClose,
                            action: function action() {
                                $.each(selectInputHasNG, function () {
                                    $(this).prop('checked', false);
                                })
                                onChangeChoosePlanDetail();
                            }
                        }
                    }
                });
            }
        }

        $('.choose_plan_detail').on('change', function() {
            const planId = $(this).data('plan_id')
            const planDetailId = $(this).data('plan_detail_id')
            if (planId == planIdFirst) {
                const planDetailsSelected = $(
                    `td[data-plan_detail_id=${planDetailId}][data-role_add=${ROLE_MANAGER}],td[data-plan_detail_id=${planDetailId}][data-role_add=${ROLE_SUPERVISOR}]`
                )
                planDetailsSelected.each(function(key, item) {
                    const planDetailsNeedUpdate = $(item).closest('tr').find(
                        `td[data-child_plan_id][data-child_plan_id!=${planId}]`)
                    const leaveStatus = $(item).data('leave_status')
                    planDetailsNeedUpdate.each(function(_, element) {
                        const leaveStatusOtherWithEle = $(element).data('leave_status_other').find(
                            el => el.plan_product_detail_id == planDetailId).value
                        let text = '';
                        switch (+leaveStatusOtherWithEle) {
                            case LEAVE_STATUS_4:
                                if (leaveStatus == LEAVE_STATUS_6) {
                                    text = LEAVE_STATUS_TYPES[LEAVE_STATUS_6]
                                } else if (leaveStatus == LEAVE_STATUS_7) {
                                    text = LEAVE_STATUS_TYPES[LEAVE_STATUS_7]
                                } else if (leaveStatus == LEAVE_STATUS_3) {
                                    text = LEAVE_STATUS_TYPES[LEAVE_STATUS_3]
                                }
                                break;
                            case LEAVE_STATUS_5:
                                text = LEAVE_STATUS_TYPES[leaveStatusOtherWithEle]
                                break;
                            case LEAVE_STATUS_3:
                                if (leaveStatus == LEAVE_STATUS_3) {
                                    text = LEAVE_STATUS_TYPES[LEAVE_STATUS_6] + LEAVE_STATUS_TYPES[LEAVE_STATUS_3]
                                } else {
                                    text = LEAVE_STATUS_TYPES[leaveStatus] + LEAVE_STATUS_TYPES[LEAVE_STATUS_3]
                                }
                                break;
                        }

                        $(element).text(text)
                    })
                })
            }

            onChangeChoosePlanDetail();
            checkNGData()
        })
        $(`.choose_plan_detail.plan_${planIdFirst}:checked`).change();

        $('body').on('click', 'input[type=submit]', function(e) {
            const form = $('#form');
            let has_error = form.find('.notice:visible,.error:visible');
            if (has_error.length == 0 && form.valid()) {
                form.submit();
            } else {
                e.preventDefault();

                let firstError = has_error.first();
                window.scroll({
                    top: firstError.offset().top - 200,
                    behavior: 'smooth'
                });
            }
        })
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/refusal/choose-plan.js') }}"></script>
@endsection
