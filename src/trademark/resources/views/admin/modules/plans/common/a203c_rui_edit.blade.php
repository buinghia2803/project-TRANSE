@extends(!$isModal ? 'admin.layouts.app': 'admin.layouts.modal')
@php
    $user = \Auth::user();
    $mCodeId = request()->__get('m_code_id') ?? '';
@endphp
@section('main-content')
<div id="contents">
    <div class="wide clearfix">
        <form id="productGroupEdit" method="POST"
              action="{{ route('admin.refusal.response-plan.product-group-edit-redirect', ['id' => request()->__get('id')]) }}">
            @csrf
            <input type="hidden" name="delete_plan_detail_product_ids" value="{{ $dataSession['delete_plan_detail_product_ids'] ?? '' }}">

            <h3>{{ __('labels.a203c_rui_edit.title') }}</h3>

            <p>{{ __('labels.a203c.step') }}</p>

            <div class="refusal_response_plan__product_group">
                <table class="normal_b column1 planProductGroupEditTbl">
                    <thead>
                        <tr>
                            <th colspan="4" rowspan="2"></th>
                            @foreach($plans as $plan)
                                <th colspan="{{ $loop->first ? count($plan->planDetails) + 1 : count($plan->planDetails) }}">
                                    {{ __('labels.a203c.plan_table.plan') }}-{{ $loop->iteration ?? '' }} {{ $plan->reason_name ?? '' }}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($plans as $plan)
                                @if($loop->first)
                                    <th></th>
                                @endif
                                @foreach($plan->planDetails as $planDetail)
                                    <th>{{ __('labels.a203c.plan_table.plan_detail') }}({{ $loop->iteration }})</th>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <th colspan="4" class="right">{{ __('labels.a203c.revolution') }}</th>
                            @foreach($plans as $plan)
                                @if($loop->first)
                                    <th></th>
                                @endif
                                @foreach($plan->planDetails as $planDetail)
                                    <th>{{ $planDetail->text_revolution ?? '' }}</th>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <th colspan="4" class="right">{{ __('labels.a203c_rui.necessity_submission') }}</th>
                            @foreach($plans as $plan)
                                @if($loop->first)
                                    <th></th>
                                @endif
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
                                @if($loop->first)
                                    <th></th>
                                @endif
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
                                @if($loop->first)
                                    <th></th>
                                @endif
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

                                @if($planDetailProduct->isRoleAddUser())
                                    <tr class="item is-user">
                                        <td class="center {{ $classRow ?? '' }}">{{ $mCode }}</td>
                                        <td class="center {{ $classRow ?? '' }}">{{ $distinction->name ?? '' }}</td>
                                        <td class="{{ $classRow ?? '' }}">{{ $productData->name ?? '' }}</td>
                                        <td class="center {{ $classRow ?? '' }}">
                                            @if($isChoice == false)
                                                {{ __('labels.a203c.no_application') }}
                                            @else
                                                {{ $reasonRefNumProd->rank ?? '' }}
                                            @endif
                                        </td>
                                        <td class="center {{ $classRow ?? '' }}" style="width: 90px;">
                                            @if($isChoice == true)
                                                <input
                                                    type="button"
                                                    data-add_distinct
                                                    data-info="{{ json_encode([
                                                    'distinction' => [
                                                        'id' => $distinction->id,
                                                        'plan_detail_distinction' => $planDetailDistinction->id,
                                                        'product_name'=> $productData->name,
                                                        'm_code_id'=> $code->id,
                                                        'code_name'=> $code->name,
                                                    ],
                                                ]) }}"
                                                    value="{{ __('labels.a203c.plan_table.add_distinct') }} ＋"
                                                    class="btn_a small mb05"
                                                >
                                                <br>
                                                <input
                                                    type="button"
                                                    data-add_product
                                                    data-info="{{ json_encode([
                                                    'distinction' => [
                                                        'id' => $distinction->id,
                                                        'plan_detail_distinction' => $planDetailDistinction->id,
                                                        'name'=> $planDetailDistinction->mDistinction->name,
                                                        'product_name'=> $productData->name,
                                                        'm_code_id'=> $code->id,
                                                        'code_name'=> $code->name,
                                                    ],
                                                ]) }}"
                                                    value="{{ __('labels.a203c.plan_table.add_product') }} ＋"
                                                    class="btn_a small"
                                                >
                                            @endif
                                        </td>
                                        @foreach($planDetails as $planDetail)
                                            @php
                                                $distinctionProd = $planDetail['distinction_prod'] ?? null;
                                                $planDetailProd = $planDetail['plan_detail_product'] ?? null;
                                            @endphp
                                            <td class="center {{ $classRow ?? '' }}">
                                                <input type="hidden" name="update_products[{{ $planDetailProduct->id }}][plan_detail_product_ids][]" value="{{ $planDetailProd->id }}">

                                                @if($isChoice == false)
                                                    {{ __('labels.none') }}
                                                @else
                                                    @if($distinctionProd->is_leave_all == true)
                                                        {{ __('labels.a203c.plan_table.leave_all') }}
                                                        <input type="hidden" name="plan_detail_products[{{ $planDetailProd->id }}][leave_status]" value="{{ $planDetailProd->leave_status }}">
                                                    @else
                                                        <select name="plan_detail_products[{{ $planDetailProd->id }}][leave_status]">
                                                            @foreach($planDetailProduct->optionProduct() as $key => $option)
                                                                <option value="{{ $key }}"
                                                                    {{ $planDetailProd->leave_status == $key ? 'selected' : '' }}
                                                                >{{ __($option) }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @else
                                    @php
                                        $ranID = \Str::random(30);
                                        $codeString = implode(' ', $codes->pluck('name')->toArray());

                                        $firstPlan = $plans->first();
                                        $firstPlanDetails = $firstPlan->planDetails;

                                        $tdClass = 'bg_yellow';
                                        if ($planDetailProduct->isRoleAddSupervisor()) {
                                            $tdClass = 'bg_purple2';
                                        }
                                    @endphp
                                    <tr class="item" data-row="{{ $ranID ?? '' }}">
                                        <td rowspan="{{ count($firstPlanDetails) + 1 }}" class="center {{ $tdClass ?? '' }}">{{ $mCode }}</td>
                                        <td rowspan="{{ count($firstPlanDetails) + 1 }}" class="center {{ $tdClass ?? '' }}">
                                            {{ $distinction->name ?? '' }}
                                            <div>
                                                <input type="button" data-delete_row value="{{ __('labels.delete_all') }}" class="btn_a small">
                                            </div>
                                        </td>
                                        <td rowspan="{{ count($firstPlanDetails) + 1 }}" class="{{ $tdClass ?? '' }}">
                                            <textarea rows="3" name="update_products[{{ $planDetailProduct->id }}][product_name]" data-input_product_name>{{ $productData->name }}</textarea>
                                        </td>
                                    </tr>

                                    @foreach($firstPlanDetails as $firstPlanDetail)
                                        <tr class="item" data-row="{{ $ranID ?? '' }}">
                                            @php $firstPlanDetailLoop = $loop; @endphp
                                            <td class="{{ $tdClass ?? '' }}">
                                                {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
                                            </td>
                                            <td class="center {{ $tdClass ?? '' }}"></td>
                                            @foreach($planDetails as $planDetail)
                                                @php
                                                    $planDetailData = $planDetail['plan_detail_product'] ?? null;
                                                    $leaveStatus = $planDetailData->leave_status;
                                                @endphp
                                                <td class="center {{ $tdClass ?? '' }}" data-plan_detail_product_id="{{ $planDetailData->id }}">
                                                    <input type="hidden" name="update_products[{{ $planDetailProduct->id }}][plan_detail_product_ids][]" value="{{ $planDetailData->id }}">

                                                    @if($planDetail['plan_id'] == $firstPlan->id)
                                                        @if($firstPlanDetail->id == $planDetailData->plan_detail_id)
                                                            @if($planDetailData->is_choice == false)
                                                                {{ __('labels.none') }}
                                                            @else
                                                                <select name="plan_detail_products[{{ $planDetailData->id }}][leave_status]">
                                                                    @foreach($planDetailProduct->optionRequired() as $key => $option)
                                                                        <option value="{{ $key }}"
                                                                            {{ $leaveStatus == $key ? 'selected' : '' }}
                                                                        >{{ __($option) }}</option>
                                                                    @endforeach
                                                                </select>
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
                                                            <select name="plan_detail_products[{{ $planDetailData->id }}][leave_status_other][{{ $firstPlanDetail->id }}]">
                                                                @foreach($planDetailProduct->optionDefault() as $key => $option)
                                                                    <option value="{{ $key }}"
                                                                        {{ $leaveStatusOtherValue == $key ? 'selected' : '' }}
                                                                    >{{ __($option) }}</option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <input type="hidden" name="m_code_id" value="{{ request()->__get('m_code_id') }}">

            <ul class="footerBtn clearfix">
                <li>
                    <a href="{{ route('admin.refusal.response-plan.product-group', ['id' => request()->__get('id')]) }}"
                       class="btn_a btn_a_goto_a203c_rui">
                        {{ __('labels.back')}}
                    </a>
                </li>
                <li>
                    <button type="button" id="gotoA203c_rui_edit02" class="btn_b">
                        {{ __('labels.a203c_rui.btn_submit')}}
                    </button>
                </li>
            </ul>
        </form>
    </div>
</div>
@endsection

@section('footerSection')
    <style>
        .btn_a_goto_a203c_rui, #gotoA203c_rui_edit02 {
            font-size: 1.3em;
            height: 38px;
            width: 112px;
            text-align: center;
            line-height: 35px;
            padding: 0 !important;
        }
    </style>
    <script src="{{ asset('common/js/functions.js') }}"></script>
    <script>
        const errorMessageRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageFormat = '{{ __('messages.general.support_U011_E001') }}';
        const errorMessageMaxLength200 = '{{ __('messages.general.Common_E020') }}'.replace('100', '200');
        const user = @json($user);
        const dataSession = @json($dataSession);

        const ROLE_MANAGER = {{ ROLE_MANAGER }};
        const ROLE_SUPERVISOR = {{ ROLE_SUPERVISOR }};

        const productHTML = '{!! \CommonHelper::minifyHtml(view('admin.modules.plans.partials.block-product-group-edit', [
            'plans' => $plans
        ])->render()) !!}';

        const distinctHTML = '{!! \CommonHelper::minifyHtml(view('admin.modules.plans.partials.block-distinct-group-edit', [
            'plans' => $plans,
            'distinctions' => $distinctionsExclude,
        ])->render()) !!}';

        $('#closeModal').click(function () {
            window.parent.closeModal('#a203c_rui-modal')
        })
    </script>
    <script src="{{ asset('admin_assets/pages/plans/a203c_rui_edit.js') }}"></script>
@endsection
