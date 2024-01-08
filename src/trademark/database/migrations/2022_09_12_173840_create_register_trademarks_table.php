<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_trademarks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trademark_id')->comment('商標登録のID（trademarks.id）');
            $table->date('user_response_deadline')->nullable()->comment('お客様回答期限日');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->tinyInteger('period_registration')->default(1)->comment('特許庁費用（4区分）　登録期間. 1: 5年 | 2: 10年');
            $table->float('period_registration_fee')->comment('登録期間の料金');
            $table->float('period_change_fee')->nullable()->comment('登録期間を5年から10年に変更する の料金');
            $table->float('reg_period_change_fee')->nullable()->comment('登録期間変更手数料の料金');
            $table->float('mailing_register_cert_fee')->nullable()->comment('登録証の郵送希望の料金');
            $table->bigInteger('regist_cert_nation_id')->comment('所在国のID（m_nations.id）');
            $table->string('regist_cert_postal_code', 7)->comment('郵便番号');
            $table->string('regist_cert_address', 255)->comment('住所');
            $table->string('regist_cert_payer_name', 50)->comment('宛先名');
            $table->tinyInteger('trademark_info_change_status')->nullable()->comment('1: 出願人名称 | 2: 住所 | 3: 名称＆住所');
            $table->float('trademark_info_change_fee')->comment('住所変更の料金');
            $table->tinyInteger('info_type_acc')->nullable()->comment('出願人種別 | 1: 法人 | 2: 個人');
            $table->bigInteger('trademark_info_nation_id')->comment('所在国のID（m_nations.id）');
            $table->bigInteger('trademark_info_address_first')->comment('出願人所在地または住所-1 都道府県のID（m_prefectures.id）');
            $table->string('trademark_info_address_second', 255)->nullable()->comment('出願人所在地または住所-2');
            $table->string('trademark_info_address_three', 255)->nullable()->comment('出願人所在地または住所-3');
            $table->string('trademark_info_name', 50)->nullable()->comment('出願人名');
            $table->string('option', 50)->comment('【オプション】。フォーマット： ["option 1", "option 2"]');
            $table->boolean('is_payment')->default(0)->comment('支払ステータス. 0:false | 1:true');
            $table->boolean('is_cancel')->default(0)->comment('中止のステータス. 0:false | 1:true');
            $table->bigInteger('agent_group_id')->comment('代理人のID(agent_groups.id)');
            $table->string('display_info_status', 20)->comment('登録料の表示。フォーマット： ["1", "2"]. 1: 商標法第６８条の４０第２項の規定による手続補正書を同時に提出 | 2: 名称（氏名）変更届を提出 | 3: 住所変更届を提出');
            $table->date('date_register')->nullable()->comment('【登録日】');
            $table->string('register_number', 12)->nullable()->comment('【登録番号】');
            $table->string('ams_comment', 500)->nullable()->comment('管理者のコメント');
            $table->tinyInteger('extension_status')->nullable()->comment('1: 後期納付期限 | 2: 更新期限');
            $table->string('representative_name', 255)->nullable()->comment('委任状の代表者氏名');
            $table->boolean('is_register_change_info')->default(0)->comment('「登録名義人の表示変更登録申請書を提出」のステータス. 0: false | 1: true');
            $table->softDeletes();
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
        Schema::dropIfExists('register_trademarks');
    }
}
