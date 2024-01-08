<table class="normal_b column1 product-table">
    <caption>{{ __('labels.admin_sft_edit.product_service') }}<br />{{ __('labels.admin_sft_edit.created_at') }} ： {{ \Carbon\Carbon::parse($supFirstTimeData->updated_at)->format('Y/m/d') }}　{{ $supFirstTimeData->admin->name }}</caption>
    <tr>
        <th style="width:5em;">{{ __('labels.admin_sft_edit.STT') }}</th>
        <th style="width:18%;">{{ __('labels.admin_sft_edit.drap') }}<br />
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
            <input type="hidden" class="totalSftSuitableProducts" value="{{ $supFirstTimeData->StfSuitableProduct->count() }}" />
        </th>
    </tr>
   <tbody class="dataOld">
   @php
        use \App\Models\MProduct;
        use \App\Models\SFTSuitableProduct;
   @endphp
   @foreach ($supFirstTimeData->StfSuitableProduct->sortBy('m_product_id')->values() as $k => $item)
       @php
           if ($item->mProduct) {
               if ($item->mProduct->type == MProduct::TYPE_SEMI_CLEAN) {
                   $item->bgColorClass = 'bg_pink';
               } else if ($item->mProduct->type == MProduct::TYPE_CREATIVE_CLEAN) {
                    $item->bgColorClass = 'bg_yellow';
               } else {
                   $item->bgColorClass = '';
               }
           }
       @endphp
       <input type="hidden" name="data[{{ $k }}][sft_suitable_product_id]" value="{{ $item->id }}" />
       <input type="hidden" name="data[{{ $k }}][is_decision]" class="is_decision is_decision_{{ $k }}" value="" />
       <input type="hidden" name="data[{{ $k }}][product_type]" class="product_type product_type_{{ $k }}" value="{{ $item->mProduct->type }}" />
       <tr class="row-table-item row-table-item-{{$k}}" data-index="{{ $k }}">
           <td class="center {{ $item->bgColorClass }}">{{ $k + 1 }}<br /> <input type="button" value="{{ __('labels.delete') }}" class="small btn_d deleteItem" data-is_delete="{{ !empty($item->deleted_at) }}"/></td>
           <!-- data draft-->
           <td class="boxes {{ $item->bgColorClass }}">
               <span class="label_m_product_name_{{$k}}">{{ $item->mProduct ? $item->mProduct->name : '' }}</span><br />
               <!--data product draft-->
               <input type="hidden" name="data[{{ $k }}][data_draft][m_product_name]" class="m_product_name_draft m_product_name_draft_{{$k}}" value="{{ $item->mProduct ? $item->mProduct->name : '' }}"/>
               <input type="hidden" name="data[{{ $k }}][data_draft][product_id]" class="m_product_id_draft m_product_id_draft_{{$k}}" value="{{ $item->mProduct ? $item->mProduct->id : '' }}"/>
               <!--button action draft-->
               <input type="button" value="{{ __('labels.support_first_times.fix') }}" class="btn_a mb05 copySingleDraftToEdit disabledAllButton disabled_btn_single_{{$k}}" data-index="{{$k}}"/>
               <input type="button" value="{{ __('labels.decision') }}" class="btn_b mb05 copySingleDraftToDecision disabledAllButton disabled_btn_single_{{$k}}" data-index="{{$k}}"/>
           </td>
           <!--m_distinction-->
           <td class="center {{ $item->bgColorClass }}">
               <span class="label_m_distinction_draft_{{$k}}">{{ $item->mDistinction ? $item->mDistinction->name : '' }}</span>
               <input type="hidden" name="data[{{ $k }}][data_draft][m_distinction_id]" class="m_distinction_draft_{{$k}}" value="{{ $item->mDistinction ? $item->mDistinction->id : '' }}" />
           </td>
           <td class="{{ $item->bgColorClass }}">
               @php
                   $codes = collect([]);
                    if($item->mProduct && $item->mProduct->code) {
                       $codes = $item->mProduct->code->pluck('name');
                   }
               @endphp
               <div class="label_m_code_draft_{{$k}}">{{ $codes->implode(' ') }}</div>
               <input type="hidden" name="data[{{ $k }}][data_draft][m_code]" value="{{ $codes->implode(',') }}" class="m_code_draft_{{ $k }}">
           </td>
           <!--data edit-->
           <td class="boxes {{ $item->bgColorClass }}">
               <div class="name_prod_{{$k}}" style="position: relative">
                   @if ($item->mProduct->type == MProduct::TYPE_SEMI_CLEAN || $item->mProduct->type == MProduct::TYPE_CREATIVE_CLEAN)
                       <input type="hidden" name="data[{{ $k }}][data_edit][product_id]" class="m_product_id_edit m_product_id_edit_{{$k}}" value=""/>
                       <input type="text" class="em30 m_product_name_edit m_product_name_edit_{{$k}}"
                              name="data[{{ $k }}][data_edit][m_product_name]"
                              {{ $item->is_block == SFTSuitableProduct::IS_BLOCK ? 'readonly' : '' }}
                              {{ $item->mProduct->type == MProduct::TYPE_SEMI_CLEAN ? 'data-suggest' : '' }}
                              autocomplete="off"
                              value="{{ old('data_edit[$k][m_product_name]', ($item->sftKeepDataProd && $item->sftKeepDataProd->product_name_edit) ? $item->sftKeepDataProd->product_name_edit : '') }}" key-prod="{{ $k }}"/><br />
                   @else
                       <input type="hidden" name="data[{{ $k }}][data_edit][product_id]" class="m_product_id_edit m_product_id_edit_{{$k}}" value=""/>
                       <input type="text" class="em30 prod_name input_product_name m_product_name_edit_{{$k}}"
                              value="{{ old("data[`$k`][data_edit][m_product_name]") }}"
                              {{ $item->is_block == SFTSuitableProduct::IS_BLOCK ? 'readonly' : '' }}
                              autocomplete="off"
                              name="data[{{ $k }}][data_edit][m_product_name]" data-suggest id="product_name_{{ $k }}" key-prod="{{ $k }}" nospace />
                   @endif
               </div>
               <input type="button" value="{{ __('labels.decision') }}" class="btn_b copySingleEditToDecision disabledAllButton disabled_btn_single_{{$k}}" data-index="{{$k}}" />
               @if ($item->mProduct->type != MProduct::TYPE_SEMI_CLEAN && $item->mProduct->type != MProduct::TYPE_CREATIVE_CLEAN)
                    <input type="button" value="{{ __('labels.edit') }}" class="btn_a mb05 disabledAllButton disabled_btn_single_{{$k}} changeBackgroundToPink" data-index="{{$k}}"/>
               @endif
           </td>
           <td class="center tr_m_distinction_edit_{{$k}} {{ $item->bgColorClass }}">
               @if ($item->mProduct->type == MProduct::TYPE_CREATIVE_CLEAN)
                    <select name="data[{{ $k }}][data_edit][m_distinction_id]" class="m_distinction_edit_{{ $k }}">
                        @foreach ($distinctions as $value => $name)
                            <option value="{{ $value }}">{{ $name }}</option>
                        @endforeach
                    </select>
                @else
                 <div class="label_m_distinction_edit_{{$k}}"></div>
                 <input type="hidden" name="data[{{ $k }}][data_edit][m_distinction_id]" class="m_distinction_edit_{{$k}}" value="" />
               @endif
           </td>

           <td class="{{ $item->bgColorClass }}">
               @if ($item->mProduct->type == MProduct::TYPE_CREATIVE_CLEAN)
                   <div data-code-list class="data-code-list">
                       <input type="text" class="em18 prod_code prod_code_old_data prod_code_old_data_{{$k}} m_code_edit_{{$k}}" value="" name="data[{{ $k }}][data_edit][code][0]"/>
                   </div>
                    <button type="button" class="add_code_old_data w135" data-index="{{ $k }}">{{ __('labels.create_support_first_time.add_code') }}</button>
                @else
                 <div class="label_m_code_edit_{{$k}}"></div>
                 <input type="hidden" name="data[{{ $k }}][data_edit][m_code]" value="" class="m_code_edit_{{ $k }}">
               @endif
           </td>

           <!--data decision-->
           <td class="{{ $item->bgColorClass }}">
               <input type="checkbox" name="data[{{ $k }}][delete_item]" class="delete_item_hide" style="display: none"/>
               <input type="hidden" name="data[{{ $k }}][data_decision][sft_suitable_product_id]" value="{{ $item->id }}" />
               <input type="hidden" name="data[{{ $k }}][data_decision][m_product_type]" class="m_product_type m_product_type_{{ $k }}" value="{{ $item->mProduct->type }}" />
               <div class="label_m_product_name_decision_{{$k }}"></div>
               <input type="hidden" name="data[{{ $k }}][data_decision][m_product_name]" class="m_product_name_decision m_product_name_decision_{{$k}}">
               <input type="hidden" name="data[{{ $k }}][data_decision][product_id]" class="m_product_id_decision m_product_id_decision_{{$k}}">
           </td>

           <td class="center {{ $item->bgColorClass }}">
                <div class="label_m_distinction_decision_{{$k }} type_{{ $item->bgColorClass }}"></div>
                <input type="hidden" name="data[{{ $k }}][data_decision][m_distinction_id]" value="" class="m_distinction_decision_{{$k}} type_{{ $item->bgColorClass }}">
           </td>

           <td class="{{ $item->bgColorClass }}">
               <div class="label_m_code_decision_{{$k}}"></div>
               <input type="hidden" name="data[{{ $k }}][data_decision][m_code]" value="" class="m_code_decision_{{ $k }}">
           </td>
           <td class="center {{ $item->bgColorClass }}">
               <input type="checkbox" class="is_block is_block_{{ $k }}" name="data[{{ $k }}][is_block]" data-id="{{ $item->id }}" {{  $item->is_block == \App\Models\SFTSuitableProduct::IS_BLOCK ? 'checked' : '' }} value="{{ $item->is_block }}"/>確認＆ロック
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
