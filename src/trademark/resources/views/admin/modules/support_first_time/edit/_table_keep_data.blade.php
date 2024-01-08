<table class="normal_b column1 product-table">
    <caption>{{ __('labels.admin_sft_edit.product_service') }}<br />{{ __('labels.admin_sft_edit.created_at') }} ： {{ \Carbon\Carbon::parse($supFirstTimeData->updated_at)->format('Y/m/d') }}　{{ $supFirstTimeData->admin->name }}</caption>
    <tr>
        <th style="width:5em;">{{ __('labels.admin_sft_edit.STT') }}</th>
        <th style="width:10%;">{{ __('labels.admin_sft_edit.drap') }}<br />
            <input type="button" value="{{ __('labels.admin_sft_edit.product_drap') }}" class="btn_a copyFromDraftToEdit disabledAllButton" />　
            <input type="button" value="{{ __('labels.admin_sft_edit.service') }}" class="btn_b copyFromDraftToDecision disabledAllButton" />
        </th>
        <th>{{ __('labels.admin_sft_edit.distinction_drap') }}</th>
        <th style="width:10%;">{{ __('labels.admin_sft_edit.code_drap') }}</th>
        <th style="width:30%;">
            {{ __('labels.admin_sft_edit.product_edit') }}<br />
            <input type="button" value="{{ __('labels.admin_sft_edit.copy_edit') }}" class="btn_b copyFromEditToDecision disabledAllButton" />
        </th>
        <th>{{ __('labels.admin_sft_edit.distinction_edit') }}</th>
        <th style="width:10%;">{{ __('labels.admin_sft_edit.code_edit') }}</th>
        <th style="width:18%;">{{ __('labels.admin_sft_edit.pro_success') }}</th>
        <th>{{ __('labels.admin_sft_edit.distinction_success') }}</th>
        <th style="width:10%;">{{ __('labels.admin_sft_edit.code_success') }}</th>
        <th>
            {{ __('labels.admin_sft_edit.check_all') }}
            <input type="checkbox" name="is_block" class="checkAllIsBlock"/>
            <input type="hidden" class="totalSftSuitableProducts" value="{{ $supFirstTimeData->sftKeepData->sftKeepDataProds->count() }}" />
        </th>
    </tr>
    <tbody class="dataOld">
    @php
        use \App\Models\MProduct;
        use \App\Models\SFTKeepDataProd;
        use \App\Models\SFTSuitableProduct;
    @endphp
    @foreach ($supFirstTimeData->sftKeepData->sftKeepDataProds->sortBy('sftSuitableProduct.m_product_id')->values() as $k => $item)
        @php
            if ($item->type_product == MProduct::TYPE_SEMI_CLEAN) {
                $item->bgColorClass = 'bg_pink';
            } else if ($item->type_product == MProduct::TYPE_CREATIVE_CLEAN) {
                $item->bgColorClass = 'bg_yellow';
            } else {
                $item->bgColorClass = '';
            }
        @endphp
        <tr class="row-table-item row-table-item-{{$k}}" data-index="{{ $k }}">
            <td class="center {{ $item->bgColorClass }}">{{ $k + 1 }}<br /><input type="button" value="{{ __('labels.delete') }}" class="small btn_d deleteItem" data-is_delete="{{ $item->is_delete ?? false }}"/>
                <input type="hidden" name="data[{{ $k }}][sft_suitable_product_id]" value="{{ $item->sft_suitable_product_id }}" />
                <input type="hidden" name="data[{{ $k }}][id]" value="{{ $item->id }}" />
                <input type="hidden" name="data[{{ $k }}][is_decision]" class="is_decision is_decision_{{ $k }}" value="{{ $item->is_decision }}" />
                <input type="hidden" name="data[{{ $k }}][product_type]" class="product_type product_type_{{ $k }}" value="{{ $item->type_product }}" />
            </td>
            <!-- data draft-->
            <!--m_product_id_draft-->
            <td class="boxes {{ $item->bgColorClass }}">
                @if ($item->sftSuitableProduct)
                <span class="label_m_product_name_{{$k}}">{{ $item->sftSuitableProduct->mProduct ? $item->sftSuitableProduct->mProduct->name : '' }}</span><br />
                <input type="hidden" name="data[{{ $k }}][data_draft][m_product_name]" class="m_product_name_draft m_product_name_draft_{{$k}}" value="{{ $item->sftSuitableProduct->mProduct ? $item->sftSuitableProduct->mProduct->name : '' }}"/>
                <input type="hidden" name="data[{{ $k }}][data_draft][product_id]" class="m_product_id_draft m_product_id_draft_{{$k}}" value="{{ $item->sftSuitableProduct->mProduct ? $item->sftSuitableProduct->mProduct->id : '' }}"/>

                    <input type="button" value="修正" class="btn_a mb05 copySingleDraftToEdit disabledAllButton disabled_btn_single_{{$k}}" data-index="{{$k}}"/>
                    <input type="button" value="決定" class="btn_b mb05 copySingleDraftToDecision disabledAllButton disabled_btn_single_{{$k}}" data-index="{{$k}}"/>
                @endif
            </td>
            <!--m_distinction_draft-->
            <td class="center {{ $item->bgColorClass }}">
                @if($item->sftSuitableProduct)
                    <span class="label_m_distinction_draft_{{$k}}">{{ $item->sftSuitableProduct->mDistinction ? $item->sftSuitableProduct->mDistinction->name : '' }}</span>
                    <input type="hidden" name="data[{{ $k }}][data_draft][m_distinction_id]" class="m_distinction_draft_{{$k}}" value="{{ $item->sftSuitableProduct->mDistinction ? $item->sftSuitableProduct->mDistinction->id : '' }}" />
                @endif
            </td>
            <td class="{{ $item->bgColorClass }}">
                @if($item->sftSuitableProduct)
                    @php
                        $codes = collect([]);
                        if($item->sftSuitableProduct->mProduct && $item->sftSuitableProduct->mProduct->mCode) {
                              $codes = $item->sftSuitableProduct->mProduct->mCode->pluck('name');
                        }
                    @endphp
                    <div class="label_m_code_draft_{{$k}}">{{ $codes->implode(' ') }}</div>
                    <input type="hidden" name="data[{{ $k }}][data_draft][m_code]" value="{{ $codes->implode(',') }}" class="m_code_draft_{{ $k }}">
                @endif
            </td>

            <!--data edit-->
            <td class="boxes {{ $item->bgColorClass }}">
                <div class="name_prod_{{$k}}" style="position: relative">
                        @php
                          $prodName = '';
                          if($item->product_id) {
                              $prodName = $item->mProduct->name;
                          } else if($item->product_name_edit) {
                              $prodName = $item->product_name_edit;
                          }
                        @endphp
                    <input type="hidden" name="data[{{ $k }}][data_edit][product_id]" class="m_product_id_edit m_product_id_edit_{{$k}}" value="{{ $item->mProduct ? $item->mProduct->id : '' }}"/>
                    <input type="text" class="em30 prod_name input_product_name m_product_name_edit_{{$k}} {{ $item->is_block == SFTSuitableProduct::IS_BLOCK ? 'pointer-events-none' : '' }}"
                           value="{{ old("data[`$k`][data_edit][m_product_name]", $prodName) }}"
                           name="data[{{ $k }}][data_edit][m_product_name]"
                           autocomplete="off"
                           {{ $item->type_product != MProduct::TYPE_CREATIVE_CLEAN ? 'data-suggest' : '' }}
                           id="product_name_{{ $k }}" key-prod="{{ $k }}" nospace />
                </div>
                <input type="button" value="決定" class="btn_b copySingleEditToDecision disabledAllButton disabled_btn_single_{{$k}}" data-index="{{$k}}" />
                @if ($item->type_product != MProduct::TYPE_SEMI_CLEAN && $item->type_product != MProduct::TYPE_CREATIVE_CLEAN)
                    <input type="button" value="編集" class="btn_a mb05 disabledAllButton disabled_btn_single_{{$k}} changeBackgroundToPink" data-index="{{$k}}"/>
                @endif
            </td>
            <td class="center tr_m_distinction_edit_{{$k}} {{ $item->bgColorClass }}">
                @if ($item->type_product == MProduct::TYPE_CREATIVE_CLEAN)
                    <select name="data[{{ $k }}][data_edit][m_distinction_id]" class="m_distinction_edit_{{ $k }}" {{ $item->is_block == SFTSuitableProduct::IS_BLOCK ? 'readonly' : '' }}>
                        @foreach ($distinctions as $value => $name)
                            <option value="{{ $value }}" {{ ($item->m_distinction_id && $item->m_distinction_id == $value) ? 'selected': '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="label_m_distinction_edit_{{$k}}">{{ $item->mDistinction ? $item->mDistinction->name : ''}}</div>
                    <input type="hidden" name="data[{{ $k }}][data_edit][m_distinction_id]" class="m_distinction_edit_{{$k}}" value="{{ $item->m_distinction_id ?? '' }}" />
                @endif
            </td>
            <td class="{{ $item->bgColorClass }}">
                @if ($item->type_product == MProduct::TYPE_CREATIVE_CLEAN)
                    <div data-code-list class="data-code-list">
                        @if ($item->sftKeepDataProdCodes && count($item->sftKeepDataProdCodes) > 0)
                            @foreach ($item->sftKeepDataProdCodes as $i => $sftKeepDataProdCode)
                                <input type="text" autocomplete="off" class="em18 prod_code prod_code_old_data prod_code_old_data_{{$k}} m_code_edit_{{$k}}" name="data[{{ $k }}][data_edit][code][{{ $i }}][name]" data-index="{{ $k }}" data-index-code="{{ $k.$i }}" {{ $item->is_block == SFTSuitableProduct::IS_BLOCK ? 'readonly' : '' }} value="{{ $sftKeepDataProdCode->code }}"/>
                                <input type="hidden" class="em18" value="{{ $sftKeepDataProdCode->id }}" name="data[{{ $k }}][data_edit][code][{{ $i }}][old_id]"/>
                            @endforeach
                        @else
                            <input type="text" autocomplete="off" class="em18 prod_code prod_code_old_data prod_code_old_data_{{$k}} m_code_edit_{{$k}} {{ $item->is_block == SFTSuitableProduct::IS_BLOCK ? 'disabled-input' : '' }}" value="" name="data[{{ $k }}][data_edit][code][0][name]" data-index="{{ $k }}" data-index-code="{{ $k }}0" />
                        @endif
                    </div>
                    <input type="button" class="add_code_old_data" data-index="{{ $k }}" value="+ 類似群コード追加">
                @elseif($item->type_product == MProduct::TYPE_SEMI_CLEAN)
                    @php
                        $codeCustomEdit = collect([]);
                        $codes = $item->sftKeepDataProdCodes;
                        foreach ($codes as $sftKeepDataProdCode) {
                            $codeCustomEdit->push($sftKeepDataProdCode->mCode->name);
                        }
                    @endphp
                    <div class="label_m_code_edit_{{$k}}">{{ $codeCustomEdit->implode(' ')  }}</div>
                    <input type="hidden" name="data[{{ $k }}][data_edit][m_code]" value="{{ $codeCustomEdit->implode(',') }}" class="m_code_edit_{{ $k }}">
                @else
                    @if ($item->mProduct && $item->mProduct->mCode)
                        <div class="label_m_code_edit_{{$k}}">{{ $item->mProduct->mCode->implode('name', ' ')  }}</div>
                        <input type="hidden" name="data[{{ $k }}][data_edit][m_code]" value="{{ $item->mProduct->mCode->implode('name', ',') }}" class="m_code_edit_{{ $k }}">
                    @else
                        <div class="label_m_code_edit_{{$k}}"></div>
                        <input type="hidden" name="data[{{ $k }}][data_edit][m_code]" value="" class="m_code_edit_{{ $k }}">
                    @endif
                @endif
            </td>
            <!--data decision-->
            <td class="{{ $item->bgColorClass }}">
                <input type="checkbox" name="data[{{ $k }}][delete_item]" class="delete_item_hide" style="display: none"/>
                <input type="hidden" name="data[{{ $k }}][data_decision][sft_suitable_product_id]" value="{{ $item->sft_suitable_product_id }}" />
                <input type="hidden" name="data[{{ $k }}][data_decision][m_product_type]" class="m_product_type m_product_type_{{ $k }}" value="{{ $item->type_product }}" />
                <!-- data_decision: m_product -->
                @if ($item->is_decision == SFTKeepDataProd::DRAFT_IS_DECISION)
                    <div class="label_m_product_name_decision_{{$k }}">{{ ($item->sftSuitableProduct && $item->sftSuitableProduct->mProduct) ? $item->sftSuitableProduct->mProduct->name : '' }}</div>
                    <input type="hidden" name="data[{{ $k }}][data_decision][m_product_name]" value="{{ ($item->sftSuitableProduct && $item->sftSuitableProduct->mProduct) ? $item->sftSuitableProduct->mProduct->name : '' }}" class="m_product_name_decision m_product_name_decision_{{$k}}">
                    <input type="hidden" name="data[{{ $k }}][data_decision][product_id]" value="{{ ($item->sftSuitableProduct && $item->sftSuitableProduct->mProduct) ? $item->sftSuitableProduct->mProduct->id : ''}}" class="m_product_id_decision m_product_id_decision_{{$k}}">
                @elseif ($item->is_decision == SFTKeepDataProd::EDIT_IS_DECISION)
                    @if ($item->type_product == MProduct::TYPE_CREATIVE_CLEAN || $item->type_product == MProduct::TYPE_SEMI_CLEAN)
                        <div class="label_m_product_name_decision_{{$k }}">{{ $item->product_name_edit }}</div>
                        <input type="hidden" name="data[{{ $k }}][data_decision][product_name_edit]" value="{{ $item->product_name_edit }}" class="m_product_name_decision m_product_name_decision_{{$k}}">
                    @else
                        <div class="label_m_product_name_decision_{{ $k }}">{{ $item->mProduct ? $item->mProduct->name : '' }}</div>
                        <input type="hidden" name="data[{{ $k }}][data_decision][m_product_name]" value="{{ $item->mProduct ? $item->mProduct->name : '' }}" class="m_product_name_decision m_product_name_decision_{{$k}}">
                        <input type="hidden" name="data[{{ $k }}][data_decision][product_id]" value="{{ $item->mProduct ? $item->mProduct->id : '' }}" class="m_product_id_decision m_product_id_decision_{{$k}}">
                    @endif
                @else
                    <div class="label_m_product_name_decision_{{$k }}"></div>
                    <input type="hidden" name="data[{{ $k }}][data_decision][m_product_name]" class="m_product_name_decision m_product_name_decision_{{$k}}">
                    <input type="hidden" name="data[{{ $k }}][data_decision][product_id]" class="m_product_id_decision m_product_id_decision_{{$k}}">
                @endif
            </td>

            <!-- data_decision: m_distinction -->
            <td class="center {{ $item->bgColorClass }}">
                @if ($item->is_decision == SFTKeepDataProd::DRAFT_IS_DECISION)
                    <div class="label_m_distinction_decision_{{$k }} type_{{ $item->bgColorClass }}">{{ ($item->sftSuitableProduct && $item->sftSuitableProduct->mDistinction) ? $item->sftSuitableProduct->mDistinction->name : ''}}</div>
                    <input type="hidden" name="data[{{ $k }}][data_decision][m_distinction_id]" value="{{ ($item->sftSuitableProduct && $item->sftSuitableProduct->mDistinction) ? $item->sftSuitableProduct->mDistinction->id : '' }}" class="m_distinction_decision_{{$k}} type_{{ $item->bgColorClass }}">
                @elseif ($item->is_decision == SFTKeepDataProd::EDIT_IS_DECISION)
                    <div class="label_m_distinction_decision_{{ $k }} type_{{ $item->bgColorClass }}">{{ $item->mDistinction ? $item->mDistinction->name : '' }}</div>
                    <input type="hidden" name="data[{{ $k }}][data_decision][m_distinction_id]" value="{{ $item->mDistinction ? $item->mDistinction->id : ''}}" class="m_distinction_decision_{{$k}} type_{{ $item->bgColorClass }}">
                @else
                    <div class="label_m_distinction_decision_{{ $k }} type_{{ $item->bgColorClass }}"></div>
                    <input type="hidden" name="data[{{ $k }}][data_decision][m_distinction_id]" value="" class="m_distinction_decision_{{$k}} type_{{ $item->bgColorClass }}">
                @endif
            </td>

            <!-- data_decision: m_code -->
            <td class="{{ $item->bgColorClass }}">
                @if ($item->is_decision == SFTKeepDataProd::DRAFT_IS_DECISION)
                    @php
                        $codes = collect([]);
                        if($item->sftSuitableProduct && $item->sftSuitableProduct->mProduct && $item->sftSuitableProduct->mProduct->mCode) {
                              $codes = $item->sftSuitableProduct->mProduct->mCode->pluck('name');
                        }
                    @endphp
                    <div class="label_m_code_decision_{{$k}}">{{ $codes->implode(' ') }}</div>
                    <input type="hidden" name="data[{{ $k }}][data_decision][m_code]" value="{{ $codes->implode(',') }}" class="m_code_decision_{{ $k }}">
                @elseif($item->is_decision == SFTKeepDataProd::EDIT_IS_DECISION)
                    @if ($item->type_product == MProduct::TYPE_CREATIVE_CLEAN)
                        @php
                            $codes = $item->sftKeepDataProdCodes->pluck('code');
                        @endphp
                        <div class="label_m_code_decision_{{$k}}">{{ $codes->implode(' ') }}</div>
                        <input type="hidden" name="data[{{ $k }}][data_decision][m_code]" value="{{ $codes->implode(',') }}" class="m_code_decision_{{ $k }}">
                    @elseif($item->type_product == MProduct::TYPE_SEMI_CLEAN)
                        @php
                            $codeCustom = collect([]);
                            $codes = $item->sftKeepDataProdCodes;
                            foreach ($codes as $sftKeepDataProdCode) {
                                $codeCustom->push($sftKeepDataProdCode->mCode->name);
                            }
                        @endphp
                        <div class="label_m_code_decision_{{$k}}">{{ $codeCustom->implode(' ') }}</div>
                        <input type="hidden" name="data[{{ $k }}][data_decision][m_code]" value="{{ $codeCustom->implode(',') }}" class="m_code_decision_{{ $k }}">
                    @else
                        @php
                            $codes = collect([]);
                             if ($item->sftSuitableProduct) {
                                 if($item->sftSuitableProduct->mProduct && $item->sftSuitableProduct->mProduct->mCode) {
                                      $codes = $item->sftSuitableProduct->mProduct->mCode->pluck('name');
                                 }
                             } else {
                                 //create new data && null suitable
                                 if($item->mProduct && $item->mProduct->code) {
                                    $codes = $item->mProduct->code->pluck('name');
                                }
                             }
                        @endphp
                        <div class="label_m_code_decision_{{$k}}">{{ $codes->implode(' ') }}</div>
                        <input type="hidden" name="data[{{ $k }}][data_decision][m_code]" value="{{ $codes->implode(',') }}" class="m_code_decision_{{ $k }}">
                    @endif
                @else
                    <div class="label_m_code_decision_{{$k}}"></div>
                    <input type="hidden" name="data[{{ $k }}][data_decision][m_code]" value="" class="m_code_decision_{{ $k }}">
                @endif
            </td>
            <td class="center {{ $item->bgColorClass }}">
                <input type="checkbox" class="is_block is_block_{{ $k }}" name="data[{{ $k }}][is_block]" data-id="{{ $item->id }}" {{ $item->is_block == SFTSuitableProduct::IS_BLOCK ? 'checked' : '' }} value="{{ $item->is_block }}"/>{{ __('labels.admin_sft_edit.confirm') }}
            </td>
        </tr>
    @endforeach
    </tbody>

    <tr class="tr_action">
        <td colspan="11" class="left"><button type="button" class="add_prod" id="add_prod">{{ __('labels.admin_sft_edit.add_white') }}</button></td>
    </tr>
    <tr>
        <td colspan="11" class="left"><button type="button" class="add_prod" id="add_prod_free">{{ __('labels.admin_sft_edit.add_yellow') }}</button>
        </td>
    </tr>
</table>

