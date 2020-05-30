<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueIndexItemTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_transfer', function (Blueprint $table) {
            $table->dropForeign('item_transfer_from_foreign');
            $table->dropForeign('item_transfer_item_id_foreign');
            $table->dropForeign('item_transfer_resource_type_id_foreign');
            $table->dropForeign('item_transfer_to_foreign');
            $table->dropForeign('item_transfer_transferred_by_foreign');
            $table->dropUnique('unique_item_partial_transfer');
        });

        Schema::table('item_transfer', function (Blueprint $table) {
            $table->foreign('resource_type_id')->references('id')->on('resource_type')->onDelete('cascade');
            $table->foreign('from')->references('id')->on('resource')->onDelete('cascade');
            $table->foreign('to')->references('id')->on('resource')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('item')->onDelete('cascade');
            $table->foreign('transferred_by')->references('id')->on('users')->onDelete('cascade');
        });
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
