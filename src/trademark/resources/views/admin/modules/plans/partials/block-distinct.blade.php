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
    <td rowspan="{{ count($planDetails) + 1 }}" class="center bg_yellow">
        <select name="products[{index}][m_distinction_id]" data-select_distinct class="distinct">
            <option value=""></option>
            @foreach($distinctions as $distinction)
                <option value="{{ $distinction->id ?? '' }}">{{ $distinction->name ?? '' }}</option>
            @endforeach
        </select>
        <input type="hidden" name="products[{index}][is_add]" value="1">
        <div>
            <input type="button" data-delete_row value="{{ __('labels.delete_all') }}" class="btn_a small">
        </div>
    </td>
    <td rowspan="{{ count($planDetails) + 1 }}" class="bg_yellow">
        <textarea rows="3" name="products[{index}][product_name]" data-input_product_name>{productName}</textarea>
    </td>
    <td rowspan="{{ count($planDetails) + 1 }}" class="bg_yellow">
        <textarea class="wide w-100" name="products[{index}][product_code]" data-input_product_code></textarea>
    </td>
</tr>
@foreach($planDetails as $planDetail)
    @php $planDetailLoop = $loop; @endphp
    <tr data-row="{rowID}">
        <td class="bg_yellow">
            {{ __('labels.a203c.plan_table.plan') }}-1 {{ __('labels.a203c.plan_table.plan_detail') }}{{ $planDetailLoop->iteration }}{{ __('labels.a203c.plan_table.relation') }}
        </td>
        <td class="center bg_yellow"></td>

        @foreach($plans as $plan)
            @php $planLoop = $loop; @endphp

            @foreach($plan->planDetails as $detail)
                @php $detailLoop = $loop; @endphp

                <td class="center bg_yellow">
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
