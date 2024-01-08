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
        @if($mProductChoose)
            @foreach ($appTrademarkProds as $keyItem => $item)
                @php $keyUnique = uniqid(); @endphp
                <tr class="before_html_product" data-id="{{ $item->id }}">
                    <td>
                        <select name="prod[{{ $keyUnique }}][m_distinction_id]" class="data-m_distinction_id distinction-mr w-100">
                            <option value="">{{ __('labels.option_default') }}</option>
                            @foreach($distinctions as $id => $name)
                                <option value="{{ $id }}" @if($item->distinction_id == $id) selected @endif>{{ __('labels.name_distinct', ['attr' => $name]) }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="boxes">
                        <input type="text" name="prod[{{ $keyUnique }}][name_product]" class="data-name_product name_prod_{{ $keyItem }} customer_boxes" key-prod="{{ $keyItem }}" data-suggest="" value="{{$item->mProduct->name}}">
                    </td>
                    <td class="center">
                        <input type="checkbox" name="prod[{{ $keyUnique }}][check]" class="checkSingleCheckBox single-checkbox"
                            {{ $item->is_apply == true ? 'checked' : '' }}
                        />
                        <input type="hidden" name="prod[{{ $keyUnique }}][app_trademark_prod_id]" value="{{ $item->app_trademark_prod_id }}">
                    </td>
                </tr>
            @endforeach
        @else
            @for($i = 1;$i <=5;$i++)
                @php $keyUnique = uniqid(); @endphp
                <tr class="before_html_product">
                    <td class="eDis">
                        <select name="prod[{{ $keyUnique }}][m_distinction_id]" class="data-m_distinction_id distinction-mr w-100">
                            <option value="">{{ __('labels.option_default') }}</option>
                            @foreach($distinctions as $id => $name)
                                <option value="{{ $id }}">{{ __('labels.name_distinct', ['attr' => $name]) }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="boxes">
                        <input type="text" name="prod[{{ $keyUnique }}][name_product]" class="data-name_product name_prod_{{ $i }} customer_boxes" key-prod="{{ $i }}" data-suggest="">
                    </td>
                    <td class="center">
                        <input type="checkbox" name="prod[{{ $keyUnique }}][check]" value="1" class="checkSingleCheckBox single-checkbox">
                    </td>
                </tr>
            @endfor
        @endif
        <tr class="add-product">
            <td colspan="3" class="left"><a href="javascript:void(0)" class="add-product-service">{{ __('labels.apply_trademark.add_product_service') }}</a></td>
        </tr>
        <tr>
            <td colspan="3" class="right">{{ __('labels.apply_trademark.total_distinction') }}<span class="total-dis"></span>{{ __('labels.apply_trademark.total_checked') }}<span class="totalChecked"></span></td>
        </tr>
    </table>
</div>
