@extends('user.layouts.app')
@section('main-content')
<div id="contents" class="normal">


    <h2>案件情報</h2>


    <form>

    <div class="info">
    <h3>【商標情報】</h3>
    <table class="info_table">
      <tr>
        <th>お客様整理番号</th>
        <td>あ123456-B <input type="submit" value="編集" class="btn_a small" /><font color="orange"><br />※PG：お客様整理番号は<br />この案件トップで変更する。（2020/06/05）</font></td>
        <th nowrap>お申込みプラン</th>
        <td>エコノミー</td>
      </tr>
      <tr>
        <th>申込番号</th>
        <td><a href="u000anken_top.html">1608005</a></td>
        <th>申込日</th>
        <td>2016/8/10</td>
      </tr>
      <tr>
        <th>商標名</th>
        <td colspan="3">とってもオレンジ</td>
      </tr>
      <tr>
        <th><a href="#">区分数</a></th>
        <td colspan="3">3区分（第1類、第10類、第35類）　<input type="submit" value="商品・サービス名を見る" class="btn_a" /><a href="u003db.html" target="_blank">→</a><font color="orange"><br />※PG：中間をやったことがある場合、補正書で増減した商品・サービス名が反映されている必要がある。（2020/10/17）</font></td>
      </tr>
      <tr>
        <th>区分数（最大値）</th>
        <td colspan="3">4区分</td>
      </tr>
      <tr>
        <th nowrap>装飾文字/ロゴ絵柄の画像</th>
        <td colspan="3"><a href="#"><img src="{{asset('user_assets/images/thumbnail.png')}}">クリックして拡大 >></a><font color="orange"><br />※PG：「商標名」のところに、商標名かこの画像がくる。</font></td>
      </tr>
      <tr>
        <th style="width:25%;" id="applicant">出願人</th>
        <td colspan="3">ABC商事株式会社　他2名　<input type="submit" value="出願人名・住所変更" class="btn_a" /><a href="u000list_change_address02.html" target="_blank">→</a></td>
      </tr>
      <tr>
        <th>出願番号</th>
        <td>12345678910</td>
        <th>出願日</th>
        <td>2018/10/11</td>
      </tr>
      <tr>
        <th>登録番号</th>
        <td>12345678910</td>
        <th>登録日</tdh>
        <td>2018/12/3</td>
      </tr>
      <tr>
        <th>登録期間（5年or10年）</th>
        <td>5年</td>
        <th>更新期限</th>
        <td>2023/12/3</td>
      </tr>
    </table>


    <p class="right notice mb10">延長制度を利用することで、特許庁への応答期限日を延ばす方法もあります（有料）。</p>
    <p class="right mb10"><input type="submit" value="拒絶理由通知対応期限日前期間延長お申込み" class="btn_b" /><a href="u210alert02.html">→</a>

    <font color="orange"><br />※PG：拒絶理由通知対応の期間中のみ表示。<br />
    中間対応申し込まれてなければ、延長のボタンおよびその上の赤字注記は出さない。<br />
    拒絶理由のN回は、3回までで想定。（2019/02/28）<br />
    ※PG:期間延長はいつでもできるようにする。<br />拒絶の期間中で、期限の3日前まで表示。（2019/02/19）</font></p>

    </div><!-- /info -->


    <h3>履歴</h3>

    <font color="orange">※PG：延長申込があったら表示。（2019/04/13）<br />
    ※PG：simpleとかselectと一緒に申し込んだ場合、<br />
    作業に先立って、延長手続きをするので、延長関連の履歴が立って<br />
    その上に次のステータスがかぶっていく。（2019/04/13）<br />
    ※PG：申込みをして振込みがない場合、「支払い待ち」が表示されて案件トップから動けない。（2020/11/14）<br /><br /></font>


    <p class="mb10"><input type="submit" value="現在のステータスの画面を見る" class="btn_a" /></p>

    <div class="js-scrollable mb10">
    <table class="normal_b">
      <tr>
        <th>お知らせ日</th>
        <th style="width:40%;">作業名</th>
        <th>期限日</th>
        <th>関連書類</th>
      </tr>
      <tr>
        <td class="bg_pink">2017/01/31</td>
        <td class="bg_pink">ステータス名のみ（ｘｘｘしてください。の文言は不要。todoでのみ）</td>
        <td class="bg_pink">2017/02/01</td>
        <td class="bg_pink"></td>
      </tr>
      <tr>
        <td class="bg_green">2017/01/31</td>
        <td class="bg_green">ステータス名のみ（ｘｘｘしてください。の文言は不要。todoでのみ）</td>
        <td class="bg_green">2017/02/01</td>
        <td class="bg_green"></td>
      </tr>
      <tr>
        <td>2016/8/1</td>
        <td>出願申請完了</td>
        <td>2016/9/30</td>
        <td><font color="orange">※PG：（お客さんがアップしたファイルは、ファイル名だけ残っていればよくて、ファイルにはアクセスできなくてよい）<br />完了後の画面へのリンクを貼る。（2019/04/13）</font></td>
      </tr>
      <tr>
        <td>2016/8/1</td>
        <td>出願処理中</td>
        <td>2016/10/1</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/8/20</td>
        <td>完了</td>
        <td>2016/10/2</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/9/10</td>
        <td>登録手続き中</td>
        <td>2016/10/3</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>★★ここから下↓が<br />フローに沿ったステータスになっている。</td>
        <td>2016/10/4</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/9/10</td>
        <td>登録完了<font color="#cc9900"><br />★画面なし。pptの「U-403_詳細画面<br />（登録完了画面）」</font></td>
        <td>2016/10/3</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>登録手続き申込完了・手続き中<font color="#cc9900"><br />★画面なし。pptの「U-400_詳細画面<br />（登録申込直後画面）」</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>登録手続きオプション選択依頼
    <font color="orange"><br />※PG：繰り返しあり。決済発生することも。</font>
    <font color="#cc9900"><br /><br />★現在、画面なし。
    <br />U-302_詳細画面<br />（管理者回答、登録オプション選択）-1
    <br />登録手続きオプション申込
    <br />登録手続き　区分が減る場合</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td> 拒絶理由通知対応：案文確認完了<font color="#cc9900"><br />★画面なし。</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td> 拒絶理由通知対応：案文確認依頼　<font color="orange"><br />※PG：1回だけ繰り返しあり。</font><br />
            <font color="#cc9900"><br />★現在、画面なし。<br />U-205b:案文確認</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td> 拒絶理由通知対応：追加資料送信完了・回答待ち <font color="#cc9900"><br />★画面なし。pptの「U-204c:詳細画面 <br />（追加資料提出直後）」</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td> 拒絶理由通知対応：追加資料送信依頼<font color="orange"><br />※PG：繰り返しあり。</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td> 拒絶理由通知対応：方針案選択完了・回答待ち <font color="#cc9900"><br />★画面なし。pptの「U-203c :詳細画面<br />（拒絶理由通知対応方針確認受付直後）」</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td> 拒絶理由通知対応：方針案選択依頼<font color="orange"><br />※PG：繰り返しあり。</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td> ★★ここまで、pptのPDF化。<br />
        拒絶理由通知対応：事前質問回答完了・返信待ち <font color="#cc9900"><br />★画面なし。pptの「U-202b:事前質問　<br />（回答　確認画面」</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td> 拒絶理由通知対応：事前質問回答のお願い <font color="#cc9900"><br />★画面なし。pptの「U-202a:詳細画面<br />あれば：（事前質問）」<br />「U-202b:事前質問　回答」</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td> 拒絶理由通知対応：申込完了・回答待ち<font color="#cc9900"><br />★画面なし。pptの「U-201c:詳細画面<br />（拒絶理由通知対応受付直後）」</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td> 拒絶理由通知対応：お知らせ・検討依頼</td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>出願完了</td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>支払い完了</td>
        <td></td>
        <td><a href="receipt.html" target="_blank">売上票</a><br />
        領収書は省略させて頂いております。銀行振込明細書をご参照ください。<font color="orange"><br />※PG:銀行振込の場合、ここに売上票と表示し、上記テキストを表示。<br />クレカの場合は「領収書」と記載して、上記テキストも非表示。（2020/11/07）<br /></font>
        </td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>出願申込完了・支払い待ち</td>
        <td>2016/10/4</td>
        <td><a href="invoice.html" target="_blank">請求書</a></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>出願申込完了</td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>プレチェックレポート申込・AMSからの回答</td>
        <td>2016/10/5</td>
        <td><a href="quote.html" target="_blank">見積書</a><font color="orange">　→（quote.htmlに別窓でリンク）<br />
        ※PG：見積書は、関連書類にリンクを置く。</font></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>プレチェックレポート申込完了・回答待ち<font color="#cc9900"><br />★画面なし。pptの「U-130_詳細画面<br />（事前C-2申込直後）」</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>はじめからサポート・AMSからの回答</td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
      <tr>
        <td>2016/10/1</td>
        <td>はじめからサポート申込完了・回答待ち　<font color="#cc9900"><br />★画面なし。pptの「U-120_詳細画面<br />（事前C-1申込直後）:事前コンサル-1」</font></td>
        <td>2016/10/5</td>
        <td></td>
      </tr>
    </table>
    </div><!-- /scroll wrap -->

    <p class="right eol"><input type="submit" value="この案件は管理不要" class="btn_a" /></p>


    <ul class="footerBtn clearfix">
        <li><input type="submit" value="戻る" class="btn_a" /></li>
    </ul>
    <font color="orange">
    ※PG：1つ目のステータスは、案件トップがないので<br />
    u000top.htmlから見積書へリンクする。<br />
    ※PG：「この案件は管理不要」を押したときに、振込待ちだった場合、<br />
    アラートが出て、お客さんへメール（キャンセルのお知らせのみ）が飛ぶ。<br />
    そして、管理者のpayment.htmlに表示されているところから消えて、<br />
    キャンセルフラグを付けて、自動で処理済みリストへ移動。（2020/11/01）
    </font>

    </form>

    </div><!-- /contents -->
@endsection

@section('footerSection')
    <script src="{{ asset('admin_assets/js/delete-all.min.js') }}"></script>
@endsection
