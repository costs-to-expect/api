<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveEffectiveDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_type_simple_expense', function (Blueprint $table) {
            $table->dropColumn('effective_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_type_simple_expense', function (Blueprint $table) {
            $table->date('effective_date');

            $table->index('effective_date');
        });
    }
}
