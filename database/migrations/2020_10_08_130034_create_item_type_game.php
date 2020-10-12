<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemTypeGame extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_type_game', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->string('name'); // Game and Datetime??
            $table->string('description')->nullable();
            $table->longText('players');
            $table->longText('game');
            $table->longText('statistics');
            $table->char('winner', 10)->nullable();
            $table->integer('score', false, true)->default(0);
            $table->tinyInteger('complete', false, true)->default(0);
            $table->timestamps();
            $table->foreign('item_id')->references('id')->on('item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_type_game');
    }
}
