<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateItemSubtype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_subtype', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->tinyIncrements('id');
            $table->unsignedInteger('item_type_id');
            $table->string('name', 255);
            $table->string('description');
            $table->timestamps();
            $table->index('name');
            $table->foreign('item_type_id')->references('id')->on('item_type');
        });

        DB::statement('INSERT INTO `item_subtype` (`item_type_id`, `name`, `description`, `created_at`) VALUES (1, "default", "Default behaviour for the allocated-exense type", NOW())');
        DB::statement('INSERT INTO `item_subtype` (`item_type_id`, `name`, `description`, `created_at`) VALUES (2, "default", "Default behaviour for the simple-expense type", NOW())');
        DB::statement('INSERT INTO `item_subtype` (`item_type_id`, `name`, `description`, `created_at`) VALUES (3, "default", "Default behaviour for the simple-item type", NOW())');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_subtype');
    }
}
