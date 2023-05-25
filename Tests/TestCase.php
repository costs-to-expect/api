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

    protected array $item_types = [
        'allocated-expense' => 'OqZwKX16bW',
        'game' => '2AP1axw6L7',
        'budget' => 'VezyrJyMlk',
        'budget-pro' => 'WkxwR04GPo'
    ];

    protected array $item_subtypes = [
        'allocated-expense' => [
            'default' => 'a56kbWV82n',
        ],
        'budget' => [
            'default' => 'Q6OV9dk5dE',
        ],
        'budget-pro' => [
            'default' => 'Y2ekBdlEbz',
        ],
        'game' => [
            'yahtzee' => '3JgkeMkB4q',
            'yatzy' => 'OZYlY5lbPJ',
        ]
    ];

    protected array $currency = [
        'GBP' => 'epMqeYqPkL',
    ];

    protected function assertJsonMatchesAllocatedExpenseItemSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/item-allocated-expense.json');
    }

    protected function assertJsonMatchesBudgetItemSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/item-budget.json');
    }

    protected function assertJsonMatchesBudgetProItemSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/item-budget-pro.json');
    }

    protected function assertJsonMatchesGameItemSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/item-game.json');
    }

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

    protected function createAllocatedExpenseResource(string $resource_type_id): string
    {
        $response = $this->createResource(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_subtype_id' => $this->item_subtypes['allocated-expense']['default'],
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the resource');
    }

    protected function createAllocatedExpenseResourceType(): string
    {
        $response = $this->createResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'data' => '{"field":true}',
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the allocated expense resource type');
    }

    protected function createBudgetProResourceType(): string
    {
        $response = $this->createResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'data' => '{"field":true}',
                'item_type_id' => $this->item_types['budget-pro'],
                'public' => false
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the budget pro resource type');
    }

    protected function createBudgetProResource(string $resource_type_id): string
    {
        $response = $this->createResource(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_subtype_id' => $this->item_subtypes['budget-pro']['default']
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the resource');
    }

    protected function createBudgetResource(string $resource_type_id): string
    {
        $response = $this->createResource(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_subtype_id' => $this->item_subtypes['budget']['default']
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the resource');
    }

    protected function createBudgetResourceType(): string
    {
        $response = $this->createResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'data' => '{"field":true}',
                'item_type_id' => $this->item_types['budget'],
                'public' => false
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the budget resource type');
    }

    protected function createGameResourceType(): string
    {
        $response = $this->createResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'data' => '{"field":true}',
                'item_type_id' => $this->item_types['game'],
                'public' => false
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the game resource type');
    }

    protected function createItem(
        string $resource_type_id,
        string $resource_id,
        array $payload
    ): TestResponse
    {
        return $this->post(
            route(
                'item.create',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id
                ]
            ),
            $payload
        );
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

    protected function createResource(string $resource_type_id, array $payload): TestResponse
    {
        return $this->post(
            route('resource.create', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function createResourceType(array $payload): TestResponse
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

    protected function createResourceTypeByItemType($item_type): string
    {
        if (array_key_exists($item_type, $this->item_types) === false) {
            $this->fail('The requested item type is not an allowable value "' . $this->item_types[$item_type] . '"');
        }

        $response = $this->createResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'data' => '{"field":true}',
                'item_type_id' => $this->item_types[$item_type],
                'public' => false
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the ' . $this->item_types[$item_type] . ' resource type');
    }

    protected function createYahtzeeResource(string $resource_type_id): string
    {
        $response = $this->createResource(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_subtype_id' => $this->item_subtypes['game']['yahtzee']
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the resource');
    }

    protected function createYatzyResource(string $resource_type_id): string
    {
        $response = $this->createResource(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_subtype_id' => $this->item_subtypes['game']['yatzy']
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the resource');
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

    protected function deleteResource(string $resource_type_id, $resource_id): TestResponse
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

    protected function randomMoneyValue(): string
    {
        return number_format($this->faker->randomFloat(2, 0.01, 99999999999.99), 2, '.', '');
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

    protected function updatedResource(string $resource_type_id, string $resource_id, array $payload): TestResponse
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
