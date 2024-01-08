<div class="js-scrollable eol">
    <table class="normal_b westimate">
        <tr>
            <th>{{ __('labels.register_precheck.m_distinctions') }}</th>
            <th>{{ __('labels.register_precheck.m_product') }}</th>
            <th class="em12">{{ __('labels.register_precheck.check_all_1') }}<br /><labels>
                <div class="error-product"></div>
                <input type="checkbox" class="checkAllCheckBox"/> {{ __('labels.register_precheck.check_all_2') }}</labels></th>
        </tr>
        @foreach ($mProductChoose as $keyCode => $products)
            @foreach ($products as $keyItem => $item)
            <tr>
                <input type="hidden" name="m_product_ids[]" value="{{ $item->id }}" />
                @if ($keyItem == 0)
                    <td rowspan="{{ $products->count() > 1 ? $products->count() : '' }}">{{__('labels.common.trademark_table.distinction.name_distinct', ['name' => $keyCode])}}</td>
                @endif
                <td class="boxes">{{ $item->name }}</td>
                <td class="center">
                    <input type="checkbox" name="m_product_choose[]" value="{{ $item->id }}" checked class="checkSingleCheckBox"/>
                </td>
            </tr>
            @endforeach
        @endforeach
        <tr>
            <td colspan="3" class="right">{{ __('labels.register_precheck.note_table') }}<span class="totalChecked"></span></td>
        </tr>
    </table>
</div><!-- /scroll wrap -->
