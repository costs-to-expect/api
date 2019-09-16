<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceTypeItemType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_type_item_type', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id');
            $table->unsignedBigInteger('resource_type_id');
            $table->unsignedTinyInteger('item_type_id');
            $table->timestamps();
            $table->foreign('resource_type_id')->references('id')->on('resource_type');
            $table->foreign('item_type_id')->references('id')->on('item_type');
            $table->unique(['resource_type_id', 'item_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_type_item_type');
    }
}
