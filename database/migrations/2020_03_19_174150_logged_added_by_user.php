<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LoggedAddedByUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permitted_user', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->default(1)->after('user_id');
            $table->foreign('added_by')->references('id')->on('users');
        });

        Schema::table('permitted_user', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permitted_user', function (Blueprint $table) {
            $table->dropForeign('permitted_user_added_by_foreign');
            $table->dropColumn('added_by');
        });
    }
}
