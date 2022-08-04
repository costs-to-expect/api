<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::insert("
            INSERT
            INTO
            `item_type`(`id`, `name`, `friendly_name`, `description`, `example`, `created_at`, `updated_at`)
            VALUES
            (5, 'budget', 'Budgeting', 'Plan your budgets', 'Annual Personal Budget, Business Budget, Savings plan...', NOW(), NULL)
        ");

        DB::insert("
            INSERT
            INTO
            `item_subtype`(`id`, `item_type_id`, `name`, `friendly_name`, `description`, `created_at`, `updated_at`)
            VALUES
            (NULL, 5, 'default', 'Default behaviour', 'Default behaviour for the budget item type', NOW(), NULL)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
