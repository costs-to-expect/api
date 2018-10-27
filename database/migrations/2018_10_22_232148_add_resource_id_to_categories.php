<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResourceIdToCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category', function (Blueprint $table) {
            $table->unsignedBigInteger('resource_type_id')
                ->after('id')
                ->default(1);
            $table->foreign('resource_type_id')
                ->references('id')
                ->on('resource_type');
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
            $table->dropForeign('category_resource_type_id_foreign');
            $table->dropColumn('resource_type_id');
        });
    }
}
