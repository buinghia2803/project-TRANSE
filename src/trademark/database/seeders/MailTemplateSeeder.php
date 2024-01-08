<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use Illuminate\Database\Seeder;

class MailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routeIndex = route('user.index');
        $routeIndex = str_replace('test_ams/test_ams', 'test_ams', $routeIndex);
        $footer = "
            ※このメールは、AMSでお客様が登録された全てのメールアドレスに送られています。<br>
            ※このメールはシステムにより自動送信しているため、このメールへ返信しても回答はされません。<br>
            <br>
            AMS　商標出願包括管理システム<br>
            $routeIndex
        ";

        // content bank transfer of u011b,u011b_31,u021b,u021b_31,u031,u031b,u031c,u031d,u031dit,u031edit_with_number
        $cntBankTransferU0x1x = '
            銀行振込にて、出願のお申込みを承りました。お支払い完了後に手続きを進めます。<br>
            お振込み期限日までに下記お振込み金額をお支払いください。<br>
            <br>
            お振込み期限日：{{payment_due_date}}<br>
            お振込み金額：{{payment_amount}}円<br>
            お振込み先：{{transfer_destination}}<br>
            <br>
            ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br>
        ' . $footer;

        // content credit card of u011b,u011b_31,u021,u021b,u021b_31,u031,u031b,u031c,u031d,u031dit,u031edit_with_number
        $cntCreditCardU0x1x = '
            お申込みを受け付けました。<br>
            2,3営業日後にご返信致します。<br>
            <br>
        ' . $footer;

        $mailSend2User = [
            // U011
            [
                'from_page' => U011,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => '
                    銀行振込にて、はじめからサポートのお申込みを受け付けました。<br/>
                    お支払い完了後に手続きを進めます。<br/>
                    <br/>
                    お振込み金額：{{payment_amount}}円<br/>
                    お振込み先：{{transfer_destination}}<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            [
                'from_page' => U011,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム　お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U011B
            [
                'from_page' => U011B,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => $cntBankTransferU0x1x,
            ],
            [
                'from_page' => U011B,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U011B_31
            [
                'from_page' => U011B_31,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => $cntBankTransferU0x1x,
            ],
            [
                'from_page' => U011B_31,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U021
            [
                'from_page' => U021,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => '
                    銀行振込にて、プレチェックレポートのお申込みを承りました。お支払い完了後に手続きを進めます。<br>
                    <br>
                    お振込み金額：{{payment_amount}}円<br>
                    お振込み先：{{transfer_destination}}<br>
                    <br>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br>
                ' . $footer,
            ],
            [
                'from_page' => U021,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム　お申込みありがとうございました',
                'content' => '
                    プレチェックレポートのお申込みを受け付けました。<br>
                    2,3営業日後にご返信致します。<br>
                    <br>
                ' . $footer,
            ],
            // U021N
            [
                'from_page' => U021N,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => '
                    銀行振込にて、プレチェックレポートのお申込みを承りました。お支払い完了後に手続きを進めます。<br>
                    <br>
                    お振込み金額：{{payment_amount}}円<br>
                    お振込み先：{{transfer_destination}}<br>
                    <br>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br>
                ' . $footer,
            ],
            [
                'from_page' => U021N,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム　お申込みありがとうございました',
                'content' => '
                    プレチェックレポートのお申込みを受け付けました。<br>
                    2,3営業日後にご返信致します。<br>
                    <br>
                ' . $footer,
            ],
            // U021B
            [
                'from_page' => U021B,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => $cntBankTransferU0x1x,
            ],
            [
                'from_page' => U021B,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U021B_31
            [
                'from_page' => U021B_31,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => $cntBankTransferU0x1x,
            ],
            [
                'from_page' => U021B_31,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U031
            [
                'from_page' => U031,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => $cntBankTransferU0x1x,
            ],
            [
                'from_page' => U031,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U031B
            [
                'from_page' => U031B,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => $cntBankTransferU0x1x,
            ],
            [
                'from_page' => U031B,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U031C
            [
                'from_page' => U031C,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => $cntBankTransferU0x1x,
            ],
            [
                'from_page' => U031C,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U031D
            [
                'from_page' => U031D,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankTransferU0x1x,
            ],
            [
                'from_page' => U031D,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U031EDIT
            [
                'from_page' => U031EDIT,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => $cntBankTransferU0x1x,
            ],
            [
                'from_page' => U031EDIT,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U031_EDIT_WITH_NUMBER
            [
                'from_page' => U031_EDIT_WITH_NUMBER,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => $cntBankTransferU0x1x,
            ],
            [
                'from_page' => U031_EDIT_WITH_NUMBER,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntCreditCardU0x1x,
            ],
            // U032 -> click 確認しました
            [
                'from_page' => U032,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '出願：提出書類ご確認',
                'content' => 'Test',
            ],
            // U032cancel -> click 確認
            [
                'from_page' => U032_CANCEL,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 中止',
                'content' => '
                    出願書類の提出が中止されました。<br>
                    出願はされません。<br>
                    <br>
                ' . $footer,
            ],
            // U201b_cancel -> click 「確認」
            [
                'from_page' => U201B_CANCEL,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 拒絶理由通知対応中止について',
                'content' => '
                    拒絶理由通知対応が中止されました。<br>
                    <br>
                ' . $footer,
            ],
            // U201simple -> u000common_payment -> click 「決定」
            [
                'from_page' => U201_SIMPLE,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => '
                    銀行振込にてシンプルプランのお申込みを承りました。お支払い完了後に手続きを進めます。<br/>
                    お振込み期限日までに下記お振込み金額をお支払いください。<br/>
                    <br/>
                    お振込み期限日：{{register_due_date}}<br/>
                    お振込み金額：{{payment_amount}}円<br/>
                    お振込み先：{{transfer_destination}}<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            [
                'from_page' => U201_SIMPLE,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム　お申込みありがとうございます',
                'content' => '
                    シンプルプランのお申し込みを承りました。<br/>
                    対応方針案を作成・提示いたしますのでお待ちください。<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            // U201Select01, U201Select01n -> u000common_payment -> click 「決定」
            [
                'from_page' => U201_SELECT_01,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => '
                    銀行振込にて、セレクトプランお申込みを承りました。お支払い完了後に手続きを進めます。<br/>
                    お振込み期限日までに下記お振込み金額をお支払いください。<br/>
                    <br/>
                    お振込み期限日：{{register_due_date}}<br/>
                    お振込み金額：{{payment_amount}}円<br/>
                    お振込み先：{{transfer_destination}}<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            [
                'from_page' => U201_SELECT_01,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム　お申込みありがとうございます',
                'content' => '
                    セレクトプランのお申し込みを承りました。<br/>
                    登録可能性評価レポートをお待ちください。<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            // U302 -> u000common_payment -> click 「決定」
            [
                'from_page' => U302,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => '
                    銀行振込にて、登録手続きのお申込みを承りました。お支払い完了後に手続きを進めます。<br/>
                    お振込み期限日までに下記お振込み金額をお支払いください。<br/>
                    <br/>
                    お振込み期限日：{{register_due_date}}<br/>
                    お振込み金額：{{payment_amount}}円<br/>
                    お振込み先：{{transfer_destination}}<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            [
                'from_page' => U302,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム 登録手続きお申込み完了',
                'content' => '
                    登録手続きのお申込みを完了いたしました。<br/>
                    特許庁への登録手続き完了のお知らせをお待ちください。<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            // U302cancel -> click 「確認」
            [
                'from_page' => U302CANCEL,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 登録手続き中止について',
                'content' => '
                    登録手続きが中止されました。<br/>
                    <br/>
                ' . $footer,
            ],
            // U000FREE -> u000common_payment -> click 「決定」
            [
                'from_page' => U000FREE,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => '
                    フリー履歴追加お申込みを承りました。お支払い完了後に手続きを進めます。<br/>
                    お振込み期限日までに下記お振込み金額をお支払いください。<br/>
                    <br/>
                    お振込み期限日：{{register_due_date}}<br/>
                    お振込み金額：{{payment_amount}}円<br/>
                    お振込み先：{{transfer_destination}}<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            [
                'from_page' => U000FREE,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム フリー履歴追加お申込み完了',
                'content' => '
                    フリー履歴追加のお申込みを完了いたしました。<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            // U402 -> u000common_payment -> click 「決定」
            [
                'from_page' => U402,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => '
                    更新手続きのお申込みを完了いたしました。お振込み期限日までに下記お振込み金額をお支払いください。<br/>
                    <br/>
                    お振込み期限日：{{register_due_date}}<br/>
                    お振込み金額：{{payment_amount}}円<br/>
                    お振込み先：{{transfer_destination}}<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            [
                'from_page' => U402,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込み完了',
                'content' => '
                    更新手続きのお申込みを完了いたしました。<br/>
                    特許庁への更新手続き完了のお知らせをお待ちください。<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            // U402cancel -> click 「確認」
            [
                'from_page' => U402CANCEL,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 更新手続き中止について',
                'content' => '
                    更新手続きが中止されました。<br/>
                    <br/>
                ' . $footer,
            ],
            // U302_402_5yr_kouki  -> u000common_payment -> click 「決定」
            [
                'from_page' => U302_402_5YR_KOUKI,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム 未完了：お申込みありがとうございます',
                'content' => '
                    納付手続きのお申込みを承りました。お支払い完了後に手続きを進めます。<br/>
                    お振込み期限日までに下記お振込み金額をお支払いください。<br/>
                    <br/>
                    お振込み期限日：{{register_due_date}}<br/>
                    お振込み金額：{{payment_amount}}円<br/>
                    お振込み先：{{transfer_destination}}<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
            [
                'from_page' => U302_402_5YR_KOUKI,
                'type' => MailTemplate::CREDIT_CARD,
                'subject' => '【AMS】商標出願包括管理システム お申込み完了',
                'content' => '
                    納付手続きのお申込みを完了いたしました。<br/>
                    <br/>
                    ※お申込み後の返金は一切承ることはできませんのでご了承ください。<br/>
                ' . $footer,
            ],
        ];

        $cntBankAdminUx1xx = '
            お支払いを確認致しました。<br/>
            2,3営業後にご返信致します。<br/>
            <br/>
        ' . $footer;

        $routeLogin = route('auth.login');
        $routeLogin = str_replace('test_ams/test_ams', 'test_ams', $routeLogin);
        $urlLogin = "
            【ログインURL】<br/>
            <a href='$routeLogin'>$routeLogin</a>
        ";

        $mailSend2Admin = [
            // payment click 「入金完了」 hoặc 「処理済み」 -> U011
            [
                'from_page' => U011,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U021
            [
                'from_page' => U021,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U011b
            [
                'from_page' => U011B,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U011b_31
            [
                'from_page' => U011B_31,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U021b
            [
                'from_page' => U021B,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U021b_31
            [
                'from_page' => U021B_31,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U031
            [
                'from_page' => U031,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U031b
            [
                'from_page' => U031B,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U031c
            [
                'from_page' => U031C,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U031D
            [
                'from_page' => U031D,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U031edit
            [
                'from_page' => U031EDIT,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U031edit_with_number
            [
                'from_page' => U031_EDIT_WITH_NUMBER,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // U032 -> A000anken_top -> お客様へ連絡
            [
                'from_page' => U032,
                'guard_type' => MailTemplate::GUARD_TYPE_ADMIN,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム 手続き完了',
                'content' => "
                    商標出願包括管理システムをご利用いただき、ありがとうございます。<br>
                    特許庁への手続きが完了しました。<br>
                    審査結果が来るまでお待ちください。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A201a -> A000anken_top -> お客様へ連絡
            [
                'from_page' => A201A,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム 拒絶理由通知が届きました',
                'content' => "
                    商標出願包括管理システムをご利用いただき、ありがとうございます。<br>
                    特許庁の審査の結果、拒絶理由通知が来ました。期限内に、審査官の指摘に合わせて必要な書類を準備し、拒絶理由がなくなれば登録へと進んで行く可能性があります。<br>
                    速やかにログインして対応をご検討ください。<br>
                    このまま放置すると登録になりませんのでご注意ください。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A201b02s, A201b02n_s -> click 「お客様へ表示」
            [
                'from_page' => A201B02S,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 登録可能性評価レポートのお知らせ',
                'content' => "
                    登録可能性評価レポートをお知らせいたします。速やかに登録可能性評価レポートをご覧いただき、拒絶理由通知対応を行う商品・サービスを選択してください。<br>
                    回答期限を越えると、拒絶理由通知対応ができなくなりますので、ご注意ください。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            [
                'from_page' => A201B02_S_N,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 登録可能性評価レポートのお知らせ',
                'content' => "
                    登録可能性評価レポートをお知らせいたします。速やかに登録可能性評価レポートをご覧いただき、拒絶理由通知対応を行う商品・サービスを選択してください。<br>
                    回答期限を越えると、拒絶理由通知対応ができなくなりますので、ご注意ください。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A202n -> click 「事前質問完了し責任者へ承認依頼」
            [
                'from_page' => A202N,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 登録可能性評価レポートのお知らせ',
                'content' => 'Test',
            ],
            // A204han_n -> click 「方針案再提示へ」
            // [
            //     'from_page' => A204HAN_N,
            //     'type' => MailTemplate::TYPE_OTHER,
            //     'subject' => '拒絶理由通知対応：方針案選択/必要資料提出済・返信待ち',
            //     'content' => 'Test',
            // ]

            // A205hiki|A205shu -> A205kakunin -> click 「お客様へ表示」
            [
                'from_page' => A205_KAKUNIN,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 提出書類のご確認をお願いいたします',
                'content' => "
                    特許庁に提出する書類について、ご確認をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A-205s-> A205kakunin-> A000anken_top-> click 「お客様へ表示」
            [
                'from_page' => A205S,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム 拒絶理由通知対応完了のお知らせ',
                'content' => "
                    特許庁に拒絶理由通知対応の書類を提出いたしました。<br>
                    トップページのお知らせをご確認下さい。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A205hiki-> A205kakunin-> A000anken_top-> click 「お客様へ表示」
            [
                'from_page' => A205_HIKI,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム 拒絶理由通知対応完了のお知らせ',
                'content' => "
                    特許庁に拒絶理由通知対応の書類を提出いたしました。<br>
                    トップページのお知らせをご確認下さい。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A205shu-> A205kakunin-> A000anken_top-> click 「お客様へ表示」
            [
                'from_page' => A205_SHU,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム 拒絶理由通知対応完了のお知らせ',
                'content' => "
                    特許庁に拒絶理由通知対応の書類を提出いたしました。<br>
                    トップページのお知らせをご確認下さい。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U201simple
            [
                'from_page' => U201_SIMPLE,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム ご入金確認のお知らせ',
                'content' => '
                    シンプルプランのお申し込みありがとうございました。ご入金を確認いたしました。<br>
                    対応方針案を作成・提示いたしますのでお待ちください。<br>
                    <br>
                ' . $footer,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U201select01
            [
                'from_page' => U201_SELECT_01,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム ご入金確認のお知らせ',
                'content' => '
                    セレクトプランのお申し込みありがとうございました。ご入金を確認いたしました。<br>
                    登録可能性評価レポートをお待ちください。<br>
                    <br>
                ' . $footer,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U302
            [
                'from_page' => U302,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム ご入金確認のお知らせ',
                'content' => '
                    登録手続きのお申込みありがとうございました。ご入金を確認いたしました。<br>
                    特許庁への登録完了のお知らせをお待ちください。<br>
                    <br>
                ' . $footer,
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> u021n
            [
                'from_page' => U021N,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム お申込みありがとうございました',
                'content' => $cntBankAdminUx1xx,
            ],
            // A301
            [
                'from_page' => A301,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム 登録査定のお知らせ',
                'content' => "
                    商標出願包括管理システムをご利用いただき、ありがとうございます。<br>
                    登録可能な状態になりました。<br>
                    ログインをして登録手続きをお申し込みください。<br>
                    このまま放置すると登録になりませんのでご注意ください。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A302 -> A000anken_top-> click 「お客様へ表示」
            [
                'from_page' => A302,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム 登録手続き完了',
                'content' => "
                    特許庁への登録手続きを完了いたしました。<br>
                    登録証が届きましたら、お知らせいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A303 -> A000anken_top-> click 「お客様へ表示」
            [
                'from_page' => A303,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム 登録証発行のお知らせ',
                'content' => "
                    登録証が発行されました。<br>
                    ログインしてご確認下さい。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U000free
            [
                'from_page' => U000FREE,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム ご入金確認のお知らせ',
                'content' => '
                    フリー履歴追加のお申込みありがとうございました。ご入金を確認いたしました。<br>
                    <br>
                ' . $footer,
            ],
            // A000free02 -> A000anken_top-> click 「お客様へ表示」
            [
                'from_page' => A000FREE02,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム フリー履歴対応完了',
                'content' => "
                    フリー履歴追加の対応を完了いたしました。<br>
                    案件情報ページをご確認下さい。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U031edit_with_number
            [
                'from_page' => U402,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム ご入金確認のお知らせ',
                'content' => '
                    更新手続きのお申込みありがとうございました。ご入金を確認いたしました。<br>
                    特許庁への更新手続き完了のお知らせをお待ちください。<br>
                    <br>
                ' . $footer,
            ],
            // A402 -> A000anken_top-> click 「お客様へ表示」
            [
                'from_page' => A402,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム 更新手続き完了',
                'content' => "
                    更新手続きを完了いたしました。<br>
                    トップページのお知らせをご確認下さい。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // payment click 「入金完了」 hoặc 「処理済み」 -> U031edit_with_number
            [
                'from_page' => U302_402_5YR_KOUKI,
                'type' => MailTemplate::BANK_TRANSFER,
                'subject' => '【AMS】商標出願包括管理システム ご入金確認のお知らせ',
                'content' => '
                    納付手続きのお申込みありがとうございました。ご入金を確認いたしました。<br>
                    <br>
                ' . $footer,
            ],
            // A302_402_5YR_KOUKI -> A000anken_top-> click 「お客様へ表示」
            [
                'from_page' => A302_402_5YR_KOUKI,
                'type' => MailTemplate::TYPE_ANKEN_TOP,
                'subject' => '【AMS】商標出願包括管理システム 納付手続き完了',
                'content' => "
                    納付手続きを完了いたしました。<br>
                    トップページのお知らせをご確認下さい。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A000free_s -> click 「案件トップへ戻ってお客様へ通知」and choose お客様へ報告のみ（庁手続きなし）
            [
                'from_page' => A000FREE_S,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => 'フリー履歴・(書類名)対応完了',
                'content' => "Test",
            ],
            // A031
            [
                'from_page' => A031,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 提出書類のご確認をお願いいたします',
                'content' => "
                    特許庁に提出する書類について、ご確認をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A011S
            [
                'from_page' => A011S,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム ご確認ください',
                'content' => "
                商標出願包括管理システムをご利用いただき、ありがとうございます。<br>
                ご確認が必要な事項があります。<br>
                ログインのうえ、内容のご確認をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A021KAN
            [
                'from_page' => A021KAN,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム ご確認ください',
                'content' => "
                商標出願包括管理システムをご利用いただき、ありがとうございます。<br>
                ご確認が必要な事項があります。<br>
                ログインのうえ、内容のご確認をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A021S
            [
                'from_page' => A021S,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム ご確認ください',
                'content' => "
                商標出願包括管理システムをご利用いただき、ありがとうございます。<br>
                ご確認が必要な事項があります。<br>
                ログインのうえ、内容のご確認をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A021RUI_SHU
            [
                'from_page' => A021RUI_SHU,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム ご確認ください',
                'content' => "
                商標出願包括管理システムをご利用いただき、ありがとうございます。<br>
                ご確認が必要な事項があります。<br>
                ログインのうえ、内容のご確認をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A202S
            [
                'from_page' => A202S,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 事前質問にご回答ください',
                'content' => "
                拒絶理由通知対応を行うにあたり、事前質問にご回答ください。<br>
                回答期限を越えると、拒絶理由通知対応ができなくなりますので、ご注意ください。<br>
                ログインのうえ、内容をご確認・ご回答をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A202S
            [
                'from_page' => A202N_S,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 事前質問にご回答ください',
                'content' => "
                拒絶理由通知対応を行うにあたり、事前質問にご回答ください。<br>
                回答期限を越えると、拒絶理由通知対応ができなくなりますので、ご注意ください。<br>
                ログインのうえ、内容をご確認・ご回答をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A203S
            [
                'from_page' => A203S,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 方針案をご選択ください',
                'content' => "
                拒絶理由通知対応の方針案をご選択ください。<br>
                回答期限を越えると、拒絶理由通知対応ができなくなりますので、ご注意ください。<br>
                ログインのうえ、内容をご確認・ご回答をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // A203C_SHU
            [
                'from_page' => A203C_SHU,
                'type' => MailTemplate::TYPE_OTHER,
                'subject' => '【AMS】商標出願包括管理システム 方針案をご選択ください',
                'content' => "
                拒絶理由通知対応の方針案をご選択ください。<br>
                回答期限を越えると、拒絶理由通知対応ができなくなりますので、ご注意ください。<br>
                ログインのうえ、内容をご確認・ご回答をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ]
        ];

        $mailRemindData = [
            // U202, U202n-> 回答要・回答期限を超えたらリマインド
            [
                'from_page' => U202,
                'type' => MailTemplate::TYPE_REMIND_JOB,
                'subject' => '【AMS】商標出願包括管理システム 事前質問への回答について',
                'content' => "
                    事前質問への回答期限を経過しました。<br>
                    特許庁への応答期限日に間に合わない可能性がありますので、速やかに回答をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // U203, U203n->  回答要・回答期限を超えたらリマインド
            [
                'from_page' => U203,
                'type' => MailTemplate::TYPE_REMIND_JOB,
                'subject' => '【AMS】商標出願包括管理システム 方針案への回答について',
                'content' => "
                    方針案への回答期限を経過しました。<br>
                    特許庁への応答期限日に間に合わない可能性がありますので、速やかに回答をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // U204->  回答要・回答期限を超えたらリマインド
            [
                'from_page' => U204,
                'type' => MailTemplate::TYPE_REMIND_JOB,
                'subject' => '【AMS】商標出願包括管理システム 必要資料の提出について',
                'content' => "
                    不足している資料の提出期限を経過しました。<br>
                    特許庁への応答期限日に間に合わない可能性がありますので、速やかに提出をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // U302 -> 1st, 2nd time-> 回答要・(Aパック)リマインド
            [
                'from_page' => U302 . '_1_2',
                'type' => MailTemplate::TYPE_REMIND_JOB,
                'subject' => '【AMS】商標出願包括管理システム 登録手続きお申込みについて',
                'content' => "
                    登録手続きのお申込み期限が近づいています。登録可能な期限日を超えた場合、ご登録いただけませんので、速やかにお申込み下さい。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // U302 -> 3rd time-> 回答要・(Aパック)リマインド
            [
                'from_page' => U302 . '_3',
                'type' => MailTemplate::TYPE_REMIND_JOB,
                'subject' => '【AMS】商標出願包括管理システム 登録手続きお申込みについて',
                'content' => "
                    登録手続きのお申込み期限を経過しました。<br>
                    再度出願する場合は、出願手続きのページよりお申込み下さい。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // U000FREE -> 回答要・(Aパック)リマインド
            [
                'from_page' => U000FREE,
                'type' => MailTemplate::TYPE_REMIND_JOB,
                'subject' => '【AMS】商標出願包括管理システム ご回答ください',
                'content' => "
                    ご確認・ご回答が必要な事項があります。<br>
                    ログインのうえ、内容をご確認・ご回答をお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // U402,U402tsuino -> 回答要・回答期限を超えたらリマインド
            [
                'from_page' => U402,
                'type' => MailTemplate::TYPE_REMIND_JOB,
                'subject' => '【AMS】商標出願包括管理システム 更新手続きのお申込みについて',
                'content' => "
                    更新手続きのお申込みの期限を経過しました。<br>
                    更新手続きを行えない可能性がありますので、速やかにお申込みをお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            //u302_402_5yr_kouki, u302_402tsuino_5yr_kouki -> 回答要・回答期限を超えたらリマインド
            [
                'from_page' => U302_402_5YR_KOUKI,
                'type' => MailTemplate::TYPE_REMIND_JOB,
                'subject' => '【AMS】商標出願包括管理システム 納付手続きのお申込みについて',
                'content' => "
                    納付手続きのお申込みの期限を経過しました。<br>
                    納付手続きを行えない可能性がありますので、速やかにお申込みをお願いいたします。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
            // u302_402_5yr_kouki-> chưa thực hiện gia hạn thương hiệu-> 後期納付期限日+6ヶ月
            [
                'from_page' => U302_402_5YR_KOUKI . '_6_months',
                'type' => MailTemplate::TYPE_REMIND_JOB,
                'subject' => '【AMS】商標出願包括管理システム 納付手続きのお申込みについて',
                'content' => "
                    納付手続きのお申込み期限を経過しました。<br>
                    特許庁への費用を、通常の2倍支払うことで手続きが可能です。<br>
                    $urlLogin<br>
                    <br>
                    $footer
                ",
            ],
        ];

        MailTemplate::truncate();
        foreach ($mailSend2User as $key => $value) {
            $value['lang'] = MailTemplate::LANG_JP;
            $value['guard_type'] = MailTemplate::GUARD_TYPE_USER;
            $value['cc'] = null;
            $value['bcc'] = null;
            $value['bcc'] = null;
            $value['attachment'] = null;
            $mailSend2User[$key] = array_merge($value, $mailSend2User[$key]);
        }

        foreach ($mailSend2Admin as $key => $value) {
            $value['lang'] = MailTemplate::LANG_JP;
            $value['guard_type'] = MailTemplate::GUARD_TYPE_ADMIN;
            $value['cc'] = null;
            $value['bcc'] = null;
            $value['bcc'] = null;
            $value['attachment'] = null;
            $mailSend2Admin[$key] = array_merge($value, $mailSend2Admin[$key]);
        }

        foreach ($mailRemindData as $key => $value) {
            $value['lang'] = MailTemplate::LANG_JP;
            $value['guard_type'] = MailTemplate::GUARD_TYPE_ADMIN;
            $value['cc'] = null;
            $value['bcc'] = null;
            $value['bcc'] = null;
            $value['attachment'] = null;
            $mailRemindData[$key] = array_merge($value, $mailRemindData[$key]);
        }

        MailTemplate::insert($mailSend2User);
        MailTemplate::insert($mailSend2Admin);
        MailTemplate::insert($mailRemindData);
    }
}
