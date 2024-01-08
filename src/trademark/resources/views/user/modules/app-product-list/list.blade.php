@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h3>{{ __('labels.product_list.title') }}</h3>
        <table class="normal_b eol column1">
            <tr>
                <th>{{ __('labels.product_list.table.distinct') }}</th>
                <th>{{ __('labels.product_list.table.product') }}</th>
            </tr>
            @foreach ($productList as $mDistinct)
                @foreach ($mDistinct as $keyItem => $product)
                    <tr data-distinction-id="{{ $product->m_distinction_id }}">
                        @if ($keyItem == 0)
                            <td rowspan="{{ $mDistinct->count() > 1 ? $mDistinct->count() : '' }}">
                                {{ isset($product->mDistinction) ? '第' . $product->mDistinction->name . '類' : '第' . $product->name . '類' }}
                            </td>
                        @endif
                        <td class="boxes">{{ isset($product->mDistinction) ? $product->name : $product->name_product }}</td>
                    </tr>
                @endforeach
            @endforeach
        </table>
    </div>
    <!-- /contents -->
@endsection
