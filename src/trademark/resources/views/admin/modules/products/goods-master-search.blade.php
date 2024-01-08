@extends('admin.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents">

        <h2>{{ __('labels.goods-master-search.title_page') }}</h2>

        <!-- contents inner -->
        <div class="wide clearfix">

            <form action="" method="POST" id="form">
                @csrf
                <p class="eol"><a href="{{ $routeResultSearchNoCode }}">{{ __('labels.goods-master-search.label_redirect_result_search') }}</a><br /></p>

                <p class="eol">
                <ul class="r_c clearfix mb-2">
                    <li><label><input type="radio" name="type" checked="checked" {{ $dataSession && $dataSession['type'] == $typeOriginal ? 'checked' : '' }} value="{{ $typeOriginal }}" />{{ __('labels.goods-master-search.type_original') }}</label></li>
                    <li><label><input type="radio" name="type" value="{{ $typeRegis }}" {{ $dataSession && $dataSession['type'] == $typeRegis ? 'checked' : '' }} />{{ __('labels.goods-master-search.type_regis') }}</label></li>
                </ul>

                <table class="normal_a">
                    @for($i = 1;$i <=3;$i++)
                        <tr class="row-data row-data-{{ $i }}" data-key="{{ $i }}">
                            <td>
                                <select name="dataSearch[{{ $i }}][field_search]" class="field_search">
                                    @foreach($optionField as $key => $field)
                                        <option value="{{ $key }}" {{ $dataSession && $dataSession['dataSearch'][$i]['field_search'] == $key ? 'selected' : '' }}>{{ $field }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 350px;"><input type="text" name="dataSearch[{{ $i }}][value_search]" class="value_search" style="width: 100%" value="{{ $dataSession && $dataSession['dataSearch'][$i]['value_search'] ? $dataSession['dataSearch'][$i]['value_search'] : '' }}"/></td>
                            <td>
                                <select name="dataSearch[{{ $i }}][condition_search]" class="condition_search">
                                    @if($dataSession && in_array($dataSession['dataSearch'][$i]['field_search'], [SEARCH_PRODUCT_NAME, SEARCH_CODE_NAME]))
                                        @foreach($conditionFilter as $value => $item)
                                            <option value="{{ $value }}" {{ $dataSession && $dataSession['dataSearch'][$i]['condition_search'] == $value ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    @else
                                        @foreach($conditionCompare as $value => $item)
                                            <option value="{{ $value }}" {{ $dataSession && $dataSession['dataSearch'][$i]['condition_search'] == $value ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                        </tr>
                    @endfor
                </table>

                <br />
                <ul class="r_c eol clearfix">
                    <li><input type="button" onclick="window.location='{{ route('admin.goods-master-search') }}'" value="{{ __('labels.clear') }}" class="btn_a" /></li>
                    <li><input type="submit" value="{{ __('labels.search') }}" class="btn_b" /></li>
                </ul>

            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->
@endsection
@section('css')
    <style>
        .condition_search {
            width: 100px;
        }
    </style>
@endsection
@section('script')
    <script type="text/JavaScript">
        const equalLabel = @json(EQUAL);
        const dataConditionFilter = @json($conditionFilter);
        const dataConditionCompare = @json($conditionCompare);
        const searchDistinctionName = @json(SEARCH_DISTINCTION_NAME);
        const searchConcept = @json(SEARCH_CONCEPT);
        const searchCodeName = @json(SEARCH_CODE_NAME);
        const Common_E021 = '{{ __('messages.general.Common_E021') }}';
        const support_A011_E003 = '{{ __('messages.general.support_A011_E003') }}';
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/pages/products/goods-master-search.js') }}"></script>
@endsection
