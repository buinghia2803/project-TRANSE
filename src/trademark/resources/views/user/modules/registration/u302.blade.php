@extends('user.layouts.app')

@section('main-content')
  @php
      $totalProdDistinct = $productsDistinct->count();
      $regis5Years = $appTrademark->period_registration == PERIOD_REGISTRATION_FIVE_YEAR;
      $regis10Years = $appTrademark->period_registration == PERIOD_REGISTRATION_TEN_YEAR;
  @endphp
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.u302.title_1') }}</h2>
        <form id="form" method="POST">
          @csrf
          @include('admin.components.includes.messages')
          <h3>{{ __('labels.u302.trademark_info') }}<br />
{{--              @dd(Carbon\Carbon::parse($matchingResult->pi_dd_date)->addDays(30))--}}
            @if (isset($matchingResult->pi_dd_date) && Carbon\Carbon::parse($matchingResult->pi_dd_date)->addDays(30)->format('Ymd') < now()->format('Ymd'))
              <span class="red">{{ __('labels.u302.attention_1') }}<br />
              <a href="{{ route('user.apply-trademark-free-input', ['id' => $matchingResult->trademark_id])}}">{{ __('labels.u302.reapply') }}</a></span>
            @endif
          </h3>

          {{-- Trademark table --}}
          @include('user.components.trademark-table', [
              'table' => $trademarkTable
          ])
         @if(in_array($appTrademark->pack, [App\Models\AppTrademark::PACK_B, App\Models\AppTrademark::PACK_C]))
            <p class="blue">{{ __('labels.u302.attention_2') }}<br />{{ __('labels.u302.attention_3') }}</p>
         @elseif($appTrademark->pack == App\Models\AppTrademark::PACK_A)
            <p class="blue">{{ __('labels.u302.attention_4') }}<br />{{ __('labels.u302.attention_5') }}</p>
         @endif
          @if($appTrademark->pack == App\Models\AppTrademark::PACK_A)
            <dl class="w08em eol clearfix">
              <dt>{{ __('labels.u302.app_deadline') }} </dt>
              <dd>
                {{ isset($registerTrademark->user_response_deadline) && $registerTrademark->user_response_deadline ? Carbon\Carbon::parse($registerTrademark->user_response_deadline)->format('Y年m月d日') : '' }}
              </dd>
            </dl>
          @else
            <dl class="w10em eol clearfix">
              <dt>{{ __('labels.u302.response_deadline_ams') }} </dt>
              <dd>{{ isset($registerTrademark->user_response_deadline) && $registerTrademark->user_response_deadline ? Carbon\Carbon::parse($registerTrademark->user_response_deadline)->format('Y年m月d日') : ''  }}
                <br />
                {{ __('labels.u302.attention_6') }}
              </dd>
            </dl>
            @endif

          <p class="eol">
            <button type="button" id="open_file_pdf" style="width: 240px; height: 32px;" class="btn_b">{{ __('labels.u302.decision_register_patent') }}</button>
          </p>
          <hr />
          <h3>{{ __('labels.u302.category_register') }}</h3>

          <p>{{ __('labels.u302.attention_7') }}</p>

          <div class="js-scrollable eol box-distinct-tbl">
            <table class="normal_b westimate">
              <thead>
                <tr>
                  <th style="width:4em;">{{ __('labels.u302.distinct') }}</th>
                  <th>{{ __('labels.u302.prod_name') }}</th>
                  <th style="width:4em;">{{ __('labels.u302.registration_target') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($productsDistinct as $distinct_id => $products)
                  @php
                      $countProd = $products->count();
                  @endphp
                    @foreach ($products as $key => $prod)
                      <tr>

                        @if(!$key)
                          <td rowspan="{{ $countProd }}">
                            {{ __('labels.u302.category', ['attr' => $prod->mProduct->mDistinction->name]) }}<br />
                            ({{ __('labels.u302.number_items') }}{{ $countProd }})
                          </td>
                        @endif
                        <td>{{ $prod->mProduct->name }}</td>
                        @if(!$key)
                          <td rowspan="{{ $countProd }}" class="center">
                            <input type="checkbox" {{ $prod->is_apply ? 'checked' : '' }} data-distinct_id="{{ $prod->mProduct->m_distinction_id }}" class="cb_distinction" name="distinct_apply[{{ $prod->mProduct->m_distinction_id }}]"/>
                          </td>
                        @endif
                      </tr>
                    @endforeach
                @endforeach
                <tr>
                  <td colspan="3" class="right">{{ __('labels.u302.number_category_regis') }}
                    <span class="totalDistinctSelected">{{ $totalProdDistinct }}</span>
                    {{ __('labels.u302.number_prod_service') }}<span id="totalProductSelected"></span>
                    <input type="hidden" name="total_distinction">
                  </td>
                </tr>
                @if($appTrademark->pack == App\Models\AppTrademark::PACK_A)
                  <tr>
                    <th colspan="2" class="right">
                      <span>
                        {{ __('labels.u302.req_mailing_regis_cert') }}
                      </span>
                      <input type="checkbox" id="product__mailing_regis_cert_input" name="is_mailing_register_cert" value="1" class="cb_mailing_regis_cert" dname="regist_a" {{ ($registerTrademark && $registerTrademark->mailing_register_cert_fee) || $appTrademark->is_mailing_regis_cert ? 'checked' : ''}} />
                      <br />
                    </th>
                    <td class="right">
                      <span id="product__mailing_regis_cert_fee"></span>
                    </td>
                  </tr>
                  <tr>
                    <th colspan="2" class="right">{{ __('labels.u302.patent_office_fee') }}（<span class="totalDistinctSelected">{{ $totalProdDistinct }}</span>区分）　{{ __('labels.u302.registration_period') }}
                      <select id="period_registration_select" name="period_registration" {{ $regis10Years ? 'disabled' : ''}}>
                        <option value="1"  {{ $registerTrademark && $registerTrademark->period_registration == PERIOD_REGISTRATION_FIVE_YEAR ? 'selected' : '' }}>{{ __('labels.u302.five_years') }}</option>
                        <option value="2" {{ $registerTrademark && $registerTrademark->period_registration == PERIOD_REGISTRATION_TEN_YEAR ? 'selected' : '' }}>{{ __('labels.u302.ten_years') }}</option>
                      </select>
                    </th>
                    <td style="width:15%;" class="right">
                      <span class="period_registration_fee"></span>
                    </td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div><!-- /scroll wrap -->
          @if(
                ($appTrademark->pack == App\Models\AppTrademark::PACK_B
                || $appTrademark->pack == App\Models\AppTrademark::PACK_C)
              )
          <div class="js-scrollable eol">
            <table class="normal_b w640">
                @if(!$appTrademark->is_mailing_regis_cert)
                  <tr>
                    <th class="right">{{ __('labels.u302.req_mailing_regis_cert') }}
                      <input
                          type="checkbox"
                          name="is_mailing_register_cert"
                          value="1"
                          class="cb_mailing_regis_cert {{ $appTrademark->is_mailing_regis_cert ? 'disabled' : '' }}"
                          {{ $appTrademark->is_mailing_regis_cert ? 'disabled' : '' }}
                          {{ ($registerTrademark && $registerTrademark->period_registration == PERIOD_REGISTRATION_FIVE_YEAR) ? 'checked' : '' }}
                      />
                    </th>
                    <td class="right"> <span id="is_mailing_register_cert_price">0</span>円</td>
                  </tr>
                @endif

                <tr>
                  <th class="right">{{ __('labels.u302.patent_office_fee') }}（<span class="totalDistinctSelected">
                      {{ $totalProdDistinct }} </span>区分）　{{ __('labels.u302.change_5_to_10_yrs') }}
                      <input
                          type="checkbox"
                          class="cb_period_registration"
                          name="period_registration"
                          dname="regist_a"
                          value="2"
                          {{ $regis10Years ? 'disabled' : '' }}
                          {{ $registerTrademark && $registerTrademark->period_registration == PERIOD_REGISTRATION_TEN_YEAR ? 'checked': '' }}
                      />
                  </th>
                  <td class="right">
                    <span class="period_registration_fee">
                      @if($regis10Years)
                        支払済
                      @else
                        {{ $registerTrademark && $registerTrademark->period_change_fee ? CommonHelper::formatPrice($registerTrademark->period_change_fee) : CommonHelper::formatPrice(($registerTermChange->pof_1st_distinction_10yrs ?? 0) * $totalProdDistinct, '円', 0) }}
                      @endif
                    </span>
                    <input type="hidden" name="period_change_fee" value="{{ $registerTermChange->pof_1st_distinction_10yrs * $totalProdDistinct }}">
                  </td>
                </tr>

                <tr>
                  <th class="right">{{ __('labels.u302.regis_period_change_fee') }}</th>
                  <td class="right">
                    <span id="reg_period_change_fee">0</span> 円
                    <input type="hidden" name="reg_period_change_fee" value="0">
                  </td>
                </tr>
                <tr>
                  <th class="right">{{ __('labels.u302.total') }}</th>
                  <td class="right">
                    <span class="total_amount_text">{{ CommonHelper::formatPrice($payment->total_amount ?? 0) }}</span>円</td>
                </tr>
            </table>
          </div><!-- /scroll wrap -->
          @endif
{{--          @if($appTrademark->pack == App\Models\AppTrademark::PACK_A)--}}
            <div style="display: none;" id="mailing_registration_info_form">
              <h3>{{ __('labels.u302.address_send_regis_cert') }}</h3>
              <dl class="w10em eol clearfix" >
                <dt>{{ __('labels.u302.country_region_name') }}<span class="red">*</span></dt>
                <dd>
                  <select name="regist_cert_nation_id">
                    @foreach ($nations as $key => $item)
                      <option {{ isset($registerTrademark->regist_cert_nation_id) && $registerTrademark->regist_cert_nation_id == $key ? 'selected' : '' }} value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                  </select>
                </dd>
                <dt>{{ __('labels.u302.postal_code') }}<span class="red">*</span></dt>
                <dd><input type="text" name="regist_cert_postal_code" class="em08" value="{{ $registerTrademark->regist_cert_postal_code ?? '' }}"/></dd>
                <dt>{{ __('labels.u302.address') }}<span class="red">*</span></dt>
                <dd><input type="text" name="regist_cert_address" class="em36" value="{{ $registerTrademark->regist_cert_address ?? ''}}" /></dd>
                <dt>{{ __('labels.u302.destination_name') }}<span class="red">*</span></dt>
                <dd><input type="text" name="regist_cert_payer_name" class="em24" value="{{ $registerTrademark->regist_cert_payer_name ?? '' }}" /></dd>
              </dl>
            </div>
{{--          @endif--}}
          <!-- / パック用のブルー行として、テスト的に独立したテーブルを設置 -->

          <br />
          <hr />
          <p>{{ __('labels.u302.attention_8') }}</p>

          <table class="normal_b mw480 mb15">
            @foreach ($trademarkInfos as $key => $trademarkInfo)
            <tr>
              <th class="left" style="width:6em;">{{ __('labels.u302.applicant_name') }} {{ $key > 1 ? '-' . $key : '' }}</th>
              <td>{{ $trademarkInfo->name ?? '' }}</td>
            </tr>
            @endforeach
          </table>

          <p>{{ __('labels.u302.attention_9') }}
            {{ __('labels.u302.attention_10') }}
            {{ __('labels.u302.attention_11') }}<a href="{{ route('user.qa.02.qa') }}" target="_blank">Q&amp;A</a>{{ __('labels.u302.attention_12') }}
          </p>

          <table class="normal_b mw480">
            <tr>
              <th></th>
              <th class="center" style="width:6em;">{{ __('labels.u302.applicant_name_2') }}</th>
              <th class="center" style="width:6em;">{{ __('labels.u302.address') }}</th>
              <th class="center" style="width:6em;">{{ __('labels.u302.name_address') }}</th>
            </tr>
            @foreach ($trademarkInfos as $key => $trademarkInfo)
              <tr>
                <td>{{ $trademarkInfo->name ?? '' }}</td>
                <td class="center"><input type="radio" data-trademark_info_id="{{ $trademarkInfo->id }}" class="change_trademark_info" data-type="trademark_info_name" name="change_trademark_info" {{ $registerTrademark && $registerTrademark->trademark_info_change_status == 1 ? 'checked' :  ''}} value="1" ></td>
                <td class="center"><input type="radio" data-trademark_info_id="{{ $trademarkInfo->id }}" class="change_trademark_info" data-type="trademark_info_address" name="change_trademark_info" {{ $registerTrademark && $registerTrademark->trademark_info_change_status == 2 ? 'checked' :  ''}} value="2"></td>
                <td class="center"><input type="radio" data-trademark_info_id="{{ $trademarkInfo->id }}" class="change_trademark_info" data-type="trademark_info_name_address" name="change_trademark_info" {{ $registerTrademark && $registerTrademark->trademark_info_change_status == 3 ? 'checked' :  ''}} value="3"></td>
              </tr>
            @endforeach
          </table>
          <input type="button" class="btn_d small mb15 clear_trademark_info" value="{{ __('labels.clear') }}">

          <p>{{ __('labels.u302.attention_13') }}</p>

          <div id="change_address_tbl" style="display: none">
            <h3>{{ __('labels.u302.address_change') }}</h3>
            <table class="normal_b mw640 eol">
              <tr>
                <th style="width:79%;" class="center">{{ __('labels.u302.changes') }}</th>
                <th class="center">{{ __('labels.u302.commission') }}</th>
              </tr>
              <tr>
                <td>
                  <strong >{{ __('labels.u302.address_time_app') }}<span id="address_infor"></span></strong><br />
                  {{ __('labels.u302.address_after_change') }} <span class="input_note">{{ __('labels.u302.attention_14') }}</span><br />
                  {{ __('labels.u302.country_region_name') }} <span class="red">*</span>：
                  <span>
                    <select name="trademark_info_nation_id">
                      @foreach ($nations as $key => $item)
                        <option {{ $registerTrademark->trademark_info_nation_id == $key ? 'selected' : '' }} value="{{ $key }}">{{ $item }}</option>
                      @endforeach
                    </select>
                  </span>
                  <br />
                  <span id="trademark_info_m_prefecture">
                    <span>
                      {{ __('labels.u302.address_1') }} </span><span class="red">*</span>：
                      <span>
                        <select name="trademark_info_m_prefecture_id" id="trademark_info_m_prefecture_id">
                          @foreach ($prefectures as $key => $item)
                            <option {{ $registerTrademark->trademark_info_address_first == $key ? 'selected' : '' }} value="{{ $key }}">{{ $item }}</option>
                          @endforeach
                        </select>
                      </span>
                      <br />
                  </span>
                  <div id="trademark_info_address_second_box" style="display:flex">
                    <div>
                      {{ __('labels.u302.address_2') }} <span class="red">*</span>：
                    </div>
                    <div>
                      <input name="trademark_info_address_second" type="text" class="em30" value="{{ $registerTrademark->trademark_info_address_second ?? '' }}" />
                    </div>
                  </div>
                  <div style="display:flex">
                    <div>
                      <span>
                        {{ __('labels.u302.address_3') }}
                      </span>
                    </div>
                    <div>
                      <input name="trademark_info_address_three" type="text" class="em30" value="{{ $registerTrademark->trademark_info_address_three ?? '' }}" />
                    </div>
                  </div>
                </td>
                <td class="right">
                  <span>
                    {{ CommonHelper::formatPrice($changeAddressFee['cost_service_base'] ?? 0) }}
                  </span>円
                </td>
              </tr>
            </table>
          </div>

          {{-- Change name --}}
          <div id="change_name_tbl" style="display: none">
            <h3>{{ __('labels.u302.app_name_change') }}</h3>
            <table class="normal_b mw640 eol" >
              <tr>
                <th style="width:79%;" class="center">{{ __('labels.u302.changes') }}</th>
                <th class="center">{{ __('labels.u302.commission') }}</th>
              </tr>
              <tr>
                <td>
                  <strong>{{ __('labels.u302.decision_registration') }}<span id="trademark_info_address_full"> {{ __('labels.u302.ABC_Trading') }}</span></strong>
                  <br />
                  {{ __('labels.u302.attention_15') }}<br />
                  <input type="text" name="trademark_info_name"  class="em30" value="{{ $registerTrademark && $registerTrademark->trademark_info_name ? $registerTrademark->trademark_info_name : '' }}" />
                </td>
                <td class="right">
                  <span>{{ CommonHelper::formatPrice($changeNameFee['cost_service_base'] ?? 0) }}</span>円
                </td>
              </tr>
            </table>
          </div>
          <hr />

          {{-- Payer info --}}
          @if(!$hideButtonSubmit)
            @include('user.components.payer-info', [
                'prefectures' => $prefectures ?? [],
                'nations' => $nations ?? [],
                'paymentFee' => $paymentFee ?? null,
                'payerInfo' => $payerInfo ?? null,
            ])
            <ul class="footerBtn clearfix">
              <li>
                <input type="button" value="{{ $appTrademark->pack != App\Models\AppTrademark::PACK_A ? __('labels.u302.proceed') : __('labels.u302.proceed_app_content') }}" class="btn_e big redirect_to_common_payment" />
              </li>
            </ul>
            <ul class="footerBtn clearfix">
              <li><input type="button" value="{{ __('labels.u302.save_draft') }}" class="btn_a" id="redirect_to_anken_top"/></li>
            </ul>
            <ul class="footerBtn clearfix">
              <li>
                  <a style="height: 38px; width: 275px; padding:0;line-height: 38px;text-align: center;" class="btn_a" href="{{ route('user.registration.cancel', $registerTrademark->id) }}">
                    {{ __('labels.u302.cancel_btn') }}
                  </a>
                </li>
            </ul>
          @else
            <p>{{ __('labels.u302.attention_17') }}</p>
            <p> {{ __('labels.u302.attention_18') }}</p>
          @endif

          <input type="hidden" name="redirect_to" value="{{ COMMON_PAYMENT }}">
          <input type="hidden" name="from_page" value="{{ U302 }}">
          <input type="hidden" name="total_product_each_add" value="">
          <input type="hidden" name="cost_service_add_prod" value="{{ $productAddOnFee['cost_service_base'] }}">
          <input type="hidden" name="product_each_add_fee" value="">
          <input type="hidden" name="sub_distinct_fee" value="">
          <input type="hidden" name="change_name_fee" value="">
          <input type="hidden" name="change_address_fee" value="">
          <input type="hidden" name="trademark_info_id" value="">
          <input type="hidden" name="tax" value="">
          <input type="hidden" name="commission" value="">
          <input type="hidden" name="maching_result_id" value="{{ request()->__get('id') }}">

          <!-- estimate box -->
          @php
            $countProduct = $productsDistinct->flatten()->where('is_apply', 1)->count();
            $productAddOn = $countProduct - 3 > 0 ? $countProduct - 3 : 0;
          @endphp
          <div class="estimateBox">
            <input type="checkbox" id="cart" /><label class="button" for="cart"><span class="open">{{ __('labels.u302.view_quote') }}</span><span
                class="close">{{ __('labels.u302.close_quote') }}</span></label>
            <div class="estimateContents">
              <h3>{{ __('labels.u302.est_total_amount') }}</h3>
              <table class="normal_b">
                <tr>
                  <td>{{ __('labels.u302.regis_procedure_service_up_3_prod') }}</td>
                  <td class="right">
                    <span>{{ CommonHelper::formatPrice($regisProcedureServiceFee->base_price + $regisProcedureServiceFee->base_price * ($setting->value/100)) }}</span> 円
                    <input type="hidden" name="cost_service_base" value="{{ $regisProcedureServiceFee->base_price + $regisProcedureServiceFee->base_price * ($setting->value/100) }}">
                  </td>
                </tr>
                <tr>
                  <td>
                    <span id="total_product_each_add">{{ $productAddOn }}</span>{{ __('labels.u302.add_prod_name') }}{{ CommonHelper::formatPrice($productAddOnFee['cost_service_base'] ?? 0) }}円）
                  </td>
                  <td class="right"><span id="est_product_each_add_fee">0</span>円</td>
                </tr>
                <tr id="tr_sub_distinct">
                  <td>{{ __('labels.u302.number_reduction') }}<br />
                  </td>
                  <td class="right">{{ CommonHelper::formatPrice($reduceNumberDistinctFee['cost_service_base'] ?? 0) }}円</td>
                </tr>
                <tr id="tr_change_name_fee">
                  <td>{{ __('labels.u302.name_change_procedure') }}</td>
                  <td class="right"><span id="td_change_name_fee">{{ CommonHelper::formatPrice($changeNameFee['cost_service_base'] ?? 0) }}</span>円</td>
                </tr>
                <tr id="tr_change_address_fee">
                  <td>{{ __('labels.u302.address_change_procedure') }}</td>
                  <td class="right">{{ CommonHelper::formatPrice($changeAddressFee['cost_service_base'] ?? 0) }}円</td>
                </tr>
                <tr>
                <tr id="tr_regis_mailing_cert">
                  <td>{{ __('labels.u302.regis_cert_sending_fee') }}</td>
                  <td class="right">{{ CommonHelper::formatPrice($mailRegisterCert['cost_service_base'] ?? 0) }}円</td>
                </tr>
                <tr id="tr_reg_period_change_fee">
                  <td>{{ __('labels.u302.regis_period_change_fee') }}</td>
                  <td class="right">{{ CommonHelper::formatPrice($registerTermChange['base_price'] + $registerTermChange['base_price'] * ($setting['value']/100)) }}円</td>
                </tr>
                <tr id="tr_payment_bank_transfer">
                  <td>{{ __('labels.u302.bank_transfer_fee') }}</td>
                  <td class="right">{{ CommonHelper::formatPrice($paymentFee['cost_service_base'] ?? 0) }}円</td>
                </tr>
                <tr>
                  <th class="right">{{ __('labels.u302.subtotal') }}</th>
                  <th class="right">
                    <span id="sub_total_text">0</span>
                    <input type="hidden" name="subtotal" id="subtotal">
                    円
                  </th>
                </tr>
                <tr id="tr_commission_tax_info">
                  <th class="right" colspan="2">
                    {{ __('labels.u302.breakdown') }}<span id="commission_text">0</span>円<br />
                    <input type="hidden" name="commission" id="commission">
                    {{ __('labels.u302.tax') }}（ {{ floor($setting['value'] * 100 ?? 0)/100 }}％）　<span id="tax_price">0</span>円</th>
                    <input type="hidden" name="tax" id="tax">
                </tr>
                <tr id="tr_register_term_change">
                  <td style="width:34em;">
                      {{ __('labels.u302.cost_patent_office') }}<span id="register_change_year">（{{ $regis5Years ? '5' : '10' }}{{ __('labels.u302.year_registration') }}）</span><br />
                    　<span class="totalDistinctSelected">{{ $totalProdDistinct }}</span>区分 1区分<span id="register_term_change_one_distinct"></span>円x<span class="totalDistinctSelected">{{ $totalProdDistinct }}</span>区分</td>
                  <td class="right"><span id="register_term_change"></span>円<br /></td>
                </tr>
                <tr>
                  <th class="right">{{ __('labels.u302.total') }}</th>
                  <th class="right" nowrap>
                    <strong style="font-size:1.2em;">
                      <span class="total_amount_text">
                        0
                      </span>円
                      <input type="hidden" name="total_amount">
                    </strong>
                </th>
                </tr>
              </table>
              <p class="red mb10">{{ __('labels.u302.attention_16') }}</p>
              @if(!$hideButtonSubmit)
                <ul class="right list">
                  <li><input type="button" value="{{ __('labels.u302.recalculation') }}" class="btn_a" /></li>
                </ul>
                <ul class="right list">
                  <li><input type="button" value="{{ __('labels.u302.save_quote') }}" id="redirect_to_quote" class="btn_a" /></li>
                </ul>

                <ul class="footerBtn right clearfix">
                  <li>
                    <input type="button" value="{{ $appTrademark->pack != App\Models\AppTrademark::PACK_A ? __('labels.u302.proceed') : __('labels.u302.proceed_app_content') }}" class="btn_e big redirect_to_common_payment" />
                  </li>
                </ul>
              @endif
            </div><!-- /estimate contents -->
          </div><!-- /estimate box -->
        </form>
    </div><!-- /contents -->
    <style>
      .disabled-checkbox {
        opacity: 0.5;
        pointer-events: none;
      }
      .estimateContents table tr td {
        min-width: 100px;
      }
    </style>
@endsection
@section('footerSection')
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.registrant_information.Common_E001') }}';
        const errorMessageNameRegex = '{{ __('messages.registrant_information.Common_E016') }}';
        const errorMessageAddressRegex = '{{ __('messages.registrant_information.Common_E020') }}';
        const errorMessageIsValidRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessageIsValidInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
        const errorMessageIsValidInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
        const errorMessageInvalidCharacter = '{{ __('messages.common.errors.Register_U001_E006') }}';
        const errorMessageInvalidFormatFile = '{{ __('messages.common.errors.Common_E023') }}';
        const errorMessageInvalidCharacterRefer = '{{ __('messages.common.errors.support_U011_E002') }}';
        const errorMessageInvalidCharacterPayer = '{{ __('messages.common.errors.Common_E016') }}';
        const errorMessageInvalidCharacterPayerFurigana = '{{ __('messages.common.errors.Common_E018') }}';
        const errorMessageContentMaxLength = '{{ __('messages.general.Common_E016') }}';
        const errorMessageContentMaxLength100 = '{{ __('messages.general.Common_E020') }}';
        const errorMessageTrademarkNameInvalid = '{{ __('messages.trademark_form_information.errors.trademark_name_invalid') }}';
        const errorMessageRegisterPrecheck = '{{ __('messages.support_first_time.support_U011_E008') }}'
        const errorMessageFormat = '{{ __('messages.common.errors.Common_E020') }}';
        const errorMessageFormatName = '{{ __('messages.common.errors.Common_E016') }}';

        const Year5Registration = '{{ __('labels.u302.5_year_registration') }}';
        const Year10Registration = '{{ __('labels.u302.10_year_registration') }}';

        const routeAjaxCalculatorCart = '{{ route('user.refusal.plans.ajax-caculator') }}';
        const regisProcedureServiceFee = @json($regisProcedureServiceFee);
        const isBlockScreen = @json($isBlockScreen);
        const setting = @json($setting);
        const productsDistinct = @json($productsDistinct);
        const registerTermChange  = @json($registerTermChange );
        const productAddOnFee = @json($productAddOnFee);
        const reduceNumberDistinctFee = @json($reduceNumberDistinctFee);
        const changeNameFee = @json($changeNameFee);
        const changeAddressFee = @json($changeAddressFee);
        const mailRegisterCert = @json($mailRegisterCert);
        const paymentFee = @json($paymentFee);
        const payerInformation = @json($payerInfo);
        const appTrademark = @json($appTrademark);
        const trademarkInfos = @json($trademarkInfos);
        const mailingRegisterCertFee = @json($mailingRegisterCertFee);
        const trademarkDocuments = @json($trademarkDocuments);
        const PERIOD_REGISTRATION_FIVE_YEAR = @json(PERIOD_REGISTRATION_FIVE_YEAR);
        const PERIOD_REGISTRATION_TEN_YEAR = @json(PERIOD_REGISTRATION_TEN_YEAR);
        const PACK_A = @json(App\Models\AppTrademark::PACK_A);
        const COMMON_PAYMENT = @json(COMMON_PAYMENT);
        const QUOTE = @json(QUOTE);
        const U000ANKEN_TOP = @json(U000ANKEN_TOP);
        const routeCancel = @json(route('user.registration.cancel', $registerTrademark->id));
        const prefectures = @JSON($prefectures);
        const nations = @JSON($nations);
        const NATION_JAPAN_ID = '{{ NATION_JAPAN_ID }}';
        const hideButtonSubmit = @JSON($hideButtonSubmit);
        if(payerInformation) {
            callClear = false
        }
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/registration/distinction-table.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/registration/u302.js') }}"></script>
@endsection
