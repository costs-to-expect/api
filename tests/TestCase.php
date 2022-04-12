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

    protected function assertJsonIsPermittedUser($content): void
    {
        $this->assertJsonMatchesSchema($content, 'api/schema/permitted-user.json');
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

    protected function deletePermittedUser(string $resource_type_id, string $permitted_user_id): TestResponse
    {
        return $this->delete(
            route('permitted-user.delete', ['resource_type_id' => $resource_type_id, 'permitted_user_id' => $permitted_user_id]), []
        );
    }

    protected function deleteResource(string $resource_type_id, $resource_id): TestResponse
    {
        return $this->delete(
            route('resource.delete', ['resource_type_id' => $resource_type_id, 'resource_id' => $resource_id]), []
        );
    }

    protected function deleteResourceType(string $resource_type_id): TestResponse
    {
        return $this->delete(
            route('resource-type.delete', ['resource_type_id' => $resource_type_id]), []
        );
    }

    protected function fetchPermittedUser(array $parameters = []): TestResponse
    {
        return $this->get(route('permitted-user.show', $parameters));
    }

    protected function fetchPermittedUsers(array $parameters = []): TestResponse
    {
        return $this->get(route('permitted-user.list', $parameters));
    }

    protected function fetchRandomUser()
    {
        return User::query()->where('id', '!=', 1)->inRandomOrder()->first();
    }

    protected function fetchResourceType(array $parameters = []): TestResponse
    {
        return $this->get(route('resource-type.show', $parameters));
    }

    protected function fetchResourceTypes(array $parameters = []): TestResponse
    {
        return $this->get(route('resource-type.list', $parameters));
    }

    protected function helperCreateResource(string $resource_type_id): string
    {
        $response = $this->postResource(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_subtype_id' => 'a56kbWV82n'
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the resource');
    }

    protected function helperCreateResourceType(): string
    {
        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'data' => '{"field":true}',
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the resource type');
    }

    protected function patchResource(string $resource_type_id, string $resource_id, array $payload): TestResponse
    {
        return $this->patch(
            route(
                'resource.update',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id
                ]
            ),
            $payload
        );
    }

    protected function patchResourceType(string $resource_type_id, array $payload): TestResponse
    {
        return $this->patch(
            route('resource-type.update', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function postPermittedUser(string $resource_type_id, array $payload): TestResponse
    {
        return $this->post(
            route('permitted-user.create', ['resource_type_id' => $resource_type_id]),
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
