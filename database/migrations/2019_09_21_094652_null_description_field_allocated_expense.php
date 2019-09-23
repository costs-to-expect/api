<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NullDescriptionFieldAllocatedExpense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_type_allocated_expense', function (Blueprint $table)
        {
            $table->string('description', 255)->nullable()->change();
        });

        DB::statement('UPDATE `item_type_allocated_expense` SET `description` = NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_type_allocated_expense', function (Blueprint $table)
        {
            $table->string('description', 255)->change();
        });

        DB::statement('UPDATE `item_type_allocated_expense` SET `description` = `name`');
    }
}
