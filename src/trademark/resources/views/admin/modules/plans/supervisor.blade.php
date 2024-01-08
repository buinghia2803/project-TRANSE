@extends('admin.layouts.app')

@section('main-content')
<!-- contents -->
<div id="contents" class="admin_wide">
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
                            <img src="" /><br />
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
                        <td>
                            <font color="#cc9900">※庁からもらって手入力。</font>
                        </td>
                        <th>登録日</th>
                        <td>
                            <font color="#cc9900">※庁からもらって手入力。</font>
                        </td>
                    </tr>
                    <tr>
                        <th>次回更新日</th>
                        <td colspan="3">
                            <font color="#cc9900">※PG：登録日に5年または10年を足した同じ日を表示（申込時に選択）。</font>
                        </td>
                    </tr>
                    <tr>
                        <th>区分数および分類</th>
                        <td colspan="4">3（第1類、第10類、第35類） <input type="submit" value="指定商品または役務" class="btn_a" /><a href="a003.html">→</a></td>
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
                        <a href="a201b02.html">→</a>
                    </td>
                </tr>
                <tr>
                    <th>拒絶理由通知対応期限日</th>
                    <td colspan="3">2016/11/30<font color="#cc9900">※PG：「拒絶通知書発送日」に40日を足した日を表示。</font></td>
                </tr>
            </table>

            <h3>責任者　拒絶理由通知対応：対応方針案　承認・差し戻し</h3>

            <dl class="w16em eol clearfix">
                <dt>お客様回答期限日</dt>
                <dd><input type="text" value="2016年9月30日" /></dd>
            </dl>

            <p>
                <a href="a202.html">←</a><input type="submit" value="&laquo; 事前質問を見る" class="btn_b" />
                <font color="orange">
                    <br />
                    ※PG：編集不可状態で表示。（2017/12/18）
                </font>
            </p>

            <p class="eol"><input type="submit" value="お客様と同じ画面を見る" onclick="window.open('a203check.html','subwin','width=1200,height=780,scrollbars=yes');return false;" class="btn_b" /></p>

            <p>◎→かなり高い ○→高い △→低い ×→極めて困難</p>

            <h3>対応策-1 理由1, 2</h3>

            <h5>【担当者記入】</h5>
            <table class="normal_b eol">
                <tr>
                    <td colspan="4">&nbsp;</td>
                    <th>方針案(1)</th>
                    <th>方針案(2)</th>
                    <th>方針案(3)</th>
                    <th>方針案(4)</th>
                    <th>方針案(5)</th>
                </tr>
                <tr>
                    <th colspan="4" class="right">方針案</th>
                    <td style="width: 20em;">
                        書類を修正してご提出ください。●を削除して必要事項をご記入ください。なお、商標の使用を開始する年月は、あくまで予定の範囲で記入できる年月で問題ありません。この年月までに必ず商標の使用開始が求められるものではありません。また、押印をお願い致します。
                    </td>
                    <td style="width: 20em;">
                        書類を修正してご提出ください。●を削除して必要事項をご記入ください。なお、商標の使用を開始する年月は、あくまで予定の範囲で記入できる年月で問題ありません。この年月までに必ず商標の使用開始が求められるものではありません。また、押印をお願い致します。
                    </td>
                    <td style="width: 20em;">書類を修正し、押印してご提出ください。●を削除して必要事項をご記入ください。この年月までに必ず商標の使用開始が求められるものではありません。</td>
                    <td style="width: 20em;">
                        書類を修正してご提出ください。●を削除して必要事項をご記入ください。なお、商標の使用を開始する年月は、あくまで予定の範囲で記入できる年月で問題ありません。この年月までに必ず商標の使用開始が求められるものではありません。また、押印をお願い致します。
                    </td>
                    <td style="width: 20em;">
                        書類を修正してご提出ください。●を削除して必要事項をご記入ください。なお、商標の使用を開始する年月は、あくまで予定の範囲で記入できる年月で問題ありません。この年月までに必ず商標の使用開始が求められるものではありません。また、押印をお願い致します。書類を修正してご提出ください。●を削除して必要事項をご記入ください。なお、商標の使用を開始する年月は、あくまで予定の範囲で記入できる年月で問題ありません。この年月までに必ず商標の使用開始が求められるものではありません。また、押印をお願い致します。書類を修正してご提出ください。●を削除して必要事項をご記入ください。なお、商標の使用を開始する年月は、
                    </td>
                </tr>
                <tr>
                    <th colspan="4" class="right">拒絶解消可能性</th>
                    <td class="center">◎</td>
                    <td class="center">○</td>
                    <td class="center">△</td>
                    <td class="center">△</td>
                    <td class="center">△</td>
                </tr>
                <tr>
                    <th colspan="4" class="right">必要資料</th>
                    <td class="center">商標の使用を開始する意思・事業予定（書）</td>
                    <td class="center">承諾書</td>
                    <td class="center">不要</td>
                    <td class="center">不要</td>
                    <td class="center">不要</td>
                </tr>
                <tr>
                    <th style="width: 5em;">区分</th>
                    <th style="width: 15em;">商品・サービス名</th>
                    <th style="width: 15em;">類似群コード</th>
                    <th style="width: 5em;">
                        評価<br />
                        レポート
                    </th>
                    <td class="center"></td>
                    <td class="center"></td>
                    <td class="center"></td>
                    <td class="center"></td>
                    <td class="center"></td>
                </tr>
                <tr>
                    <td class="center">32</td>
                    <td>瓶詰ジュース</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center bg_pink">削除</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">32</td>
                    <td>保存可能ジュース※</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">B</td>
                    <td class="center bg_green">※</td>
                    <td class="center">残す</td>
                    <td class="center bg_green">※</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center bg_yellow">32</td>
                    <td class="bg_yellow">追加の商品名</td>
                    <td class="bg_yellow">15A01 22C03 12E05</td>
                    <td class="center bg_yellow">C</td>
                    <td class="center bg_yellow">追加</td>
                    <td class="center">残す</td>
                    <td class="center bg_yellow">追加</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">32</td>
                    <td>オレンジジュース</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">20</td>
                    <td>コルク製栓</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">D</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">20</td>
                    <td>プラスチック製栓</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">申込なし</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                </tr>
                <tr>
                    <td class="center">20</td>
                    <td>ゴム製栓</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">6</td>
                    <td>ゴム製ふた</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">6</td>
                    <td>xxxxx</td>
                    <td>15A01 22C03 12E05 <a href="#">+</a></td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">5</td>
                    <td>xxxxx</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
            </table>

            <h3>対応策-2 理由3,4</h3>

            <h5>【担当者記入】</h5>
            <table class="normal_b eol">
                <tr>
                    <td colspan="4">&nbsp;</td>
                    <th>方針案(1)</th>
                    <th>方針案(2)</th>
                </tr>
                <tr>
                    <th colspan="4" class="right">方針案</th>
                    <td style="width: 15em;">「シューストレッチャー」→「靴用ストレッチャー」「金属製棚」→削除（別の指定商品（役務）にある「家具」に含まれるため）</td>
                    <td style="width: 15em;">追加書類をご提出ください。事業計画書が不足しています。</td>
                </tr>
                <tr>
                    <th colspan="4" class="right">拒絶解消可能性</th>
                    <td class="center">◎</td>
                    <td class="center">○</td>
                </tr>
                <tr>
                    <th colspan="4" class="right">必要資料</th>
                    <td class="center">不要</td>
                    <td class="center">不要</td>
                </tr>
                <tr>
                    <th style="width: 5em;">区分</th>
                    <th style="width: 15em;">商品・サービス名</th>
                    <th style="width: 15em;">類似群コード</th>
                    <th style="width: 5em;">評価レポート</th>
                    <td class="center">&nbsp;</td>
                    <td class="center">&nbsp;</td>
                </tr>
                <tr>
                    <td class="center">32</td>
                    <td>瓶詰ジュース</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">32</td>
                    <td>保存可能ジュース※</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">B</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center bg_yellow">32</td>
                    <td class="bg_yellow"></td>
                    <td class="bg_yellow">15A01 22C03 12E05</td>
                    <td class="center bg_yellow">C</td>
                    <td class="center bg_yellow">残す</td>
                    <td class="center bg_yellow">残す</td>
                </tr>
                <tr>
                    <td class="center">32</td>
                    <td>オレンジジュース</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">20</td>
                    <td>コルク製栓</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">D</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">20</td>
                    <td>プラスチック製栓</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">申込なし</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                </tr>
                <tr>
                    <td class="center">20</td>
                    <td>ゴム製栓</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">6</td>
                    <td>ゴム製ふた</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">6</td>
                    <td>xxxxx</td>
                    <td>15A01 22C03 12E05 <a href="#">+</a></td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">5</td>
                    <td>xxxxx</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">1</td>
                    <td class="middle">分割元のブロックの内容を表示。</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
            </table>

            <h3>対応策-3 理由5</h3>

            <h5>【担当者記入】</h5>
            <table class="normal_b eol">
                <tr>
                    <td colspan="4">&nbsp;</td>
                    <th>方針案(1)</th>
                    <th>方針案(2)</th>
                </tr>
                <tr>
                    <th colspan="4" class="right">方針案</th>
                    <td style="width: 15em;">「シューストレッチャー」→「靴用ストレッチャー」「金属製棚」→削除（別の指定商品（役務）にある「家具」に含まれるため）</td>
                    <td style="width: 15em;">追加書類をご提出ください。事業計画書が不足しています。</td>
                </tr>
                <tr>
                    <th colspan="4" class="right">拒絶解消可能性</th>
                    <td class="center">◎</td>
                    <td class="center">○</td>
                </tr>
                <tr>
                    <th colspan="4" class="right">必要資料</th>
                    <td class="center">不要</td>
                    <td class="center">不要</td>
                </tr>
                <tr>
                    <th style="width: 5em;">区分</th>
                    <th style="width: 15em;">商品・サービス名</th>
                    <th style="width: 15em;">類似群コード</th>
                    <th style="width: 5em;">評価レポート</th>
                    <td class="center">&nbsp;</td>
                    <td class="center">&nbsp;</td>
                </tr>
                <tr>
                    <td class="center">32</td>
                    <td>瓶詰ジュース</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">32</td>
                    <td>保存可能ジュース※</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">B</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center bg_yellow">32</td>
                    <td class="bg_yellow"></td>
                    <td class="bg_yellow">15A01 22C03 12E05</td>
                    <td class="center bg_yellow">C</td>
                    <td class="center bg_yellow">残す</td>
                    <td class="center bg_yellow">残す</td>
                </tr>
                <tr>
                    <td class="center">32</td>
                    <td>オレンジジュース</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">20</td>
                    <td>コルク製栓</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">D</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">20</td>
                    <td>プラスチック製栓</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">申込なし</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                </tr>
                <tr>
                    <td class="center">20</td>
                    <td>ゴム製栓</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">6</td>
                    <td>ゴム製ふた</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">6</td>
                    <td>xxxxx</td>
                    <td>15A01 22C03 12E05 <a href="#">+</a></td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">5</td>
                    <td>xxxxx</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
                <tr>
                    <td class="center">1</td>
                    <td class="middle">分割元のブロックの内容を表示。</td>
                    <td>15A01 22C03 12E05</td>
                    <td class="center">A</td>
                    <td class="center">残す</td>
                    <td class="center">残す</td>
                </tr>
            </table>

            <font color="orange">
                ※PG：完全ダーティの行と、準クリーンと登録クリーンで編集して変更されたものに<br />
                ※を付ける。<br />
                類似群コードは4つ目以降、非表示で「+」をクリックして表示。（2020/05/23）<br />
                <br />
            </font>

            <dl class="w08em eol clearfix">
                <dt>社内用コメント：</dt>
                <dd>2018/01/03　xxxx xxx（再提出時のコメント固定表示）</dd>
                <dt>社内用コメント：</dt>
                <dd>2018/01/02　xxxx xxx（差し戻し時のコメント固定表示）</dd>
                <dt>社内用コメント：</dt>
                <dd>2018/01/01　xxxx xxx（最初に提出したときのコメント固定表示）</dd>
            </dl>

            <font color="orange">
                ※PG：差し戻しでこの画面に来たら、最大で上記のコメントを表示。（2018/05/10）<br />
                <br />
                ※PG：選んだものにより、セルの背景色を変える（削除はピンク、※は緑）。<br />
                追加はユーザ画面では背景白のまま。（2018/02/08）<br />
            </font>

            <ul class="footerBtn clearfix">
                <li><a href="a203sashi.html">←</a><input type="submit" value="差し戻し" class="btn_a" /></li>
                <li><input type="submit" value="修正" class="btn_c" /><a href="a203shu.html">→</a></li>
            </ul>

            <hr />

            <p class="eol">
                社内用コメント：<br />
                <textarea class="middle_c"></textarea>
            </p>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="承認してお客様へ表示" class="btn_b" /></li>
            </ul>

            <font color="orange">
                ※PG：差し戻しが訂正されて責任者に戻ってきたら、<br />
                N回目の差し戻しはなく、引き上げに行く。意見書もN回はない。（2018/05/10）<br />
                <br />
                ※PG：担当者には「表示せずに保存」と「確認依頼する」を表示。<br />
                責任者は「差し戻し」「承認してお客様へ表示」を表示。（権限設定が必要になる）<br />
                「差し戻し」を押したら、「差し戻しコメント」欄を表示し、<br />
                差し戻された担当弁理士に、コメントとして表示させる。<br />
                <br />
                差し戻したら、担当弁理士は、上書きして修正する。<br />
                <br />
                ※PG：担当者の記載内容に上書きする場合も、責任者へ渡した時点の画面が後から閲覧できるようにする。<br />
                （＝引き上げ画面で上書きしても、担当者の作業内容は履歴から閲覧できるようにする）<br />
                （DBとしては、「決定」欄に格納する感じか？）（2021/02/16）
            </font>
        </form>
    </div>
    <!-- /contents inner -->
</div>
<!-- /contents -->
@endsection
