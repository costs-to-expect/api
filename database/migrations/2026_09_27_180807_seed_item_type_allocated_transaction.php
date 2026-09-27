<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = date('Y-m-d H:i:s');

        DB::insert("
            INSERT INTO
            `item_type` (`id`, `name`, `friendly_name`, `description`, `example`, `created_at`, `updated_at`)
            VALUES
            (7, 'allocated-transaction', 'Create a transaction chronological tracker', 'Track income and expenses over time in a single tracker.', 'Examples include, a shared household account or a small business current account.', ?, NULL)
        ", [$now]);

        DB::insert("
            INSERT
            INTO
            `item_subtype` (`id`, `item_type_id`, `name`, `friendly_name`, `description`, `created_at`, `updated_at`)
            VALUES
            (10, 7, 'default', 'Default behaviour', 'Default behaviour for the allocated-transaction type', ?, NULL)
        ", [$now]);
    }

    public function down(): void
    {
        DB::delete("DELETE FROM `item_subtype` WHERE `id` = 10");
        DB::delete("DELETE FROM `item_type` WHERE `id` = 7");
    }
};
