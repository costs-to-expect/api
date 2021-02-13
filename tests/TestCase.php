<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
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

    protected function assertJsonIsResource($content): void
    {
        $this->assertJsonMatchesSchema($content, 'api/schema/resource.json');
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

    protected function deleteResourceType(string $resource_type_id): TestResponse
    {
        return $this->delete(
            route('resource-type.update', ['resource_type_id' => $resource_type_id]), []
        );
    }

    protected function patchResourceType(string $resource_type_id, array $payload): TestResponse
    {
        return $this->patch(
            route('resource-type.update', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function postResource(string $resource_type_id, array $payload): TestResponse
    {
        return $this->post(
            route('resource.create', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function postResourceType(array $payload): TestResponse
    {
        return $this->post(route('resource-type.create'), $payload);
    }
}
