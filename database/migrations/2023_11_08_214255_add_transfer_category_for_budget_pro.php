<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('item_type_budget_pro', function (Blueprint $table) {

            DB::statement("
                ALTER TABLE item_type_budget_pro MODIFY category ENUM('income', 'fixed', 'flexible', 'savings', 'transfer') NOT NULL
            ");

        });
    }

    public function down()
    {
        // No down
    }
};
