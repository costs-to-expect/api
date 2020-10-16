<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateItemTypeNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('UPDATE `item_type` SET `friendly_name` = "Create an expense chronological tracker" WHERE `name` = "allocated-expense"');
        DB::statement('UPDATE `item_type` SET `friendly_name` = "Create an expense bucket tracker" WHERE `name` = "simple-expense"');
        DB::statement('UPDATE `item_type` SET `friendly_name` = "Create a list" WHERE `name` = "simple-item"');
        DB::statement('UPDATE `item_type` SET `friendly_name` = "Track a game" WHERE `name` = "game"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No down
    }
}
