@extends('admin.layouts.app')

@section('main-content')
<!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form>
                <h5 class="membertitle">【会員情報】<a href="a001kaiin.html" target="_blank">詳細ページ</a></h5>
                <ul class="memberinfo">
                    <li>NNNNNN</li>
                    <li>鈴木太郎 様</li>
                    <li><a class="btn_b" href="a000qa_from_ams.html">Q&amp;A作成</a></li>
                </ul>

                <div class="info mb20">
                    <table class="info_table">
                        <caption>
                            【基本情報】
                        </caption>
                        <tr>
                            <th style="width: 10em;">申込番号</th>
                            <td>1608005 <a href="a000anken_top.html" target="_blank">履歴確認 ＞＞</a></td>
                            <th style="width: 10em;">申込日</th>
                            <td>2017年1月15日</td>
                        </tr>
                        <tr>
                            <th>申込タイプ</th>
                            <td colspan="3">早割パッケージA<font color="#cc9900">※パッケージ購入の場合、パッケージ名表示。</font></td>
                        </tr>
                        <tr>
                            <th>種別</th>
                            <td colspan="3">法人</td>
                        </tr>
                        <tr>
                            <th>出願人名（権利者）</th>
                            <td colspan="3">ABC商事株式会社　他2名 <input type="submit" value="出願人情報" class="btn_a" /></td>
                        </tr>
                        <tr>
                            <th>商標出願種別</th>
                            <td colspan="3">文字　装飾文字/ロゴ絵柄<font color="orange">※PG：いずれか表示。</font></td>
                        </tr>
                        <tr>
                            <th>商標名</th>
                            <td colspan="3">とってもオレンジ</td>
                        </tr>
                        <tr>
                            <th>装飾文字/ロゴ絵柄の画像</th>
                            <td colspan="3">
                                <img src="images/thumbnail.png" /><br />
                                <font color="orange">※PG；画像をクリックして拡大する。（2021/04/24）</font>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 10em;">出願番号</th>
                            <td style="width: 20em;">12345678910</td>
                            <th style="width: 10em;">出願日（提出日）</th>
                            <td style="width: 20em;">2016/8/10</td>
                        </tr>
                        <tr>
                            <th>登録番号</th>
                            <td><font color="#cc9900">※庁からもらって手入力。</font></td>
                            <th>登録日</th>
                            <td><font color="#cc9900">※庁からもらって手入力。</font></td>
                        </tr>
                        <tr>
                            <th>次回更新日</th>
                            <td colspan="3"><font color="#cc9900">※PG：登録日に5年または10年を足した同じ日を表示（申込時に選択）。</font></td>
                        </tr>
                        <tr>
                            <th>区分数および分類</th>
                            <td colspan="4">3（第1類、第10類、第35類） <input type="submit" value="指定商品または役務" class="btn_a" /><a href="u031submit.html">→</a></td>
                        </tr>
                    </table>
                </div>

                <ul class="btn_left eol">
                    <li><input type="submit" value="はじめからサポート情報" class="btn_a" /><a href="a011.html">→</a></li>
                    <li><input type="submit" value="プレチェックレポート情報" class="btn_a" /><a href="a021.html">→</a></li>
                    <li><input type="submit" value="履歴" class="btn_a" /><a href="a000anken_top.html">→</a></li>
                </ul>
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->

                <table class="normal_a eol">
                    <caption>
                        【拒絶理由通知対応関連】
                    </caption>
                    <tr>
                        <th>拒絶通知書発送日</th>
                        <td colspan="3">
                            2016/11/10 <font color="#cc9900">※XMLで一括インポートして格納＆表示。</font> <input type="submit" value="特許庁からの通知書を見る" class="btn_b" /> <input type="submit" value="登録可能性評価レポート" class="btn_a" />
                            <a href="a201b02.html" target="_blank">→</a> <input type="submit" value="方針案・必要資料確認" class="btn_a" /><a href="a204n_finish.html" target="_blank">→</a>
                            <input type="submit" value="方針案（商品別）" class="btn_a" /><a href="a204_no_mat.html" target="_blank" target="_blank">→</a>
                        </td>
                    </tr>
                    <tr>
                        <th>拒絶通知書対応期限日</th>
                        <td colspan="3">2016/11/30<font color="#cc9900">※PG：「拒絶通知書発送日」に40日を足した日を表示。</font></td>
                    </tr>
                </table>

                <dl class="w16em clearfix">
                    <h3>拒絶理由通知対応：提出書類確認</h3>

                    <dl class="w16em eol clearfix">
                        <dt>社内用コメント：</dt>
                        <dd>
                            2018/01/03　xxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxx xxxxxxx xxxxxxx
                            xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx xxxxxxx
                            xxxxxxx xxx（再提出時のコメント固定表示）
                        </dd>
                        <dt>社内用コメント：</dt>
                        <dd>2018/01/02　xxxx xxx（差し戻し時のコメント固定表示）</dd>
                        <dt>社内用コメント：</dt>
                        <dd>2018/01/01　xxxx xxx（最初に提出したときのコメント固定表示）</dd>
                        <br />
                        <br />
                    </dl>

                    <dt>【整理番号】</dt>
                    <dd>AT4-2227<font color="orange">※PG：申込番号取り込み。</font></dd>

                    <dt>【あて先】</dt>
                    <dd>特許庁長官　殿<font color="orange">※PG：固定</font></dd>
                </dl>

                <h4>【事件の表示】</h4>
                <dl class="w16em clearfix">
                    <dt>【出願番号】</dt>
                    <dd>商願2016-054763<font color="orange">※PG：出願番号取り込み。</font></dd>
                </dl>

                <h4>【補正をする者】</h4>
                <dl class="w16em clearfix">
                    <dt>【識別番号】</dt>
                    <dd>516066903<font color="orange">※PG：付与されていれば出願人識別番号取り込み。</font></dd>

                    <dt>【住所又は居所】</dt>
                    <dd>東京都府中市何処町１－１－１<font color="orange">※PG：出願人所在地取り込み。識別番号ある場合は【住所又は居所】不要</font></dd>

                    <dt>【氏名又は名称】</dt>
                    <dd>XYZカンパニー<font color="orange">※PG：出願人名取り込み</font></dd>
                </dl>

                <h4>【代理人】</h4>
                <dl class="w16em eol clearfix">
                    <dt>【識別番号】</dt>
                    <dd>100106002<font color="orange">※PG：プルダウン</font></dd>

                    <dt>【弁理士】</dt>
                    <dd>&nbsp;</dd>

                    <dt>【氏名又は名称】</dt>
                    <dd>正林　真之<font color="orange">※PG：プルダウン選択の番号に紐づく弁理士名表示。</font></dd>
                </dl>

                <dl class="w16em eol clearfix">
                    <dt>【発送番号】</dt>
                    <dd>44989</dd>
                </dl>

                <p>
                    <iframe src="a205hosei01window.html" width="49%" height="1200"></iframe>
                    <iframe src="a205iken02window.html" width="49%" height="1200"></iframe>
                </p>

                <ul class="footerBtn clearfix">
                    <li><input type="submit" value="戻る" class="btn_a" /></li>
                    <li><input type="submit" value="確認依頼する" class="btn_c" /><a href="a205s.html">→</a></li>
                    <br />
                    <font color="orange">※PG：担当者の場合「確認依頼」を、責任者へは以下を表示。（2018/04/24）</font>
                    <br />
                    <li><input type="submit" value="お客様へ表示" class="btn_c" /></li>
                </ul>
            </form>
        </div>
        <!-- /contents inner -->
    </div>
<!-- /contents -->
@endsection
