<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddSimpleItemType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('INSERT INTO `item_type` (`name`, `description`, `created_at`) VALUES ("simple-item", "Log a list of items including their quantities.", NOW())');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No down, we are not removing this type
    }
}
