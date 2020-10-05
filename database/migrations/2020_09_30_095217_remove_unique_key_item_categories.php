<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueKeyItemCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_category', function (Blueprint $table) {
            $table->dropForeign('item_category_category_id_foreign');
            $table->dropForeign('item_category_item_id_foreign');

            $table->dropUnique('item_category_item_id_unique');

            $table->unique(['item_id', 'category_id']);

            $table->foreign('item_id')->references('id')->on('item');
            $table->foreign('category_id')->references('id')->on('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_category', function (Blueprint $table) {
            $table->dropForeign('item_category_category_id_foreign');
            $table->dropForeign('item_category_item_id_foreign');

            $table->dropUnique(['item_id', 'category_id']);

            $table->unique(['item_id']);

            $table->foreign('item_id')->references('id')->on('item');
            $table->foreign('category_id')->references('id')->on('category');
        });
    }
}
