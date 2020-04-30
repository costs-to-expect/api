<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DescriptionFieldsText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category', function (Blueprint $table) {
            $table->text('description')->change();
        });

        Schema::table('item_type_allocated_expense', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });

        Schema::table('item_type_simple_expense', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });

        Schema::table('item_type_simple_item', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });

        Schema::table('resource', function (Blueprint $table) {
            $table->text('description')->change();
        });

        Schema::table('resource_type', function (Blueprint $table) {
            $table->text('description')->change();
        });

        Schema::table('sub_category', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category', function (Blueprint $table) {
            $table->string('description', 255)->change();
        });

        Schema::table('item_type_allocated_expense', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
        });

        Schema::table('item_type_simple_expense', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
        });

        Schema::table('item_type_simple_item', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
        });

        Schema::table('resource', function (Blueprint $table) {
            $table->string('description')->change();
        });

        Schema::table('resource_type', function (Blueprint $table) {
            $table->string('description')->change();
        });

        Schema::table('sub_category', function (Blueprint $table) {
            $table->string('description')->change();
        });
    }
}
