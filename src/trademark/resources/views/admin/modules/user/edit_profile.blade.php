@extends('admin.layouts.app')

@section('main-content')
   <!-- contents -->
   <div id="contents" class="normal">
        <h2>会員情報編集</h2>
        <form>
            <p>変更が必要なお客様情報を入力して、次へ進んでください。</p>
            <h3>【会員情報】</h3>
            <dl class="w18em eol clearfix">
                <dt>法人または個人</dt>
                <dd>法人
                    <font color="orange"><br />※PG：以下、法人か個人の選択により、表示項目を変更する。<br />氏名または法人名はいったん登録したら変更不可にする。</font>
                </dd>
                <dt>法人名</dt>
                <dd>ｘｘｘｘｘｘ<font color="orange">※PG：個人の場合、氏名</font><br />
                    <span class="notice">※法人名（ふりがな含む）の変更には別途ご申請が必要です。</span>
                    <font color="orange"><br />※PG：個人の場合は以下を表示<br /></font>
                    <span class="notice">※氏名（ふりがな含む）の変更には別途ご申請が必要です。</span>
                </dd>
                <dt>法人名（ふりがな）</dt>
                <dd>ｘｘｘｘｘｘ<font color="orange">※PG：個人の場合、氏名</font>
                </dd>
                <dt>法人番号</dt>
                <dd>1234567891011<font color="orange">※PG：個人の場合、不要</font><br />
                    <span class="notice">※法人番号の変更には別途ご申請が必要です。</span>
                </dd>
                <dt>国または地域名 <span class="red">*</span></dt>
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
                <dt>郵便番号（半角、ハイフンなし） <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /> <input type="submit" value="郵便番号から住所を入力" class="btn_a" />
                </dd>
                <dt>所在地または住所-1 <span class="red">*</span></dt>
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
                        <option value="東京都" selected>東京都</option>
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

                <dt>所在地または住所-2 <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" class="em30" /><br />
                    <span class="input_note">ひらがな・カタカナ・漢字・数字で入力してください（記号は全角ハイフンのみ使用可）。</span>
                </dd>

                <dt>所在地または住所-3（建物名）</dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" class="em30" /><br />
                    <span class="input_note">全角文字で入力してください。</span>
                </dd>

                <dt>電話番号（半角、ハイフンなし）<span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /></dd>

                <dt>登録メールアドレス<br />（連絡用メールアドレス-1）</dt>
                <dd>xxx@xxxx.com　<input type="submit" value="メールアドレスの変更" class="btn_c" /><a href=""></a>
                    <font color="orange"><br />PG：メールで仮登録完了したアドレスを編集不可の形で表示。</font>
                </dd>
            </dl>
            <dl class="w18em eol clearfix">
                <dt>会員ID <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /> <input type="submit" value="ID重複確認" class="btn_a" /><br />
                    <span class="input_note">※アルファベットと数字を混ぜた8文字以上30文字まで及び使用可能な記号（「-」「.」「_」「@」）。</span>
                </dd>
                <dt>会員パスワード <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /><br />
                    <span class="input_note">※アルファベットと数字を混ぜた8文字以上16文字まで。</span>
                </dd>
                <dt>会員パスワード（確認用） <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /></dd>
                <dt>性別</dt>
                <dd>男性</dd>
                <dt>生年月日</dt>
                <dd>1970年6月5日
                    <br /><span class="notice">※生年月日の変更には別途ご申請が必要です。</span>
                </dd>
                <dt>パスワード復帰用質問 <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" class="em30" /></dd>
                <dt>パスワード復帰用回答 <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" class="em30" /><br />
                    <span class="notice">※パスワード復帰時に必要になります。</span>
                </dd>
            </dl>
            <hr />
            <h3>【連絡先】　<input type="submit" value="会員情報をコピー" class="btn_a" /></h3>
            <dl class="w18em eol clearfix">
                <dt>法人または個人 <span class="red">*</span></dt>
                <dd>
                    <ul class="r_c clearfix">
                        <li><label><input type="radio" name="type" checked />法人</label></li>
                        <li><label><input type="radio" name="type" />個人</label></li>
                    </ul>
                    <font color="orange">※PG：以下、法人か個人の選択により、表示項目を変更する。</font>
                </dd>

                <dt>法人名 <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" />
                    <font color="orange">※PG：個人の場合、氏名</font>
                </dd>

                <dt>法人名（ふりがな） <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" />
                    <font color="orange">※PG：個人の場合、氏名</font>
                </dd>
                <dt>所属部署名</dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" />
                    <font color="orange">※PG：個人の場合、不要</font>
                </dd>
                <dt>所属部署名（ふりがな）</dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" />
                    <font color="orange">※PG：個人の場合、不要</font>
                </dd>
                <dt>ご担当者名 <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" />
                    <font color="orange">※PG：個人の場合、不要</font>
                </dd>
                <dt>ご担当者名（ふりがな） <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" />
                    <font color="orange">※PG：個人の場合、不要</font>
                </dd>
                <dt>国または地域名 <span class="red">*</span></dt>
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

                <dt>郵便番号（半角、ハイフンなし） <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /> <input type="submit" value="郵便番号から住所を入力" class="btn_a" />
                </dd>

                <dt>所在地または住所-1 <span class="red">*</span></dt>
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
                        <option value="東京都" selected>東京都</option>
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

                <dt>所在地または住所-2 <span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" class="em30" /><br />
                    <span class="input_note">ひらがな・カタカナ・漢字・数字で入力してください（記号は全角ハイフンのみ使用可）。</span>
                </dd>

                <dt>所在地または住所-3（建物名）</dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" class="em30" /><br />
                    <span class="input_note">全角文字で入力してください。</span>
                </dd>

                <dt>電話番号（半角、ハイフンなし）<span class="red">*</span></dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /></dd>

                <dt>連絡用メールアドレス-1<br />
                    （登録メールアドレス）</dt>
                <dd>xxx@xxxx.com <input type="submit" value="メールアドレスの変更" class="btn_c" /><a
                        href="u001edit_mail01.html">→</a>
                    <font color="orange">
                        ※PG：クリックするとメルアド仮登録～メルアド確認の処理が始まる。（2020/03/28）※PG：上記のどちらの「メールアドレスの変更」ボタンで変更しても、どちらも変更になる。
                    </font><br />
                    ※連絡用メールアドレス-2と3にもAMSからの連絡が同時に送られます。
                </dd>

                <dt>連絡用メールアドレス-2</dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /></dd>

                <dt>連絡用メールアドレス-2（確認用）</dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /></dd>

                <dt>連絡用メールアドレス-3</dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /></dd>

                <dt>連絡用メールアドレス-3（確認用）</dt>
                <dd><input type="text" value="ｘｘｘｘｘｘ" /><span
                        style="color:orange;"><br />※PG：登録メルアドと連絡先-1は同じで、2と3は必須ではなく、他の会員のアドレスと重複可。（2018/10/25）<br />
                        ※PG：案件関連のメールは全ての連絡用に送る。（2018/10/25）</span></dd>
            </dl>

            <font color="orange">※PG：ここでも送信時、メルアド重複チェックを行う。（2018/10/19）</font>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="戻る" class="btn_a" /></li>
                <li><input type="submit" value="確認画面へ" class="btn_b" /></li>
            </ul>
            <font color="orange">「会員情報編集」は完了したら会員トップに戻す。</font>
        </form>
    </div><!-- /contents -->
@endsection
@section('footerSection')

@endsection
