<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefactorMachingResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maching_results', function (Blueprint $table) {
            $table->dropForeign('maching_results_admin_id_foreign');
            $table->dropForeign('maching_results_application_trademark_id_foreign');
        });

        Schema::table('comparison_trademark_results', function (Blueprint $table) {
            $table->dropForeign('comparison_trademark_results_maching_result_id_foreign');
        });

        Schema::dropIfExists('maching_results');

        Schema::create('maching_results', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('trademark_id')->comment('商標登録のID（trademarks.id）');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');

            $table->string('document_type', 255);
            $table->tinyInteger('unconfirmed_state')->default(0);
            $table->string('computer_name', 255);
            $table->string('user_name', 255);
            $table->string('distinction_number', 255);

            // relation-file
            $table->string('rf_input_check_result', 255)->nullable();
            $table->string('rf_application_receipt_list', 255)->nullable();

            // result
            $table->string('pi_result_software_message', 255)->nullable();
            $table->tinyInteger('pi_result_level')->nullable();
            $table->string('pi_result_communication_result', 255);
            $table->string('pi_result_fd_and_cdr', 255)->nullable();

            $table->tinyInteger('pi_law');
            $table->string('pi_document_name', 255);
            $table->string('pi_document_code', 255);
            $table->string('pi_file_reference_id', 255)->nullable();
            $table->string('pi_invention_title', 255)->nullable();

            // application-reference
            $table->string('pi_ar_registration_number', 255)->nullable();
            $table->string('pi_ar_application_number', 255)->nullable();
            $table->string('pi_ar_application_date', 255)->nullable();
            $table->string('pi_ar_international_application_number', 255)->nullable();
            $table->string('pi_ar_international_application_date', 255)->nullable();
            $table->string('pi_ar_reference_id', 255)->nullable();
            $table->string('pi_ar_appeal_reference_number', 255)->nullable();
            $table->string('pi_ar_appeal_reference_date', 255)->nullable();
            $table->string('pi_ar_number_of_annexation', 255)->nullable();

            // submission-date
            $table->string('pi_sd_date', 255)->nullable();
            $table->string('pi_sd_time', 255)->nullable();

            $table->string('pi_page', 255)->nullable();
            $table->string('pi_image_total', 255)->nullable();
            $table->string('pi_size', 255)->nullable();
            $table->string('pi_receipt_number', 255)->nullable();
            $table->string('pi_wad_message_digest_compare', 255)->nullable();

            // input-date
            $table->string('pi_ip_date', 255);
            $table->string('pi_ip_time', 255);

            $table->string('pi_html_file_name', 255)->nullable();

            // applicant-article
            $table->integer('pi_aa_total')->nullable();

            $table->string('pi_claims_total', 255)->nullable();
            $table->string('pi_abstract', 255)->nullable();

            // payment
            $table->string('pi_payment_account_number', 255)->nullable();
            $table->string('pi_payment_fee_code', 255)->nullable();
            $table->string('pi_payment_amount', 255)->nullable();

            // representation_image
            $table->text('pi_ri_tile')->nullable();
            $table->text('pi_ri_file_name')->nullable();

            // time-for-response
            $table->string('pi_tfr_division', 255);
            $table->string('pi_tfr_period', 255);

            $table->string('pi_dispatch_number', 255)->nullable();

            // dispatch-date
            $table->string('pi_dd_date', 255);
            $table->string('pi_dd_time', 255);

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
        Schema::dropIfExists('maching_results');
        Schema::create('maching_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->bigInteger('application_trademark_id')->comment('出願登録のID（application_trademarks.id）');
            $table->string('XML_result', 255)->comment('XML: 結果');
            $table->string('XML_document_name', 255)->comment('XML: 書類名');
            $table->string('XML_reference_number', 255)->comment('XML: 整理番号');
            $table->string('XML_application_number', 255)->comment('XML: 出願番号');
            $table->dateTime('XML_delivery_date')->comment('XML: 発送日');
            $table->string('XML_shipping_number', 255)->comment('XML: 発送番号');
            $table->string('target_reference_number', 255)->comment('突合先: 整理番号');
            $table->string('target_application_number', 255)->comment('突合先: 出願番号');
            $table->tinyInteger('type')->default(1)->comment('1: 拒絶理由通知対応 | 2: 登録');
            $table->boolean('is_confirm')->default(0)->comment('確認のステータス. 0: false | 1: true');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('maching_results', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->foreign('admin_id')->references('id')->on('admins');

            $table->unsignedBigInteger('application_trademark_id')->change();
            $table->foreign('application_trademark_id')->references('id')->on('app_trademarks');
        });

        Schema::table('comparison_trademark_results', function (Blueprint $table) {
            $table->unsignedBigInteger('maching_result_id')->change();
            $table->foreign('maching_result_id')->references('id')->on('maching_results');
        });
    }
}
