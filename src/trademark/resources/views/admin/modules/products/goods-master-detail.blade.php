@extends('admin.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="normal clearfix">
            <form action="" method="GET" id="form-sort-table">

                <h2>{{ __('labels.goods-master-result.title_page_screen_detail') }}</h2>
                @include('admin.components.includes.messages')
                <div>
                    <input type="hidden" name="s" class="session_key" value="{{ $keySession ?? '' }}" />
                    <!--sort data-->
                    <input type="hidden" name="m_product_id" id="m_product_id" />
                    <input type="hidden" name="products_number" id="products_number" />
                    <input type="hidden" name="m_distinction_name" id="m_distinction_name" />
                </div>
            </form>
            <form action="" method="POST" id="form">
                @csrf
                <table class="normal_a column1">
                    <tbody><tr>
                        <th>{{ __('labels.goods-master-result.no') }} <a href="" class="sort-btn sort_prod_id" data-target="m_product_id" data-sort="{{ SORT_BY_ASC }}">▼</a><a href="" class="sort-btn sort_prod_id" data-target="m_product_id" data-sort="{{ SORT_BY_DESC }}">▲</a></th>
                        <th>{{ __('labels.goods-master-result.branch_number') }}</th>
                        <th>{{ __('labels.goods-master-result.products_number') }} <a href="#" class="sort-btn sort-products_number" data-target="products_number" data-sort="{{ SORT_BY_ASC }}">▼</a><a href="#" class="sort-btn sort-products_number" data-target="products_number" data-sort="{{ SORT_BY_DESC }}">▲</a></th>
                        <th>{{ __('labels.distinction_name') }} <a href="#" class="sort-btn" data-target="m_distinction_name" data-sort="{{ SORT_BY_ASC }}">▼</a><a href="#" class="sort-btn" data-target="m_distinction_name" data-sort="{{ SORT_BY_DESC }}">▲</a></th>
                        <th>{{ __('labels.goods-master-result.block_no') }}</th>
                        <th>{{ __('labels.goods-master-result.parent_product_number') }}</th>
                        <th>{{ __('labels.goods-master-result.prod_name') }}</th>
                        <th>{{ __('labels.goods-master-result.list_code') }}</th>
                    </tr>
                    @foreach($dataProduct as $k => $product)
                        <tr class="row-item" data-row="{{ $k }}">
                            <td>{{ $product->id }} <input type="hidden" name="data[{{ $k }}][m_product_id]" value="{{ $product->id }}" id="m_product_id_old"/></td>
                            <td>{{ !$product->mCode->isEmpty() ? $product->mCode->first()->branch_number : '' }}</td>
                            <td>{{ $product->products_number }}</td>
                            <td>
                                <select class="m_distinction_name" name="data[{{ $k }}][m_distinction_id]">
                                    @foreach($distinctions as $key => $value)
                                        <option value="{{ $key }}" {{ $product->m_distinction_id == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{{ $product->block }}</td>
                            <td style="width: 307px">
                                <input type="checkbox" name="data[{{ $k }}][is_parent]" value="{{ $isParentStatus }}" {{ $product->is_parent == $isParentStatus && !$product->parent_id ? 'checked' : '' }} {{ $product->parent_id ? 'disabled' : '' }} class="is_parent">
                                <input type="text" name="data[{{ $k }}][parent_product_number]" nospace {{ $product->is_parent == $isParentStatus ? 'disabled' : '' }} value="{{ $product->parent ? $product->parent->products_number : '' }}" class="em10 parent_id parent_product_number"><br>
                            </td>
                            <td><input type="text" class="em20 m_product_name" nospace name="data[{{ $k }}][m_product_name]" value="{{ $product->name }}"></td>
                            <td>
                                <div class="wp_codes">
                                    @foreach($product->mCode as $i => $code)
                                        <div class="mt-1 item-code item-code-{{ $i }}" data-key="{{ $i }}">
                                            <input type="text" value="{{ $code->name }}" name="data[{{ $k }}][m_codes][{{$i}}][name]" class="em06 m_code_name"><a class="delete delete-code" href="javascript:void(0)">×</a><br>
                                            <input type="hidden" name="data[{{ $k }}][m_codes][{{$i}}][id]" value="{{ $code->id }}" class="em06 m_code_id">
                                            <input type="hidden" name="data[{{ $k }}][m_codes][{{$i}}][status_delete]" value="{{ NOT_DELETE }}" class="em06 status_delete">
                                        </div>
                                    @endforeach
                                </div>
                                <a href="javascript:void(0)" class="add_code">{{ __('labels.goods-master-result.add_code_btn') }}</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <br>
                <ul class="r_c eol clearfix">
                    <li><input type="button" onclick="window.location='{{ route('admin.goods-master-result', ['s' => $keySession]) }}'" value="{{ __('labels.back') }}" class="btn_a"></li>
                    <li><input type="submit" value="{{ __('labels.save') }}" class="btn_b"></li>
                </ul>

            </form>

        </div><!-- /contents inner -->

    </div>    <!-- /contents -->
@endsection
@section('css')
    <style>
        .item-code-delete {
            display: none;
        }
    </style>
@endsection
@section('script')
    <script type="text/javascript">
        const statusIsDelete = @json(IS_DELETE);
        const Common_E001 = '{{ __('messages.general.Common_E001') }}';
        const support_A011_E003 = '{{ __('messages.general.support_A011_E003') }}';
        const support_U011_E001 = '{{ __('messages.general.support_U011_E001') }}';
        const a000goods_master_detail_E0001 = '{{ __('messages.general.a000goods_master_detail_E0001') }}';
        const a000goods_master_detail_E0002 = '{{ __('messages.general.a000goods_master_detail_E0002') }}';
        const routeCheckNumberProductAjax = '{{ route('admin.check-product-number.ajax') }}';
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/pages/products/goods-master-detail.js') }}"></script>
@endsection
