<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddGameItemTypeAndSubtypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('INSERT INTO `item_type` (`name`, `friendly_name`, `description`, `example`, `created_at`) VALUES ("game", "Board and card games", "Track your board and card game sessions with your friends and family.", "Check the item_subtype collection, more added on request", NOW())');

        DB::statement('INSERT INTO `item_subtype` (`item_type_id`, `name`, `description`, `created_at`) VALUES (4, "carcassonne", "Track your Carcassonne games, wins and losses", NOW())');
        DB::statement('INSERT INTO `item_subtype` (`item_type_id`, `name`, `description`, `created_at`) VALUES (4, "scrabble", "Track your Scrabble games, wins and losses", NOW())');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No down, we aren't removing subtypes or types
    }
}
