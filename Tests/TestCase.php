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

    protected string $email_for_expected_test_user = 'test-account-email@email.com';
    protected string $password_for_expected_test_user = 'test-account-secret-password';

    protected function assertJsonMatchesCategorySchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/category.json');
    }

    protected function assertJsonMatchesItemTypeSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/item-type.json');
    }

    protected function assertJsonMatchesPermittedUserSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/permitted-user.json');
    }

    protected function assertJsonMatchesResourceSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/resource.json');
    }

    protected function assertJsonMatchesResourceTypeSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/resource-type.json');
    }

    protected function assertJsonMatchesResourceTypeWhichIncludesPermittedUsersSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/resource-type-include-permitted-users.json');
    }

    protected function assertJsonMatchesResourceTypeWhichIncludesResourcesSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/resource-type-include-resources.json');
    }

    protected function assertProvidedJsonMatchesDefinedSchema($content, $schema_file): void
    {
        $schema = Schema::fromJsonString(file_get_contents(public_path($schema_file)));
        $validator = new Validator();

        $result = $validator->schemaValidation(json_decode($content), $schema);
        self::assertTrue($result->isValid());
    }

    protected function createRandomCategory(string $resource_type_id): string
    {
        $response = $this->createRequestedCategory(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the category');
    }

    protected function createRandomResource(string $resource_type_id): string
    {
        $response = $this->createRequestedResource(
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

    protected function createRandomResourceType(): string
    {
        $response = $this->createRequestedResourceType(
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

    protected function createRandomSubcategory(string $resource_type_id, string $category_id): string
    {
        $response = $this->createRequestedSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the subcategory');
    }

    protected function createRequestedCategory(string $resource_type_id, array $payload): TestResponse
    {
        return $this->post(
            route('category.create', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function createRequestedPermittedUser(string $resource_type_id, array $payload): TestResponse
    {
        return $this->post(
            route('permitted-user.create', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function createRequestedResource(string $resource_type_id, array $payload): TestResponse
    {
        return $this->post(
            route('resource.create', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function createRequestedResourceType(array $payload): TestResponse
    {
        return $this->post(route('resource-type.create'), $payload);
    }

    protected function createRequestedSubcategory(string $resource_type_id, string $category_id, array $payload): TestResponse
    {
        return $this->post(
            route(
                'subcategory.create',
                [
                    'resource_type_id' => $resource_type_id,
                    'category_id' => $category_id
                ]
            ),
            $payload
        );
    }

    protected function deleteRequestedCategory(string $resource_type_id, $category_id): TestResponse
    {
        return $this->delete(
            route('category.delete', ['resource_type_id' => $resource_type_id, 'category_id' => $category_id]), []
        );
    }

    protected function deleteRequestedPermittedUser(string $resource_type_id, string $permitted_user_id): TestResponse
    {
        return $this->delete(
            route('permitted-user.delete', ['resource_type_id' => $resource_type_id, 'permitted_user_id' => $permitted_user_id]), []
        );
    }

    protected function deleteRequestedResource(string $resource_type_id, $resource_id): TestResponse
    {
        return $this->delete(
            route('resource.delete', ['resource_type_id' => $resource_type_id, 'resource_id' => $resource_id]), []
        );
    }

    protected function deleteRequestedResourceType(string $resource_type_id): TestResponse
    {
        return $this->delete(
            route('resource-type.delete', ['resource_type_id' => $resource_type_id]), []
        );
    }

    protected function deleteRequestedSubcategory(string $resource_type_id, $category_id, $subcategory_id): TestResponse
    {
        return $this->delete(
            route(
                'subcategory.delete',
                [
                    'resource_type_id' => $resource_type_id,
                    'category_id' => $category_id,
                    'subcategory_id' => $subcategory_id
                ]
            ),
            []
        );
    }

    protected function fetchAllItemTypes(array $parameters = []): TestResponse
    {
        return $this->generatedRoute('item-type.list', $parameters);
    }

    protected function fetchAllPermittedUsers(array $parameters = []): TestResponse
    {
        return $this->generatedRoute('permitted-user.list', $parameters);
    }

    protected function fetchAllResourceTypes(array $parameters = []): TestResponse
    {
        return $this->generatedRoute('resource-type.list', $parameters);
    }

    protected function fetchItemType(array $parameters = []): TestResponse
    {
        return $this->generatedRoute('item-type.show', $parameters);
    }

    protected function fetchPermittedUser(array $parameters = []): TestResponse
    {
        return $this->generatedRoute('permitted-user.show', $parameters);
    }

    protected function fetchRandomUser()
    {
        return User::query()->where('id', '!=', 1)->inRandomOrder()->first();
    }

    protected function fetchResourceType(array $parameters = []): TestResponse
    {
        return $this->generatedRoute('resource-type.show', $parameters);
    }

    protected function fetchOptionsForCreatePassword(array $parameters = []): TestResponse
    {
        return $this->generateOptionsRoute('auth.create-password.options', $parameters);
    }

    protected function fetchOptionsForRegister(array $parameters = []): TestResponse
    {
        return $this->generateOptionsRoute('auth.register.options', $parameters);
    }

    protected function fetchOptionsForResourceType(array $parameters = []): TestResponse
    {
        return $this->generateOptionsRoute('resource-type.show.options', $parameters);
    }

    protected function fetchOptionsForResourceTypeCollection(array $parameters = []): TestResponse
    {
        return $this->generateOptionsRoute('resource-type.list.options', $parameters);
    }

    protected function generatedRoute(string $route, array $parameters = []): TestResponse
    {
        return $this->get(route($route, $parameters));
    }

    protected function generateOptionsRoute(string $route, array $parameters = []): TestResponse
    {
        return $this->options(route($route, $parameters));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $result = DB::select(DB::raw("SHOW TABLES LIKE 'users';"));

        if (count($result) === 0) {
            $this->artisan('migrate:fresh');

            $user = new User();
            $user->name = $this->faker->name;
            $user->email = $this->email_for_expected_test_user;
            $user->password = Hash::make($this->password_for_expected_test_user);
            $user->save();
        }
    }

    protected function updateRequestedCategory(string $resource_type_id, string $category_id, array $payload): TestResponse
    {
        return $this->patch(
            route(
                'category.update',
                [
                    'resource_type_id' => $resource_type_id,
                    'category_id' => $category_id
                ]
            ),
            $payload
        );
    }

    protected function updatedRequestedResource(string $resource_type_id, string $resource_id, array $payload): TestResponse
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

    protected function updatedRequestedResourceType(string $resource_type_id, array $payload): TestResponse
    {
        return $this->patch(
            route('resource-type.update', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function updatedRequestedSubcategory(
        string $resource_type_id,
        string $category_id,
        string $subcategory_id,
        array $payload
    ): TestResponse
    {
        return $this->patch(
            route(
                'subcategory.update',
                [
                    'resource_type_id' => $resource_type_id,
                    'category_id' => $category_id,
                    'subcategory_id' => $subcategory_id
                ]
            ),
            $payload
        );
    }
}
