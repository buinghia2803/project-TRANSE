@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <form id="form" action="{{ route('user.refusal.response-plan.save_data_choose_plan') }}" method="POST">
            @csrf

            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <h2> {{ __('labels.refusal_plans.u203.title') }} </h2>

            <h3>{{ __('labels.refusal_plans.trademark_info') }}</h3>
            <div class="info mb10">
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable,
                ])
            </div><!-- /info -->
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

            <p>{{ __('labels.refusal_plans.u203.content_1') }}</p>

            @if (!in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01, OVER_04, OVER_04B, OVER_05]))
                <p class="red">{{ __('labels.refusal_plans.u203.content_2') }}<br />
                    {{ __('labels.refusal_plans.u203.content_3') }}</p>
                <p class="eol">
                    <a href="{{ route('user.refusal.extension-period.alert', ['id' => $planCorrespondence->id]) }}"
                       class="btn_b">{{ __('labels.refusal_plans.u203.btn.btn_4') }}</a>
                </p>
            @endif

            <dl class="w16em clearfix">
                <dt>
                    <h3><strong> {{ __('labels.refusal_plans.u203.content_4') }}</strong></h3>
                </dt>
                <dd>
                    <h3>
                        <strong>
                            {{ CommonHelper::formatTime($trademarkPlan->response_deadline ?? '') }}
                        </strong>
                    </h3>
                </dd>
            </dl>
            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif
            @include('user.modules.refusal.include.refuse-content', [
                'comparisonTrademarkResult' => $planCorrespondence,
                'mLawRegulationContentDefault' => $mLawRegulationContentDefault
            ])

            @foreach ($trademarkPlan->plans as $keyPlan => $plan)
                <h3>{{ __('labels.refusal_plans.u203.key_plan', ['attr' => $keyPlan + 1]) }}
                    <span>
                        @foreach ($plan->planReasons->unique('reason_id') as $key => $planReason)
                            @foreach ($planReason->reasons as $reason)
                                {{ $plan->planReasons->unique('reason_id')->count() > 1 ? $reason->reason_name . ($key < $plan->planReasons->unique('reason_id')->count() - 1 ? ',' : '') : $reason->reason_name }}
                            @endforeach
                        @endforeach
                    </span>
                    {{ __('labels.refusal_plans.u203.reason') }}
                </h3>
                @foreach ($plan->planReasons->unique('reason_id') as $key => $planReason)
                    @foreach ($planReason->reasons as $reason)
                        <p>{{ __('labels.refusal_plans.u203.reason_name', ['attr' => $reason->reason_name]) }}<br />
                            {{ $reason->mLawsRegulation->name }}
                        </p>
                    @endforeach
                @endforeach
                <p>{{ __('labels.refusal_plans.u203.content_5') }}<br />
                    {{ __('labels.refusal_plans.u203.content_6') }}</p>
                <div class="js-scrollable parent_table mb-2">
                    <input type="hidden" name="plan_ids[]" value="{{ $plan->id }}" class="plan_ids">
                    <table class="normal_b westimate mb-2 planCorrespondenceTbl planTbl{{ $plan->id }}" data-key="{{ $keyPlan }}">
                        <tr>
                            <th>{{ __('labels.refusal_plans.u203.is_choice') }}</th>
                            <th style="width:50%;">{{ __('labels.refusal_plans.u203.plan_description') }}</th>
                            <th>{{ __('labels.refusal_plans.u203.possibility_resolution_1') }}<br />{{ __('labels.refusal_plans.u203.possibility_resolution_2') }}
                            </th>
                            <th style="width:25%;">{{ __('labels.refusal_plans.u203.m_type_plan_doc_name') }}</th>
                            <th style="width:10%;" class="d-none hidden_m_distinct" id="">
                                {{ __('labels.refusal_plans.u203.m_distinct_1') }}<br />{{ __('labels.refusal_plans.u203.m_distinct_2') }}<br />{{ __('labels.refusal_plans.u203.m_distinct_3') }}
                            </th>
                            <th class="d-none hidden_m_distinct">
                                {{ __('labels.refusal_plans.u203.m_distinct_4') }}<br />{{ __('labels.refusal_plans.u203.m_distinct_5') }}<br />{{ __('labels.refusal_plans.u203.m_distinct_6') }}
                            </th>
                        </tr>
                        @foreach ($plan->planDetails as $keyPlanDetail => $planDetail)
                            <tr>
                                <th>{{ __('labels.refusal_plans.u203.content_7') }}({{ $keyPlanDetail + 1 }})<br />
                                    <input type="radio" class="radio_is_choice" name="is_choice[{{ $plan->id }}]"
                                        {{ $planDetail->is_choice == IS_CHOICE ? 'checked' : '' }}
                                        value="{{ $planDetail->id }}" data-possibility_resolution="{{ $planDetail->possibility_resolution }}" />

                                </th>
                                <td style="white-space: break-spaces;"> {{ $planDetail->plan_description }}</td>
                                <td style="text-align: center"> {{ $planDetail->getStrRevolution() }} <br>
                                    {{ $planDetail->getTextRevolution() }}</td>
                                <td>
                                    @if (isset($planDetail->mTypePlan->id) && in_array($planDetail->mTypePlan->id, [1, 3, 6]))
                                        {{ __('labels.refusal_plans.u203c.none') }}
                                    @else
                                        @foreach ($planDetail->mTypePlan->mTypePlanDocs ?? [] as $mTypePlanDoc)
                                            @if ($mTypePlanDoc->url && isset($mTypePlanDoc->url))
                                                <a href="{{ $mTypePlanDoc->url }}" target="_blank"
                                                    title="{{ $mTypePlanDoc->name }}" class=""
                                                    style="">{{ $mTypePlanDoc->name }}</a>
                                                <br>
                                            @else
                                                {{-- <span class="doc_name"> --}}
                                                <span>{{ $mTypePlanDoc->name }}</span>
                                                <br>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td class="d-none hidden_m_distinct">
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
                                    @if ($planDetailDistinctUnique->count())
                                        @foreach ($planDetailDistinctUnique as $name)
                                            {{ __('labels.refusal_plans.u203.content_8', ['attr' => $name]) }}<br>
                                        @endforeach
                                    @else
                                        {{ __('labels.refusal_plans.u203c.none') }}
                                    @endif
                                </td>
                                <td class="d-none hidden_m_distinct number_distinct_{{ $planDetail->id }}">
                                    @if ($distinctionsAddOn->count())
                                        {{ $distinctionsAddOn->count() }}
                                    @else
                                        なし
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    @error('is_choice')
                        <div class="red">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endforeach
            <p><input type="button" value="{{ __('labels.refusal_plans.u203.btn.btn_11') }}" class="btn_a"
                    id="clear_checked" /></p>
            <p class="eol"><input type="submit" value="{{ __('labels.refusal_plans.u203.btn.btn_2') }}" class="btn_b"
                    data-submit="{{ U203C_N }}" />
            </p>

            <p>{{ __('labels.refusal_plans.u203.content_9') }}</p>
            @if ($mDistinctsNotRegister->count())
                <span style="font-weight: bold" id="product_regex">
                    @foreach ($mDistinctsNotRegister as $index => $mDistincts)
                        @foreach ($mDistincts as $key => $mProduct)
                            {{ $index . '類' . $mProduct->name . ',' }}
                        @endforeach
                    @endforeach
                </span>
            @else
            @endif
            <h3>{{ __('labels.refusal_plans.u203.content_10') }}</h3>

            <p class="fs12">
                <strong>{{ __('labels.refusal_plans.u203.content_11') }}<span class="number_distinct_add"></span>
                    {{ __('labels.refusal_plans.u203.content_12') }} <span class="cost_additional"></span>
                    円</strong><br />
                <strong>{{ __('labels.refusal_plans.u203.content_13') }}<span
                        class="number_plan_detail_prods"></span>{{ __('labels.refusal_plans.u203.content_14') }}<span
                        class="cost_add_prod_name"></span>円</strong><br />
                <strong>{{ __('labels.refusal_plans.u203.content_15') }}<span id="total"></span> 円</strong> <input
                    type="button" value="{{ __('labels.refusal_plans.u203.btn.btn_12') }}" class="btn_b" />
            </p>

            <p class="note eol">{{ __('labels.refusal_plans.u203.content_16') }}<br />
                {{ __('labels.refusal_plans.u203.content_17') }}<br />
                {{ __('labels.refusal_plans.u203.content_18') }}
            </p>

            <h3> {{ __('labels.refusal_plans.u203.plan_detail_past') }}</h3>
            @foreach ($trademarkPlanDetailsIsPast->plans as $keyPlan => $plan)
                <h3>
                    {{ __('labels.refusal_plans.u203.key_plan', ['attr' => $keyPlan + 1]) }}
                    <span>
                        @foreach ($plan->planReasons->unique('reason_id') as $key => $planReason)
                            @foreach ($planReason->reasons as $reason)
                                {{ $plan->planReasons->unique('reason_id')->count() > 1 ? $reason->reason_name . ($key < $plan->planReasons->unique('reason_id')->count() - 1 ? ',' : '') : $reason->reason_name }}
                            @endforeach
                        @endforeach
                    </span>
                    {{ __('labels.refusal_plans.u203.reason') }}
                </h3>
                <p>{{ __('labels.refusal_plans.u203.content_5') }}<br />
                    {{ __('labels.refusal_plans.u203.content_6') }}</p>
                <div class="js-scrollable mb-2">
                    <table class="normal_b westimate mb20">
                        <tr>
                            <th></th>
                            <th style="width:50%;"> {{ __('labels.refusal_plans.u203.content_7') }}</th>
                            <th>{{ __('labels.refusal_plans.u203.possibility_resolution_1') }}<br />{{ __('labels.refusal_plans.u203.possibility_resolution_2') }}
                            </th>
                            <th style="width:10%;">{{ __('labels.refusal_plans.u203.content_22') }} <br>
                                {{ __('labels.refusal_plans.u203.content_23') }}</th>
                            <th style="width:10%;">{{ __('labels.refusal_plans.u203.content_24') }} <br>
                                {{ __('labels.refusal_plans.u203.content_25') }}</th>
                        </tr>
                        @foreach ($plan->planDetails as $keyPlanDetail => $planDetail)
                            @php
                                $clsBg = $planDetail->is_choice_past == App\Models\PlanDetail::IS_CHOICE_PAST_TRUE ? 'bg_gray' : '';
                            @endphp
                            <tr>
                                <th class="{{ $clsBg }}">{{ __('labels.refusal_plans.u203.content_7') }}({{ $keyPlanDetail + 1 }})<br />
                                </th>

                                <td class="{{ $clsBg }}" style="white-space: break-spaces;"> {{ $planDetail->plan_description }}

                                </td>
                                <td class="{{ $clsBg }}" style="text-align: center"> {{ $planDetail->getStrRevolution() }} <br>
                                    {{ $planDetail->getTextRevolution() }} </td>
                                <td class="center {{ $clsBg }}">
                                    @if (isset($planDetail->mTypePlan->id) && in_array($planDetail->mTypePlan->id, [1, 3, 6]))
                                        {{ __('labels.refusal_plans.u203.no_need') }}
                                    @else
                                        {{ __('labels.refusal_plans.u203.need') }}
                                        {{-- @foreach ($planDetail->mTypePlan->mTypePlanDocs ?? [] as $mTypePlanDoc)
                                            @if ($mTypePlanDoc->url && isset($mTypePlanDoc->url))
                                                <a href="{{ $mTypePlanDoc->url }}" target="_blank"
                                                    title="{{ $mTypePlanDoc->name }}" class=""
                                                    style="">{{ $mTypePlanDoc->name }}</a>
                                                <br>
                                            @else
                                                <span>{{ $mTypePlanDoc->name }}</span>
                                                <br>
                                            @endif
                                        @endforeach --}}
                                    @endif
                                </td>
                                <td class="{{ $clsBg }}">
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
                                            ->pluck('mDistinction.name')
                                            ->unique();
                                    @endphp
                                    @if ($planDetailDistinctUnique->count())
                                        @foreach ($planDetailDistinctUnique as $name)
                                            {{ __('labels.refusal_plans.u203.content_8', ['attr' => $name]) }}<br>
                                        @endforeach
                                    @else
                                        {{ __('labels.refusal_plans.u203c.none') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endforeach
            <div class="hidden_common">
                <hr />
                @include('user.components.payer-info', [
                    'prefectures' => $prefectures ?? [],
                    'nations' => $nations ?? [],
                    'paymentFee' => [
                        'cost_service_base' => $costBankTransfer ?? '',
                    ],
                    'payerInfo' => $paymentDraft->payerInfo ?? null,
                ])
                <hr />
            </div>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.refusal_plans.u203.btn.btn_1') }}" class="btn_e mb20"
                        data-submit="{{ U203B02 }}" /></li>
                <li><input type="submit" value="{{ __('labels.refusal_plans.u203.btn.btn_2') }}" class="btn_b"
                        data-submit="{{ U203C_N }}" /></li>
            </ul>

            <ul class="btn_left eol">
                <li>
                    <a href="{{ route('user.refusal.notification.cancel', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}"
                        class="btn_a mb-2" style="height: 32px">{{ __('labels.refusal_plans.u203.btn.back') }}</a>
                </li>
                <li><input type="button" value="{{ __('labels.refusal_plans.u203.btn.btn_3') }}" class="btn_a"
                        data-submit="{{ U000ANKEN_TOP }}" id="save_draft" /></li>
            </ul>

            <p>{{ __('labels.refusal_plans.u203.content_19') }}<a
                    href="{{ route('user.refusal.response-plan.stop', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $trademarkPlan->id,
                    ]) }}">{{ __('labels.refusal_plans.u203.content_20') }}</a>{{ __('labels.refusal_plans.u203.content_21') }}
            </p>

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
            <input type="hidden" name="comparison_trademark_result_id"
                value="{{ $planCorrespondence->comparisonTrademarkResult->id }}">
            <input type="hidden" name="cost_bank_transfer" id="cost_bank_transfer-input">
            <input type="hidden" name="subtotal" id="subtotal-input">
            <input type="hidden" name="commission" id="commission-input">
            <input type="hidden" name="tax" id="tax-input">
            <input type="hidden" name="cost_one_distintion" id="cost_one_distintion-input">
            <input type="hidden" name="total_amount" id="total_amount-input">
            <input type="hidden" name="cost_prod_add" id="cost_prod_add-input">
            <input type="hidden" name="submit_type">
            <input type="hidden" name="trademark_id"
                value="{{ $planCorrespondence->comparisonTrademarkResult->trademark->id }}">
            <input type="hidden" name="trademark_number"
                value="{{ $planCorrespondence->comparisonTrademarkResult->trademark->trademark_number }}">
            <input type="hidden" name="payer_info_id" value="{{ $paymentDraft->payerInfo->id ?? '' }}">
            <input type="hidden" name="from_page" value="{{ $paymentDraft->from_page ?? '' }}">
            <input type="hidden" name="redirect_to" value="{{ U203N }}">
            <input type="hidden" name="total" value="" id="total_input">
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
        const dataCost = @json($data);
        const trademarkPlan = @json($trademarkPlan);
        const messageCheck = '{{ __('messages.general.Hoshin_U203_S001') }}';
        const routeAjax = '{{ route('user.refusal.response-plan.ajax-choose-plan') }}';
        const trademarkDocument = @json($trademarkDocuments);
        const messageCheckIsCancel = '{{ __('messages.general.Hoshin_U203_S002') }}';
        const plans = @json($trademarkPlan->plans);
        const messagePopup = '{{ __('messages.general.Sentaku_U203_E001') }}';
        const messageError = '{{ __('messages.general.Common_E025') }}';
        const localRules = {};
        const localMessages = {};
        const revolutionTypes = @json(App\Models\PlanDetail::getRevolutionTypes());
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
