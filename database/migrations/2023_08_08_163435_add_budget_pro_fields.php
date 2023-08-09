<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('item_type_budget_pro', static function (Blueprint $table) {
            $table->boolean('deleted')->default(false)->after('disabled');

            $table->index(['disabled']);
            $table->index(['deleted']);
        });
    }

    public function down()
    {
        // No down
    }
};
