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
        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->string('registered_via')->default('api');
            $table->timestamps();
        });
        
        Schema::create('cache', static function (Blueprint $table) {
            $table->string('key')->unique();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', static function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('item_type', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 25)->index();
            $table->string('friendly_name')->nullable();
            $table->string('description');
            $table->string('example')->nullable();
            $table->timestamps();
        });

        Schema::create('item_subtype', static function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedInteger('item_type_id');
            $table->string('name');
            $table->string('friendly_name')->nullable();
            $table->string('description');
            $table->timestamps();
            
            $table->foreign('item_type_id')
                ->references('id')
                ->on('item_type');
            $table->index('name');
        });

        Schema::create('resource_type', static function (Blueprint $table) {
            $table->id();
            $table->boolean('public')->default(false);
            $table->string('name');
            $table->text('description');
            $table->longText('data')->nullable();
            $table->timestamps();
            
            $table->index('public');
        });

        Schema::create('resource_type_item_type', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_type_id')->nullable();
            $table->unsignedInteger('item_type_id')->nullable();
            $table->timestamps();
            
            $table->foreign('resource_type_id')
                ->references('id')
                ->on('resource_type');
            $table->foreign('item_type_id')
                ->references('id')
                ->on('item_type');
        });

        Schema::create('resource', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_type_id');
            $table->string('name');
            $table->text('description');
            $table->longText('data')->nullable();
            $table->timestamps();
            
            $table->foreign('resource_type_id')
                ->references('id')
                ->on('resource_type');
            $table->unique(['resource_type_id', 'name']);
        });

        Schema::create('resource_item_subtype', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_id');
            $table->unsignedTinyInteger('item_subtype_id');
            $table->timestamps();
            
            $table->foreign('resource_id')
                ->references('id')
                ->on('resource');
            $table->foreign('item_subtype_id')
                ->references('id')
                ->on('item_subtype');
            $table->unique(['resource_id', 'item_subtype_id']);
        });

        Schema::create('category', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_type_id')->default(1);
            $table->string('name');
            $table->text('description');
            $table->timestamps();
            
            $table->unique(['name', 'resource_type_id']);
            $table->foreign('resource_type_id')
                ->references('id')
                ->on('resource_type');
        });

        Schema::create('sub_category', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->text('description');
            $table->timestamps();
            
            $table->foreign('category_id')
                ->references('id')
                ->on('category');
            $table->unique(['name', 'category_id']);
        });

        Schema::create('currency', static function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->char('code', 3);
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('error_log', static function (Blueprint $table) {
            $table->id();
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
            $table->unsignedTinyInteger('attempts');
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
            
            $table->foreign('resource_id')
                ->references('id')
                ->on('resource');
            $table->foreign('created_by')
                ->references('id')
                ->on('users');
            $table->foreign('updated_by')
                ->references('id')
                ->on('users');
        });

        Schema::create('item_type_allocated_expense', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('effective_date');
            $table->date('publish_after')->nullable();
            $table->unsignedTinyInteger('currency_id')->default(1);
            $table->decimal('total', 13, 2);
            $table->tinyInteger('percentage');
            $table->decimal('actualised_total', 13, 2);
            $table->timestamps();
            
            $table->foreign('item_id')
                ->references('id')
                ->on('item');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currency');
            $table->index('effective_date');
            $table->index('publish_after');
        });

        Schema::create('item_type_budget', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->char('account', 36);
            $table->char('target_account', 36)->nullable() ;
            $table->text('description')->nullable();
            $table->decimal('amount', 13, 2);
            $table->unsignedTinyInteger('currency_id');
            $table->enum('category', ['income', 'fixed', 'flexible', 'savings']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('disabled')->default(0);
            $table->json('frequency');
            $table->timestamps();

            $table->foreign('item_id')
                ->references('id')
                ->on('item');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currency');
        });

        Schema::create('item_type_budget_pro', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->char('account', 36);
            $table->char('target_account', 36)->nullable() ;
            $table->text('description')->nullable();
            $table->decimal('amount', 13, 2);
            $table->unsignedTinyInteger('currency_id');
            $table->enum('category', ['income', 'fixed', 'flexible', 'savings', 'transfer']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('disabled')->default(0);
            $table->boolean('deleted')->default(0);
            $table->json('frequency');
            $table->timestamps();

            $table->foreign('item_id')
                ->references('id')
                ->on('item');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currency');
            $table->index('deleted');
            $table->index('disabled');
        });

        Schema::create('item_type_game', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->longText('game');
            $table->longText('statistics');
            $table->char('winner', 10)->nullable();
            $table->unsignedInteger('score')->default(0);
            $table->unsignedTinyInteger('complete')->default(0);
            $table->timestamps();

            $table->foreign('item_id')
                ->references('id')
                ->on('item');
        });

        Schema::create('item_category', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();
            
            $table->unique(['item_id', 'category_id']);
            $table->foreign('item_id')
                ->references('id')
                ->on('item');
            $table->foreign('category_id')
                ->references('id')
                ->on('category');
        });

        Schema::create('item_data', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('key');
            $table->json('value')->nullable();
            $table->timestamps();
            
            $table->foreign('item_id')
                ->references('id')
                ->on('item');
        });

        Schema::create('item_log', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('message');
            $table->json('parameters')->nullable();
            $table->timestamps();

            $table->foreign('item_id')
                ->references('id')
                ->on('item');
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

            $table->foreign('resource_type_id')
                ->references('id')
                ->on('resource_type')
                ->onDelete('cascade');
            $table->foreign('item_id')
                ->references('id')
                ->on('item')
                ->onDelete('cascade');
            $table->foreign('from')
                ->references('id')
                ->on('resource')
                ->onDelete('cascade');
            $table->foreign('to')
                ->references('id')
                ->on('resource')
                ->onDelete('cascade');
            $table->foreign('transferred_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unique(['resource_type_id', 'from', 'item_id']);
        });

        Schema::create('item_sub_category', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_category_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->timestamps();
            
            $table->foreign('sub_category_id')
                ->references('id')
                ->on('sub_category');
            $table->foreign('item_category_id')
                ->references('id')
                ->on('item_category');
            $table->unique(['item_category_id', 'sub_category_id']);
        });

        Schema::create('item_transfer', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_type_id');
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('transferred_by');
            $table->timestamps();

            $table->foreign('resource_type_id')
                ->references('id')
                ->on('resource_type')
                ->onDelete('cascade');
            $table->foreign('item_id')
                ->references('id')
                ->on('item')
                ->onDelete('cascade');
            $table->foreign('from')
                ->references('id')
                ->on('resource')
                ->onDelete('cascade');;
            $table->foreign('to')
                ->references('id')
                ->on('resource')
                ->onDelete('cascade');;
            $table->foreign('transferred_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');;
        });

        Schema::create('password_creates', static function (Blueprint $table) {
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            
            $table->index('email');
        });

        Schema::create('password_resets', static function (Blueprint $table) {
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            $table->index('email');
        });

        Schema::create('permitted_user', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_type_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('added_by');
            $table->timestamps();
            
            $table->foreign('resource_type_id')
                ->references('id')
                ->on('resource_type');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('added_by')
                ->references('id')
                ->on('users');
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
            
            $table->index('source');
        });

        Schema::create('sessions', static function (Blueprint $table) {
            $table->string('id')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');
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
