<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMPriceListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_price_lists', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('service_type')->default(1)->comment('1: 出願前, 2: 出願, 3: 拒絶理由通知対応, 4: 登録, 5: 5年後期納付（登録）, 6: 各決済時, 7: 登録時, 8: 更新, 9: 5年後期納付（更新）, 10: 変更手続, 11: フリー履歴, 12: その他');
            $table->string('package_type', 10)->default('1')->comment('1.1: はじめからサポート（商標区分選定サポート）　1回につき, 1.2: プレチェックサービス―１（簡易調査サービス）　3商品まで, 1.3: プレチェックサービス―１（簡易調査サービス）　追加3商品ごと, 1.4: プレチェックサービス―２（詳細調査サービス）　3商品まで,, 1.5: プレチェックサービス―２（詳細調査サービス）　追加3商品ごと 2.1: パックA 3商品まで, 2.2: パックA 追加3商品ごと, 2.3: パックB 3商品まで, 2.4: パックB 追加3商品ごと, 2.5: パックC 3商品まで, 2.6: パックC 追加3商品ごと, 3.1: シンプルプラン　基本料金, 3.2: シンプルプラン　追加追加3商品ごと加, 3.3: セレクトプラン　登録可能性評価レポート 基本料金　, 3.4: セレクトプラン　登録可能性評価レポート 1商品ごと, 3.5: セレクトプラン　A評価, 3.6: セレクトプラン　B,C,D,E 評価, 3.7: 商品・サービス名の追加オプション　追加1商品ごと, 3.8: 期限日前　単独申込, 3.9: 期限日前　拒絶通知対応サービスと同時申込, 3.10: 期間外延長サービス, 3.11: 補正対応サービス, 4.1: 登録手続サービス 3商品まで, 4.2: 登録手続サービス　追加3商品ごと, 4.3: 登録手続サービス　登録区分数削減手続サービス, 4.4: 「登録証」送付手数料, 4.5: 出願人住所変更手続 , 4.6: 出願人名称変更手続, 4.7: 登録期間変更手数料（5年から10年へ変更。パックBとパックC用）, 4.8: 登録証選択, 5.1: 納付サービス　3区分まで, 5.2: 納付サービス　追加1区分ごと, 6.1: 銀行振込 取扱手数料, 7.1: 登録証の郵送, 8.1: 更新サービス　3区分まで, 8.2: 更新　追加1区分ごと, 9.1: 納付サービス　3区分まで, 9.2: 納付サービス　追加1区分ごと, 10.1: 住所変更手続, 10.2: 名称変更手続, 11.1: フリー履歴デフォルト手数料, 12.1: 登録証再発行手続');
            $table->float('base_price')->nullable()->comment('本体価格');
            $table->float('pof_1st_distinction_5yrs')->nullable()->comment('pof = patent_office_fees, 特許庁費用 最初の1区分 - 5年');
            $table->float('pof_1st_distinction_10yrs')->nullable()->comment('pof = patent_office_fees, 特許庁費用 最初の1区分 - 10年');
            $table->float('pof_2nd_distinction_5yrs')->nullable()->comment('pof = patent_office_fees, 特許庁費用 2区分目から1区分ごと - 5年');
            $table->float('pof_2nd_distinction_10yrs')->nullable()->comment('pof = patent_office_fees, 特許庁費用 2区分目から1区分ごと - 10年');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_price_lists');
    }
}
