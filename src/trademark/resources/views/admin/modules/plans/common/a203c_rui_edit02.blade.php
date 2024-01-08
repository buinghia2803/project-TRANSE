@extends(!$isModal ? 'admin.layouts.app': 'admin.layouts.modal')
@php
    $user = \Auth::user();
@endphp
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="productGroupEditConfirm" method="POST">
                @csrf
                <input type="hidden" name="secret" value="{{ $secret }}">

                <h3>{{ __('labels.a203c_rui_edit02.title') }}</h3>

                @include('admin.components.includes.messages')

                <p>{{ __('labels.a203c.step') }}</p>

                <div class="refusal_response_plan__product_group">
                    <table class="normal_b column1">
                        <thead>
                        <tr>
                            <th colspan="4" rowspan="2"></th>
                            @foreach($plans as $plan)
                                <th colspan="{{ count($plan->planDetails) }}">
                                    {{ __('labels.a203c.plan_table.plan') }}-{{ $loop->iteration ?? '' }} {{ $plan->reason_name ?? '' }}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
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
                        </tr>
                        <tr>
                            <th colspan="4" class="right">{{ __('labels.a203c_rui.necessity_submission') }}</th>
                            @foreach($plans as $plan)
                                @foreach($plan->planDetails as $planDetail)
                                    <th class="center">
                                        <p class="mb-0 line line-1">{{ \Str::limit($planDetail->getTypePlanName() ?? '', $limit = 9, $end = '...') }}</p>
                                    </th>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <th colspan="4" class="right">{{ __('labels.a203c.additional') }}</th>
                            @foreach($plans as $plan)
                                @foreach($plan->planDetails as $planDetail)
                                    <th class="center">{{ $planDetail->distinct_is_add_text ?? '' }}</th>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <th>{{ __('labels.a203c.code') }}</th>
                            <th>{{ __('labels.a203c.distinction') }}</th>
                            <th style="width:18%;">{{ __('labels.a203c.product_name') }}</th>
                            <th>{{ __('labels.a203c.rank') }}</th>
                            @foreach($plans as $plan)
                                @foreach($plan->planDetails as $planDetail)
                                    <th class="center"></th>
                                @endforeach
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($productTable as $mCode => $products)
                            @php
                                $firstPlan = $plans->first();
                                $firstPlanDetails = $firstPlan->planDetails;

                                $rowSpan = 0;
                                foreach($products as $key => $product) {
                                    if(!empty($product['is_add_product']) || !empty($product['is_add_distinct'])) {
                                        $rowSpan += count($firstPlanDetails) + 1;
                                    } else {
                                        if ($product['plan_detail_product']->isRoleAddUser()) {
                                            $rowSpan += 1;
                                        } else {
                                            $rowSpan += count($firstPlanDetails) + 1;
                                        }
                                    }
                                }
                            @endphp
                            @foreach($products as $key => $product)
                                @if(!empty($product['is_add_product']) || !empty($product['is_add_distinct']))
                                    @php
                                        $distinction = $product['distinction'] ?? null;
                                        $classRow = $product['classRow'] ?? null;
                                    @endphp
                                    <tr>
                                        @if (!$key)
                                            <td rowspan="{{ $rowSpan }}" class="center">{{ $mCode }}</td>
                                        @endif
                                        <td rowspan="{{ count($firstPlanDetails) + 1 }}" class="{{ $classRow ?? '' }}">{{ $distinction->name ?? '' }}</td>
                                        <td rowspan="{{ count($firstPlanDetails) + 1 }}" class="{{ $classRow ?? '' }}">{{ $product['product_name'] ?? '' }}</td>
                                    </tr>

                                    @foreach($firstPlanDetails as $firstPlanDetail)
                                        @php $firstPlanDetailLoop = $loop; @endphp
                                        <tr>
                                            <td class="center {{ $classRow ?? '' }}">
                                                {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
                                            </td>

                                            @foreach($product['plan_details'] as $planDetail)
                                                @php $planDetailLoop = $loop; @endphp
                                                <td class="center {{ $classRow ?? '' }}">
                                                    @if(!empty($planDetail['leave_status']))
                                                        @if($planDetailLoop->index == $firstPlanDetailLoop->index)
                                                            {{ LEAVE_STATUS_TYPES[$planDetail['leave_status'] ?? ''] ?? '' }}
                                                        @endif
                                                    @else
                                                        {{ LEAVE_STATUS_TYPES[$planDetail['leave_status_other'][$firstPlanDetail->id] ?? ''] ?? '' }}
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @else
                                    @php
                                        $planDetailProduct = $product['plan_detail_product'] ?? null;
                                        $productData = $product['product'] ?? null;
                                        $planDetailDistinction = $product['plan_detail_distinction'] ?? null;
                                        $distinction = $product['distinction'] ?? null;
                                        $codes = collect($product['codes'] ?? []);
                                        $code = $codes->where('name', $mCode)->first();
                                        $reasonRefNumProd = $product['reasonRefNumProd'] ?? null;
                                        $planDetails = collect($product['plan_details'] ?? []);

                                        $isChoice = $planDetailProduct->is_choice;

                                        $classRow = '';
                                        switch ($planDetailProduct->role_add) {
                                            case ROLE_MANAGER:
                                                $classRow = 'bg_yellow';
                                                break;
                                            case ROLE_SUPERVISOR:
                                                $classRow = 'bg_purple2';
                                                break;
                                        }

                                        $firstPlan = $plans->first();
                                        $firstPlanDetails = $firstPlan->planDetails;
                                    @endphp
                                    @if($planDetailProduct->isRoleAddUser())
                                        <tr>
                                            @if (!$key)
                                                <td rowspan="{{ $rowSpan }}" class="center">{{ $mCode }}</td>
                                            @endif
                                            <td class="{{ $classRow ?? '' }}">{{ $distinction->name ?? '' }}</td>
                                            <td class="{{ $classRow ?? '' }}">{{ $productData->name ?? '' }}</td>
                                            <td class="center {{ $classRow ?? '' }}">
                                                @if($isChoice == false)
                                                    {{ __('labels.a203c.no_application') }}
                                                @else
                                                    {{ $reasonRefNumProd->rank ?? '-' }}
                                                @endif
                                            </td>

                                            @foreach($planDetails as $planDetail)
                                                @php
                                                    $distinctionProd = $planDetail['distinction_prod'] ?? null;
                                                    $planDetailProd = $planDetail['plan_detail_product'] ?? null;
                                                @endphp
                                                <td class="center {{ $classRow ?? '' }}">
                                                    @if($isChoice == false)
                                                        {{ __('labels.none') }}
                                                    @else
                                                        @if($distinctionProd->is_leave_all == true)
                                                            {{ __('labels.a203c.plan_table.leave_all') }}
                                                        @else
                                                            {{ $planDetailProd->getLeaveStatusText($planDetails) }}
                                                        @endif
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @else
                                        <tr>
                                            @if (!$key)
                                                <td rowspan="{{ $rowSpan }}" class="center">{{ $mCode }}</td>
                                            @endif
                                            <td rowspan="{{ count($firstPlanDetails) + 1 }}" class="{{ $classRow ?? '' }}">{{ $distinction->name ?? '' }}</td>
                                            <td rowspan="{{ count($firstPlanDetails) + 1 }}" class="{{ $classRow ?? '' }}">{{ $productData->name ?? '' }}</td>
                                        </tr>
                                        @foreach($firstPlanDetails as $firstPlanDetail)
                                            <tr data-row="{{ $ranID ?? '' }}">
                                                @php $firstPlanDetailLoop = $loop; @endphp
                                                <td class="center {{ $classRow ?? '' }}">
                                                    {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
                                                </td>
                                                @foreach($planDetails as $planDetail)
                                                    @php
                                                        $planDetailData = $planDetail['plan_detail_product'] ?? null;
                                                        $leaveStatus = $planDetailData->leave_status;
                                                    @endphp
                                                    <td class="center {{ $classRow ?? '' }}" data-plan_detail_product_id="{{ $planDetailData->id }}">
                                                        <input type="hidden" name="update_products[{{ $planDetailProduct->id }}][plan_detail_product_ids][]" value="{{ $planDetailData->id }}">

                                                        @if($planDetail['plan_id'] == $firstPlan->id)
                                                            @if($firstPlanDetail->id == $planDetailData->plan_detail_id)
                                                                @if($planDetailData->is_choice == false)
                                                                    {{ __('labels.none') }}
                                                                @else
                                                                    {{ LEAVE_STATUS_TYPES[$leaveStatus] ?? '' }}
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
                                                                {{ __('labels.none') }}
                                                            @else
                                                                {{ LEAVE_STATUS_TYPES[$leaveStatusOtherValue] ?? '' }}
                                                            @endif
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <table class="normal_b mb30">
                    <tbody>
                    <tr>
                        <th>{{ __('labels.a203c_rui.leave') }}</th>
                        <td>{{ __('labels.a203c_rui.leaving_procedures') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.a203c_rui.delete') }}</th>
                        <td>{{ __('labels.a203c_rui.delete_proceed') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.a203c_rui.addition') }}</th>
                        <td>{{ __('labels.a203c_rui.name_added') }}</td>
                    </tr>
                    <tr>
                        <th>※</th>
                        <td>{{ __('labels.a203c_rui.to_comment') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.a203c_rui.no_application_none') }}</th>
                        <td>{{ __('labels.a203c_rui.customer_not_apply') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.a203c_rui.leave_all') }}</th>
                        <td>{{ __('labels.a203c_rui.keep_all') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.a203c_rui.not_good') }}</th>
                        <td>{{ __('labels.a203c_rui.customer_measure_1') }}</td>
                    </tr>
                    <tr>
                        <th>－</th>
                        <td>{{ __('labels.a203c_rui.is_no_problem_add') }}</td>
                    </tr>
                    </tbody>
                </table>

                <ul class="footerBtn clearfix">
                    <li>
                        <a class="btn_a btn_back"
                           href="{{ route('admin.refusal.response-plan.product-group-edit', [
                                'id' => request()->__get('id'),
                                'm_code_id' => request()->__get('m_code_id'),
                                's' => request()->__get('s'),
                            ]) }}"
                        >{{ __('labels.back') }}</a>
                    </li>
                    <li><input type="submit" value="確定・保存" class="btn_b"/></li>
                </ul>
            </form>
        </div>
    </div>
    <style>
        .btn_back {
            height: 38px;
            width: 112px;
            padding: 0 !important;
            text-align: center;
            line-height: 35px;
        }
    </style>
@endsection
