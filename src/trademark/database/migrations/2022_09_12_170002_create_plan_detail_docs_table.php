<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanDetailDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_detail_docs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_detail_id')->comment('方針案の詳細のID（plan_details.id）');
            $table->tinyInteger('doc_requirement')->default(1)->comment('1: 資格を有することを証明する書面 | 2: 新聞・雑誌の記事、チラシ・ホームページなどの印刷物 | 3: 使用意思（フォーマット付き） | 4: 承諾書（フォーマット付き） | 5: 自由記述 | 6: 不要');
            $table->string('doc_requirement_des', 500);
            $table->text('attachment_user')->nullable()->comment('アップロードしたお客様のファイルのURL。フォーマット： ["attach 1", "attach 2"]');
            $table->text('attachment_ams')->nullable()->comment('AMSのファイルのURL。フォーマット： ["attach 1", "attach 2"]');
            $table->boolean('is_completed')->default(0)->comment('完了のステータス. 0:false | 1:true');
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
        Schema::dropIfExists('plan_detail_docs');
    }
}
