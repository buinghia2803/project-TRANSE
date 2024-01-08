@extends('admin.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="normal clearfix">

            <form action="" method="GET" id="form">
                <h2>{{ __('labels.goods-master-result.title_page') }}</h2>
                <div>
                    <input type="hidden" name="s" class="session_key" value="{{ $key ?? '' }}" />
                    <!--sort data-->
                    <input type="hidden" name="m_product_id" id="m_product_id" />
                    <input type="hidden" name="products_number" id="products_number" />
                    <input type="hidden" name="m_distinction_name" id="m_distinction_name" />
                </div>

                <table class="normal_a column1">
                    <tr>
                        <th>{{ __('labels.goods-master-result.no') }} <a href="" class="sort-btn sort_prod_id" data-target="m_product_id" data-sort="{{ SORT_BY_ASC }}">▼</a><a href="" class="sort-btn sort_prod_id" data-target="m_product_id" data-sort="{{ SORT_BY_DESC }}">▲</a></th>
                        <th>{{ __('labels.goods-master-result.branch_number') }}</th>
                        <th>{{ __('labels.goods-master-result.products_number') }} <a href="#" class="sort-btn sort-products_number" data-target="products_number" data-sort="{{ SORT_BY_ASC }}">▼</a><a href="#" class="sort-btn sort-products_number" data-target="products_number" data-sort="{{ SORT_BY_DESC }}">▲</a></th>
                        <th>{{ __('labels.distinction_name') }} <a href="#" class="sort-btn" data-target="m_distinction_name" data-sort="{{ SORT_BY_ASC }}">▼</a><a href="#" class="sort-btn" data-target="m_distinction_name" data-sort="{{ SORT_BY_DESC }}">▲</a></th>
                        <th>{{ __('labels.goods-master-result.block_no') }}</th>
                        <th>{{ __('labels.goods-master-result.prod_name') }}</th>
                        <th>{{ __('labels.goods-master-result.list_code') }}</th>
                    </tr>
                    @foreach($dataProduct as $k => $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ !$product->mCode->isEmpty() ? $product->mCode->first()->branch_number : '' }}</td>
                            <td>{{ $product->products_number }}</td>
                            <td>{{ $product->mDistinction->name }}</td>
                            <td>{{ $product->block }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{!! $product->mCode->implode('name', '</br>') !!}</td>
                        </tr>
                    @endforeach
                </table>
                @if($dataProduct->isEmpty())
                    <div class="text-center">{{ __('messages.general.Common_E032') }}</div>
                @endif
                <br />
                <ul class="r_c eol clearfix">
                    <li><input type="button" onclick="window.location='{{ $routeBack }}'" value="{{ __('labels.back') }}" class="btn_a" /></li>
                    <li><input type="button" onclick="window.location='{{ $routeGoodsMasterDetail }}'" value="{{ __('labels.edit') }}" class="btn_b" /></li>
                </ul>
            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->
@endsection
@section('css')
@endsection
@section('script')
    <script type="text/JavaScript" src="{{ asset('admin_assets/pages/products/goods-master-result.js') }}"></script>
@endsection
