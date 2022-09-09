<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_type_budget', static function (Blueprint $table) {

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->uuid('account');
            $table->uuid('target_account')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 13, 2);
            $table->tinyInteger('currency_id')->unsigned();
            $table->enum('category', ['income', 'fixed', 'flexible', 'savings']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('disabled')->default(false);
            $table->json('frequency');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('item');
            $table->foreign('currency_id')->references('id')->on('currency');
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
};
