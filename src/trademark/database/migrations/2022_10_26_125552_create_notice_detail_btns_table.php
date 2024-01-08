<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticeDetailBtnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice_detail_btns', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('notice_detail_id');
            $table->tinyInteger('btn_type')->nullable()->comment('1: HTML作成 | 2: XMLアップロード | 3: 修正 | 4: リマインドメール送信 | 5: お客様へ連絡 | 6: 責任者へ連絡 | 7: PDFアップロード');
            $table->text('url')->nullable();
            $table->dateTime('date_click')->nullable()->comment('【提出日】');

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
        Schema::dropIfExists('notice_detail_btns');
    }
}
