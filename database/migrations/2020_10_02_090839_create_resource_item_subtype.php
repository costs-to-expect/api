<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceItemSubtype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_item_subtype', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id');
            $table->unsignedBigInteger('resource_id');
            $table->unsignedTinyInteger('item_subtype_id');
            $table->timestamps();
            $table->foreign('resource_id')->references('id')->on('resource');
            $table->foreign('item_subtype_id')->references('id')->on('item_subtype');
            $table->unique(['resource_id', 'item_subtype_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_item_subtype');
    }
}
