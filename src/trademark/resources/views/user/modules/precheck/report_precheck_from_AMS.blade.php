@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>プレチェックサービス：AMSからのレポート</h2>
        <form>
            <h3>【商標情報】</h3>
            <div class="info">
                <table class="info_table">
                    <tr>
                        <th>お客様整理番号</th>
                        <td>{{ $precheck->trademark->reference_number ?? '' }}</td>
                        <th nowrap>お申込みプラン</th>
                        <td>
                            @if ($precheck->pack == 1)
                                パックA
                            @elseif ($precheck->pack == 2)
                                パックB
                            @elseif ($precheck->pack == 3)
                                パックC
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>申込番号</th>
                        <td> {{ $precheck->trademark->trademark_number ?? '' }} </td>
                        <th>申込日</th>
                        <td>{{ isset($precheck->trademark->created_at) ? \Carbon\Carbon::parse($precheck->trademark->created_at)->format('Y/m/d') : '' }}</td>
                    </tr>
                    <tr>
                        <th>商標出願種別</th>
                        <td colspan="3">{{ isset($precheck->trademark->type_trademark) ? \App\Models\Trademark::listTradeMarkTypeOptions()[$precheck->trademark->type_trademark] : ''  }}</td>
                    </tr>
                    <tr>
                        <th>商標名</th>
                        <td colspan="3">{{ $precheck->trademark->name_trademark ?? '' }}</td>
                    </tr>
                    <tr>
                        <th nowrap>装飾文字/ロゴ絵柄の画像</th>
                        <td colspan="3">
                            @if($precheck->trademark->image_trademark)
                                <a href="{{ asset($precheck->trademark->image_trademark) }}" target="_blank"><img src="{{ asset($precheck->trademark->image_trademark) }}" class="logo_trademark">{{ __('labels.trademark_info.click_to_enlarge') }} >></a>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <!-- /info -->

            <hr />

            <h3>【プレチェックレポート】</h3>
            <p>プレチェックが完了しましたのでご報告致します。よろしければ、出願申込にお進みください。<br />
                ｘｘｘｘのサービス名はｘｘｘｘｘの写しの提出が必要となります。
            </p>
            <p>
                ＜表の見方＞
                <span id="toggle-btn" style="cursor: pointer;">+</span>
                <div id="toggle-example">
                    今回お申込み頂いた商品名については赤字で表示しています。<br />
                    ～詳細～<br />
                    ○　「識別力」「同一・類似」から見て、登録可能性が高い。<br />
                    △　少し懸念はあるが、「識別力」「同一・類似」から見て、登録に期待が持てる。<br />
                    ▲　「識別力」「同一・類似」から見て、このままでは、この商品名での登録可能性は低い。<br />
                    ×　「識別力」「同一・類似」から見て、このままでは、この商品名での登録は難しい。<br />
                    登録の可能性：A＞B＞C＞D<br />
                    ～簡易～<br />
                    有　登録済みまたは出願中の商標と同一のものがあります。<br />
                    無　登録済みまたは出願中の商標と同一のものはみつかりませんでした。
                </div>
            </p>

            <div class="js-scrollable mb20 highlight">
                <table class="normal_b">
                    <tr>
                        <th rowspan="2" class="em04">区分</th>
                        <th rowspan="2" class="bg_green">商品・サービス名</th>
                        <th>簡易</th>
                        <th colspan="3">詳細</th>
                        <th rowspan="2" class="bg_green">申し込む<br /><label><input type="checkbox" checked class="all-checkbox" />
                                全て選択</label></th>
                    </tr>
                    <tr>
                        <th class="em04 bg_whitesmoke">同一</th>
                        <th class="em07">(1)識別力</th>
                        <th class="em07">(2)同一・類似</th>
                        <th class="em05 bg_whitesmoke">(1)+(2)</th>
                    </tr>
                    <div class="error-m-product-id"></div>
                    @error('m_product_ids[]')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                    @foreach ($getProductOfDistinction as $distinction => $products)
                        @foreach ($products as $key => $item)
                            <tr>
                                @if ($key == 0)
                                    <td rowspan="{{ $products->count() > 0 ? $products->count() : '' }}"
                                        class="bg_blue inv_blue">{{ $distinction }}</td>
                                @endif
                                <td class="bg_green">{{ $item->name }}</td>
                                @if ($precheck->type_precheck == \App\Models\Precheck::TYPE_PRECHECK_SIMPLE_REPORT)
                                    <td class="center">
                                        @if ($item->precheckResults->isNotEmpty($item->precheckResults) &&
                                            $item->precheckResults->max('result_similar_simple'))
                                            {{ \App\Models\PrecheckResult::listResultSmilarSimpleOptions()[$item->precheckResults->max('result_similar_simple')] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="center bg_gray">-</td>
                                    <td class="center bg_gray">-</td>
                                    <td class="center">-</td>
                                @elseif ($precheck->type_precheck == \App\Models\Precheck::TYPE_PRECHECK_DETAILED_REPORT)
                                    <td class="center">-</td>
                                    @if ($item->precheckResults->count() > 0)
                                        <td class="center bg_gray">
                                            {{ $item->precheckResults->max('result_identification_detail') ? \App\Models\PrecheckResult::listResultIdentificationDetailOptions()[$item->precheckResults->max('result_identification_detail')] : '-' }}
                                        </td>
                                        <td class="center bg_gray">
                                            {{ $item->precheckResults->max('result_similar_detail') ? \App\Models\PrecheckResult::listResultSimilarDetailOptions()[$item->precheckResults->max('result_similar_detail')] : '-' }}
                                        </td>
                                        <td class="center">
                                            {{ $item->precheckResults->max('result_similar_detail') ? \App\Models\PrecheckResult::getResultDetailPrecheck($item->precheckResults->max('result_identification_detail'), $item->precheckResults->max('result_similar_detail')) : '-' }}
                                        </td>
                                    @else
                                        <td class="center bg_gray">-</td>
                                        <td class="center bg_gray">-</td>
                                        <td class="center">-</td>
                                    @endif
                                @endif
                                <td class="center bg_green">
                                    <input type="checkbox" name="m_product_ids[]"
                                        {{ $item->prechecks[0]->pivot->is_register_product == \App\Models\PrecheckProduct::IS_PRECHECK_PRODUCT ? 'checked' : '' }}
                                        class="single-checkbox single-checkbox-{{ $item->id }}"
                                        value="{{ $item->id }}" data-name-distinction="{{ $distinction }}" />
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    <tr>
                        <td colspan="7" class="right">
                            出願対象区分数：<span class="total-dis"></span>　出願対象商品・サービス名数：<span class="total-checkbox-checked"></span>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- /scroll wrap -->

            <p>
                {{-- <a href="u020b.html">←</a> --}}
                <input type="submit" value="AI検索の提案を受け商品・サービス名の追加を検討" class="btn_f" />
            </p>

            <p>
                <input type="submit" value="過去に登録となった商品・サービス名を参照し追加を検討"
                    onclick="window.open('u031past.html','subwin','width=640,height=640,scrollbars=yes');return false;"
                    class="btn_f" />
            </p>

            <p><input type="submit" value="ご自身で直接商品・サービス名を追加して出願" class="btn_a" />
                {{-- <a href="u031edit_with_number.html">→</a> --}}
                <br />
                <span class="note">※プレチェックサービスが受けられなくなります。<br />
                    ※お得なセットプランのご購入ができなくなります。<br />
                    ※「直接商品・サービス名を入力」されると、特許庁の審査において、<br />
                    商品・サービス名が不明確である等の拒絶理由になるおそれがあります。<br />
                    ※AI検索がご利用できなくなくなります。</span>
            </p>

            <p>上記の結果を受けて、別の商標名で検討される場合、以下ボタンよりお進みください。<br />
                なお、検討されていた商品・サービス名は引き継がれますが、新たなお申込みになります。</p>

            <p>
                <input type="submit" value="別の商標名で申し込む" class="btn_a" />
                {{-- <a href="u021c.html">→</a> --}}
            </p>
            <br />

            <p class="note">※AMSでは、特許庁が公開しているデータベースを元にプレチェックレポートを作成しております。<br />
                特許庁が公開しているデータベースは、数ヶ月程度のタイムラグが発生する他、意図しないデータの欠損等の可能性があります。このような理由により、他人の商標と同一、類似であるとされることや、AMSのレポートで指摘した同一・類似が解消している場合があります。
            </p>
            <p class="note">※このレポートは作成時点におけるものです。時間が経過することによって結果が異なる可能性があります。</p>
            <p class="note">※このレポートにおいて、識別力、類似の判断が弊所と特許庁で異なる場合があり、必ずしも上記の結果にならない場合もあります。</p>
            <p class="note eol">※審査の過程で特許庁から使用の意思を確認されることがあります。</p>

            <hr />

            <h3>【プラン選択】</h3>
            <table class="normal_b eol">
                <tr>
                    <td style="width:34em;"><input type="radio" name="pack" id="package_a" value="1" class="type_package"/> <span
                            id="name_package_a">パックA</span>（3商品名まで）　<span
                            id="price_package_a">{{ $pricePackage[0][0]['base_price_multiplication_tax'] }}</span>円<br />
                        出願手続きのみのプランです。<br />
                        4商品名以降、追加3商品名ごとに<span
                            id="price_product_add_pack_a">{{ $pricePackage[1][0]['base_price_multiplication_tax'] }}</span>円がかかります。<br />
                        ※登録時にも、特許庁への印紙代が別途発生します。</td>
                </tr>
                <tr>
                    <td><input type="radio" name="pack" id="package_b" value="2" class="type_package"/> <span id="name_package_b">パックB</span>
                        （3商品名まで）<span
                            id="price_package_b">{{ $pricePackage[0][1]['base_price_multiplication_tax'] }}</span>円<br />
                        出願手続きと登録手続きがセットになったプランです。<br />
                        4商品名以降、追加3商品名ごとに<span
                            id="price_product_add_pack_b">{{ $pricePackage[1][1]['base_price_multiplication_tax'] }}</span>円がかかります。
                    </td>
                </tr>
                <tr>
                    <td><input type="radio" name="pack" id="package_c" value="3" class="type_package" checked/> <span id="name_package_c">パックC</span>
                        （3商品名まで）<span
                            id="price_package_c">{{ $pricePackage[0][2]['base_price_multiplication_tax'] }}</span>円<br />
                        出願手続きと登録手続きに加え、拒絶理由通知対応がセットになったプランです。<br />
                        4商品名以降、追加3商品名ごとに<span id="price_product_add_pack_c">
                            {{ $pricePackage[1][2]['base_price_multiplication_tax'] }}</span>円がかかります。</td>
                </tr>
            </table>

            <hr />

            <h3>【登録証の郵送】</h3>
            <p class="eol"><input type="checkbox" name="is_mailing_register_cert" id="is_mailing_register_cert" value="1"/>登録証の郵送を希望する。<br />
                <span class="note">※別途手数料（{{ $mailRegisterCert->base_price_multiplication_tax }}円）が発生します。<br />
                    ※希望されない場合、登録証はPDFでのご提供となります。</span>
            </p>

            <hr />

            <h3>【登録期間】</h3>
            <p class="eol"><input type="checkbox" name="period_registration"
                    id="period_registration" value="2"/>10年登録にする。（チェックがない場合、登録期間は5年です）<br />
                <span class="note">※登録時に5年登録を10年登録に期間変更できますが、別途手数料（{{ $periodRegistration->base_price_multiplication_tax }}円）が発生します。</span>
            </p>

            <hr />

            @include('user.modules.common.trademark_registrant_information', [
                'information' => $information,
                'nations' => $nations,
                'prefectures' => $prefectures,
            ])

            <hr />

            @include('user.modules.common.payer_infor')

            <hr />


            <ul class="footerBtn clearfix">
                <li><input type="submit" value="この内容で申込む" class="btn_e big" /><a href="u000common_payment.html">→</a>
                </li>
            </ul>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="再度プレチェックサービスへ" class="btn_b" /><a href="u021n.html">→</a></li>
            </ul>


            <ul class="btn_left eol">
                <li><input type="submit" value="出願をやめる" class="btn_a" /><a href="u032cancel.html">→</a></li>
                <li><a href="u000anken_top.html">←</a><input type="submit" value="保存して案件トップへ戻る" class="btn_a" /></li>
            </ul>

            <!-- estimate box -->
            <div class="estimateBox">
                <input type="checkbox" id="cart" /><label class="button" for="cart"><span
                        class="open">お見積金額を見る</span><span class="close">お見積金額を閉じる</span></label>

                <div class="estimateContents">

                    <h3>{{ __('labels.list_change_address.cart.text_3') }}</h3>
                    <table class="normal_b">
                        <tr>
                            <td><span class="red">オススメ！！</span>パックC<br />（3商品名まで、商標出願＋拒絶理由通知対応＋登録手続）</td>
                            <td class="right"><span class="cost_service_base"></span>円</td>
                        </tr>
                        <tr>
                            <td style="width:34em;">追加　4商品（3商品名ごと4,400円）</td>
                            <td class="right"><span class="cost_service_add_prod"></span>円<br /></td>
                        </tr>
                        <tr class="tr_cost_bank_transfer">
                            <td>銀行振込による手数料</td>
                            <td class="right">
                                <span class="cost_bank_transfer"></span>円
                            </td>
                        </tr>
                        <tr>
                            <th class="right"><strong>7商品名　小計</strong></th>
                            <th class="right"><strong><span class="subtotal"></span>円</strong></th>
                        </tr>
                        <tr>
                            <th class="right" colspan="2">
                                内訳：実手数料　<span class="commission"></span>円<br />
                                消費税（<span class="tax_percentage"></span>％）　<span class="tax"></span>円</th>
                        </tr>
                        <tr>
                            <td style="width:34em;">特許庁への費用（出願料）　4区分<br />
                                1区分12,000円+8,600円x3区分</td>
                            <td class="right"><span class="cost_is_mailing_register_cert"></span>円<br /></td>
                        </tr>
                        <tr>
                            <td style="width:34em;">特許庁への費用（5年登録）<br />
                                4区分 1区分16,400円x4区分</td>
                            <td class="right"><span class="cost_period_registration"></span>円<br /></td>
                        </tr>
                        <tr>
                            <th class="right"><strong style="font-size:1.2em;">合計：</strong></th>
                            <th class="right" nowrap><strong style="font-size:1.2em;"><span class="total"></span>円</strong></th>
                        </tr>
                    </table>
                    <p class="red mb10">※いかなる理由に関わらず、お申込み後の返金は一切ございません。</p>

                    <ul class="right list">
                        <li><input type="submit" value="再計算" class="btn_a getInfoPayment" /></li>
                    </ul>

                    <ul class="right list">
                        <li><input type="submit" value="保存・見積書表示" class="btn_a" /></li>
                    </ul>

                    <ul class="footerBtn right clearfix">
                        <li><input type="submit" value="この内容で申込む" class="btn_e big" /><a href="u031b.html">→</a></li>
                    </ul>

                </div>
                <!-- /estimate contents -->
            </div>
            <!-- /estimate box -->
        </form>
    </div>
    <!-- /contents -->
@endsection

@section('script')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script src="{{ asset('end-user/common/registrant_information/js/index.js') }}"></script>
    <script src="{{ asset('end-user/payer_infos/js/index.js') }}"></script>
    <script src="{{ asset('end-user/prechecks/precheck/index.js') }}"></script>
    <script>
        const routeAjaxGetInfoPayment = '{{ route('user.ajax-get-cart-payment') }}';
        const nationJPId  = '{{ NATION_JAPAN_ID }}';
        const typePrecheckDetailedReport  = '{{ \App\Models\Precheck::TYPE_PRECHECK_DETAILED_REPORT }}';
        const errorMessageRequired = '{{ __('messages.registrant_information.Common_E001') }}';
        const errorMessageNameRegex = '{{ __('messages.registrant_information.Common_E016') }}';
        const errorMessageAddressRegex = '{{ __('messages.registrant_information.Common_E020') }}';
        const label_type_acc = '{{ __('labels.registrant_information.form.type_acc') }}'
        const label_type_acc_1 = '{{ __('labels.registrant_information.form.type_acc_1') }}'
        const label_type_acc_2 = '{{ __('labels.registrant_information.form.type_acc_2') }}'
        const label_name = '{{ __('labels.registrant_information.form.name') }}'
        const label_m_nation_id = '{{ __('labels.registrant_information.form.m_nation_id') }}'
        const label_select_default = '{{ __('labels.registrant_information.select_default') }}'
        const label_m_prefecture_id = '{{ __('labels.registrant_information.form.m_prefecture_id') }}'
        const label_select_default2 = '{{ __('labels.registrant_information.select_default2') }}'
        const label_address_second = '{{ __('labels.registrant_information.form.address_second') }}'
        const label_note_address_second = '{{ __('labels.registrant_information.form.note_address_second') }}'
        const label_address_three_1 = '{{ __('labels.registrant_information.form.address_three_1') }}'
        const label_address_three_2 = '{{ __('labels.registrant_information.form.address_three_2') }}'
        const label_note_address_three = '{{ __('labels.registrant_information.form.note_address_three') }}'
        $('body').on('click', '.click_append', function() {
            let lengthAppend = $('.registrant_information').length

            $(".append_html").append(`
                <hr>
                <dl class="w16em clearfix registrant_information">
                    <dt>${label_type_acc} <span class="red">*</span></dt>
                    <dd class="eTypeAcc">
                        <ul class="r_c clearfix fTypeAcc">
                            <li>
                                <label><input type="radio" class="data-type_acc" name="data[${lengthAppend}][type_acc]" value="1" /> ${label_type_acc_1}</label>
                            </li>
                            <li>
                                <label><input type="radio" class="data-type_acc" name="data[${lengthAppend}][type_acc]" value="2" /> ${label_type_acc_1}</label>
                            </li>
                        </ul>
                    </dd>

                    <dt>${label_name} <span class="red">*</span></dt>
                    <dd><input type="text" class="data-name" name="data[${lengthAppend}][name]" /></dd>

                    <dt>${label_m_nation_id}<span class="red">*</span></dt>
                    <dd class="eNation">
                        <select name="data[${lengthAppend}][m_nation_id]" class="data-m_nation_id" id="m_nation_id">
                            <option value="">${label_select_default}</option>
                            @if(isset($nations) && count($nations))
                            @foreach ($nations as $k => $nation)
                                <option value="{{ $k }}" {{ old('m_nation_id') == $k ? "selected" : "" }}>{{ $nation ?? '' }}</option>
                            @endforeach
                            @endif
                        </select>
                    </dd>

                    <dt>${label_m_prefecture_id}<span class="red">*</span></dt>
                    <dd class="ePerfecture">
                        <select name="data[${lengthAppend}][m_prefecture_id]" class="data-m_prefecture_id" id="m_prefecture_id">
                            <option value="">${label_select_default2}</option>
                            @if (isset($prefectures) )
                                @foreach ($prefectures as $k => $item)
                                <option value="{{ $k }}" {{ old('m_prefecture_id') == $k ? "selected" : "" }}>{{ $item }}</option>
                                @endforeach
                            @endif
                        </select>
                    </dd>

                    <dt>${label_address_second}<span class="red">*</span></dt>
                    <dd>
                        <input type="text" class="data-address_second em30" name="data[${lengthAppend}][address_second]" /><br />
                        <span class="input_note">${label_note_address_second}</span>
                    </dd>

                    <dt>
                        ${label_address_three_1}<br />
                        ${label_address_three_2}
                    </dt>
                    <dd>
                        <input type="text" class="data-address_three em30" name="data[${lengthAppend}][address_three]" /><br />
                        <span class="input_note">${label_note_address_three}</span>
                    </dd>
                </dl>
            `);

            if (lengthAppend == 4) {
                $('.click_append').remove()
            }
        })
    </script>
@endsection
