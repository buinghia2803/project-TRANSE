<table class="normal_b eol product-table">
    <caption>
        {{ __('labels.support_first_times.category_product_name') }}
        <br/>{{ __('labels.support_first_times.final_update') }}{{ $sft->updated_at ? date_format($sft->updated_at, 'Y/m/d') : '' }} {{ $sft->admin ? $sft->admin->name : '' }}
    </caption>
    <thead>
    <tr>
        <th style="width:5em;">{{ __('labels.create_support_first_time.index') }}</th>
        <th style="width:43em;">{{ __('labels.create_support_first_time.product_name') }}</th>
        <th style="width:8em;">{{ __('labels.create_support_first_time.distinguishing') }}</th>
        <th style="width:10em;">{{ __('labels.create_support_first_time.group_code') }}</th>
    </tr>
    </thead>
    <tbody id="dynamic">
    @if (count($sftSuitableProduct) > 0)
        @foreach ($sftSuitableProduct as $i => $item)
            @php
             $bgColor = '';
             if ($item->type == \App\Models\MProduct::TYPE_CREATIVE_CLEAN) {
                 $bgColor = 'bg_yellow';
             } else if ($item->type == \App\Models\MProduct::TYPE_SEMI_CLEAN) {
                 $bgColor = 'bg_pink';
             }
            @endphp
            <tr id="prod{{$i}}" class="item_product">
                <td class="center">
                    <span class="no-number">{{ $i + 1 }}</span><br/>
                    <input type="button" value="{{ __('labels.create_support_first_time.delete') }}" class="small btn_d deleteItem deleteItem-{{ $i }}" data-index="{{ $i }}" id="delete-item"
                           key-prod="{{ $i }}"/>
                    <input type="text" hidden name="support_first_time_id" value="{{ $sft ? $sft->id : '' }}"/>
                    <input type="text" hidden name="data[{{ $i }}][sft_suitable_product_id]" value="{{ $item->id }}"/>
                    <input type="checkbox" name="data[{{ $i }}][delete_item]" class="delete_item_hide delete_item_hide_{{ $i }}" style="display: none"/>
                </td>
                <td class="boxes name_prod_{{ $i }} {{ $bgColor }}" data-name>
                    <input type="hidden" name="data[{{ $i }}][type]" type-prod value="{{ $item->type }}" />
                    @if ($item->type == \App\Models\MProduct::TYPE_ORIGINAL_CLEAN || $item->type == \App\Models\MProduct::TYPE_REGISTERED_CLEAN)
                    <input type="button" value="編集" class="btn_a mb05" id="edit_name_prod"/>
                    @endif
                    <input type="text" class="em40 prod_name input_product_name"
                           value="{{ old("data[`$i`][name]" , $item->mProduct ? $item->mProduct->name : '') }}"
                           name="data[{{ $i }}][name]"
                           {{ $item->type == \App\Models\MProduct::TYPE_ORIGINAL_CLEAN || $item->type == \App\Models\MProduct::TYPE_REGISTERED_CLEAN ? 'data-suggest' : '' }}
                           {{ $item->type == \App\Models\MProduct::TYPE_ORIGINAL_CLEAN || $item->type == \App\Models\MProduct::TYPE_REGISTERED_CLEAN ? 'readonly' : '' }}
                           id="product_name_{{ $i }}"
                           key-prod="{{ $i }}" nospace />
                    <input type="hidden" name="data[{{ $i }}][m_product_id]" value="{{ $item->mProduct ? $item->mProduct->id : '' }}" />
                </td>
                <td class="center distinction {{ $bgColor }}" id="distinction_{{ $i }}">
                    {{ $item->distinction_name }}
                    <input hidden="" name="data[{{ $i }}][distinction]" class="m-distinction" value="{{ $item->m_distinction_id }}" prod-distinction="">
                </td>
                <td class="prod_code {{ $bgColor }}" id="prod_code_{{ $i }}">
                    @if ($item->mProduct && $item->mProduct->type != \App\Models\MProduct::TYPE_CREATIVE_CLEAN)
                        <span class="label_prod_code_{{ $i }}">
                            {{  $item->mProduct->productCode->implode('code_name', ' ') }}
                        </span>
                        <input type="hidden" name="data[{{ $i }}][code_ids]" value="{{  $item->mProduct->productCode->implode('m_code_id', ',') }}" class="code_ids code_ids_{{ $i }}" />
                    @else
                        <div data-code id="input-code-{{ $i }}">
                            @if (count($item->mProduct->productCode) > 0)
                                @foreach ($item->mProduct->productCode as $j => $code)
                                    <input type="text" class="em18 prod_code" value="{{ $code->code_name }}" name="data[{{ $i }}][code][{{ $j }}][name]" nospace/>
                                    <input type="hidden" class="em18 prod_code" value="{{ $code->m_code_id }}" name="data[{{ $i }}][code][{{ $j }}][id]"/>
                                @endforeach
                            @else
                                <input type="text" class="em18 prod_code" value="" name="data[{{ $i }}][code][0][name]" nospace/>
                            @endif
                        </div>
                        <button type="button" id="add_code" class="add_code" data-index="{{ $i }}">{{ __('labels.create_support_first_time.add_code') }}</button>
                    @endif
                </td>
            </tr>
        @endforeach
    @else
        @for($i = 0;$i <= 4;$i++)
            <tr id="item_product prod{{$i}}" class="item_product">
                <td class="center">
                    <span class="no-number">{{ $i + 1 }}</span><br/>
                    <input type="button" value="{{ __('labels.create_support_first_time.delete') }}" class="small btn_d remove-prod" id="remove-product"
                           key-prod="{{ $i }}"/>
                    <input type="text" hidden name="support_first_time_id" value=""/>
                </td>
                <td class="boxes name_prod_{{ $i }}" data-name>
                    <input type="button" value="{{ __('labels.create_support_first_time.edit_prod') }}" class="btn_a mb05" id="edit_name_prod"/>
                    <input type="text" class="em40 prod_name input_product_name"
                           value="{{ old("data[`$i`][name]") }}"
                           name="data[{{ $i }}][name]" data-suggest id="product_name_{{ $i }}" key-prod="{{ $i }}" nospace />
                </td>
                <td class="center distinction" id="distinction_{{ $i }}"></td>
                <td class="prod_code" id="prod_code_{{ $i }}">
                     <span class="label_prod_code_{{ $i }}"></span>
                    <input type="hidden" name="data[{{ $i }}][code_ids]" value="" class="code_ids code_ids_{{ $i }}" />
                </td>
            </tr>
        @endfor
    @endif
    </tbody>
    <tr>
        <td colspan="4" class="left">
            @if (auth('admin')->user()->role == ROLE_SUPERVISOR)
                <button type="button" disabled>{{ __('labels.create_support_first_time.add_prod') }}</button>
            @else
                <button type="button" class="add_prod" id="add_prod">{{ __('labels.create_support_first_time.add_prod') }}</button>
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="4" class="left">
            @if (auth('admin')->user()->role == ROLE_SUPERVISOR)
                <button type="button" disabled>{{ __('labels.create_support_first_time.add_prod_free') }}</button>
            @else
                <button type="button" type="button" class="add_prod" id="add_prod_free">{{ __('labels.create_support_first_time.add_prod_free') }}</button>
            @endif
        </td>
    </tr>
</table>
