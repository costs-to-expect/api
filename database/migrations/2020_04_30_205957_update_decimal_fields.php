<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDecimalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_type_allocated_expense', function (Blueprint $table) {
            $table->decimal('total', 13, 2)->change();
            $table->decimal('actualised_total', 13, 2)->change();
        });

        Schema::table('item_type_simple_expense', function (Blueprint $table) {
            $table->decimal('total', 13, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_type_allocated_expense', function (Blueprint $table) {
            $table->decimal('total', 10, 2)->change();
            $table->decimal('actualised_total', 10, 2)->change();
        });

        Schema::table('item_type_simple_expense', function (Blueprint $table) {
            $table->decimal('total', 10, 2)->change();
        });
    }
}
