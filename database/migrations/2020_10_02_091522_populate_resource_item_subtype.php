<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PopulateResourceItemSubtype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Jack and Niall
        DB::statement('INSERT INTO `resource_item_subtype` (`resource_id`, `item_subtype_id`, `created_at`) VALUES (1, 1, NOW())');
        DB::statement('INSERT INTO `resource_item_subtype` (`resource_id`, `item_subtype_id`, `created_at`) VALUES (3, 1, NOW())');

        // Holiday 2018 and 2020
        DB::statement('INSERT INTO `resource_item_subtype` (`resource_id`, `item_subtype_id`, `created_at`) VALUES (2, 2, NOW())');
        DB::statement('INSERT INTO `resource_item_subtype` (`resource_id`, `item_subtype_id`, `created_at`) VALUES (157, 2, NOW())');

        // Office storage and Niall's words
        DB::statement('INSERT INTO `resource_item_subtype` (`resource_id`, `item_subtype_id`, `created_at`) VALUES (4, 3, NOW())');
        DB::statement('INSERT INTO `resource_item_subtype` (`resource_id`, `item_subtype_id`, `created_at`) VALUES (163, 3, NOW())');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No down. not removing relations
    }
}
