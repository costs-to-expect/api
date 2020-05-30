<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemTypeSimpleExpense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_type_simple_expense', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->string('description');
            $table->date('effective_date');
            $table->decimal('total', 10, 2);
            $table->timestamps();
            $table->foreign('item_id')->references('id')->on('item');
        });

        DB::statement('INSERT INTO `item_type` (`name`, `description`, `created_at`) VALUES ("simple-expense", "Track expenses for a specific event or function.", NOW())');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_type_simple_expense');
    }
}
