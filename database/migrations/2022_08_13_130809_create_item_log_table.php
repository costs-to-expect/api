<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('item_log', static function (Blueprint $table) {

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('message');
            $table->json('parameters')->nullable();
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('item');
        });
    }

    public function down()
    {
        // No down
    }
};
