<div class="estimateBox">
    <input type="checkbox" id="cart" /><label class="button" for="cart"><span
            class="open">{{ __('labels.refusal_plans.estimate-box.open_cart') }}</span><span
            class="close">{{ __('labels.refusal_plans.estimate-box.close_cart') }}</span></label>

    <div class="estimateContents">

        <h3>{{ __('labels.refusal_plans.estimate-box.title') }}</h3>


        <p>{{ __('labels.refusal_plans.estimate-box.content_1') }}
            <br />
            <span class="fs15" id="worst_result">{{ __('labels.refusal_plans.estimate-box.content_18') }}</span>
        </p>

        <p>{{ __('labels.refusal_plans.estimate-box.content_2') }}</p>
        <p>{{ __('labels.refusal_plans.estimate-box.content_3') }}</p>

        <p>
            <a href="{{ $redirect_to ?? '#' }}" class="btn_b">{{ __('labels.refusal_plans.u203.btn.btn_7') }}</a>
        </p>

        <table class="normal_b eol">
            <tr>
                <th>{{ __('labels.refusal_plans.estimate-box.distinct_name') }}</th>
                <th>{{ __('labels.refusal_plans.estimate-box.product_name') }}</th>
                <th class="center em05">{{ __('labels.refusal_plans.estimate-box.plan_detail_check') }}</th>
            </tr>
            @foreach ($mDistinctCart as $key => $mDistincts)
                @foreach ($mDistincts as $mProduct)
                    <tr>
                        <td class="right">{{ $key }}</td>
                        <td>{{ $mProduct->name }}</td>
                        <td class="center bg_orange">
                            @if (in_array($mProduct->planDetailProduct->role_add, [2, 3]))
                                －
                            @elseif($mProduct->planDetailProduct->leave_status != LEAVE_STATUS_2)
                                {{ $mProduct->planDetailProduct->planDetail->getTextRevolution() }}
                            @else
                                削除
                            @endif
                        </td>
                    </tr>
                    <input type="hidden" name="productIds[]" id="" value="{{ $mProduct->id }}">
                @endforeach
            @endforeach
        </table>
        <h3>{{ __('labels.refusal_plans.estimate-box.content_4') }}</h3>
        <table class="normal_b">
            <tr>
                <td>{{ __('labels.refusal_plans.estimate-box.content_5') }}<span
                        class="number_plan_detail_prods"></span>{{ __('labels.refusal_plans.estimate-box.content_6') }}<br />{{ __('labels.refusal_plans.estimate-box.content_7') }}
                    <span id="prod_add"></span>
                    {{ __('labels.refusal_plans.estimate-box.content_8') }}
                </td>
                <td class="right"><span id="cost_prod_add"></span> 円</td>
            </tr>
            <tr id="hidden_cost_bank_transfer">
                <td>{{ __('labels.refusal_plans.estimate-box.content_9') }}</td>
                <td class="right"><span id="cost_bank_transfer"></span> 円</td>
            </tr>
            <tr>
                <th class="right">{{ __('labels.refusal_plans.estimate-box.content_10') }}</th>
                <th class="right"><span id="sub_total"></span> 円</th>
            </tr>
            <tr id="hidden_commission_tax">
                <th class="right" colspan="2">
                    {{ __('labels.refusal_plans.estimate-box.content_11') }}<span id="commission"></span> 円<br />
                    {{ __('labels.refusal_plans.estimate-box.content_12') }}<span
                        id="percent_tax"></span>{{ __('labels.refusal_plans.estimate-box.content_13') }}<span
                        id="tax"></span> 円</th>
            </tr>
            <tr>
                <td>{{ __('labels.refusal_plans.estimate-box.content_14') }}<span
                        class="number_distinct_add"></span>{{ __('labels.refusal_plans.estimate-box.number_distinct_add') }}<br />{{ __('labels.refusal_plans.estimate-box.content_15') }}<span
                        id="patent_cost"></span>
                    {{ __('labels.refusal_plans.estimate-box.content_16') }}<span
                        class="number_distinct_add"></span>{{ __('labels.refusal_plans.estimate-box.number_distinct_add') }}
                </td>
                <td class="right"><span class="cost_additional"></span> 円</td>
            </tr>
            <tr>
                <th class="right"><strong
                        style="font-size:1.2em;">{{ __('labels.refusal_plans.estimate-box.content_19') }}</strong></th>
                <th class="right"><strong style="font-size:1.2em;"><span id="total_amount"></span> 円</strong></th>
            </tr>
        </table>
        <p class="red mb10">{{ __('labels.refusal_plans.estimate-box.content_17') }}</p>

        <ul class="right list">
            <li><input type="submit" value="{{ __('labels.refusal_plans.u203.btn.btn_8') }}" class="btn_a"
                    data-submit="{{ _QUOTES }}" /></li>
        </ul>

        <p class="right mb10"><input type="submit" value="{{ __('labels.refusal_plans.u203.btn.btn_9') }}"
                class="btn_b" data-submit="{{ isset($flag) && $flag ? U203C_N : U203C }}" /></p>
        <ul class="right footerBtn clearfix">
            <li style="margin-bottom:1em"></li>
            <li><input type="submit" value="{{ __('labels.refusal_plans.u203.btn.btn_10') }}" class="btn_e"
                    data-submit="{{ U203B02 }}" /></li>
        </ul>
    </div>
    <!-- /estimate contents -->

</div>
