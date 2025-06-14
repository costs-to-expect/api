<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cache', static function (Blueprint $table) {
            $table->string('key')->unique();
            $table->text('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->unique();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('resource_type', static function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('public')->default(false);
            $table->string('name');
            $table->text('description');
            $table->longText('data')->nullable();
            $table->timestamps();
        });

        Schema::create('category', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('resource_type_id');
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('currency', static function (Blueprint $table) {
            $table->increments('id');
            $table->char('3');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('error_log', static function (Blueprint $table) {
            $table->increments('id');
            $table->text('message');
            $table->string('file');
            $table->string('line');
            $table->text('trace');
            $table->timestamps();
        });

        Schema::create('jobs', static function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('failed_jobs', static function (Blueprint $table) {
            $table->id();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
