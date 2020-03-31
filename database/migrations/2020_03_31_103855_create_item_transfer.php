<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_transfer', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id');
            $table->unsignedBigInteger('resource_type_id');
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('transferred_by');
            $table->timestamps();
            $table->foreign('resource_type_id')->references('id')->on('resource_type');
            $table->foreign('from')->references('id')->on('resource');
            $table->foreign('to')->references('id')->on('resource');
            $table->foreign('item_id')->references('id')->on('item');
            $table->foreign('transferred_by')->references('id')->on('users');
            $table->unique(['resource_type_id', 'from', 'item_id'], 'unique_item_partial_transfer');
        });
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
}
