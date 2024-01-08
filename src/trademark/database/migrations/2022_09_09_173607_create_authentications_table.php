<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthenticationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authentications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('ユーザのID（users.id）');
            $table->tinyInteger('type')->default(1)->comment('1: 会員登録 | 2: 退会 | 3: パスワード再設定 | 4: 新しいパスワード・登録メールアドレス再設定 | 5: 登録メールアドレスの変更');
            $table->string('value', 255)->nullable();
            $table->string('token', 60)->comment('トークン');
            $table->string('code', 12)->nullable()->comment('認証コード');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authentications');
    }
}
