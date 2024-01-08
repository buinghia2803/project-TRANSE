@extends($isModal ? 'admin.layouts.modal': 'admin.layouts.app')
@php
    $user = \Auth::user();
@endphp
@section('main-content')
    <!-- wrapper -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form>
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
                                @foreach($products as $key => $product)
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
                                    <tr>
                                        @if (!$key)
                                            <td rowspan="{{ count($products) }}" class="center">
                                                {{ $mCode }}<br>
                                                @if (!$isModal)
                                                    <a style="padding: 2px 1em;font-size: 0.7em;"
                                                       class="btn_a small"
                                                       href="{{ route('admin.refusal.response-plan.product-group-edit', ['id' => request()->__get('id'), 'm_code_id' => $code->id]) }}"
                                                    >{{ __('labels.edit') }}</a>
                                                @endif
                                            </td>
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

                <ul class="clearfix {{ $isModal ? 'center fs12' : 'footerBtn' }}">
                    <li>
                        @if ($isModal)
                            <input type="button" id="closeModal" data-dismiss="modal"
                                   value="{{ __('labels.a203c_rui.close_up') }}" class="btn_a">
                        @else
                            @if ($user->role == ROLE_SUPERVISOR)
                                <a href="{{ route('admin.refusal.response-plan.product.edit.supervisor', ['id' => $comparisonTrademarkResult->id, 'trademark_plan_id' => request()->__get('id')]) }}"
                                   class="btn_a btn__back">
                                    {{ __('labels.a203c_rui.return_product_list') }}
                                </a>
                            @else
                                <a href="{{ route('admin.refusal.response-plan.product.create', ['id' => $comparisonTrademarkResult->id, 'trademark_plan_id' => request()->__get('id')]) }}"
                                   class="btn_a btn__back">
                                    {{ __('labels.a203c_rui.return_product_list') }}
                                </a>
                            @endif
                        @endif
                    </li>
                </ul>
            </form>
        </div>
    </div>
    <style>
        .btn__back {
            width: 247px;
            height: 38px;
            padding: 0 !important;
            text-align: center;
            line-height: 34px;
        }
    </style>

    <script src="{{ asset('common/js/functions.js') }}"></script>
    <script>
        const ErrorApplicationU031E004 = '{{__('messages.general.Application_U031_E004')}}';
        const constNo = '{{__('labels.support_first_times.No')}}';
        const constKind = '{{__('labels.support_first_times.kind')}}';

        $('#closeModal').click(function () {
            window.parent.closeModal('#open-modal-iframe')
            window.parent.$('body').removeClass('fixed-body')
        })
    </script>
@endsection
