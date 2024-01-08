<div class="js-scrollable mb15 highlight">
    <table class="normal_b westimate table_product_choose" id="tbl-suitable-products">
        <tr>
            <th>{{ __('labels.u031b.m_distinctions') }}</th>
            <th>{{ __('labels.u031b.m_product') }}</th>
            @if($showColumnChoose)
                <th class="em12">{{ __('labels.u031b.app') }}<br /><label><input type="checkbox" id="check-all" class="check-all"/>{{ __('labels.u031b.check_all') }}</label></th>
            @endif
        </tr>
        @foreach ($products as $keyCode => $product)
            @foreach ($product as $keyItem => $item)
                <tr data-distinction-id="{{ $item->m_distinction_id }}" data-id="{{$item->id}}">
                    @if ($keyItem == 0)
                        <td rowspan="{{ $product->count() > 1 ? $product->count() : '' }}" class="td-distinction">
                            {{ \App\Models\MDistinction::formatNameMDistinction($item->mDistinction->name) }}
                        </td>
                    @endif
                    <td class="boxes">{{ $item->name }}</td>
                    @if($showColumnChoose)
                        <td class="center">
                            <input type="checkbox" class="single-checkbox productIdsChoose-{{ $item->id }}" name="m_product_ids_choose[]"
                                   data-foo="is_apply[]" value="{{ $item->id }}" {{ $productIsChoice && in_array($item->id, $productIsChoice) ? 'checked' : '' }}/>
                            <input type="hidden" name="m_product_ids[]" value="{{ $item->id }}" />
                        </td>
                    @endif
                </tr>
            @endforeach
        @endforeach
        <tr class="add-product">
            <td colspan="3" class="right">
                {{ __('labels.u031b.num_dis') }}<span id="total_distinction">0</span>　
                {{ __('labels.u031b.num_prod') }}<span id="product-checked">0</span>
                <input type="hidden" name="total_distinction" class="input_total_distinction" />
            </td>
        </tr>
    </table>
    <div id="error-table-choose-prod" class="notice">{{ __('labels.u031b.error-table-choose-prod') }}</div>
</div>

