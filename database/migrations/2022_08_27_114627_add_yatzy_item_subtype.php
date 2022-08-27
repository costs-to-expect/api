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
            `item_subtype`(`id`, `item_type_id`, `name`, `friendly_name`, `description`, `created_at`, `updated_at`)
            VALUES
            (NULL, 4, 'yatzy', 'Yatzy', 'Track your Yatzy games, wins and losses', NOW(), NULL)
        ");
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
