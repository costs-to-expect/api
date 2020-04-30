<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailToItemTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_type', function (Blueprint $table) {
            $table->string('example')->after('description')->nullable();
            $table->string('friendly_name')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_type', function (Blueprint $table) {
            $table->dropColumn('example');
            $table->dropColumn('friendly_name');
        });
    }
}
