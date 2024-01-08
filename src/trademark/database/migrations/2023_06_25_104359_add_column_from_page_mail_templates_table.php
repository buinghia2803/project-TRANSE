<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFromPageMailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->string('from_page', 100)->after('deleted_at')->nullable()->comment('mail sent from screen, ex: u011, u021,...');
            $table->tinyInteger('guard_type')->after('deleted_at')->default(1)->comment('1: send mail from user web, 2: send mail from admin web');
            $table->boolean('type')->default(1)->nullable()->comment('1: Credit card, 2: bank transfer')->change();
            $table->string('lang', 10)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->dropColumn('from_page');
            $table->dropColumn('guard_type');
            $table->boolean('type')->nullable(false)->change();
            $table->string('lang', 10)->nullable(false)->change();
        });
    }
}
