<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CurrencyFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_type_allocated_expense', function (Blueprint $table) {
            $table->unsignedTinyInteger('currency_id')->default(1)->after('publish_after');
            $table->foreign('currency_id', 'item_type_allocated_expense_currency_id_foreign')->references('id')->on('currency');
        });

        Schema::table('item_type_simple_expense', function (Blueprint $table) {
            $table->unsignedTinyInteger('currency_id')->default(1)->after('description');
            $table->foreign('currency_id', 'item_type_simple_expense_currency_id_foreign')->references('id')->on('currency');
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
            $table->dropForeign('item_type_allocated_expense_currency_id_foreign');
            $table->dropColumn('currency_id');
        });

        Schema::table('item_type_simple_expense', function (Blueprint $table) {
            $table->dropForeign('item_type_simple_expense_currency_id_foreign');
            $table->dropColumn('currency_id');
        });
    }
}
