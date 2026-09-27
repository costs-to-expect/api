<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_type_allocated_transaction', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('effective_date');
            $table->date('publish_after')->nullable();
            $table->unsignedTinyInteger('currency_id')->default(1);
            $table->decimal('total', 13, 2);
            $table->tinyInteger('percentage');
            $table->decimal('actualised_total', 13, 2);
            $table->enum('transaction_type', ['expense', 'income']);
            $table->timestamps();

            $table->foreign('item_id')
                ->references('id')
                ->on('item');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currency');
            $table->index('effective_date');
            $table->index('publish_after');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_type_allocated_transaction');
    }
};
