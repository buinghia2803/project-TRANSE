<style>
    .js-scrollable {
        position: unset !important;
        overflow: unset !important;
        overflow-y: unset !important;
    }
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
            <th style="min-width: 82px">{{ __('labels.register_precheck.m_distinctions') }}</th>
            <th style="min-width: 464px">{{ __('labels.register_precheck.m_product') }}</th>
            <th class="em12">{{ __('labels.register_precheck.check_all_1') }}<br />
                <input type="checkbox" class="checkAllCheckBox"/> {{ __('labels.register_precheck.check_all_2') }}</th>
        </tr>
        @foreach ($mProductChoose as $keyCode => $products)
            @foreach ($products as $keyItem => $item)
                @php $keyUnique = uniqid(); @endphp
                <tr>
                    <td class="eDis">
                        <select name="prod[{{ $keyUnique }}][m_distinction_id]" class="data-m_distinction_id distinction-mr w-100">
                            @foreach($distinctions as $distinctionID => $distinctionName)
                                <option value="{{ $distinctionID }}" {{ ($keyCode == $distinctionName) ? 'selected' : '' }}>
                                    {{ __('labels.apply_trademark._table_product_choose.distinctions', [
                                        'distinction' => $distinctionName
                                    ]) }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="boxes boxes_{{ $keyUnique }}">
                        <input type="text" name="prod[{{ $keyUnique }}][name_product]" class="data-name_product name_prod_{{ $keyUnique }} customer_boxes" key-prod="{{ $keyUnique }}" data-suggest="" value="{{ $item->name }}">
                    </td>
                    <td class="center">
                        <input type="checkbox" name="prod[{{ $keyUnique }}][check]" value="{{ $item->id }}" class="checkSingleCheckBox single-checkbox" data-name-distinction="第{{ $keyCode }}類"
                            {{ $item->is_apply == true ? 'checked' : '' }}
                        />
                        <input type="hidden" name="prod[{{ $keyUnique }}][app_trademark_prod_id]" value="{{ $item->id }}">
                    </td>
                </tr>
            @endforeach
        @endforeach
        <tr class="add-product">
            <td colspan="3" class="left"><a href="javascript:void(0)" class="add-product-service">{{ __('labels.apply_trademark.add_product_service') }}</a></td>
        </tr>
        <tr>
            <td colspan="3" class="right">{{ __('labels.apply_trademark.total_distinction') }}<span class="total-dis"></span>{{ __('labels.apply_trademark.total_checked') }}<span class="totalChecked"></span></td>
        </tr>
    </table>
</div>
