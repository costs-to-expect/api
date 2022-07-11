<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('item_type_simple_item');
        Schema::dropIfExists('item_type_simple_item');
        DB::delete(
        "DELETE FROM `item_sub_category` WHERE `sub_category_id` IN 
            (SELECT `id` FROM `sub_category` WHERE `category_id` NOT IN (1, 2, 3 ))
        ");
        DB::delete(
            "DELETE FROM `item_category` WHERE `category_id` IN 
            (SELECT `id` FROM `category` WHERE `resource_type_id` NOT IN (1, 7))
        ");
        DB::delete(
        "DELETE FROM `sub_category` WHERE `category_id` NOT IN (1, 2, 3 ))"
        );
        DB::delete(
        "DELETE FROM `category` WHERE `resource_type_id` NOT IN (1, 7))"
        );
        DB::delete(
        "DELETE FROM `item` WHERE `resource_id` NOT IN (1, 3, 168, 184, 573)"
        );
        DB::delete(
        "DELETE FROM `resource_item_subtype` WHERE `resource_id` NOT IN (1, 3, 168, 184, 573)"
        );
        DB::delete(
        "DELETE FROM `resource` WHERE `id` NOT IN (1, 3, 168, 184, 573)"
        );
        DB::delete(
        "DELETE FROM `resource_type_item_type` WHERE `resource_type_id` NOT IN (1, 7)"
        );
        DB::delete(
        "DELETE FROM `permitted_user` WHERE `resource_type_id` NOT IN (1, 7)"
        );
        DB::delete(
        "DELETE FROM `resource_type` WHERE `id` NOT IN (1, 7)"
        );
        DB::delete(
        "DELETE FROM `item_subtype` WHERE `item_type_id` NOT IN (1, 4)"
        );
        DB::delete(
        "DELETE FROM `item_type` WHERE `id` NOT IN (1, 4)"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
