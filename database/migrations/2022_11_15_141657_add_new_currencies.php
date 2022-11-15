<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        \Illuminate\Support\Facades\DB::insert(
            "INSERT INTO `currency` (`code`, `name`, `created_at`) 
                VALUES ('CAD', 'Canadian Dollar', NOW()), 
                       ('AUD', 'Australian Dollar', NOW()),
                       ('NZD', 'New Zealand Dollar', NOW()),
                       ('INR', 'Indian Rupee ', NOW())"
        );
    }

    public function down()
    {
        // No down
    }
};
