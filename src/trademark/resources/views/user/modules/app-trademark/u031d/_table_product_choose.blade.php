<div class="js-scrollable mb15 highlight">
    <table class="normal_b westimate table_product_choose" id="tbl-suitable-products">
        <tr>
            <th>{{ __('labels.user_common_payment.division') }}</th>
            <th>{{ __('labels.user_common_payment.product_service_name') }}</th>
            @if($showColumnChoose)
                <th class="em12">{{ __('labels.regis-by-trademark.redirectToScreenU031C') }}<br /><label><input type="checkbox" id="check-all" class="checkAllCheckBox"/> 全て選択</label></th>
            @endif
        </tr>
        @foreach ($products as $keyCode => $product)
            @foreach ($product as $keyItem => $item)
                <tr data-distinction-id="{{ $item->m_distinction_id }}" data-id="{{$item->id}}">
                    @if ($keyItem == 0)
                        <td rowspan="{{ $product->count() > 1 ? $product->count() : '' }}">
                            {{ __('labels.apply_trademark._table_product_choose.distinctions', [
                                'distinction' => $item->mDistinction->name
                            ]) }}
                        </td>
                    @endif
                    <td class="boxes">{{ $item->name }}</td>
                    @if($showColumnChoose)
                        <td class="center">
                            <input type="checkbox" class="checkSingleCheckBox single-checkbox is_choice_user_{{ $item->id }}" name="is_choice_user_{{ $item->id }}"
                                   {{ !empty($item->SftSuitableProduct) && $item->SftSuitableProduct->is_choice_user ? 'checked' : '' }}
                                   data-foo="is_choice_user[]" value="{{ $item->id }}" />
                            <input type="hidden" name="productIds[]" value="{{ $item->id }}" />
                        </td>
                    @endif
                </tr>
            @endforeach
        @endforeach
        @if($showColumnChoose)
            <input type="hidden" class="mDistrintions" name="mDistrintions[]" value="{{ $products }}"/>
        @endif
        <tr class="add-product">
            <td colspan="3" class="right">{{ __('labels.apply_trademark.total_distinction') }}<span class="total-dis"></span>{{ __('labels.apply_trademark.total_checked') }}<span class="totalChecked"></span></td>
        </tr>
    </table>
</div>
