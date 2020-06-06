<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PopulateItemTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('UPDATE `item_type` SET `friendly_name` = "Items" WHERE `name` = "simple-item"');
        DB::statement('UPDATE `item_type` SET `example` = "Examples include, stock, your lego collection or your Barry White CD collection" WHERE `name` = "simple-item"');

        DB::statement('UPDATE `item_type` SET `friendly_name` = "Bucket expenses" WHERE `name` = "simple-expense"');
        DB::statement('UPDATE `item_type` SET `example` = "Track expenses for a specific event or function." WHERE `name` = "simple-expense"');

        DB::statement('UPDATE `item_type` SET `friendly_name` = "Chronological expenses" WHERE `name` = "allocated-expense"');
        DB::statement('UPDATE `item_type` SET `example` = "Examples include, the cost to raise a child and start-up expenses for your business." WHERE `name` = "allocated-item"');
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
