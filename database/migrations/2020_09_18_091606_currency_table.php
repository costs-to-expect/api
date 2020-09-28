<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->tinyIncrements('id');
            $table->char('code', 3);
            $table->string('name');
            $table->timestamps();
        });

        DB::statement('INSERT INTO `currency` (`code`, `name`, `created_at`) VALUES ("GBP", "Sterling", NOW())');
        DB::statement('INSERT INTO `currency` (`code`, `name`, `created_at`) VALUES ("USD", "US Dollar", NOW())');
        DB::statement('INSERT INTO `currency` (`code`, `name`, `created_at`) VALUES ("EUR", "Euro", NOW())');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency');
    }
}
