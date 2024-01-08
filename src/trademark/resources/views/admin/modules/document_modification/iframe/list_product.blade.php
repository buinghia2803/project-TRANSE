@extends('admin.layouts.app')
@section('main-content')
    <div id="contents" class="normal mt-2">
        <table class="normal_b column1">
            <tr>
                <th>{{ __('labels.product_list.table.distinct') }}</th>
                <th>{{ __('labels.product_list.table.product') }}</th>
            </tr>
            @foreach ($mDistincts as $mDistinct)
                @php
                    $totalProdNotApply = $mDistinct->where('registerTrademarkProd.is_apply', 0);
                    $notApplyAll = $totalProdNotApply->count() == count($mDistinct);
                @endphp
                @foreach ($mDistinct as $keyItem => $product)
                    <tr data-distinction-id="{{ $product->m_distinction_id }}">
                        @if ($keyItem == 0)
                            <td rowspan="{{ $mDistinct->count() > 1 ? $mDistinct->count() : '' }}"
                                style="{{ $notApplyAll ? 'background-color:#D3D3D3;' : '' }}">
                                {{ isset($product->mDistinction) ? '第' . $product->mDistinction->name . '類' : '第' . $product->name . '類' }}
                            </td>
                        @endif
                        <td class="boxes"
                            style="{{ $product->registerTrademarkProd->is_apply == 0 ? 'background-color:#D3D3D3;' : '' }}">
                            {{ isset($product->mDistinction) ? $product->name : $product->name_product }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </table>
        <p class="left eol">{{ __('labels.document_modification.text_2') }}</p>
    </div>
@endsection
<style>
    #header {
        display: none;
    }
</style>
