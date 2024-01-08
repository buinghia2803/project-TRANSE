<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSubmissionSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc_submission_sets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('doc_submission_id')->comment('提出書類のID(doc_submissions.id)');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->bigInteger('agent_group_id')->comment('セットのID（agent_groups.id）');
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
        Schema::dropIfExists('doc_submission_sets');
    }
}
