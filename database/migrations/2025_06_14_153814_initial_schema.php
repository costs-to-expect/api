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

        Schema::create('item_type', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('friendly_name')->nullable();
            $table->string('description');
            $table->string('example')->nullable();
            $table->timestamps();
        });

        Schema::create('item_subtype', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_type_id');
            $table->string('name');
            $table->string('friendly_name')->nullable();
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('resource_type', static function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('public')->default(false);
            $table->string('name');
            $table->text('description');
            $table->longText('data')->nullable();
            $table->timestamps();
        });

        Schema::create('resource_type_item_type', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('resource_type_id');
            $table->unsignedInteger('item_type_id');
            $table->timestamps();
        });

        Schema::create('resource', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('resource_type_id');
            $table->string('name');
            $table->text('description');
            $table->longText('data')->nullable();
            $table->timestamps();
        });

        Schema::create('resource_item_subtype', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('resource_id');
            $table->unsignedInteger('item_subtype_id');
            $table->timestamps();
        });

        Schema::create('category', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('resource_type_id');
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('sub_category', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('category_id');
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

        Schema::create('item', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('created_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('item_type_allocated_expense', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('effective_date');
            $table->date('publish_after')->nullable();
            $table->unsignedBigInteger('currency_id');
            $table->decimal('total', 15, 2);
            $table->tinyInteger('percentage');
            $table->decimal('actualised_total', 15, 2);
            $table->timestamps();
        });

        Schema::create('item_type_budget', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->char('account', 36);
            $table->char('target_account', 36)->nullable() ;
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('currency_id');
            $table->enum('category', ['income', 'fixed', 'flexible', 'savings']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->tinyInteger('disabled')->default(0);
            $table->json('frequency');
            $table->timestamps();
        });

        Schema::create('item_type_budget_pro', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->char('account', 36);
            $table->char('target_account', 36)->nullable() ;
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('currency_id');
            $table->enum('category', ['income', 'fixed', 'flexible', 'savings', 'transfer']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->tinyInteger('disabled')->default(0);
            $table->tinyInteger('deleted')->default(0);
            $table->json('frequency');
            $table->timestamps();
        });

        Schema::create('item_type_game', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('game');
            $table->longText('statistics');
            $table->char('winner', 10)->nullable();
            $table->unsignedInteger('score')->default(0);
            $table->tinyInteger('complete')->default(0);
            $table->timestamps();
        });

        Schema::create('item_category', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();
        });

        Schema::create('item_data', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('key');
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('item_log', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('message');
            $table->json('parameters')->nullable();
            $table->timestamps();
        });

        Schema::create('item_partial_transfer', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_type_id');
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->unsignedBigInteger('item_id');
            $table->unsignedTinyInteger('percentage');
            $table->unsignedBigInteger('transferred_by');
            $table->timestamps();
        });

        Schema::create('item_sub_category', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_category_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->timestamps();
        });

        Schema::create('item_transfer', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_type_id');
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('transferred_by');
            $table->timestamps();
        });

        Schema::create('password_creates', static function (Blueprint $table) {
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('password_resets', static function (Blueprint $table) {
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('permitted_user', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_type_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('request_error_log', static function (Blueprint $table) {
            $table->id();
            $table->char('method', 8);
            $table->string('source', 25)->default('api');
            $table->string('debug')->nullable();
            $table->unsignedSmallInteger('expected_status_code');
            $table->unsignedSmallInteger('returned_status_code');
            $table->string('request_uri');
            $table->timestamps();
        });

        Schema::create('sessions', static function (Blueprint $table) {
            $table->string('id')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');
        });

        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->string('registered_via')->default('api');
            $table->timestamps();
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
