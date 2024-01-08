<style>
    .js-scrollable table {
        width: unset;
        min-width: unset;
    }
    .table_product_choose {
        margin-bottom: 15px;
    }
</style>
<div class="js-scrollable">
    <table class="normal_b mw640 table_product_choose">
        <tr>
            <th>{{ __('labels.register_precheck.m_distinctions') }}</th>
            <th>{{ __('labels.register_precheck.m_product') }}</th>
            <th class="em12">{{ __('labels.register_precheck.check_all_1') }}<br />
                <input type="checkbox" class="checkAllCheckBox"/> {{ __('labels.register_precheck.check_all_2') }}</th>
        </tr>
        @foreach ($mProductChoose as $keyCode => $products)
            @foreach ($products as $keyItem => $item)
            <tr>
                @if ($keyItem == 0)
                    <td rowspan="{{ $products->count() > 1 ? $products->count() : '' }}">
                        {{ __('labels.apply_trademark._table_product_choose.distinctions', [
                            'distinction' => $keyCode
                        ]) }}
                    </td>
                @endif
                <td class="boxes">{{ $item->name }}</td>
                <td class="center">
                    <input type="checkbox" name="m_product_ids[]" value="{{ $item->id }}" class="checkSingleCheckBox single-checkbox" data-name-distinction="第{{ $keyCode }}類"
                        {{ $item->is_apply == true ? 'checked' : '' }}
                    />
                    <input type="hidden" name="mProducts[]" value="{{ $item->id }}">
                </td>
            </tr>
            @endforeach
        @endforeach
        <tr class="add-product">
            <td colspan="3" class="left"><a href="javascript:void(0)" class="add-product-service">{{ __('labels.apply_trademark.add_product_service') }}</a></td>
        </tr>
        <tr class="tr_total">
            <td colspan="3" class="right">{{ __('labels.apply_trademark.total_distinction') }}<span class="total-dis"></span>{{ __('labels.apply_trademark.total_checked') }}<span class="totalChecked"></span></td>
        </tr>
    </table>
</div>
