@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <div class="wide clearfix">
            @include('compoments.messages')

            <form id="form" action="{{ route('admin.refusal.response-plan.product.edit.supervisor.post', [
                'id' => $comparisonTrademarkResult->id,
                'trademark_plan_id' => $trademarkPlan->id,
            ]) }}" method="post">
                @csrf
                <input type="hidden" name="is_edit_plan" value="{{ $trademarkPlan->is_edit_plan ?? 0 }}">
                <input type="hidden" name="is_decision" value="{{ $trademarkPlan->is_decision ?? 0 }}">
                <input type="hidden" name="delete_plan_detail_product_ids" value="">
                <input type="hidden" name="restore_plan_detail_product_ids" value="">

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])

                {{-- Common 203 here --}}
                @include('admin.modules.plans.common.common_a203', ['dataCommon' => $dataCommon])

                <p>{{ __('labels.a203c.step') }}</p>

                <table class="normal_b column1" id="product_detail_table">
                    <tr>
                        @php
                            $totalDetail = 0;
                            foreach($plans as $plan) {
                                $totalDetail += count($plan->planDetails);
                            }
                        @endphp
                        <td colspan="{{ 4 + $totalDetail }}">
                            {{ __('labels.a203c.table_info_edit.title') }}　
                            <input type="button" data-edit_plan value="{{ __('labels.a203c.table_info_edit.fix') }}" class="btn_a mb05"/>　
                            <input type="button" data-decision value="{{ __('labels.a203c.table_info_edit.decide') }}" class="btn_b mb05"/>
                        </td>
                        <td colspan="{{ 5 + $totalDetail }}">
                            {{ __('labels.a203c.table_info_edit.fix') }}：　
                            <input type="button" data-decision_edit value="{{ __('labels.a203c.table_info_edit.decide') }}" class="btn_b mb05"/>
                        </td>
                        <td colspan="{{ 4 + $totalDetail }}">{{ __('labels.a203c.table_info_edit.decide') }}：</td>
                    </tr>
                    <tr>
                        <td colspan="4" rowspan="2">&nbsp;</td>
                        @foreach($plans as $plan)
                            <th colspan="{{ count($plan->planDetails) }}">
                                {{ __('labels.a203c.plan_table.plan') }}-{{ $loop->iteration ?? '' }} {{ $plan->reason_name ?? '' }}
                            </th>
                        @endforeach

                        <td colspan="5" rowspan="2">&nbsp;</td>
                        @foreach($plans as $plan)
                            <th colspan="{{ count($plan->planDetails) }}">
                                {{ __('labels.a203c.plan_table.plan') }}-{{ $loop->iteration ?? '' }} {{ $plan->reason_name ?? '' }}
                            </th>
                        @endforeach

                        <td colspan="4" rowspan="2"></td>
                        @foreach($plans as $plan)
                            <th colspan="{{ count($plan->planDetails) }}">
                                {{ __('labels.a203c.plan_table.plan') }}-{{ $loop->iteration ?? '' }} {{ $plan->reason_name ?? '' }}
                                <label><input type="checkbox" data-plan_confirm="{{ $plan->id }}" name="plans[{{ $plan->id }}][is_confirm]" {{ $plan->is_confirm == true ? 'checked' : '' }} value="{{ true ?? false }}"/> 確認＆ロック</label>
                                <input type="hidden" name="plan_ids[]" value="{{ $plan->id }}">
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th>{{ __('labels.a203c.plan_table.plan_detail') }}({{ $loop->iteration }})</th>
                            @endforeach
                        @endforeach

                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th>{{ __('labels.a203c.plan_table.plan_detail') }}({{ $loop->iteration }})</th>
                            @endforeach
                        @endforeach

                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th>{{ __('labels.a203c.plan_table.plan_detail') }}({{ $loop->iteration }})</th>
                            @endforeach
                        @endforeach
                    </tr>
                    <tr>
                        <th colspan="4" class="right">{{ __('labels.a203c.revolution') }}</th>
                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th>{{ $planDetail->text_revolution ?? '' }}</th>
                            @endforeach
                        @endforeach

                        <th colspan="5" class="right">{{ __('labels.a203c.revolution') }}</th>
                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th>{{ $planDetail->text_revolution ?? '' }}</th>
                            @endforeach
                        @endforeach

                        <th colspan="4" class="right">{{ __('labels.a203c.revolution') }}</th>
                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th>{{ $planDetail->text_revolution ?? '' }}</th>
                            @endforeach
                        @endforeach
                    </tr>
                    <tr>
                        <th colspan="4" class="right">{{ __('labels.a203c.type_plan_name') }}</th>
                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th class="center">
                                    <p class="mb-0 line line-1">{{ \Str::limit($planDetail->mTypePlan->name ?? '', $limit = 9, $end = '...') }}</p>
                                </th>
                            @endforeach
                        @endforeach

                        <th colspan="5" class="right">{{ __('labels.a203c.type_plan_name') }}</th>
                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th class="center">
                                    <p class="mb-0 line line-1">{{ \Str::limit($planDetail->mTypePlan->name ?? '', $limit = 9, $end = '...') }}</p>
                                </th>
                            @endforeach
                        @endforeach

                        <th colspan="4" class="right">{{ __('labels.a203c.type_plan_name') }}</th>
                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th class="center">
                                    <p class="mb-0 line line-1">{{ \Str::limit($planDetail->mTypePlan->name ?? '', $limit = 9, $end = '...') }}</p>
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                    <tr>
                        <th colspan="4" class="right">{{ __('labels.a203c.additional') }}</th>
                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th rowspan="2" class="center">{{ $planDetail->distinct_is_add_text ?? '&nbsp;' }}</th>
                            @endforeach
                        @endforeach

                        <th colspan="5" class="right">{{ __('labels.a203c.additional') }}</th>
                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th rowspan="2" class="center">{{ $planDetail->distinct_is_add_text ?? '&nbsp;' }}</th>
                            @endforeach
                        @endforeach

                        <th colspan="4" class="right">{{ __('labels.a203c.additional') }}</th>
                        @foreach($plans as $plan)
                            @foreach($plan->planDetails as $planDetail)
                                <th rowspan="2" class="center">{{ $planDetail->distinct_is_add_text ?? '&nbsp;' }}</th>
                            @endforeach
                        @endforeach
                    </tr>
                    <tr>
                        <th>{{ __('labels.a203c.distinction') }}</th>
                        <th class="em16">{{ __('labels.a203c.product_name') }}</th>
                        <th>{{ __('labels.a203c.code') }}</th>
                        <th class="em14">{{ __('labels.a203c.rank') }}</th>

                        <th>{{ __('labels.a203c.distinction') }}</th>
                        <th class="em16">{{ __('labels.a203c.product_name') }}</th>
                        <th>{{ __('labels.a203c.code') }}</th>
                        <th class="em14">{{ __('labels.a203c.rank') }}</th>
                        <th>&nbsp;</th>

                        <th>{{ __('labels.a203c.distinction') }}</th>
                        <th class="em16">{{ __('labels.a203c.product_name') }}</th>
                        <th>{{ __('labels.a203c.code') }}</th>
                        <th class="em14">{{ __('labels.a203c.rank') }}</th>
                    </tr>

                    @foreach($products as $product)
                        @php
                            $planDetailProduct = $product['plan_detail_product'] ?? null;
                            $productData = $product['product'] ?? null;
                            $planDetailDistinction = $product['plan_detail_distinction'] ?? null;
                            $distinction = $product['distinction'] ?? null;
                            $codes = $product['codes'] ?? null;
                            $reasonRefNumProd = $product['reasonRefNumProd'] ?? null;
                            $planDetails = collect($product['plan_details'] ?? []);

                            $codeNotEdit = $codes->whereNotIn('type', [
                                \App\Models\MCode::TYPE_CREATIVE_CLEAN,
                                \App\Models\MCode::TYPE_SEMI_CLEAN,
                            ]);
                            $codeNotEditName = implode(' ', $codeNotEdit->pluck('name')->toArray());

                            $codeEdit = $codes->whereIn('type', [
                                \App\Models\MCode::TYPE_CREATIVE_CLEAN,
                                \App\Models\MCode::TYPE_SEMI_CLEAN,
                            ]);
                            $codeEditName = implode(' ', $codeEdit->pluck('name')->toArray());
                            $codeName = implode(' ', $codes->pluck('name')->toArray());
                            $codeData = $codes->map(function ($item) {
                                return ['id' => $item->id, 'name' => $item->name, 'type' => $item->type];
                            });

                            $optionProduct = $planDetailProduct->optionProduct();
                            $optionRequired = $planDetailProduct->optionRequired();
                            $optionDefault = $planDetailProduct->optionDefault();

                            $isChoice = $planDetailProduct->is_choice;
                        @endphp
                        @if($planDetailProduct->isRoleAddUser())
                            <tr class="item is-user" data-is_choice="{{ $isChoice ?? false }}">
                                {{-- Group 1 --}}
                                <td class="center">
                                    <div class="step_1">
                                        <span>{{ $distinction->name ?? '' }}</span>
                                        <input type="hidden" data-step_1="m_distinction_id" value="{{ $distinction->id ?? '' }}">
                                    </div>
                                </td>
                                <td class="">
                                    <div class="step_1">
                                        <span>{{ $productData->name ?? '' }}</span>
                                        <input type="hidden" data-step_1="product_name" value="{{ $productData->name ?? '' }}">
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="step_1">
                                        <div class="code-block">
                                            @foreach($codes as $code)
                                                <span data-type="{{ $code->type }}" class="{{ $loop->index > 2 ? 'hidden' : '' }}">
                                                <span>{{ $code->name ?? '' }}</span>

                                                @if(count($codes) > 3 && $loop->index == 2)
                                                    <span class="show_all_code cursor-pointer">+</span>
                                                @endif
                                            </span>
                                            @endforeach
                                            <input type="hidden" data-step_1="code_name" value="{{ $codeName ?? '' }}">
                                        </div>
                                        <textarea class="hidden" data-step_1="code_data" data-id="{{ $planDetailProduct->id }}">@json($codeData->toArray())</textarea>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="step_1">
                                        @if($isChoice == false)
                                            <span>{{ __('labels.a203c.no_application') }}</span>
                                            <input type="hidden" data-step_1="rank" value="{{ __('labels.a203c.no_application') }}">
                                        @else
                                            <span>{{ $reasonRefNumProd->rank ?? '' }}</span>
                                            <input type="hidden" data-step_1="rank" value="{{ $reasonRefNumProd->rank ?? '' }}">
                                        @endif
                                    </div>
                                </td>
                                @foreach($planDetails as $planDetail)
                                    @php
                                        $distinctionProd = $planDetail['distinction_prod'] ?? null;
                                        $planDetailProd = $planDetail['plan_detail_product'] ?? null;
                                    @endphp
                                    <td class="center">
                                        <div class="step_1">
                                            @if($isChoice == false)
                                                <span>{{ __('labels.none') }}</span>
                                            @else
                                                @php
                                                    $leaveStatusText = '-';
                                                    if(!empty($planDetailProd->leave_status)) {
                                                        $leaveStatusText = LEAVE_STATUS_TYPES[$planDetailProd->leave_status] ?? '';
                                                    } elseif($planDetailProd->is_choice != 0) {
                                                        $leaveStatusText = __('labels.a203c.plan_table.leave_all');
                                                    }
                                                @endphp
                                                <input type="hidden" data-step_1="leave_status_{{ $planDetailProd->id }}" value="{{ $planDetailProd->leave_status ?? $leaveStatusText }}">

                                                <span>{{ $leaveStatusText ?? '' }}</span>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach

                                {{-- Group 2 --}}
                                <td class="center">
                                    <div class="step_2">
                                        <span>{{ $distinction->name ?? '' }}</span>
                                        <input type="hidden" data-step_2="m_distinction_id" name="update_products[{{ $planDetailProduct->id }}][m_distinction_id_edit]" value="{{ $planDetailProduct->m_distinction_id_edit ?? '' }}">
                                    </div>
                                </td>
                                <td class="">
                                    <div class="step_2">
                                        <input type="hidden" data-step_2="product_name" name="update_products[{{ $planDetailProduct->id }}][product_name_edit]" value="{{ $planDetailProduct->product_name_edit ?? '' }}">

                                        <span>{{ $planDetailProduct->product_name_edit ?? '' }}</span>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="step_2">
                                        <div class="code-block">
                                            @foreach($codes as $code)
                                                <span data-type="{{ $code->type }}" class="{{ $loop->index > 2 ? 'hidden' : '' }}">
                                                    {{ $code->name ?? '' }}

                                                    @if(count($codes) > 3 && $loop->index == 2)
                                                        <span class="show_all_code cursor-pointer">+</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                        <div class="code-edit mt-1">
                                            <input data-step_2="code_name_fix" type="hidden" name="update_products[{{ $planDetailProduct->id }}][code_name_edit_fix]" value="{{ $codeNotEditName }}">
                                            @if(count($codeEdit))
                                                @if($isChoice == true)
                                                    <input type="button" data-edit_code value="{{ __('labels.edit') }}" class="btn_a small">
                                                @endif
                                                <textarea data-step_2="code_name" data-input_product_code name="update_products[{{ $planDetailProduct->id }}][code_name_edit]" class="wide w-100 hidden">{{ $codeEditName ?? '' }}</textarea>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="center">
                                    <span class="step_2">
                                        @if($isChoice == false)
                                            <span>{{ __('labels.a203c.no_application') }}</span>
                                            <input type="hidden" data-step_2="rank" value="{{ __('labels.a203c.no_application') }}">
                                        @else
                                            <span>{{ $reasonRefNumProd->rank ?? '' }}</span>
                                            <input type="hidden" data-step_2="rank" value="{{ $reasonRefNumProd->rank ?? '' }}">
                                        @endif
                                    </span>
                                </td>
                                <td class="center">
                                    <div class="step_2">
                                        @if($isChoice == true)
                                            <input type="button" value="{{ __('labels.a203c.plan_table.add_distinct') }} ＋" class="btn_a small mb05"
                                                   data-add_distinct
                                                   data-info="{{ json_encode([
                                                'distinction' => [
                                                    'id' => $distinction->id,
                                                    'plan_detail_distinction' => $planDetailDistinction->id,
                                                    'product_name'=> $productData->name,
                                                ],
                                            ]) }}"
                                            ><br>
                                            <input type="button" value="{{ __('labels.a203c.plan_table.add_product') }} ＋" class="btn_a small"
                                                   data-add_product
                                                   data-info="{{ json_encode([
                                                'distinction' => [
                                                    'id' => $distinction->id,
                                                    'plan_detail_distinction' => $planDetailDistinction->id,
                                                    'name'=> $planDetailDistinction->mDistinction->name,
                                                    'product_name'=> $productData->name,
                                                ],
                                           ]) }}"
                                            >
                                        @endif
                                    </div>
                                </td>
                                @foreach($planDetails as $planDetail)
                                    @php
                                        $distinctionProd = $planDetail['distinction_prod'] ?? null;
                                        $planDetailProd = $planDetail['plan_detail_product'] ?? null;

                                        $leaveStatus = $planDetailProd->leave_status_edit ?? $planDetailProd->leave_status ?? null;
                                    @endphp
                                    <td class="center">
                                        <div class="step_2" data-is_plan_confirm="{{ $planDetail['plan_id'] ?? 0 }}">
                                            <input type="hidden" name="plan_detail_product_ids[{{ $planDetailProduct->id }}][]" value="{{ $planDetailProd->id }}">

                                            @if($isChoice == false)
                                                <span>{{ __('labels.none') }}</span>
                                            @else
                                                @if(!empty($planDetailProd->leave_status))
                                                    @if($distinctionProd->is_leave_all == true)
                                                        <span>{{ __('labels.a203c.plan_table.leave_all') }}</span>
                                                        <input type="hidden" data-step_2="leave_status_{{ $planDetailProd->id }}" value="{{ __('labels.a203c.plan_table.leave_all') }}">
                                                    @else
                                                        <select data-step_2="leave_status_{{ $planDetailProd->id }}" name="plan_detail_products[{{ $planDetailProd->id }}][leave_status_edit]">
                                                            @foreach($optionProduct as $key => $option)
                                                                <option value="{{ $key }}"
                                                                    {{ $leaveStatus == $key ? 'selected' : '' }}
                                                                >{{ __($option) }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                @elseif($planDetailProd->is_choice != 0)
                                                    <span>{{ __('labels.a203c.plan_table.leave_all') }}</span>
                                                    <input type="hidden" data-step_2="leave_status_{{ $planDetailProd->id }}" value="{{ __('labels.a203c.plan_table.leave_all') }}">
                                                @else
                                                    <span>-</span>
                                                    <input type="hidden" data-step_2="leave_status_{{ $planDetailProd->id }}" value="-">
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                @endforeach

                                {{-- Group 3 --}}
                                <td class="center">
                                    <div class="step_3">
                                        <input type="hidden" data-finish="m_distinction_id" name="update_products[{{ $planDetailProduct->id }}][m_distinction_id_decision]" value="{{ $planDetailProduct->m_distinction_id_decision ?? '' }}">

                                        <span data-step_3="m_distinction_id">
                                            {{ $allDistinctionData[$planDetailProduct->m_distinction_id_decision] ?? '' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="">
                                    <div class="step_3">
                                        <input type="hidden" data-finish="product_name" name="update_products[{{ $planDetailProduct->id }}][product_name_decision]" value="{{ $planDetailProduct->product_name_decision ?? '' }}">

                                        <span data-step_3="product_name" data-value="{{ $planDetailProduct->product_name_decision ?? '' }}">
                                            {{ $planDetailProduct->product_name_decision ?? '' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="step_3">
                                        <div class="code-block">
                                            @php
                                                $codeNameDecision = json_decode($planDetailProduct->code_name_decision ?? '[]');
                                                $codeString = implode(' ', $codeNameDecision);
                                            @endphp
                                            <input type="hidden" data-finish="code_name" name="update_products[{{ $planDetailProduct->id }}][code_name_decision]" value="{{ $codeString ?? '' }}">

                                            <span data-step_3="code_name">{{ $codeString ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="step_3">
                                        @if($isChoice == false)
                                            <span data-step_3="rank" data-value="{{ __('labels.a203c.no_application') }}">
                                                {{ __('labels.a203c.no_application') }}
                                            </span>
                                        @else
                                            <span data-step_3="rank" data-value="{{ $reasonRefNumProd->rank ?? '' }}">
                                                {{ $reasonRefNumProd->rank ?? '' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                @foreach($planDetails as $planDetail)
                                    @php
                                        $planDetailProd = $planDetail['plan_detail_product'] ?? null;
                                    @endphp
                                    <td class="center">
                                        <div class="step_3">
                                            <input type="hidden" data-finish="leave_status_{{ $planDetailProd->id }}" name="plan_detail_products[{{ $planDetailProd->id }}][leave_status_decision]" value="{{ $planDetailProd->leave_status_decision }}">

                                            @if($isChoice == false)
                                                <span>{{ __('labels.none') }}</span>
                                            @else
                                                <span data-step_3="leave_status_{{ $planDetailProd->id }}">
                                                    @if(!empty($planDetailProd->leave_status_decision))
                                                        {{ LEAVE_STATUS_TYPES[$planDetailProd->leave_status_decision] ?? '' }}
                                                    @elseif($planDetailProd->is_choice != 0)
                                                        {{ __('labels.a203c.plan_table.leave_all') }}
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @else
                            @php
                                $ranID = \Str::random(30);

                                $firstPlan = $plans->first();
                                $firstPlanDetails = $firstPlan->planDetails;

                                $tdClass = 'bg_yellow';
                                $isRole = 'is-manager';
                                if ($planDetailProduct->isRoleAddSupervisor()) {
                                    $tdClass = 'bg_purple2';
                                    $isRole = 'is-supervisor';
                                }

                                if ($planDetailProduct->is_deleted == true) {
                                    $tdClass = 'bg_gray';
                                }

                                $hasEditClass = '';
                                if (!empty($planDetailProduct->product_name_edit)) {
                                    $hasEditClass = 'has-edit';
                                }
                            @endphp

                            @foreach($firstPlanDetails as $firstPlanDetail)
                                @php $firstPlanDetailLoop = $loop; @endphp

                                @if($firstPlanDetailLoop->first)
                                    <tr class="item {{ $isRole }} {{ $hasEditClass }}" data-row="{{ $ranID ?? '' }}" data-is_choice="{{ $isChoice ?? false }}">
                                        {{-- Group 1 top --}}
                                        <td rowspan="{{ count($firstPlanDetails) }}" class="center {{ $tdClass ?? '' }}">
                                            <div class="step_1">
                                                <span>{{ $distinction->name }}</span>
                                                <input type="hidden" data-step_1="m_distinction_id" value="{{ $distinction->id }}">
                                            </div>
                                        </td>
                                        <td rowspan="{{ count($firstPlanDetails) }}" class="{{ $tdClass ?? '' }}">
                                            <div class="step_1">
                                                <span>{{ $productData->name }}</span>
                                                <input type="hidden" data-step_1="product_name" value="{{ $productData->name }}">
                                            </div>
                                        </td>
                                        <td rowspan="{{ count($firstPlanDetails) }}" class="{{ $tdClass ?? '' }}">
                                            <div class="step_1">
                                                <div class="code-block">
                                                    @foreach($codes as $code)
                                                        <span data-type="{{ $code->type }}" class="{{ $loop->index > 2 ? 'hidden' : '' }}">
                                                        {{ $code->name ?? '' }}

                                                            @if(count($codes) > 3 && $loop->index == 2)
                                                                <span class="show_all_code cursor-pointer">+</span>
                                                            @endif
                                                    </span>
                                                    @endforeach

                                                    <input type="hidden" data-step_1="code_name" value="{{ $codeName ?? '' }}">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center {{ $tdClass ?? '' }}">
                                            <div class="step_1">
                                                {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
                                            </div>
                                        </td>
                                        @foreach($planDetails as $planDetail)
                                            @php
                                                $planDetailData = $planDetail['plan_detail_product'] ?? null;
                                                $leaveStatus = $planDetailData->leave_status;
                                            @endphp
                                            <td class="center {{ $tdClass ?? '' }}">
                                                <div class="step_1">
                                                    @if($planDetail['plan_id'] == $firstPlan->id)
                                                        @if($firstPlanDetail->id == $planDetailData->plan_detail_id)
                                                            @if($planDetailData->is_choice == false)
                                                                <span>{{ __('labels.none') }}</span>
                                                                <input type="hidden" data-step_1="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ __('labels.none') }}">
                                                            @else
                                                                <span>{{ $optionRequired[$leaveStatus] ?? '' }}</span>
                                                                <input type="hidden" data-step_1="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ $leaveStatus ?? '' }}">
                                                            @endif
                                                        @endif
                                                    @else
                                                        @php
                                                            $leaveStatusOther = $planDetailData->leave_status_other ?? '[]';
                                                            $leaveStatusOtherData = collect(json_decode($leaveStatusOther))
                                                                ->where('plan_product_detail_id', $firstPlanDetail->id)->first();
                                                            $leaveStatusOtherValue = $leaveStatusOtherData->value ?? null;
                                                        @endphp
                                                        @if($planDetailData->is_choice == false)
                                                            <span>{{ __('labels.none') }}</span>
                                                            <input type="hidden" data-step_1="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ __('labels.none') }}">
                                                        @else
                                                            <span>{{ $optionDefault[$leaveStatusOtherValue] ?? '' }}</span>
                                                            <input type="hidden" data-step_1="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ $leaveStatusOtherValue }}">
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach

                                        {{-- Group 2 top --}}
                                        <td rowspan="{{ count($firstPlanDetails) }}" class="center {{ $tdClass ?? '' }}">
                                            <div class="step_2">
                                                @if($planDetailDistinction->is_add == true)
                                                    <select data-step_2="m_distinction_id" name="update_products[{{ $planDetailProduct->id }}][m_distinction_id_edit]" data-select_distinct>
                                                        <option value=""></option>
                                                        @foreach($distinctions as $item)
                                                            <option value="{{ $item->id ?? '' }}"
                                                                    {{ $planDetailProduct->m_distinction_id_edit == $item->id ? 'selected' : '' }}
                                                            >{{ $item->name ?? '' }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <span>{{ $distinction->name }}</span>
                                                    <input type="hidden" data-step_2="m_distinction_id" name="update_products[{{ $planDetailProduct->id }}][m_distinction_id_edit]" value="{{ $distinction->id }}">
                                                @endif
                                                <div>
                                                    @if($planDetailProduct->is_deleted == true)
                                                        <input type="button" data-restore_row value="{{ __('labels.restore') }}" class="btn_a small">
                                                    @else
                                                        <input type="button" data-delete_row value="{{ __('labels.delete_all') }}" class="btn_a small">
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td rowspan="{{ count($firstPlanDetails) }}" class="{{ $tdClass ?? '' }}">
                                            <div class="step_2">
                                                <textarea data-step_2="product_name" rows="3" name="update_products[{{ $planDetailProduct->id }}][product_name_edit]" data-input_product_name>{{ $planDetailProduct->product_name_edit }}</textarea>
                                            </div>
                                        </td>
                                        <td rowspan="{{ count($firstPlanDetails) }}" class="{{ $tdClass ?? '' }}">
                                            <div class="step_2">
                                                <div class="code-block">
                                                    @php
                                                        $codeNameEdit = json_decode($planDetailProduct->code_name_edit ?? '[]');
                                                        $codeString = implode(' ', $codeNameEdit);
                                                    @endphp
                                                    <textarea data-step_2="code_name" class="wide w-100" name="update_products[{{ $planDetailProduct->id }}][code_name_edit]" data-input_product_code>{{ $codeString ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center {{ $tdClass ?? '' }}">
                                            <div class="step_2">
                                                {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
                                            </div>
                                        </td>
                                        <td class="center {{ $tdClass ?? '' }}"></td>
                                        @foreach($planDetails as $planDetail)
                                            @php
                                                $planDetailData = $planDetail['plan_detail_product'] ?? null;
                                            @endphp
                                            <td class="center {{ $tdClass ?? '' }}" data-plan_detail_product_id="{{ $planDetailData->id }}">
                                                <div class="step_2" data-is_plan_confirm="{{ $planDetail['plan_id'] ?? 0 }}">
                                                    <input type="hidden" name="plan_detail_product_ids[{{ $planDetailProduct->id }}][]" value="{{ $planDetailData->id }}">

                                                    @if($planDetail['plan_id'] == $firstPlan->id)
                                                        @if($firstPlanDetail->id == $planDetailData->plan_detail_id)
                                                            @if($planDetailData->is_choice == false)
                                                                <span>{{ __('labels.none') }}</span>
                                                                <input type="hidden" data-step_2="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ __('labels.none') }}">
                                                            @else
                                                                <select data-step_2="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" name="plan_detail_products[{{ $planDetailData->id }}][leave_status_edit]">
                                                                    @foreach($optionRequired as $key => $option)
                                                                        <option value="{{ $key }}"
                                                                                {{ $planDetailData->leave_status_edit == $key ? 'selected' : '' }}
                                                                        >{{ __($option) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @php
                                                            $leaveStatusOtherEdit = $planDetailData->leave_status_other_edit ?? '[]';
                                                            $leaveStatusOtherEditData = collect(json_decode($leaveStatusOtherEdit))
                                                                ->where('plan_product_detail_id', $firstPlanDetail->id)->first();
                                                            $leaveStatusOtherEditValue = $leaveStatusOtherEditData->value ?? null;
                                                        @endphp
                                                        @if($planDetailData->is_choice == false)
                                                            <span>{{ __('labels.none') }}</span>
                                                            <input type="hidden" data-step_2="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ __('labels.none') }}">
                                                        @else
                                                            <select data-step_2="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" name="plan_detail_products[{{ $planDetailData->id }}][leave_status_other_edit][{{ $firstPlanDetail->id }}]">
                                                                @foreach($optionDefault as $key => $option)
                                                                    <option value="{{ $key }}"
                                                                            {{ $leaveStatusOtherEditValue == $key ? 'selected' : '' }}
                                                                    >{{ __($option) }}</option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach

                                        {{-- Group 3 top --}}
                                        <td rowspan="{{ count($firstPlanDetails) }}" class="center {{ $tdClass ?? '' }}">
                                            <div class="step_3">
                                                <input type="hidden" data-finish="m_distinction_id" name="update_products[{{ $planDetailProduct->id }}][m_distinction_id_decision]" value="{{ $planDetailProduct->m_distinction_id_decision ?? '' }}">

                                                <span data-step_3="m_distinction_id">
                                                    {{ $allDistinctionData[$planDetailProduct->m_distinction_id_decision] ?? '' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td rowspan="{{ count($firstPlanDetails) }}" class="{{ $tdClass ?? '' }}">
                                            <div class="step_3">
                                                <input type="hidden" data-finish="product_name" name="update_products[{{ $planDetailProduct->id }}][product_name_decision]" value="{{ $planDetailProduct->product_name_decision ?? '' }}">

                                                <span data-step_3="product_name" data-value="{{ $planDetailProduct->product_name_decision ?? '' }}">
                                                    {{ $planDetailProduct->product_name_decision ?? '' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td rowspan="{{ count($firstPlanDetails) }}" class="{{ $tdClass ?? '' }}">
                                            <div class="step_3">
                                                @php
                                                    $codeNameDecision = json_decode($planDetailProduct->code_name_decision ?? '[]');
                                                    $codeString = implode(' ', $codeNameDecision);
                                                @endphp
                                                <div class="code-block">
                                                    <span data-step_3="code_name">{{ $codeString ?? '' }}</span>
                                                    <input type="hidden" data-finish="code_name" name="update_products[{{ $planDetailProduct->id }}][code_name_decision]" value="{{ $codeString ?? '' }}">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center {{ $tdClass ?? '' }}">
                                            <div class="step_3">
                                                <span>{{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}</span>
                                            </div>
                                        </td>
                                        @foreach($planDetails as $planDetail)
                                            @php
                                                $planDetailProd = $planDetail['plan_detail_product'] ?? null;
                                            @endphp
                                            <td class="center {{ $tdClass ?? '' }}">
                                                <div class="step_3">
                                                    @if($planDetail['plan_id'] == $firstPlan->id)
                                                        @if($firstPlanDetail->id == $planDetailProd->plan_detail_id)
                                                            @if($planDetailProd->is_choice == false)
                                                                <span>{{ __('labels.none') }}</span>
                                                            @else
                                                                <input type="hidden" data-finish="leave_status_{{ $planDetailProd->id }}_{{ $firstPlanDetail->id }}" name="plan_detail_products[{{ $planDetailProd->id }}][leave_status_decision]" value="{{ $planDetailProd->leave_status_decision }}">

                                                                <span data-step_3="leave_status_{{ $planDetailProd->id }}_{{ $firstPlanDetail->id }}">
                                                                    {{ LEAVE_STATUS_TYPES[$planDetailProd->leave_status_decision] ?? '' }}
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @php
                                                            $leaveStatusOtherDecision = $planDetailProd->leave_status_other_decision ?? '[]';
                                                            $leaveStatusOtherDecisionData = collect(json_decode($leaveStatusOtherDecision))
                                                                ->where('plan_product_detail_id', $firstPlanDetail->id)->first();
                                                            $leaveStatusOtherDecisionValue = $leaveStatusOtherDecisionData->value ?? null;
                                                        @endphp
                                                        @if($planDetailProd->is_choice == false)
                                                            <span>{{ __('labels.none') }}</span>
                                                        @else
                                                            <input type="hidden" data-finish="leave_status_{{ $planDetailProd->id }}_{{ $firstPlanDetail->id }}" name="plan_detail_products[{{ $planDetailProd->id }}][leave_status_other_decision][{{ $firstPlanDetail->id }}]" value="{{ $leaveStatusOtherDecisionValue }}">

                                                            <span data-step_3="leave_status_{{ $planDetailProd->id }}_{{ $firstPlanDetail->id }}">
                                                                {{ LEAVE_STATUS_TYPES[$leaveStatusOtherDecisionValue] ?? '' }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @else
                                    <tr class="item {{ $isRole }} {{ $hasEditClass }}" data-row="{{ $ranID ?? '' }}" data-is_choice="{{ $isChoice ?? false }}">
                                         {{-- Group 1 bot --}}
                                        <td class="center {{ $tdClass ?? '' }}">
                                            <div class="step_1">
                                                {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
                                            </div>
                                        </td>
                                        @foreach($planDetails as $planDetail)
                                            @php
                                                $planDetailData = $planDetail['plan_detail_product'] ?? null;
                                                $leaveStatus = $planDetailData->leave_status;
                                            @endphp
                                            <td class="center {{ $tdClass ?? '' }}">
                                                <div class="step_1">
                                                    @if($planDetail['plan_id'] == $firstPlan->id)
                                                        @if($firstPlanDetail->id == $planDetailData->plan_detail_id)
                                                            @if($planDetailData->is_choice == false)
                                                                <span>{{ __('labels.none') }}</span>
                                                                <input type="hidden" data-step_1="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ __('labels.none') }}">
                                                            @else
                                                                <span>{{ $optionRequired[$leaveStatus] ?? '' }}</span>
                                                                <input type="hidden" data-step_1="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ $leaveStatus }}">
                                                            @endif
                                                        @endif
                                                    @else
                                                        @php
                                                            $leaveStatusOther = $planDetailData->leave_status_other ?? '[]';
                                                            $leaveStatusOtherData = collect(json_decode($leaveStatusOther))
                                                                ->where('plan_product_detail_id', $firstPlanDetail->id)->first();
                                                            $leaveStatusOtherValue = $leaveStatusOtherData->value ?? null;
                                                        @endphp
                                                        @if($planDetailData->is_choice == false)
                                                            <span>{{ __('labels.none') }}</span>
                                                            <input type="hidden" data-step_1="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ __('labels.none') }}">
                                                        @else
                                                            <span>{{ $optionDefault[$leaveStatusOtherValue] ?? '' }}</span>
                                                            <input type="hidden" data-step_1="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ $leaveStatusOtherValue }}">
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach

                                         {{-- Group 2 bot --}}
                                        <td class="center {{ $tdClass ?? '' }}">
                                            <div class="step_2">
                                                {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
                                            </div>
                                        </td>
                                        <td class="center {{ $tdClass ?? '' }}"></td>
                                        @foreach($planDetails as $planDetail)
                                            @php
                                                $planDetailData = $planDetail['plan_detail_product'] ?? null;
                                                $leaveStatus = $planDetailData->leave_status;
                                            @endphp
                                            <td class="center {{ $tdClass ?? '' }}" data-plan_detail_product_id="{{ $planDetailData->id }}">
                                                <div class="step_2" data-is_plan_confirm="{{ $planDetail['plan_id'] ?? 0 }}">
                                                    <input type="hidden" name="plan_detail_product_ids[{{ $planDetailProduct->id }}][]" value="{{ $planDetailData->id }}">

                                                    @if($planDetail['plan_id'] == $firstPlan->id)
                                                        @if($firstPlanDetail->id == $planDetailData->plan_detail_id)
                                                            @if($planDetailData->is_choice == false)
                                                                <span>{{ __('labels.none') }}</span>
                                                                <input type="hidden" data-step_2="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ __('labels.none') }}">
                                                            @else
                                                                <select data-step_2="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" name="plan_detail_products[{{ $planDetailData->id }}][leave_status_edit]">
                                                                    @foreach($optionRequired as $key => $option)
                                                                        <option value="{{ $key }}"
                                                                            {{ $leaveStatus == $key ? 'selected' : '' }}
                                                                        >{{ __($option) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @php
                                                            $leaveStatusOtherEdit = $planDetailData->leave_status_other_edit ?? '[]';
                                                            $leaveStatusOtherEditData = collect(json_decode($leaveStatusOtherEdit))
                                                                ->where('plan_product_detail_id', $firstPlanDetail->id)->first();
                                                            $leaveStatusOtherEditValue = $leaveStatusOtherEditData->value ?? null;
                                                        @endphp
                                                        @if($planDetailData->is_choice == false)
                                                            <span>{{ __('labels.none') }}</span>
                                                            <input type="hidden" data-step_2="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" value="{{ __('labels.none') }}">
                                                        @else
                                                            <select data-step_2="leave_status_{{ $planDetailData->id }}_{{ $firstPlanDetail->id }}" name="plan_detail_products[{{ $planDetailData->id }}][leave_status_other_edit][{{ $firstPlanDetail->id }}]">
                                                                @foreach($optionDefault as $key => $option)
                                                                    <option value="{{ $key }}"
                                                                        {{ $leaveStatusOtherEditValue == $key ? 'selected' : '' }}
                                                                    >{{ __($option) }}</option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach

                                         {{-- Group 3 bot --}}
                                        <td class="center {{ $tdClass ?? '' }}">
                                            <div class="step_3">
                                                {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
                                            </div>
                                        </td>
                                        @foreach($planDetails as $planDetail)
                                            @php
                                                $planDetailProd = $planDetail['plan_detail_product'] ?? null;
                                            @endphp
                                            <td class="center {{ $tdClass ?? '' }}">
                                                <div class="step_3">
                                                    @if($planDetail['plan_id'] == $firstPlan->id)
                                                        @if($firstPlanDetail->id == $planDetailProd->plan_detail_id)
                                                            @if($planDetailProd->is_choice == false)
                                                                <span>{{ __('labels.none') }}</span>
                                                            @else
                                                                <input type="hidden" data-finish="leave_status_{{ $planDetailProd->id }}_{{ $firstPlanDetail->id }}" name="plan_detail_products[{{ $planDetailProd->id }}][leave_status_decision]" value="{{ $planDetailProd->leave_status_decision }}">

                                                                <span data-step_3="leave_status_{{ $planDetailProd->id }}_{{ $firstPlanDetail->id }}">
                                                                    {{ LEAVE_STATUS_TYPES[$planDetailProd->leave_status_decision] ?? '' }}
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @php
                                                            $leaveStatusDecisionOther = $planDetailProd->leave_status_other_decision ?? '[]';
                                                            $leaveStatusOtherDecisionData = collect(json_decode($leaveStatusDecisionOther))
                                                                ->where('plan_product_detail_id', $firstPlanDetail->id)->first();
                                                            $leaveStatusOtherDecisionValue = $leaveStatusOtherDecisionData->value ?? null;
                                                        @endphp
                                                        @if($planDetailProd->is_choice == false)
                                                            <span>{{ __('labels.none') }}</span>
                                                        @else
                                                            <input type="hidden" data-finish="leave_status_{{ $planDetailProd->id }}_{{ $firstPlanDetail->id }}" name="plan_detail_products[{{ $planDetailProd->id }}][leave_status_other_decision][{{ $firstPlanDetail->id }}]" value="{{ $leaveStatusOtherDecisionValue }}">

                                                            <span data-step_3="leave_status_{{ $planDetailProd->id }}_{{ $firstPlanDetail->id }}">
                                                                {{ LEAVE_STATUS_TYPES[$leaveStatusOtherDecisionValue] ?? '' }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </table>

                <table class="normal_b mb30">
                    <tbody>
                        <tr>
                            <th>{{ __('labels.a203c.table_info.title_1') }}</th>
                            <td>{{ __('labels.a203c.table_info.desc_1') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.a203c.table_info.title_2') }}</th>
                            <td>{{ __('labels.a203c.table_info.desc_2') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.a203c.table_info.title_3') }}</th>
                            <td>{{ __('labels.a203c.table_info.desc_3') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.a203c.table_info.title_4') }}</th>
                            <td>{{ __('labels.a203c.table_info.desc_4') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.a203c.table_info.title_5') }}</th>
                            <td>{{ __('labels.a203c.table_info.desc_5') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.a203c.table_info.title_6') }}</th>
                            <td>{{ __('labels.a203c.table_info.desc_6') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.a203c.table_info.title_7') }}</th>
                            <td>{{ __('labels.a203c.table_info.desc_7') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.a203c.table_info.title_8') }}</th>
                            <td>{{ __('labels.a203c.table_info.desc_8') }}</td>
                        </tr>
                    </tbody>
                </table>

                <p class="eol">
                    {{ __('labels.a203c.comment') }}：<br>
                    <textarea class="middle_c" name="content">{{ $planComment->content ?? '' }}</textarea>
                </p>

                <p class="eol">
                    @foreach($planComments as $item)
                        <span class="white-space-pre">・{{ __('labels.a203c.comment') }}：{{ $item->created_at->format('Y/m/d') }}　{!! $item->content !!}</span>
                        <br>
                    @endforeach
                </p>

                <ul class="footerBtn clearfix">
                    <li>
                        @if(isset($isConfirmA203shu) && $isConfirmA203shu)
                            <p class="error-validate mb10">{{ __('labels.a203c.is_confirm_a203shu') }}</p>
                        @endif
                        <input type="button" value="{{ __('labels.a203c.back') }}" class="btn_a"
                           onclick="window.location = '{{ $backUrl ?? route('admin.home') }}'"
                        >
                    </li>
                </ul>

                <ul class="footerBtn clearfix">
                    <li><input type="submit" name="{{ DRAFT }}" value="{{ __('labels.a203c.btn_draft') }}" class="btn_b"></li>
                    <li><input type="submit" name="{{ SUBMIT }}" value="{{ __('labels.btn_send_to_user') }}" class="btn_c"></li>
                </ul>
            </form>
        </div>
    </div>
@endsection

@section('headSection')
    <style>
        [data-step_3=code_name] {
            width: 100%;
            overflow: auto;
            max-height: 12em;
            display: block;
        }
        .none-event {
            pointer-events: none;
        }
        .none-event select,
        .none-event textarea,
        .none-event input {
            opacity: 0.7;
        }
    </style>
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const DECISION_DRAFT = '{{ \App\Models\TrademarkPlan::DECISION_DRAFT }}';
        const DECISION_EDIT = '{{ \App\Models\TrademarkPlan::DECISION_EDIT }}';
        const OPTION_LEAVE_STATUS = @json(LEAVE_STATUS_TYPES);
        const ALL_DISTINCTION = @json($allDistinctionData);
        const DRAFT = '{{ DRAFT }}';
        const SUBMIT = '{{ SUBMIT }}';
        const LABEL_EDIT = '{{ __('labels.edit') }}';
        const LABEL_DELETE_ALL = '{{ __('labels.delete_all') }}';
        const LABEL_RESTORE = '{{ __('labels.restore') }}';

        const errorMessageMaxLength1000 = '{{ __('messages.general.Common_E026') }}';

        const errorMessageRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageFormat = '{{ __('messages.general.support_U011_E001') }}';
        const errorMessageMaxLength200 = '{{ __('messages.general.Common_E020') }}';
        const errorMessageFormatCode = '{{ __('messages.general.support_A011_E003') }}';

        const errorMessageNotDecision = '{{ __('messages.general.Hoshin_A203_E001') }}';
        const errorMessageNotConfirm = '{{ __('messages.general.Precheck_U021_E007') }}';
        const MESSAGE_DELETE_IS_SUPERVISOR = '{{ __('messages.general.Hoshin_A203_E002') }}';

        const CANCEL = '{{ __('labels.btn_cancel') }}';
        const OK = '{{ __('labels.btn_ok') }}';

        const productHTML = '{!! \CommonHelper::minifyHtml(view('admin.modules.plans.partials.block-product-edit-supervisor', [
            'plans' => $plans,
        ])->render()) !!}';
        const distinctHTML = '{!! \CommonHelper::minifyHtml(view('admin.modules.plans.partials.block-product-edit-supervisor', [
            'plans' => $plans,
            'distinctions' => $distinctions,
        ])->render()) !!}';
    </script>
    <script src="{{ asset('admin_assets/pages/plans/product-edit-supervisor.js') }}"></script>
    @if($isBlockScreen == true)
        <script>disabledScreen();</script>
    @endif
@endsection
