<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, Withfaker;

    protected string $test_user_email = 'test-account-email@email.com';
    protected string $test_user_password = 'test-account-secret-password';

    protected function setUp(): void
    {
        parent::setUp();

        $result = DB::select(DB::raw("SHOW TABLES LIKE 'users';"));

        if (!count($result)) {
            $this->artisan('migrate:fresh');

            $user = new User();
            $user->name = $this->faker->name;
            $user->email = $this->test_user_email;
            $user->password = Hash::make($this->test_user_password);
            $user->save();
        }
    }
}
