<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemTypeAllocatedExpense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_type_allocated_expense', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->string('description');
            $table->date('effective_date');
            $table->date('publish_after')->nullable();
            $table->decimal('total', 10, 2);
            $table->unsignedTinyInteger('percentage');
            $table->decimal('actualised_total', 10, 2);
            $table->timestamps();
            $table->foreign('item_id')->references('id')->on('item');
            $table->index('effective_date');
            $table->index('publish_after');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_type_allocated_expense');
    }
}
