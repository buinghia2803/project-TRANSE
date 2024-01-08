@php
    $planDetailProductModel = new \App\Models\PlanDetailProduct();
    $optionRequired = $planDetailProductModel->optionRequired();
    $optionDefault = $planDetailProductModel->optionDefault();

    $firstPlan = $plans->first();
    $planDetails = $firstPlan->planDetails;

    $requiredIndex = 0;
    $optionIndex = 0;
    $totalPlanDetails = count($planDetails);
@endphp

<tr data-row="{rowID}">
    <td rowspan="{{ count($planDetails) + 1 }}" class="center bg_purple2">
        @if(!empty($distinctions))
            <select name="products[{index}][m_distinction_id]" data-select_distinct>
                <option value=""></option>
                @foreach($distinctions as $distinction)
                    <option value="{{ $distinction->id ?? '' }}">{{ $distinction->name ?? '' }}</option>
                @endforeach
            </select>
            <input type="hidden" name="products[{index}][is_add]" value="1">
        @else
            {distinctionName}
            <input type="hidden" name="products[{index}][plan_detail_distinct_id]" value="{planDetailDistinctionID}">
            <input type="hidden" name="products[{index}][m_distinction_id]" value="{distinctionID}">
            <input type="hidden" name="products[{index}][is_add]" value="0">
        @endif
        <br>
        <input type="button" data-delete_row value="{{ __('labels.delete_all') }}" class="btn_a small">
    </td>
    <td rowspan="{{ count($planDetails) + 1 }}" class="bg_purple2">
        <textarea rows="3" name="products[{index}][product_name]" data-input_product_name>{productName}</textarea>
    </td>
    <td rowspan="{{ count($planDetails) + 1 }}" class="bg_purple2">
        <textarea class="wide w-100" name="products[{index}][product_code]" data-input_product_code></textarea>
    </td>
</tr>
@foreach($planDetails as $planDetail)
    @php $planDetailLoop = $loop; @endphp
    <tr data-row="{rowID}">
        <td class="bg_purple2">
            {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $planDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
        </td>
        <td class="center bg_purple2"></td>

        @foreach($plans as $plan)
            @php $planLoop = $loop; @endphp

            @foreach($plan->planDetails as $detail)
                @php $detailLoop = $loop; @endphp

                <td class="center bg_purple2">
                    @if($planLoop->first)
                        @if($planDetailLoop->index == $detailLoop->index)
                            <select name="products[{index}][plan_details][{{ $detail->id }}][leave_status]">
                                @foreach($optionRequired as $key => $option)
                                    <option value="{{ $key }}">{{ __($option) }}</option>
                                @endforeach
                            </select>
                        @endif
                    @else
                        <select name="products[{index}][plan_details][{{ $detail->id }}][leave_status_other][{{ $planDetail->id }}]">
                            @foreach($optionDefault as $key => $option)
                                <option value="{{ $key }}">{{ __($option) }}</option>
                            @endforeach
                        </select>
                    @endif
                </td>
            @endforeach
        @endforeach
    </tr>
@endforeach
