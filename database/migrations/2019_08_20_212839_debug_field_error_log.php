<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DebugFieldErrorLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_error_log', function (Blueprint $table) {
            $table->string('debug', 255)
                ->after('source')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_error_log', function (Blueprint $table) {
            $table->dropColumn('debug');
        });
    }
}
