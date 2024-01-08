@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <div class="wide clearfix">
            @include('compoments.messages')

            <form id="form" action="{{ route('admin.refusal.response-plan.product.re-create.supervisor.post', [
                'id' => $comparisonTrademarkResult->id,
                'trademark_plan_id' => $trademarkPlan->id,
            ]) }}" method="post">
                @csrf
                <input type="hidden" name="delete_plan_detail_product_ids" value="">
                <input type="hidden" name="restore_plan_detail_product_ids" value="">

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])

                {{-- Common 203 here --}}
                @include('admin.modules.plans.common.common_a203', [
                    'dataCommon' => $dataCommon,
                    'title' => __('labels.a203c.title_c_n'),
                ])

                <p>{{ __('labels.a203c.step') }}</p>

                {{-- Plan Table --}}
                <div class="w-100 mb-4 overflow-auto">
                    <table class="normal_b" style="min-width: 1300px;">
                        <caption>{{ __('labels.a203c.plan_table.title') }}</caption>

                        <tbody>
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
                            <th colspan="4" class="right">{{ __('labels.a203c.type_plan_name') }}</th>
                            @foreach($plans as $plan)
                                @if($loop->first)
                                    <th></th>
                                @endif
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
                                @if($loop->first)
                                    <th></th>
                                @endif
                                @foreach($plan->planDetails as $planDetail)
                                    <th class="center">{{ $planDetail->distinct_is_add_text ?? '' }}</th>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <th>{{ __('labels.a203c.distinction') }}</th>
                            <th style="width:18%;">{{ __('labels.a203c.product_name') }}</th>
                            <th>{{ __('labels.a203c.code') }}</th>
                            <th>{{ __('labels.a203c.rank') }}</th>
                            @foreach($plans as $plan)
                                @if($loop->first)
                                    <th class="center"></th>
                                @endif
                                @foreach($plan->planDetails as $planDetail)
                                    <th class="center"></th>
                                @endforeach
                            @endforeach
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

                                $isChoice = $planDetailProduct->is_choice;
                                $classRow = '';
                                if ($isChoice == false) {
                                    $classRow = 'bg_gray';
                                }
                            @endphp
                            @if($planDetailProduct->isRoleAddUser())
                                <tr>
                                    <td class="center {{ $classRow ?? '' }}">{{ $distinction->name ?? '' }}</td>
                                    <td class="{{ $classRow ?? '' }}">{{ $productData->name ?? '' }}</td>
                                    <td class="center {{ $classRow ?? '' }}" style="width: 220px;">
                                        @if(count($codes) == 0)
                                            <textarea data-input_product_code name="update_products[{{ $planDetailProduct->id }}][product_code]" class="wide w-100"></textarea>
                                        @else
                                            <div class="code-block">
                                                @foreach($codes as $code)
                                                    <span data-type="{{ $code->type }}"
                                                          class="{{ $loop->index > 2 ? 'hidden' : '' }}"
                                                    >
                                                        {{ $code->name ?? '' }}

                                                        @if(count($codes) > 3 && $loop->index == 2)
                                                            <span class="show_all_code cursor-pointer">+</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                            <div class="code-edit mt-1">
                                                <input type="hidden" name="update_products[{{ $planDetailProduct->id }}][product_code_fix]" value="{{ $codeNotEditName }}">
                                                @if(count($codeEdit))
                                                    @if($isChoice == true)
                                                        <input type="button" data-edit_code value="{{ __('labels.edit') }}" class="btn_a small">
                                                    @endif
                                                    <textarea data-input_product_code name="update_products[{{ $planDetailProduct->id }}][product_code]" class="wide w-100 hidden">{{ $codeEditName ?? '' }}</textarea>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
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
                                                    ],
                                                ]) }}"
                                                value="{{ __('labels.a203c.plan_table.add_product') }} ＋"
                                                class="btn_a small">
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
                                    $isRole = 'is-manager';
                                    if ($planDetailProduct->isRoleAddSupervisor()) {
                                        $tdClass = 'bg_purple2';
                                        $isRole = 'is-supervisor';
                                    }

                                    if ($planDetailProduct->is_deleted == true) {
                                        $tdClass = 'bg_gray';
                                    }
                                @endphp
                                <tr class="item {{ $isRole }}" data-row="{{ $ranID ?? '' }}">
                                    <td rowspan="{{ count($firstPlanDetails) + 1 }}" class="center {{ $tdClass ?? '' }}">
                                        @if($planDetailDistinction->is_add == true)
                                            <select name="update_products[{{ $planDetailProduct->id }}][m_distinction_id]" data-select_distinct>
                                                <option value=""></option>
                                                @foreach($distinctions as $item)
                                                    <option value="{{ $item->id ?? '' }}"
                                                        {{ $planDetailDistinction->m_distinction_id == $item->id ? 'selected' : '' }}
                                                    >{{ $item->name ?? '' }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            {{ $distinction->name }}
                                        @endif
                                        <div>
                                            @if($planDetailProduct->is_deleted == true)
                                                <input type="button" data-restore_row value="{{ __('labels.restore') }}" class="btn_a small">
                                            @else
                                                <input type="button" data-delete_row value="{{ __('labels.delete_all') }}" class="btn_a small">
                                            @endif
                                        </div>
                                    </td>
                                    <td rowspan="{{ count($firstPlanDetails) + 1 }}" class="{{ $tdClass ?? '' }}">
                                        <textarea rows="3" name="update_products[{{ $planDetailProduct->id }}][product_name]" data-input_product_name>{{ $productData->name }}</textarea>
                                    </td>
                                    <td rowspan="{{ count($firstPlanDetails) + 1 }}" class="{{ $tdClass ?? '' }}">
                                        <textarea class="wide w-100" name="update_products[{{ $planDetailProduct->id }}][product_code]" data-input_product_code>{{ $codeString }}</textarea>
                                    </td>
                                </tr>

                                @foreach($firstPlanDetails as $firstPlanDetail)
                                    <tr class="item {{ $isRole }}" data-row="{{ $ranID ?? '' }}">
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
                        </tbody>
                    </table>
                </div>

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

                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{{ __('labels.back') }}" class="btn_a"
                               onclick="window.location = '{{ $backUrl ?? route('admin.home') }}'"
                        >
                    </li>
                </ul>

                <ul class="footerBtn clearfix">
                    <li><input type="submit" name="{{ DRAFT }}" value="{{ __('labels.a203c.btn_draft') }}"
                               class="btn_b"></li>
                    <li><input type="submit" name="{{ SUBMIT }}" value="{{ __('labels.btn_send_to_user') }}"
                               class="btn_c"></li>
                </ul>
            </form>
        </div>
    </div>
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const errorMessageMaxLength1000 = '{{ __('messages.general.Common_E026') }}';

        const errorMessageRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageFormat = '{{ __('messages.general.support_U011_E001') }}';
        const errorMessageMaxLength200 = '{{ __('messages.general.Common_E051') }}';
        const errorMessageFormatCode = '{{ __('messages.general.support_A011_E003') }}';
        const errorMessageMax50Code = '{{ __('messages.general.Common_E052') }}';

        const LABEL_DELETE_ALL = '{{ __('labels.delete_all') }}';
        const LABEL_RESTORE = '{{ __('labels.restore') }}';

        const productHTML = '{!! \CommonHelper::minifyHtml(view('admin.modules.plans.partials.block-product-re-create-supervisor', [
            'plans' => $plans,
        ])->render()) !!}';
        const distinctHTML = '{!! \CommonHelper::minifyHtml(view('admin.modules.plans.partials.block-product-re-create-supervisor', [
            'plans' => $plans,
            'distinctions' => $distinctions,
        ])->render()) !!}';
    </script>
    <script src="{{ asset('admin_assets/pages/plans/product-re-create-supervisor.js') }}"></script>
    @if($isBlockScreen == true)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_SUPERVISOR] ])
@endsection
