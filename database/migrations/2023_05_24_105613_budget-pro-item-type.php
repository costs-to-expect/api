<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::insert("
            INSERT
            INTO
            `item_type`(`id`, `name`, `friendly_name`, `description`, `example`, `created_at`, `updated_at`)
            VALUES
            (6, 'budget-pro', 'Budgeting', 'Plan your budgets', 'Annual Personal Budget, Business Budget, Savings plan...', NOW(), NULL)
        ");

        DB::insert("
            INSERT
            INTO
            `item_subtype`(`id`, `item_type_id`, `name`, `friendly_name`, `description`, `created_at`, `updated_at`)
            VALUES
            (NULL, 6, 'default', 'Default behaviour', 'Default behaviour for the budget pro item type', NOW(), NULL)
        ");

        Schema::create('item_type_budget_pro', static function (Blueprint $table) {

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

    public function down()
    {
        // No down
    }
};
