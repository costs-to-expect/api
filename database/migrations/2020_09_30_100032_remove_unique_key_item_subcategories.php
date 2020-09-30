<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueKeyItemSubcategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_sub_category', function (Blueprint $table) {
            $table->dropForeign('item_sub_category_item_category_id_foreign');
            $table->dropForeign('item_sub_category_sub_category_id_foreign');
            $table->dropUnique(['item_category_id', 'sub_category_id']);

            $table->foreign('item_category_id')->references('id')->on('item_category');
            $table->foreign('sub_category_id')->references('id')->on('sub_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_sub_category', function (Blueprint $table) {
            $table->dropForeign('item_sub_category_item_category_id_foreign');
            $table->dropForeign('item_sub_category_sub_category_id_foreign');

            $table->foreign('item_category_id')->references('id')->on('item_category');
            $table->foreign('sub_category_id')->references('id')->on('sub_category');

            $table->unique(['item_category_id', 'sub_category_id']);
        });
    }
}
