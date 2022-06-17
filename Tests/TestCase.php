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

    protected function assertJsonIsCategory($content): void
    {
        $this->assertJsonMatchesSchema($content, 'api/schema/category.json');
    }

    protected function assertJsonIsItemType($content): void
    {
        $this->assertJsonMatchesSchema($content, 'api/schema/item-type.json');
    }

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

    protected function createAndReturnCategoryId(string $resource_type_id): string
    {
        $response = $this->postCategory(
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

    protected function createAndReturnResourceId(string $resource_type_id): string
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

    protected function createAndReturnResourceTypeId(): string
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

    protected function createAndReturnSubcategoryId(string $resource_type_id, string $category_id): string
    {
        $response = $this->postSubcategory(
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

    protected function deleteCategory(string $resource_type_id, $category_id): TestResponse
    {
        return $this->delete(
            route('category.delete', ['resource_type_id' => $resource_type_id, 'category_id' => $category_id]), []
        );
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

    protected function deleteSubcategory(string $resource_type_id, $category_id, $subcategory_id): TestResponse
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

    protected function getARandomUser()
    {
        return User::query()->where('id', '!=', 1)->inRandomOrder()->first();
    }

    protected function getItemType(array $parameters = []): TestResponse
    {
        return $this->getRoute('item-type.show', $parameters);
    }

    protected function getItemTypes(array $parameters = []): TestResponse
    {
        return $this->getRoute('item-type.list', $parameters);
    }

    protected function getPermittedUser(array $parameters = []): TestResponse
    {
        return $this->getRoute('permitted-user.show', $parameters);
    }

    protected function getPermittedUsers(array $parameters = []): TestResponse
    {
        return $this->getRoute('permitted-user.list', $parameters);
    }

    protected function getResourceType(array $parameters = []): TestResponse
    {
        return $this->getRoute('resource-type.show', $parameters);
    }

    protected function getResourceTypes(array $parameters = []): TestResponse
    {
        return $this->getRoute('resource-type.list', $parameters);
    }

    protected function getRoute(string $route, array $parameters = []): TestResponse
    {
        return $this->get(route($route, $parameters));
    }

    protected function patchCategory(string $resource_type_id, string $category_id, array $payload): TestResponse
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

    protected function patchSubcategory(
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

    protected function optionsCreatePassword(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.create-password.options', $parameters);
    }

    protected function optionsRegister(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.register.options', $parameters);
    }

    protected function optionsResourceType(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('resource-type.show.options', $parameters);
    }

    protected function optionsResourceTypeCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('resource-type.list.options', $parameters);
    }

    protected function optionsRoute(string $route, array $parameters = []): TestResponse
    {
        return $this->options(route($route, $parameters));
    }

    protected function postCategory(string $resource_type_id, array $payload): TestResponse
    {
        return $this->post(
            route('category.create', ['resource_type_id' => $resource_type_id]),
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

    protected function postSubcategory(string $resource_type_id, string $category_id, array $payload): TestResponse
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
