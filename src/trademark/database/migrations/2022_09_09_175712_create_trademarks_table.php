<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trademarks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('ユーザのID（users.id）');
            $table->string('trademark_number', 11)->unique()->comment('仮申込番号（LAnnnyynnnn）か申込番号（QyyANNN0C）');
            $table->string('application_number', 11)->unique()->comment('出願番号');
            $table->tinyInteger('type_trademark')->default(1)->comment('商標出願種別. 1: 文字 | 2: それ以外（装飾文字、ロゴ絵柄等）');
            $table->string('name_trademark', 30)->comment('商標名');
            $table->text('image_trademark')->nullable()->comment('装飾文字/ロゴ絵柄の画像');
            $table->string('reference_number', 20)->nullable()->comment('お客様整理番号');
            $table->boolean('status_management')->default(1)->comment('管理のステータス. 0:false | 1: true');
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
        Schema::dropIfExists('trademarks');
    }
}
