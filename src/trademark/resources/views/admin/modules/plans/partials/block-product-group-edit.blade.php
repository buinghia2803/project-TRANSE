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
    <td rowspan="{{ count($planDetails) + 1 }}" class="center {bgClass}">
        {codeName}
        <input type="hidden" name="products[{index}][m_code_name]" value="{codeName}">
        <input type="hidden" name="products[{index}][m_code_id]" value="{codeId}">
        <input type="hidden" name="products[{index}][role_add]" value="{roleAdd}">
        <input type="hidden" name="products[{index}][is_add_product]" value="1">
    </td>
    <td rowspan="{{ count($planDetails) + 1 }}" class="center {bgClass}">
        {distinctionName}
        <br>
        <input type="button" data-delete_row value="{{ __('labels.delete_all') }}" class="btn_a small">
        <input type="hidden" name="products[{index}][plan_detail_distinct_id]" value="{planDetailDistinctionID}">
        <input type="hidden" name="products[{index}][m_distinction_name]" value="{distinctionName}">
        <input type="hidden" name="products[{index}][m_distinction_id]" value="{distinctionID}">
        <input type="hidden" name="products[{index}][is_add]" value="0">
    </td>
    <td rowspan="{{ count($planDetails) + 1 }}" class="{bgClass}">
        <textarea rows="3" name="products[{index}][product_name]" data-input_product_name>{productName}</textarea>
    </td>
</tr>
@foreach($planDetails as $planDetail)
    @php $planDetailLoop = $loop; @endphp
    <tr data-row="{rowID}">
        <td class="{bgClass}">
            {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $planDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
        </td>
        <td class="center {bgClass}"></td>

        @foreach($plans as $plan)
            @php $planLoop = $loop; @endphp

            @foreach($plan->planDetails as $detail)
                @php $detailLoop = $loop; @endphp

                <td class="center {bgClass}">
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
