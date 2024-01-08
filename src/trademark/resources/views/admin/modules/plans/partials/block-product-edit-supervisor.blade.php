@php
    $planDetailProductModel = new \App\Models\PlanDetailProduct();
    $optionRequired = $planDetailProductModel->optionRequired();
    $optionDefault = $planDetailProductModel->optionDefault();

    $firstPlan = $plans->first();
    $firstPlanDetails = $firstPlan->planDetails;

    $requiredIndex = 0;
    $optionIndex = 0;
    $totalFirstPlanDetails = count($firstPlanDetails);
@endphp

@foreach($firstPlanDetails as $firstPlanDetail)
    @php $firstPlanDetailLoop = $loop; @endphp

    @if($firstPlanDetailLoop->first)
        <tr class="item is-supervisor item-add" data-row="{rowID}">
            {{-- block 1 --}}
            <td rowspan="{{ $totalFirstPlanDetails }}" class="center bg_purple2">&nbsp;</td>
            <td rowspan="{{ $totalFirstPlanDetails }}" class="center bg_purple2">&nbsp;</td>
            <td rowspan="{{ $totalFirstPlanDetails }}" class="center bg_purple2">&nbsp;</td>
            <td class="center bg_purple2">&nbsp;</td>
            @foreach($plans as $plan)
                @foreach($plan->planDetails as $detail)
                    <td class="center bg_purple2">&nbsp;</td>
                @endforeach
            @endforeach

            {{-- block 2 --}}
            <td rowspan="{{ $totalFirstPlanDetails }}" class="center bg_purple2">
                <div class="step_2">
                    @if(!empty($distinctions))
                        <select data-step_2="m_distinction_id" name="products[{index}][m_distinction_id_edit]" data-select_distinct>
                            <option value=""></option>
                            @foreach($distinctions as $distinction)
                                <option value="{{ $distinction->id }}">{{ $distinction->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="products[{index}][is_add]" value="1">
                    @else
                        <input type="hidden" data-step_2="distinction_name" value="{distinctionName}">
                        <span>{distinctionName}</span>
                        <input type="hidden" name="products[{index}][plan_detail_distinct_id]" value="{planDetailDistinctionID}">
                        <input type="hidden" name="products[{index}][m_distinction_id_edit]" value="{distinctionID}">
                        <input type="hidden" name="products[{index}][is_add]" value="0">
                    @endif
                   <div>
                       <input type="button" data-delete_row value="{{ __('labels.delete_all') }}" class="btn_a small">
                   </div>
                </div>
            </td>
            <td rowspan="{{ $totalFirstPlanDetails }}" class="bg_purple2">
                <div class="step_2">
                    <textarea data-step_2="product_name" rows="3" name="products[{index}][product_name_edit]" data-input_product_name>{productName}</textarea>
                </div>
            </td>
            <td rowspan="{{ $totalFirstPlanDetails }}" class=" center bg_purple2">
                <div class="step_2">
                    <textarea data-step_2="code_name" class="wide w-100" name="products[{index}][code_name_edit]" data-input_product_code></textarea>
                </div>
            </td>
            <td class="center bg_purple2">
                {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
            </td>
            <td class="center bg_purple2">&nbsp;</td>
            @foreach($plans as $plan)
                @php $planLoop = $loop; @endphp

                @foreach($plan->planDetails as $detail)
                    @php $detailLoop = $loop; @endphp

                    <td class="center bg_purple2">
                        <div class="step_2" data-is_plan_confirm="{{ $plan->id }}">
                            @if($planLoop->first)
                                @if($firstPlanDetailLoop->index == $detailLoop->index)
                                    <select data-step_2="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}" name="products[{index}][plan_details][{{ $detail->id }}][leave_status_edit]">
                                        @foreach($optionRequired as $key => $option)
                                            <option value="{{ $key }}">{{ __($option) }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            @else
                                <select data-step_2="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}" name="products[{index}][plan_details][{{ $detail->id }}][leave_status_other_edit][{{ $firstPlanDetail->id }}]">
                                    @foreach($optionDefault as $key => $option)
                                        <option value="{{ $key }}">{{ __($option) }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </td>
                @endforeach
            @endforeach

            {{-- group 3 --}}
            <td rowspan="{{ $totalFirstPlanDetails }}" class="center bg_purple2">
                <div class="step_3 hidden">
                    <input type="hidden" data-finish="m_distinction_id" name="products[{index}][m_distinction_id_decision]" value="">
                    <span data-step_3="m_distinction_id"></span>
                </div>
            </td>
            <td rowspan="{{ $totalFirstPlanDetails }}" class="bg_purple2">
                <div class="step_3 hidden">
                    <input type="hidden" data-finish="product_name" name="products[{index}][product_name_decision]" value="">
                    <span data-step_3="product_name"></span>
                </div>
            </td>
            <td rowspan="{{ $totalFirstPlanDetails }}" class="center bg_purple2">
                <div class="step_3 hidden">
                    <div class="code-block">
                        <input type="hidden" data-finish="code_name" name="products[{index}][code_name_decision]" value="">
                        <span data-step_3="code_name"></span>
                    </div>
                </div>
            </td>
            <td class="center bg_purple2">
                <div class="step_3 hidden">
                    {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
                </div>
            </td>
            @foreach($plans as $plan)
                @php $planLoop = $loop; @endphp

                @foreach($plan->planDetails as $detail)
                    @php $detailLoop = $loop; @endphp

                    <td class="center bg_purple2">
                        <div class="step_3 hidden">
                            @if($planLoop->first)
                                @if($firstPlanDetailLoop->index == $detailLoop->index)
                                    <input type="hidden" data-finish="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}" name="products[{index}][plan_details][{{ $detail->id }}][leave_status_decision]" value="">
                                    <span data-step_3="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}"></span>
                                @endif
                            @else
                                <input type="hidden" data-finish="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}" name="products[{index}][plan_details][{{ $detail->id }}][leave_status_other_decision][{{ $firstPlanDetail->id }}]" value="">
                                <span data-step_3="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}"></span>
                            @endif
                        </div>
                    </td>
                @endforeach
            @endforeach
        </tr>
    @else
        <tr class="item item-add is-supervisor" data-row="{rowID}">
            {{-- block 1 --}}
            <td class="center bg_purple2">&nbsp;</td>
            @foreach($plans as $plan)
                @foreach($plan->planDetails as $detail)
                    <td class="center bg_purple2">&nbsp;</td>
                @endforeach
            @endforeach

            {{-- block 2 --}}
            <td class="center bg_purple2">
                {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
            </td>
            <td class="center bg_purple2">&nbsp;</td>
            @foreach($plans as $plan)
                @php $planLoop = $loop; @endphp

                @foreach($plan->planDetails as $detail)
                    @php $detailLoop = $loop; @endphp

                    <td class="center bg_purple2">
                        <div class="step_2" data-is_plan_confirm="{{ $plan->id }}">
                            @if($planLoop->first)
                                @if($firstPlanDetailLoop->index == $detailLoop->index)
                                    <select data-step_2="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}" name="products[{index}][plan_details][{{ $detail->id }}][leave_status_edit]">
                                        @foreach($optionRequired as $key => $option)
                                            <option value="{{ $key }}">{{ __($option) }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            @else
                                <select data-step_2="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}" name="products[{index}][plan_details][{{ $detail->id }}][leave_status_other_edit][{{ $firstPlanDetail->id }}]">
                                    @foreach($optionDefault as $key => $option)
                                        <option value="{{ $key }}">{{ __($option) }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </td>
                @endforeach
            @endforeach

            {{-- block 3 --}}
            <td class="center bg_purple2">
                <div class="step_3 hidden">
                    {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $firstPlanDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
                </div>
            </td>
            @foreach($plans as $plan)
                @php $planLoop = $loop; @endphp

                @foreach($plan->planDetails as $detail)
                    @php $detailLoop = $loop; @endphp

                    <td class="center bg_purple2">
                        <div class="step_3 hidden">
                            @if($planLoop->first)
                                @if($firstPlanDetailLoop->index == $detailLoop->index)
                                    <input type="hidden" data-finish="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}" name="products[{index}][plan_details][{{ $detail->id }}][leave_status_decision]" value="">
                                    <span data-step_3="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}"></span>
                                @endif
                            @else
                                <input type="hidden" data-finish="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}" name="products[{index}][plan_details][{{ $detail->id }}][leave_status_other_decision][{{ $firstPlanDetail->id }}]" value="">
                                <span data-step_3="leave_status_{{ $detail->id }}_{{ $firstPlanDetail->id }}"></span>
                            @endif
                        </div>
                    </td>
                @endforeach
            @endforeach
        </tr>
    @endif
@endforeach
