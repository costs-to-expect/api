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
                VALUES ('NOK', 'Norwegian Krone', NOW())"
        );
    }

    public function down()
    {
        // No down
    }
};
