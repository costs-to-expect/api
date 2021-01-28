<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, Withfaker;

    protected string $test_account_name;
    protected string $test_account_email;

    protected function setUp(): void
    {
        parent::setUp();

        $this->test_account_name = $this->faker->name;
        $this->test_account_email = $this->faker->email;

        $result = DB::select(DB::raw("SHOW TABLES LIKE 'users';"));

        if (!count($result)) {
            $this->artisan('migrate:fresh');
        }
    }
}
