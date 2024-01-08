<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachingResultApplicantArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maching_result_applicant_articles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('maching_result_id');
            $table->string('applicant_division')->nullable();
            $table->string('applicant_identification_number')->nullable();
            $table->string('applicant_name')->nullable();
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
        Schema::dropIfExists('maching_result_applicant_articles');
    }
}
