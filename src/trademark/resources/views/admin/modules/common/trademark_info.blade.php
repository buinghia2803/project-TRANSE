@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">

          <form>

            <h2>案件トップ</h2>

            <h5 class="membertitle">【会員情報】<a href="a001kaiin.html" target="_blank">詳細ページ</a></h5>
            <ul class="memberinfo">
              <li>NNNNNN</li>
              <li>鈴木太郎 様</li>
              <li><a class="btn_b" href="a000qa_from_ams.html">Q&amp;A作成</a></li>
            </ul>

            <div class="info mb20">
              <table class="info_table">
                <caption>【基本情報】</caption>
                <tr>
                  <th style="width:10em;">申込番号</th>
                  <td>1608005　<a href="a000anken_top.html" target="_blank">履歴確認 ＞＞</a>
                    <font color="#cc9900"><br />※システムで生成LCyyANN</font>
                  </td>
                  <th style="width:10em;">申込日</th>
                  <td>2017年1月15日</td>
                </tr>
                <tr>
                  <th style="width:10em;">種別</th>
                  <td colspan="3">法人</td>
                </tr>
                <tr>
                  <th>出願人名（権利者）</th>
                  <td colspan="3">ABC商事株式会社　他2名　<input type="submit" value="出願人情報" class="btn_a" /></td>
                </tr>
                <tr>
                  <th>商標出願種別</th>
                  <td colspan="3">文字/装飾文字/ロゴ絵柄</td>
                </tr>
                <tr>
                  <th>商標名</th>
                  <td colspan="3">とってもオレンジ</td>
                </tr>
                <tr>
                  <th>装飾文字/ロゴ絵柄の画像</th>
                  <td colspan="3"><img src="images/thumbnail.png"><br />
                    <font color="orange">※PG；画像をクリックして拡大する。（2021/04/24）</font>
                  </td>
                </tr>
                <tr>
                  <th style="width:10em;">出願番号</th>
                  <td style="width:20em;">XMLから取り込み</td>
                  <th style="width:10em;">出願日</th>
                  <td style="width:20em;">2016/8/10</td>
                </tr>
                <tr>
                  <th>登録番号</th>
                  <td>12345678910<font color="#cc9900">※庁からもらって手入力。</font>
                  </td>
                  <th>登録日</th>
                  <td>2016/8/10</td>
                </tr>
                <tr>
                  <th>出願人名（権利者）</th>
                  <td colspan="4">3（第1類、第10類、第35類）　<input type="submit" value="指定商品または役務" class="btn_a" /><a
                      href="a003.html">→</a></td>
                </tr>
              </table>
            </div>
            <!-- 【出願人情報】テーブル -->


            <ul class="btn_left eol">
              <li><input type="submit" value="はじめからサポート情報" class="btn_a" /><a href="a011.html">→</a></li>
              <li><input type="submit" value="プレチェックレポート情報" class="btn_a" /><a href="a021.html">→</a></li>
              <li><input type="submit" value="履歴" class="btn_a" /><a href="a000anken_top.html">→</a></li>
            </ul>


            <h3>作業履歴</h3>

            <a name="rireki"></a>
            <p><a class="btn_b" href="a000free.html">フリー履歴作成</a></p>
            <p><a href="">この案件を復活する ＞＞</a></p>
            <font color="orange">※PG：（2021/05/30）<br />
              ※PG：クローズされている場合：<br />
              - クローズの履歴を表示。この行の背景をピンクにする。<br />
              - 「この案件を復活する」ボタンを表示（上記はクローズ時のサンプル）。<br />
              クリックすると、「本当に復活させてよいですか？ OK　キャンセル」と小窓で表示・確認<br />
              - クローズ案件で登録査定が出たら、「登録査定」の履歴の行が追加になり、<br />
              行の背景をピンクにして、「お客様へ連絡」「PDFアップロード」のボタンは非表示。<br />
              クローズ解除したら、ピンク表示もなくなり、ボタンも表示される。<br />
              お客さんからの中止の依頼の種類（中間対応など）<br />
              　・依頼しない<br />
              　・管理不要（u000list_taikai.html）<br />
              　・期限が来ためクローズ<br />
              いずれもクローズ案件として扱い、1番上の行をピンクにする。<br />
              完了日はクローズした日を表示。<br />
              （2021/05/30）<br />
              <br />
              ※PG：お客さんが期限があるのにほったらかした場合など、拒絶で期限が経過したものは<br />
              自動的にクローズ。お客様から中止のリクエストが来た場合も、自動でクローズする。<br />
              放置＆自動クローズのタイミング：<br />
              ・庁期限の翌日（期間外延長をしている場合）<br />
              ・庁期限の2ヶ月後（期間外延長をしていない場合）<br />
              ・フリー履歴の期限日の翌日<br />
              ・登録納付料期限日の翌日<br />
              （2020/09/16）<br />
              <br />
              ※PG：クローズになったら、「この案件を復活」ボタンを表示。
            </font>
            <table class="normal_b column1">
              <tr>
                <th style="width:6em;">作成日<a href="#">▼</a><a href="#">▲</a></th>
                <th style="width:6em;">属性<a href="#">▼</a><a href="#">▲</a></th>
                <th style="width:20em;">作業内容<a href="#">▼</a><a href="#">▲</a></th>
                <th style="width:6em;">期限日<a href="#">▼</a><a href="#">▲</a></th>
                <th style="width:6em;">完了日<a href="#">▼</a><a href="#">▲</a></th>
                <th style="width:8em;">対応者<a href="#">▼</a><a href="#">▲</a></th>
                <th style="width:24em;">関連書類<a href="#">▼</a><a href="#">▲</a></th>
                <th>所内備考</th>
              </tr>
              <tr>
                <td>2020/08/29</td>
                <td>特許庁から<font color="orange"><br />※PG：拒絶対応を行って拒絶査定が出た場合、事務担当から責任者へ連絡。
                    責任者のtodoに上がった後、コメントを記載してお客様へ連絡する。（2020/08/29）</font>
                </td>
                <td>拒絶査定受領
                  <font color="orange"><br />※PG：フロー：<br />
                    XML取り込み→突合（sys）→事務担当・UL<br />
                    →事務担当ボタンクリック・責任者に連絡→責任者・コメント記述＆お客様に連絡<br />
                    　 or<br />
                    →事務担当ボタンクリック・お客様に連絡</font>
                </td>
                <td>－</td>
                <td>－</td>
                <td> </td>
                <td><input type="submit" value="PDFアップロード" class="btn_a" /> <input type="submit" value="責任者へ連絡"
                    class="btn_b" /><a href="a301kyo_s.html">→</a></td>
                <td> </td>
              </tr>
              <tr>
                <td>2020/08/29</td>
                <td>特許庁から</td>
                <td>拒絶査定受領</td>
                <td>－</td>
                <td>－</td>
                <td></td>
                <td><input type="submit" value="PDFアップロード" class="btn_a" /> <input type="submit" value="お客様へ連絡"
                    class="btn_b" />
                  <font color="orange">※PG：放置（または中止）して、拒絶査定が出た場合は、事務担当がボタン1つでお客様へ直接連絡。
                    PDFをULすると、確認画面を経て、「お客様へお知らせする」ボタンが表示される。（2020/08/29）</font>
                </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/08/10</td>
                <td>所内処理</td>
                <td>登録証：入力内容チェック<br />【登録番号】514130091<br />
                  【登録日】2019年2月1日<font color="orange">
                    <br />※PG：第三者チェック用画面。入力完了ステータスの次に「チェック」ステータスとして立てる。<br />修正の場合はページ遷移して上書き保存。（2018/12/18）<br />【登録情報】確認のステータスは、事務担当と責任者にあげる。（2019/01/31）
                  </font>
                </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td><input type="submit" value="修正" class="btn_a" /><a href="a303.html">→</a> <input type="submit"
                    value="お客様へ連絡" class="btn_b" />
                  <font color="orange">
                    <br />※PG：「OK」を押すと、登録DBに格納され、お客様へも見えるようになる。<br />OKが押されるまでは、案件情報にも登録番号をお客さんに表示してはいけない。<br />※PG：修正ボタン　OKボタン　PDFボタン　連絡ボタン全部表示していていいけど、OKとPDFがそろって「連絡」ボタンが押せるように。
                  </font>
                </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/08/01</td>
                <td>所内処理</td>
                <td><a href="a000free.html" target="_blank">フリー履歴</a></td>
                <td></td>
                <td></td>
                <td></td>
                <td><input type="submit" value="リマインドメール送信" class="btn_a" />
                  <font color="orange"><br />※PG：期限が来るまでは、「リマインドメール送信」ボタンを設置。<br />手で押してリマインドする。期限日までは表示・押下可能。</font>
                </td>
                <td></td>
              </tr>
              <tr>
                <td>2018/01/31</td>
                <td>お客様へ</td>
                <td><a href="#" target="_blank">拒絶理由通知書：必要資料依頼連絡</a></td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td><input type="submit" value="PDFアップロード" class="btn_a" /> <input type="submit" value="お客様へ連絡"
                    class="btn_b" /><a href="a000list_pdf_kakunin.html">→</a>
                  <font color="orange"><br />※PG：ステータスによって以下のボタン表示。ファイルアップ後はファイル名表示・リンク。（2018/11/23）<br />
                    [PDFアップロード][お客様へ連絡]ボタン（現状のボタンのまま表示。[PDF確認]だけ不要）<br />
                    ※PG：複数ファイルアップロード可能にする。<br />
                    2回目以降は、その前のファイルを削除してアップされるようにする（最新のアップのファイルのみ有効になる）。（2018/11/23）<br />
                    ※PG：PDFのUL後は、確認画面を経てお客様へ表示。変更できないようにする。（2020/06/30）<br /></font>
                </td>
                <td> </td>
              </tr>
              <tr>
                <td>2018/01/30</td>
                <td>お客様から</td>
                <td><a href="#" target="_blank">拒絶理由通知書：方針案選択受領</a></td>
                <td>2018/02/15</td>
                <td>2018/02/10 23:59<font color="orange"><br />※PG：「完了日」は年月日時刻を表示（2018/12/06）</font>
                </td>
                <td>鈴木</td>
                <td>会社案内 [<a href="pdf/pdf.pdf" target="_blank">company.pdf</a>]
                  <font color="orange"><br />※PG：作成日と期限日はyyyy/mm/dd、完了日はyyyy/mm/dd hh:MM<br />
                    ステータスはクリッカブルで、HTMLかPDFがリンクされるように。<br />
                    対応完了日として、プログラムからメールを送信した日時を格納。<br />
                    次のステータスができたら、[PDFアップロード] [確認メール送信]ボタンは非表示に。（2018/11/23）<br />
                    ※PG：「属性」は、Excelのアクション属性から取得。（2018/11/23）<br />
                    　お客様から<br />
                    　お客様へ<br />
                    　特許庁へ<br />
                    　特許庁から<br />
                    　所内処理</font>
                </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">登録完了報告 ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">補正書作成済み ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td><input type="submit" value="HTML生成" class="btn_a" /><a href="a000free_html.html" target="_blank">→</a>
                  <a href="u031subumit.html" target="_blank">→</a> <input type="submit" value="XMLアップロード" class="btn_a" />
                  <font color="orange"><br />※PG：「HTML生成」ボタンを押したら、「XMLアップロード」ボタンが使えるように。<br />
                    （HTML生成ボタンを押さない限り、XMLボタンはアクティブにならない）<br />
                    ※PG:【提出日】は、HTMLの出力日を自動でセット。（2020/07/24）<br />
                    ※PG：HTML生成が複数回押されたら、日付を上書きしてセットする。<br />
                    HTMLはブラウザに表示すればOK。<br />
                    XMLボタンも出しておいてOK。（2020/06/15）<br />
                    <br />
                    ※PG：お客様へ送信して24時間後に、ステータスにHTML生成ボタンを表示。<br />
                    HTML生成は、意見書と補正書があれば、2つHTML生成<br />
                    XMLは、意見書か補正書の受領日を取得して、対応完了日として格納。（2019/01/18）<br />
                    ※PG：「HTML生成」ボタンを押した年月日をHTML上の「提出日」欄に表示。（2019/01/31）<br />
                    ※PG：HTML生成→XML（特許庁からもらう）をアップロード<br />
                    →PDF（XMLを変換したやつ）アップ→お客さまへ連絡（2021/04/11）<br />
                    ※PG：インポート時、a000import02.htmlのプログラムと同様の突合処理をする。<br />
                    XMLの中の日付が当日ではなかったら以下のアラートを出す。<br />
                    ---------------<br />
                    日付が異なりますが、大丈夫ですか？<br />
                    はい / キャンセル<br />
                    ---------------<br />
                    こちらのXMLの日付は、以下から取得する<br />
                    submision date<br />
                    ここでは、出願のときは、申込番号（XML側：整理番号）で突合して、
                    出願番号とsubumission dateを取り込み、それ以外は、
                    出願番号で突合して、subumission dateを取り込む。<br />
                    また、「PDFアップロード」ボタンでPDF（プルーフ）をアップする。（2021/05/04）
                  </font>
                </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">登録回答 ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">登録回答 ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">拒絶理由通知対応：黙認 ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">拒絶理由通知対応：案文作成 ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">拒絶理由通知対応：非承認・ご意見 ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a205.html" target="_blank">拒絶理由通知対応：案文作成</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a204-1.html" target="_blank">拒絶理由通知対応：追加資料・回答</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a204.html" target="_blank">拒絶理由通知対応：追加資料依頼</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a204-1.html" target="_blank">拒絶理由通知対応：追加資料・回答</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a204.html" target="_blank">拒絶理由通知対応：追加資料依頼</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a203-1.html" target="_blank">拒絶理由通知対応：方針案選択</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a203.html" target="_blank">拒絶理由通知対応：方針案作成</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">拒絶理由通知対応：事前質問・回答 ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">拒絶理由通知対応：事前質問 ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">拒絶理由通知対応：事前質問・回答 ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">拒絶理由通知対応：事前質問 ★画面なし、ppt「A-202:事前質問（ある場合）」</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="#shutsgan_t" target="_blank">拒絶理由通知対応：申込 ★画面なし</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a201.html" target="_blank">拒絶理由通知対応：お知らせ</a></td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a031.html" target="_blank">出願申込</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a021.html" target="_blank">事前コンサル-2完了</a>
                  <font color="orange">※PG：お客様へ表示モードになったら、管理画面は編集不可の状態で表示。</font>
                </td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a021.html" target="_blank">事前コンサル-2申込</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a011.html" target="_blank">事前コンサル-1完了</a>
                  <font color="orange">※PG：お客様へ表示モードになったら、管理画面は編集不可の状態で表示。</font>
                </td>
                <td>－</td>
                <td>－</td>
                <td>弁理士：鈴木</td>
                <td> </td>
                <td> </td>
              </tr>
              <tr>
                <td>2016/01/01</td>
                <td> </td>
                <td><a href="a011.html" target="_blank">事前コンサル-1申込</a></td>
                <td>－</td>
                <td>－</td>
                <td>お客様</td>
                <td> </td>
                <td> </td>
              </tr>
            </table>

            <font color="orange">
              ※PG：受領のXMLを取り込んでそのステータスを終了（XMLの上出願番号を突合する）。（2022/05/21）<br />
              <br />
              ※PG：ステータスで終わったら水色にする。<br />
              ・ステータスクリックで固定された画面が見えるようにする。（2018/11/23）<br />
              <br />
              ※PG：各画面で「保存・見積書」ボタンを押されたら、上記にステータスとして1行追加され、<br />
              見積書PDFへのリンクが作成される（全ての見積書が残る）。（2020/05/30）<br />
              申込番号のない場合、この画面で見積のステータスが1本だけ立って、<br />
              a001の検索からたどり着くようにする。<br />
              ここにどんどん見積へのリンクが蓄積されていく。<br />
              案件になったら、仮番号が申込番号になり、申込などのステータスがその上に追加される。（2020/06/28）
              <br />
              <br />
              ※PG：u302.htmlで、登録と名称/住所変更の依頼があったら、<br />
              住所/名称変更のtodoがまず上がり、a700shutsugannin01.htmlに飛び、<br />
              名称変更、住所変更を1本ずつ手続きし、<br />
              提出書類ULのボタンで書類がアップロードされると、<br />
              次のtodo（補正書）が上がる。<br />
              （補正書に進むには住所/名称変更を両方クリアする必要あり）<br />
              （2020/08/16）<br /><br />
            </font>


            <table class="normal_b eol">
              <caption>【支払履歴】</caption>
              <tr>
                <th style="width:6em;">支払日</th>
                <th style="width:20em;">支払細目</th>
                <th style="width:6em;">支払方法</th>
              </tr>
              <tr>
                <td>2016/02/01</td>
                <td>早割ａ トータルサービス（商標出願＋拒絶理由通知対応＋登録手続き基本、1区分付）+2区分</td>
                <td>銀行振込</td>
              </tr>
            </table>


          </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->
@endsection

@section('footerSection')
    <script src="{{ asset('admin_assets/js/delete-all.min.js') }}"></script>
@endsection
