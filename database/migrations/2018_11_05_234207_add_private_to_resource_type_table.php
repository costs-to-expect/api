<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrivateToResourceTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resource_type', function (Blueprint $table) {
            $table->tinyInteger('private')
                ->after('id')
                ->default(0);
            $table->index('private');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resource_type', function (Blueprint $table) {
            $table->dropIndex('resource_type_private_index');
            $table->dropColumn('private');
        });
    }
}