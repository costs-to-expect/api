<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFriendlyName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_subtype', function (Blueprint $table) {
            $table->string('friendly_name')->after('name')->nullable();
        });

        DB::statement('UPDATE `item_subtype` SET `friendly_name` = "Default behaviour" WHERE `item_type_id` = 1');
        DB::statement('UPDATE `item_subtype` SET `friendly_name` = "Default behaviour" WHERE `item_type_id` = 2');
        DB::statement('UPDATE `item_subtype` SET `friendly_name` = "Default behaviour" WHERE `item_type_id` = 3');
        DB::statement('UPDATE `item_subtype` SET `friendly_name` = "Carcassonne board games" WHERE `id` = 4');
        DB::statement('UPDATE `item_subtype` SET `friendly_name` = "Scrabble board games" WHERE `id` = 5');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_subtype', function (Blueprint $table) {
            $table->dropColumn('friendly_name');
        });
    }
}
