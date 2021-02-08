<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Opis\JsonSchema\Schema;
use Opis\JsonSchema\Validator;

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

    protected function assertJsonIsResourceType($content): void
    {
        $this->assertJsonMatchesSchema($content, 'api/schema/resource-type.json');
    }

    protected function assertJsonMatchesSchema($content, $schema_file): void
    {
        $schema = Schema::fromJsonString(file_get_contents(public_path($schema_file)));
        $validator = new Validator();

        $result = $validator->schemaValidation(json_decode($content), $schema);
        self::assertTrue($result->isValid());
    }
}
