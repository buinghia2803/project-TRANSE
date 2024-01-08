<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name_trademark', 30)->nullable()->comment('お考えの商標名');
            $table->boolean('is_image_trademark')->default(0)->comment('お考えの商標がロゴなど画像の種類. 0: false | 1: true');
            $table->string('email', 255)->unique()->comment('メールアドレス');
            $table->string('user_number', 5)->nullable()->unique()->comment('ユーザID, フォーマット：LAnnn');

            // Info
            $table->tinyInteger('info_type_acc')->nullable()->comment('【会員情報】の法人または個人種類. 1: 法人 | 2: 個人');
            $table->string('info_name', 50)->nullable()->comment('【会員情報】の法人名');
            $table->string('info_name_furigana', 50)->nullable()->comment('【会員情報】の法人名（ふりがな）');
            $table->string('info_corporation_number', 255)->nullable()->comment('【会員情報】の法人番号');
            $table->bigInteger('info_nation_id')->nullable()->comment('【会員情報】の所在国のID（m_nations.id）');
            $table->string('info_postal_code', 7)->nullable()->comment('【会員情報】の郵便番号（半角、ハイフンなし）');
            $table->bigInteger('info_prefectures_id')->nullable()->comment('【会員情報】の都道府県のID（m_prefectures.id）');
            $table->string('info_address_second', 255)->nullable()->comment('【会員情報】の所在地または住所-2');
            $table->string('info_address_three', 255)->nullable()->comment('【会員情報】の所在地または住所-3');
            $table->string('info_phone', 15)->nullable()->comment('【会員情報】の電話番号');
            $table->string('info_member_id', 30)->nullable()->unique()->comment('ログインで使用ユーザID');
            $table->string('password', 255)->nullable()->comment('会員パスワード');
            $table->tinyInteger('info_gender')->nullable()->comment('1: 女性 | 2: 男性');
            $table->string('info_birthday', 50)->nullable()->comment('【会員情報】の生年月日');
            $table->string('info_question', 255)->nullable()->comment('パスワード復帰用質問');
            $table->string('info_answer', 255)->nullable()->comment('パスワード復帰用回答');

            // Contact
            $table->tinyInteger('contact_type_acc')->nullable()->comment('【連絡先】の法人または個人種類. 1: 法人 | 2: 個人');
            $table->string('contact_name', 50)->nullable()->comment('【連絡先】の法人名');
            $table->string('contact_name_furigana', 50)->nullable()->comment('【連絡先】の法人名（ふりがな）');
            $table->string('contact_name_department', 50)->nullable()->comment('【連絡先】の所属部署名');
            $table->string('contact_name_department_furigana', 50)->nullable()->comment('【連絡先】の所属部署名（ふりがな）');
            $table->string('contact_name_manager', 50)->nullable()->comment('【連絡先】のご担当者名');
            $table->string('contact_name_manager_furigana', 50)->nullable()->comment('【連絡先】のご担当者名（ふりがな）');
            $table->bigInteger('contact_nation_id')->nullable()->comment('【連絡先】の都道府県のID（m_nations.idテーブル）');
            $table->string('contact_postal_code', 7)->nullable()->comment('【連絡先】の郵便番号（半角、ハイフンなし）');
            $table->bigInteger('contact_prefectures_id')->nullable()->comment('【連絡先】の都道府県のID（m_prefectures.idテーブル）');
            $table->string('contact_address_second', 255)->nullable()->comment('【連絡先】の所在地または住所-2');
            $table->string('contact_address_three', 255)->nullable()->comment('【連絡先】の所在地または住所-3');
            $table->string('contact_phone', 15)->nullable()->comment('【連絡先】の電話番号');
            $table->string('contact_email_second', 255)->nullable()->comment('【連絡先】の連絡用メールアドレス-2');
            $table->string('contact_email_three', 255)->nullable()->comment('【連絡先】の連絡用メールアドレス-3');

            // Status
            $table->tinyInteger('status')->default(1)->comment('0 = 仮の | 1 = 有効');

            // Withdraw
            $table->boolean('status_withdraw')->default(0)->comment('0: 退会済み | 1: 未退会');
            $table->string('reason_withdraw', 255)->nullable()->comment('退会の理由');
            $table->string('problems', 10)->nullable()->comment('確認事項, 保存フォーマット：1,2,3. 1:過去データが閲覧できなくなります。 | 2:全案件が見られなくなります。 | 3:AMSでの管理が終了します。');

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
