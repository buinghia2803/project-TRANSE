@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">


        <h2>出願：お申込み</h2>

        <form id="form" class="form-validate">
            <h3>【商標情報】</h3>

            <div class="info">
                <table class="info_table">
                    <tr>
                        <th>お客様整理番号</th>
                        <td>あ123456-B</td>
                        <th nowrap>お申込みプラン</th>
                        <td>-</td>
                    </tr>
                    <tr>
                        <th>申込番号</th>
                        <td>1608005</td>
                        <th>申込日</th>
                        <td>2016/8/10</td>
                    </tr>
                    <tr>
                        <th>商標名</th>
                        <td colspan="3">とってもオレンジ</td>
                    </tr>
                    <tr>
                        <th>商標出願種別</th>
                        <td colspan="3">標準文字/それ以外</td>
                    </tr>
                    <tr>
                        <th>商標名</th>
                        <td colspan="3">とってもオレンジ</td>
                    </tr>
                    <tr>
                        <th nowrap>装飾文字/ロゴ絵柄の画像</th>
                        <td colspan="3"><a href="#"><img src="images/thumbnail.png">クリックして拡大 >></a></td>
                    </tr>
                </table>
            </div><!-- /info -->


            <hr />


            <h3>【区分、商品・サービス名】</h3>

            <p>出願を希望する商品・サービス名を選択してください。
            </p>

            <table class="normal_b mb15">
                <tr>
                    <th>区分</th>
                    <th class="em34">商品・サービス名</th>
                    <th>出願する</th>
                </tr>
                <tr>
                    <td nowrap>第6類</td>
                    <td>ゴム製栓 ゴム製ふた</td>
                    <td class="center"><input type="checkbox" checked /></td>
                </tr>
                <tr>
                    <td nowrap rowspan="2">第20類</td>
                    <td>木製、竹製又はプラスチック製の包装用容器</td>
                    <td class="center"><input type="checkbox" checked /></td>
                </tr>
                <tr>
                    <td>コルク製栓</td>
                    <td class="center"><input type="checkbox" checked /></td>
                </tr>
                <tr>
                    <td nowrap>第25類</td>
                    <td>被服</td>
                    <td class="center"><input type="checkbox" checked /></td>
                </tr>
                <tr>
                    <td nowrap rowspan="4">第32類</td>
                    <td>果実飲料</td>
                    <td class="center"><input type="checkbox" checked /></td>
                </tr>
                <tr>
                    <td>清涼飲料</td>
                    <td class="center"><input type="checkbox" checked /></td>
                </tr>
                <tr>
                    <td>乳清飲料</td>
                    <td class="center"><input type="checkbox" checked /></td>
                </tr>
                <tr>
                    <td>ｘｘｘｘｘｘｘ</td>
                    <td class="center"><input type="checkbox" checked /></td>
                </tr>
                <tr>
                    <td nowrap>第40類</td>
                    <td>ｘｘｘｘｘｘｘ</td>
                    <td class="center"><input type="checkbox" checked /></td>
                </tr>
                <tr>
                    <td colspan="3" class="right">出願対象区分数：5　出願対象商品・サービス名数：9</td>
                </tr>
            </table>

            <p><a href="u020b.html">←</a><input type="submit" value="AI検索の提案を受け商品・サービス名の追加を検討" class="btn_f" /></p>

            <p><input type="submit" value="過去に登録となった商品・サービス名を参照し追加を検討"
                    onclick="window.open('u031past.html','subwin','width=640,height=640,scrollbars=yes');return false;"
                    class="btn_f" /></p>

            <p><input type="submit" value="ご自身で直接商品・サービス名を追加して出願" class="btn_a" /><a
                    href="u031edit_with_number.html">→</a><br />
                <span class="note">※プレチェックサービスが受けられなくなります。<br />
                    ※プランのご購入ができなくなります。<br />
                    ※「直接商品・サービス名を入力」されると、特許庁の審査において、<br />
                    商品・サービス名が不明確である等の拒絶理由になるおそれがあります。<br />
                    ※AI検索がご利用できなくなくなります。</span>
            </p>

            <p>上記の結果を受けて、別の商標名で検討される場合、以下ボタンよりお進みください。<br />
                なお、検討されていた商品・サービス名は引き継がれますが、新たなお申込みになります。</p>

            <p><input type="submit" value="別の商標名で申し込む" class="btn_a" /><a href="u021c.html">→</a></p>


            <hr />


            <h3>【プラン選択】</h3>
            <table class="normal_b eol">
                <tr>
                    <td style="width:34em;"><input type="radio" name="type" checked /> パックA（3商品名まで）　2,980円<br />
                        出願手続きのみのプランです。<br />
                        4商品名以降、追加3商品名ごとに2,200円がかかります。<br />
                        ※登録時にも、特許庁への印紙代が別途発生します。</td>
                </tr>
                <tr>
                    <td><input type="radio" name="type" /> パックB（3商品名まで）　10,800円<br />
                        出願手続きと登録手続きがセットになったプランです。<br />
                        4商品名以降、追加3商品名ごとに3,300円がかかります。</td>
                </tr>
                <tr>
                    <td><input type="radio" name="type" /> パックC（3商品名まで）　19,800円<br />
                        出願手続きと登録手続きに加え、拒絶理由通知対応がセットになったプランです。<br />
                        4商品名以降、追加3商品名ごとに4,400円がかかります。</td>
                </tr>
            </table>

            <hr />

            <h3>【登録証の郵送】</h3>

            <p class="eol"><input type="checkbox" />登録証の郵送を希望する。<br />
                <span class="note">※別途手数料（3,300円）が発生します。<br />
                    ※希望されない場合、登録証はPDFでのご提供となります。</span>
            </p>

            <hr />

            <h3>【登録期間】</h3>

            <p><input type="checkbox" />10年登録にする。（チェックがない場合、登録期間は5年です）<br />
                <span class="note">※登録時に5年登録を10年登録に期間変更できますが、別途手数料（3,300円）が発生します。<br />
                    ※10年登録にした場合、5年登録に戻すことはできません。</span>
            </p>

            <hr />

            <h3>【出願人情報】</h3>
            <p class="eol">以下に出願人の情報を入力してください。なお、既に当システムに登録されている情報をコピーする場合は、以下のボタンをクリックしてください。</p>

            <dl class="w16em eol clearfix">
                <dt>当システムに登録済みの出願人</dt>
                <dd>
                    <ul>
                        <li><input type="submit" value="会員情報をコピーする" class="btn_b" /></li>
                        <li>または</li>
                        <li><input type="submit" value="AMSにて出願済みの出願人をコピーする" class="btn_b" /><a
                                href="u031past_shutsugannin.html" target="_blank">→</a><br /></li>
                    </ul>
                </dd>
            </dl>

            <dl class="w16em clearfix">

                <dt>出願人種別 <span class="red">*</span></dt>
                <dd>
                    <ul class="r_c clearfix">
                        <li><label><input type="radio" name="type" checked /> 法人</label></li>
                        <li><label><input type="radio" name="type" /> 個人</label></li>
                    </ul>
                </dd>

                <dt>出願人名 <span class="red">*</span></dt>
                <dd><input type="text" /></dd>

                <dt>出願人 所在国 <span class="red">*</span></dt>
                <dd>
                    <select name="country">
                        <option value="日本" selected>日本</option>
                        <option value="アイスランド共和国">アイスランド共和国</option>
                        <option value="アイルランド">アイルランド</option>
                        <option value="アゼルバイジャン共和国">アゼルバイジャン共和国</option>
                        <option value="アフガニスタン・イスラム共和国">アフガニスタン・イスラム共和国</option>
                        <option value="アメリカ合衆国">アメリカ合衆国</option>
                        <option value="Andorra">途中略</option>
                        <option value="ロシア連邦">ロシア連邦</option>
                        <option value="台湾">台湾</option>
                        <option value="パレスチナ">パレスチナ</option>
                        <option value="香港">香港</option>
                        <option value="マカオ">マカオ</option>
                    </select>
                </dd>

                <dt>出願人所在地または住所-1 <span class="red">*</span></dt>
                <dd><select name="" id="">
                        <option value="" selected="selected">都道府県</option>
                        <option value="北海道">北海道</option>
                        <option value="青森県">青森県</option>
                        <option value="岩手県">岩手県</option>
                        <option value="宮城県">宮城県</option>
                        <option value="秋田県">秋田県</option>
                        <option value="山形県">山形県</option>
                        <option value="福島県">福島県</option>
                        <option value="茨城県">茨城県</option>
                        <option value="栃木県">栃木県</option>
                        <option value="群馬県">群馬県</option>
                        <option value="埼玉県">埼玉県</option>
                        <option value="千葉県">千葉県</option>
                        <option value="東京都">東京都</option>
                        <option value="神奈川県">神奈川県</option>
                        <option value="新潟県">新潟県</option>
                        <option value="富山県">富山県</option>
                        <option value="石川県">石川県</option>
                        <option value="福井県">福井県</option>
                        <option value="山梨県">山梨県</option>
                        <option value="長野県">長野県</option>
                        <option value="岐阜県">岐阜県</option>
                        <option value="静岡県">静岡県</option>
                        <option value="愛知県">愛知県</option>
                        <option value="三重県">三重県</option>
                        <option value="滋賀県">滋賀県</option>
                        <option value="京都府">京都府</option>
                        <option value="大阪府">大阪府</option>
                        <option value="兵庫県">兵庫県</option>
                        <option value="奈良県">奈良県</option>
                        <option value="和歌山県">和歌山県</option>
                        <option value="鳥取県">鳥取県</option>
                        <option value="島根県">島根県</option>
                        <option value="岡山県">岡山県</option>
                        <option value="広島県">広島県</option>
                        <option value="山口県">山口県</option>
                        <option value="徳島県">徳島県</option>
                        <option value="香川県">香川県</option>
                        <option value="愛媛県">愛媛県</option>
                        <option value="高知県">高知県</option>
                        <option value="福岡県">福岡県</option>
                        <option value="佐賀県">佐賀県</option>
                        <option value="長崎県">長崎県</option>
                        <option value="熊本県">熊本県</option>
                        <option value="大分県">大分県</option>
                        <option value="宮崎県">宮崎県</option>
                        <option value="鹿児島県">鹿児島県</option>
                        <option value="沖縄県">沖縄県</option>
                    </select></dd>

                <dt>出願人所在地または住所-2 <span class="red">*</span></dt>
                <dd><input type="text" class="em30" /><br />
                    <span class="input_note">ひらがな・カタカナ・漢字・数字で入力してください（記号は全角ハイフンのみ使用可）。</span>
                </dd>

                <dt>出願人所在地または住所-3<br />（建物名）</dt>
                <dd><input type="text" class="em30" /><br />
                    <span class="input_note">全角文字で入力してください。</span>
                </dd>


            </dl>

            <p class="eol"><a href="#">+ 共同出願人情報の追加</a></p>


            <hr />

            <!--payer-info -->
            @include('user.modules.common.payer_infor')

            <hr />

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="この内容で申込む" class="btn_e big" /><a href="u000common_payment.html">→</a>
                </li>
            </ul>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="プレチェックサービス申込みへ" class="btn_b" /><a
                        href="u021.html">→</a><br />（出願前の同一または類似に関する調査）</li>
            </ul>


            <ul class="btn_left eol">
                <li><input type="submit" value="出願をやめる" class="btn_a" /></li>
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
                            <td class="right">19,800円</td>
                        </tr>
                        <tr>
                            <td style="width:34em;">追加　6商品名（3商品名ごと4,400円）</td>
                            <td class="right">8,800円<br /></td>
                        </tr>
                        <tr>
                            <th class="right"><strong>9商品名　小計</strong></th>
                            <th class="right"><strong>28,600円</strong></th>
                        </tr>
                        <tr>
                            <th colspan="2" class="right">内訳：実手数料　26,000円<br />
                                消費税（10％）　2,600円</th>
                        </tr>
                        <tr>
                            <td style="width:34em;">特許庁への費用（出願料）　5区分<br />
                                1区分12,000円+8,600円x4区分</td>
                            <td class="right">46,400円<br /></td>
                        </tr>
                        <tr>
                            <td>特許庁への費用　5区分ｘ16,400円<br />
                                （5年登録時の印紙代）</td>
                            <td class="right">82,000円</td>
                        </tr>
                        <tr>
                            <th class="right">合計：</th>
                            <th class="right" nowrap><strong style="font-size:1.2em;">157,000円</strong></th>
                        </tr>
                    </table>
                    <p class="red mb10">※いかなる理由に関わらず、お申込み後の返金は一切ございません。</p>

                    <ul class="right list">
                        <li><input type="submit" value="再計算" class="btn_a" /></li>
                    </ul>

                    <ul class="right list">
                        <li><input type="submit" value="保存・見積書表示" class="btn_a" /></li>
                    </ul>
                    <ul class="footerBtn right clearfix">
                        <li><input type="submit" value="この内容で申込む" class="btn_e big" /><a
                                href="u000common_payment.html">→</a></li>
                    </ul>

                </div><!-- /estimate contents -->

            </div><!-- /estimate box -->

        </form>

    </div><!-- /contents -->
@endsection
@section('common-payer-info-script')
    <script src="{{ asset('common/js/yubinbango.js') }}" charset="UTF-8"></script>
    <script>
        const errorMessageIsValidRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessageIsValidInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
        const errorMessageIsValidInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
        const routeGetInfoUserAjax = '{{ route('user.get-info-user-ajax') }}';
    </script>
    <script src="{{ asset('end-user/payer_infos/js/validate.js') }}"></script>
    <script src="{{ asset('end-user/payer_infos/js/index.js') }}"></script>
@endsection
