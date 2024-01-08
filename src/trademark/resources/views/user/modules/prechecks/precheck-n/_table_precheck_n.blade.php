<div class="collapse-div">
    <p class="hideShowClick">
        {{ __('labels.precheck_n.toggle_div') }}
        <span class="icon-text">+</span>
    </p>
    <p class="toggle-info">
        {!! __('labels.precheck_n.note_type') !!}
    </p>

</div>

<div class="js-scrollable mb20 highlight">
    <table class="normal_b">
        <tr>
            <th rowspan="3" class="em04">{{ __('labels.precheck_n.m_distinctions') }}</th>
            <th rowspan="3" class="bg_green">{{ __('labels.precheck_n.m_product') }}</th>
            <th colspan="2">{{ __('labels.precheck_n.type_simple') }}</th>
            <th colspan="6">{{ __('labels.precheck_n.type_select') }}</th>
            <th rowspan="3" class="bg_green">{{ __('labels.precheck_n.app') }}<br />
                <label>
                    <input type="checkbox" class="all-checkbox" />
                    {{ __('labels.precheck_n.check_all') }}
                </label>
                <div class="error-product"></div>
            </th>
        </tr>
        <tr>
            <th>{{ __('labels.precheck.passed') }}</th>
            <th>{{ __('labels.precheck.this_time') }}</th>
            <th colspan="3">{{ __('labels.precheck.passed') }}</th>
            <th colspan="3">{{ __('labels.precheck.this_time') }}</th>
        </tr>
        <tr>
            <th class="em04 bg_whitesmoke">{{ __('labels.precheck_n.identical') }}</th>
            <th class="em04 bg_whitesmoke">{{ __('labels.precheck_n.identical') }}</th>

            <th class="em07">{{ __('labels.precheck_n.discernment') }}</th>
            <th class="em07">{{ __('labels.precheck_n.similar') }}</th>
            <th class="em05 bg_whitesmoke">{{ __('labels.precheck_n.sub') }}

            <th class="em07">{{ __('labels.precheck_n.discernment') }}</th>
            <th class="em07">{{ __('labels.precheck_n.similar') }}</th>
            <th class="em05 bg_whitesmoke">{{ __('labels.precheck_n.sub') }}</th>
        </tr>

        <div class="error-m-product-id"></div>
        @error('m_product_ids[]') <div class="notice">{{ $message }}</div> @enderror

        @foreach ($infoPrecheckTable as $distinction => $products)
            @foreach($products as $key => $item)
                @php
                    $hasPrecheckSimple = 0;
                    $hasPrecheckDetail = 0;

                    $historyPrecheckResult = collect([]);
                    foreach ($precheckBefore as $beforeData) {
                        $precheckProducts = $beforeData->precheckProduct->where('m_product_id', $item->id);

                        foreach ($precheckProducts as $precheckProduct) {
                            $precheckResult = $precheckProduct->precheckResult ?? collect([]);

                            foreach ($precheckResult as $result) {
                                $historyPrecheckResult->push($result);
                            }
                        }
                    }

                    $precheckProducts = $item->precheckProduct;
                    $lastPrecheckProducts = $precheckProducts->last();

                    if (in_array($lastPrecheckProducts->m_product_id, $registerProduct['simple'])) {
                        $hasPrecheckSimple = 1;
                    }
                    if (in_array($lastPrecheckProducts->m_product_id, $registerProduct['detail'])) {
                        $hasPrecheckDetail = 1;
                    }
                @endphp
                <tr class="product-item" data-is_simple="{{ $hasPrecheckSimple }}" data-is_detail="{{ $hasPrecheckDetail }}">
                    <input type="hidden" name="m_product_ids[]" value="{{ $item->id }}" />
                    @if ($key == 0)
                        <td rowspan="{{ $products->count() > 0 ? $products->count() : '' }}" class="bg_blue inv_blue">{{ __('labels.precheck.table_precheck.name_distinct', ['attr' => $distinction]) }}</td>
                    @endif
                    <td class="bg_green {{ $item->prechecks[0]->pivot->is_register_product == 1 ? 'red' : '' }}">{{ $item->name }}</td>

                    {{-- $historyPrecheckResult --}}
                    <td class="center">
                        @if (!empty($historyPrecheckResult) && $historyPrecheckResult->min('result_similar_simple'))
                            {{ \App\Models\PrecheckResult::listResultSmilarSimpleOptions()[$historyPrecheckResult->min('result_similar_simple')] }}
                        @else
                            -
                        @endif
                    </td>

                    {{-- $lastPrecheckProducts --}}
                    <td class="center">
                        @if (!empty($lastPrecheckProducts)
                            && $lastPrecheckProducts->precheckResult
                            && $lastPrecheckProducts->precheckResult->isNotEmpty($lastPrecheckProducts->precheckResult)
                            && $lastPrecheckProducts->precheckResult->min('result_similar_simple')
                        )
                            {{ \App\Models\PrecheckResult::listResultSmilarSimpleOptions()[$lastPrecheckProducts->precheckResult->min('result_similar_simple')] }}
                        @else
                            -
                        @endif
                    </td>

                    {{-- $historyPrecheckResult --}}
                    <td class="center bg_gray">
                        @if (!empty($historyPrecheckResult) && $historyPrecheckResult->max('result_identification_detail'))
                            {{ \App\Models\PrecheckResult::listResultIdentificationDetailOptions()[$historyPrecheckResult->max('result_identification_detail')] }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="center bg_gray">
                        @if (!empty($historyPrecheckResult) && $historyPrecheckResult->max('result_similar_detail'))
                            {{ \App\Models\PrecheckResult::listResultSimilarDetailOptions()[$historyPrecheckResult->max('result_similar_detail')] }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="center">
                        @if (!empty($historyPrecheckResult) && $historyPrecheckResult->max('result_similar_detail'))
                            {{ \App\Models\PrecheckResult::getResultDetailPrecheck($historyPrecheckResult->max('result_identification_detail'), $historyPrecheckResult->max('result_similar_detail')) }}
                        @else
                            -
                        @endif
                    </td>

                    {{-- $lastPrecheckProducts --}}
                    <td class="center bg_gray">
                        @if (!empty($lastPrecheckProducts)
                            && $lastPrecheckProducts->precheckResult
                            && $lastPrecheckProducts->precheckResult->isNotEmpty($lastPrecheckProducts->precheckResult)
                            && $lastPrecheckProducts->precheckResult->max('result_identification_detail')
                        )
                            {{ \App\Models\PrecheckResult::listResultIdentificationDetailOptions()[$lastPrecheckProducts->precheckResult->max('result_identification_detail')] }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="center bg_gray">
                        @if (!empty($lastPrecheckProducts)
                            && $lastPrecheckProducts->precheckResult
                            && $lastPrecheckProducts->precheckResult->isNotEmpty($lastPrecheckProducts->precheckResult)
                            && $lastPrecheckProducts->precheckResult->max('result_similar_detail')
                        )
                            {{ \App\Models\PrecheckResult::listResultSimilarDetailOptions()[$lastPrecheckProducts->precheckResult->max('result_similar_detail')] }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="center">
                        @if (!empty($lastPrecheckProducts)
                            && $lastPrecheckProducts->precheckResult
                            && $lastPrecheckProducts->precheckResult->isNotEmpty($lastPrecheckProducts->precheckResult)
                            && $lastPrecheckProducts->precheckResult->max('result_similar_detail')
                        )
                            {{ \App\Models\PrecheckResult::getResultDetailPrecheck($lastPrecheckProducts->precheckResult->max('result_identification_detail'), $lastPrecheckProducts->precheckResult->max('result_similar_detail')) }}
                        @else
                            -
                        @endif
                    </td>

                    @php
                       if($precheckNext && $precheckNext->precheckProduct->count() > 0) {
                           $data = $precheckNext->precheckProduct->filter(function($value, $key) use ($item) {
                               if($value->m_product_id == $item->id && ($value->product->mDistinction->id == $item->m_distinction_id)) {
                                   $item->is_register_product_check = $value->is_register_product;
                               }
                           });
                       }
                    @endphp

                    <td class="center bg_green">
                        <input type="checkbox" name="m_product_choose[]" {{ $item->is_register_product_check ? 'checked' : '' }} class="single-checkbox single-checkbox-{{ $item->id }}" value="{{ $item->id }}" />
                    </td>
                </tr>
            @endforeach
        @endforeach
        <tr>
            <td colspan="11" class="right">{{ __('labels.precheck_n.note_table') }}<span class="total-checkbox-checked"></span></td>
        </tr>
    </table>
</div><!-- /scroll wrap -->

<hr />
<style>
    .hideShowClick {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }
    .icon-text {
        position: absolute;
        font-size: 20px;
        font-weight: bold;
        top: -4px;
        right: -18px;
    }
</style>
