<?php

namespace Tests;

use App\Models\PermittedUser;
use App\Models\ResourceType;
use App\Models\ResourceTypeItemType;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

    protected function assertJsonMatchesCategorySchemaWhichIncludesSubcategories($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/category-include-subcategories.json');
    }

    protected function assertJsonMatchesItemTypeSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/item-type.json');
    }

    protected function assertJsonMatchesCurrencySchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/currency.json');
    }

    protected function assertJsonMatchesItemSubtypeSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/item-subtype.json');
    }

    protected function assertJsonMatchesItemCategorySchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/item-category.json');
    }

    protected function assertJsonMatchesItemSubcategorySchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/item-subcategory.json');
    }

    protected function assertJsonMatchesTransferSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/transfer.json');
    }

    protected function assertJsonMatchesPartialTransferSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/partial-transfer.json');
    }

    protected function assertJsonMatchesResourceTypeItemAllocatedExpenseSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/resource-type-item-allocated-expense.json');
    }

    protected function assertJsonMatchesResourceTypeItemGameSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/resource-type-item-game.json');
    }

    protected function assertJsonMatchesErrorLogSchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/error-log.json');
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

    protected function assertJsonMatchesSubcategorySchema($content): void
    {
        $this->assertProvidedJsonMatchesDefinedSchema($content, 'api/schema/subcategory.json');
    }

    protected function assertProvidedJsonMatchesDefinedSchema($content, $schema_file): void
    {
        $schema = Schema::fromJsonString(file_get_contents(public_path($schema_file)));
        $validator = new Validator();

        $result = $validator->schemaValidation(json_decode($content), $schema);

        if ($result->isValid()) {
            self::assertTrue(true);
        } else {
            dd($result->getErrors());
        }
    }

    protected function quickCreateRandomCategory(
        string $resource_type_id,
        array $override = []
    ): string
    {
        $payload = [
            'name' => $this->faker->text(200),
            'description' => $this->faker->text(200),
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToCategoryCreate($resource_type_id, $payload);

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the category');
    }

    protected function quickCreateAllocatedExpenseItem(
        string $resource_type_id,
        string $resource_id,
        array $override = []
    ): string
    {
        $payload = [
            'name' => $this->faker->text(200),
            'description' => $this->faker->text(200),
            'effective_date' => $this->faker->date(),
            'currency_id' => $this->currency['GBP'],
            'total' => $this->randomMoneyValue(),
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            $payload
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the allocated expense item');
    }

    protected function quickCreateAllocatedExpenseResource(string $resource_type_id): string
    {
        $response = $this->postToResourceCreate(
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

    protected function quickCreateAllocatedExpenseResourceType(array $override = []): string
    {
        $payload = [
            'name' => $this->faker->text(255),
            'description' => $this->faker->text,
            'data' => '{"field":true}',
            'item_type_id' => $this->item_types['allocated-expense'],
            'public' => false
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToResourceTypeCreate($payload);

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the allocated expense resource type');
    }

    protected function quickCreateBudgetItem(
        string $resource_type_id,
        string $resource_id,
        array $override = []
    ): string
    {
        $payload = [
            'name' => $this->faker->text(200),
            'account' => Str::uuid()->toString(),
            'description' => $this->faker->text(200),
            'amount' => $this->randomMoneyValue(),
            'currency_id' => $this->currency['GBP'],
            'category' => 'income',
            'start_date' => $this->faker->date(),
            'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            $payload
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the budget item');
    }

    protected function quickCreateBudgetProItem(
        string $resource_type_id,
        string $resource_id,
        array $override = []
    ): string
    {
        $payload = [
            'name' => $this->faker->text(200),
            'account' => Str::uuid()->toString(),
            'description' => $this->faker->text(200),
            'amount' => $this->randomMoneyValue(),
            'currency_id' => $this->currency['GBP'],
            'category' => 'income',
            'start_date' => $this->faker->date(),
            'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            $payload
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the budget pro item');
    }

    protected function quickCreateBudgetProResourceType(): string
    {
        $response = $this->postToResourceTypeCreate(
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

    protected function quickCreateBudgetProResource(string $resource_type_id): string
    {
        $response = $this->postToResourceCreate(
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

    protected function quickCreateBudgetResource(
        string $resource_type_id,
        array $override = []
    ): string
    {
        $payload = [
            'name' => $this->faker->text(200),
            'description' => $this->faker->text(200),
            'item_subtype_id' => $this->item_subtypes['budget']['default']
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToResourceCreate($resource_type_id, $payload);

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the resource');
    }

    protected function quickCreateBudgetResourceType(): string
    {
        $response = $this->postToResourceTypeCreate(
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

    protected function quickCreateGameResourceType(): string
    {
        $response = $this->postToResourceTypeCreate(
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

    protected function quickCreateItemCategory(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $category_id
    ): string
    {
        $response = $this->postToItemCategoryCreate(
            $resource_type_id,
            $resource_id,
            $item_id,
            ['category_id' => $category_id]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the item category');
    }

    protected function postToItemCategoryCreate(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        array $payload
    ): TestResponse
    {
        return $this->post(
            route(
                'item.categories.create',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id
                ]
            ),
            $payload
        );
    }

    protected function quickCreateItemSubcategory(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $subcategory_id
    ): string
    {
        $response = $this->postToItemSubcategoryCreate(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            ['subcategory_id' => $subcategory_id]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the item subcategory');
    }

    protected function postToItemSubcategoryCreate(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        array $payload
    ): TestResponse
    {
        return $this->post(
            route(
                'item-subcategory.create',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id,
                    'item_category_id' => $item_category_id
                ]
            ),
            $payload
        );
    }

    protected function quickCreateItemData(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        array $override = []
    ): string
    {
        $payload = [
            'key' => $this->faker->unique()->word(),
            'value' => json_encode(['field' => $this->faker->word()], JSON_THROW_ON_ERROR),
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToItemDataCreate($resource_type_id, $resource_id, $item_id, $payload);

        if ($response->assertStatus(201)) {
            return $payload['key'];
        }

        $this->fail('Unable to create the item data');
    }

    protected function postToItemDataCreate(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        array $payload
    ): TestResponse
    {
        return $this->post(
            route(
                'item-data.create',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id
                ]
            ),
            $payload
        );
    }

    protected function patchToItemDataUpdate(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $key,
        array $payload
    ): TestResponse
    {
        return $this->patch(
            route(
                'item-data.update',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id,
                    'key' => $key
                ]
            ),
            $payload
        );
    }

    protected function deleteToItemDataDelete(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $key
    ): TestResponse
    {
        return $this->delete(
            route(
                'item-data.delete',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id,
                    'key' => $key
                ]
            )
        );
    }

    protected function quickCreateItemLog(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        array $override = []
    ): string
    {
        $payload = [
            'message' => $this->faker->text(200),
            'parameters' => json_encode(['field' => $this->faker->word()], JSON_THROW_ON_ERROR),
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToItemLogCreate($resource_type_id, $resource_id, $item_id, $payload);

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the item log');
    }

    protected function postToItemLogCreate(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        array $payload
    ): TestResponse
    {
        return $this->post(
            route(
                'item-log.create',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id
                ]
            ),
            $payload
        );
    }

    protected function postToItemTransferCreate(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        array $payload
    ): TestResponse
    {
        return $this->post(
            route(
                'item.transfer.create',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id
                ]
            ),
            $payload
        );
    }

    protected function postToItemPartialTransferCreate(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        array $payload
    ): TestResponse
    {
        return $this->post(
            route(
                'item.partial-transfer.create',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id
                ]
            ),
            $payload
        );
    }

    protected function quickCreateItemPartialTransfer(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $to_resource_id,
        int $percentage = 50
    ): string
    {
        $response = $this->postToItemPartialTransferCreate(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'resource_id' => $to_resource_id,
                'percentage' => $percentage
            ]
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the item partial transfer');
    }

    protected function deleteToItemPartialTransferDelete(
        string $resource_type_id,
        string $item_partial_transfer_id
    ): TestResponse
    {
        return $this->delete(
            route(
                'partial-transfers.delete',
                [
                    'resource_type_id' => $resource_type_id,
                    'item_partial_transfer_id' => $item_partial_transfer_id
                ]
            )
        );
    }

    protected function postToItemCreate(
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

    protected function quickCreateRandomSubcategory(
        string $resource_type_id,
        string $category_id,
        array $override = []
    ): string
    {
        $payload = [
            'name' => $this->faker->text(200),
            'description' => $this->faker->text(200),
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToSubcategoryCreate(
            $resource_type_id,
            $category_id,
            $payload
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the subcategory');
    }

    protected function postToCategoryCreate(string $resource_type_id, array $payload): TestResponse
    {
        return $this->post(
            route('category.create', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function postToPermittedUserCreate(string $resource_type_id, array $payload): TestResponse
    {
        return $this->post(
            route('permitted-user.create', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function postToResourceCreate(string $resource_type_id, array $payload): TestResponse
    {
        return $this->post(
            route('resource.create', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function postToResourceTypeCreate(array $payload): TestResponse
    {
        return $this->post(route('resource-type.create'), $payload);
    }

    protected function postToSubcategoryCreate(
        string $resource_type_id,
        string $category_id,
        array $payload
    ): TestResponse
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

        $response = $this->postToResourceTypeCreate(
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

    protected function createUserAndReturnId(): int
    {
        $user = new User();
        $user->name = $this->faker->name;
        $user->email = $this->faker->email;
        $user->password = Hash::make($this->faker->password);
        $user->save();

        return $user->id;
    }
    
    protected function createUser(): User
    {
        $user = new User();
        $user->name = $this->faker->name;
        $user->email = $this->faker->email;
        $user->password = Hash::make($this->faker->password);
        $user->save();

        return $user;
    }

    protected function quickCreateYahtzeeGameItem(
        string $resource_type_id,
        string $resource_id,
        array $override = []
    ): string
    {
        $payload = [
            'name' => $this->faker->text(200),
            'description' => $this->faker->text(200),
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            $payload
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the yahtzee game item');
    }

    protected function quickCreateYatzyGameItem(
        string $resource_type_id,
        string $resource_id,
        array $override = []
    ): string
    {
        $payload = [
            'name' => $this->faker->text(200),
            'description' => $this->faker->text(200),
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            $payload
        );

        if ($response->assertStatus(201)) {
            return $response->json('id');
        }

        $this->fail('Unable to create the yatzy game item');
    }

    protected function quickCreateYahtzeeResource(string $resource_type_id): string
    {
        $response = $this->postToResourceCreate(
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

    protected function quickCreateYatzyResource(string $resource_type_id): string
    {
        $response = $this->postToResourceCreate(
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

    protected function deleteToItemDelete(string $resource_type_id, $resource_id, string $item_id): TestResponse
    {
        return $this->delete(
            route(
                'item.delete',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id
                ]
            )
        );
    }

    protected function deleteToCategoryDelete(string $resource_type_id, $category_id): TestResponse
    {
        return $this->delete(
            route('category.delete', ['resource_type_id' => $resource_type_id, 'category_id' => $category_id]), []
        );
    }

    protected function deleteToPermittedUserDelete(string $resource_type_id, string $permitted_user_id): TestResponse
    {
        return $this->delete(
            route('permitted-user.delete', ['resource_type_id' => $resource_type_id, 'permitted_user_id' => $permitted_user_id]), []
        );
    }

    protected function deleteToResourceDelete(string $resource_type_id, $resource_id): TestResponse
    {
        return $this->delete(
            route('resource.delete', ['resource_type_id' => $resource_type_id, 'resource_id' => $resource_id]), []
        );
    }

    protected function deleteToResourceTypeDelete(string $resource_type_id): TestResponse
    {
        return $this->delete(
            route('resource-type.delete', ['resource_type_id' => $resource_type_id]), []
        );
    }

    protected function deleteToSubcategoryDelete(string $resource_type_id, $category_id, $subcategory_id): TestResponse
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

    protected function getToIndex(array $parameters = []): TestResponse
    {
        return $this->route('index.show', $parameters);
    }

    protected function fetchOptionsForIndex(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('index.show.options', $parameters);
    }

    protected function getToChangelog(array $parameters = []): TestResponse
    {
        return $this->route('index.changelog', $parameters);
    }

    protected function fetchOptionsForChangelog(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('index.changelog.options', $parameters);
    }

    protected function getToStatus(array $parameters = []): TestResponse
    {
        return $this->route('index.status', $parameters);
    }

    protected function fetchOptionsForStatus(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('index.status.options', $parameters);
    }

    protected function getToItemTypeList(array $parameters = []): TestResponse
    {
        return $this->route('item-type.list', $parameters);
    }

    protected function getToCurrencyList(array $parameters = []): TestResponse
    {
        return $this->route('currency.list', $parameters);
    }

    protected function getToCurrencyShow(array $parameters = []): TestResponse
    {
        return $this->route('currency.show', $parameters);
    }

    protected function fetchOptionsForCurrencyCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('currency.list.options', $parameters);
    }

    protected function fetchOptionsForCurrency(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('currency.show.options', $parameters);
    }

    protected function getToItemSubtypeList(array $parameters = []): TestResponse
    {
        return $this->route('item-subtype.list', $parameters);
    }

    protected function getToItemSubtypeShow(array $parameters = []): TestResponse
    {
        return $this->route('item-subtype.show', $parameters);
    }

    protected function fetchOptionsForItemSubtypeCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item-subtype.list.options', $parameters);
    }

    protected function fetchOptionsForItemSubtype(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item-subtype.show.options', $parameters);
    }

    protected function getToQueueList(array $parameters = []): TestResponse
    {
        return $this->route('queue.list', $parameters);
    }

    protected function getToQueueShow(array $parameters = []): TestResponse
    {
        return $this->route('queue.show', $parameters);
    }

    protected function fetchOptionsForQueueCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('queue.list.options', $parameters);
    }

    protected function fetchOptionsForQueue(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('queue.show.options', $parameters);
    }

    protected function getToItemCategoryList(array $parameters = []): TestResponse
    {
        return $this->route('item.categories.list', $parameters);
    }

    protected function getToItemCategoryShow(array $parameters = []): TestResponse
    {
        return $this->route('item.categories.show', $parameters);
    }

    protected function fetchOptionsForItemCategoryCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item.categories.list.options', $parameters);
    }

    protected function fetchOptionsForItemCategory(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item.categories.show.options', $parameters);
    }

    protected function deleteToItemCategoryDelete(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): TestResponse
    {
        return $this->delete(
            route(
                'item.categories.delete',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id,
                    'item_category_id' => $item_category_id
                ]
            )
        );
    }

    protected function getToItemSubcategoryList(array $parameters = []): TestResponse
    {
        return $this->route('item-subcategory.list', $parameters);
    }

    protected function getToItemSubcategoryShow(array $parameters = []): TestResponse
    {
        return $this->route('item-subcategory.show', $parameters);
    }

    protected function fetchOptionsForItemSubcategoryCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item-subcategory.list.options', $parameters);
    }

    protected function fetchOptionsForItemSubcategory(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item-subcategory.show.options', $parameters);
    }

    protected function getToItemDataList(array $parameters = []): TestResponse
    {
        return $this->route('item-data.list', $parameters);
    }

    protected function getToItemDataShow(array $parameters = []): TestResponse
    {
        return $this->route('item-data.show', $parameters);
    }

    protected function fetchOptionsForItemDataCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item-data.list.options', $parameters);
    }

    protected function fetchOptionsForItemData(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item-data.show.options', $parameters);
    }

    protected function getToItemLogList(array $parameters = []): TestResponse
    {
        return $this->route('item-log.list', $parameters);
    }

    protected function getToItemLogShow(array $parameters = []): TestResponse
    {
        return $this->route('item-log.show', $parameters);
    }

    protected function fetchOptionsForItemLogCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item-log.list.options', $parameters);
    }

    protected function fetchOptionsForItemLog(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item-log.show.options', $parameters);
    }

    protected function getToItemTransferList(array $parameters = []): TestResponse
    {
        return $this->route('item-transfer.list', $parameters);
    }

    protected function getToItemTransferShow(array $parameters = []): TestResponse
    {
        return $this->route('item-transfer.show', $parameters);
    }

    protected function fetchOptionsForItemTransferCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item-transfer.list.options', $parameters);
    }

    protected function fetchOptionsForItemTransfer(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item-transfer.show.options', $parameters);
    }

    protected function fetchOptionsForItemTransferAction(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item.transfer.options', $parameters);
    }

    protected function getToItemPartialTransferList(array $parameters = []): TestResponse
    {
        return $this->route('partial-transfers.list', $parameters);
    }

    protected function getToItemPartialTransferShow(array $parameters = []): TestResponse
    {
        return $this->route('partial-transfers.show', $parameters);
    }

    protected function fetchOptionsForItemPartialTransferCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('partial-transfers.list.options', $parameters);
    }

    protected function fetchOptionsForItemPartialTransfer(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('partial-transfers.show.options', $parameters);
    }

    protected function fetchOptionsForItemPartialTransferAction(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item.partial-transfer.options', $parameters);
    }

    protected function getToResourceTypeItemList(array $parameters = []): TestResponse
    {
        return $this->route('resource-type-item.list', $parameters);
    }

    protected function fetchOptionsForResourceTypeItemCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('resource-type-item.list.options', $parameters);
    }

    protected function getToSummaryResourceTypeItemList(array $parameters = []): TestResponse
    {
        return $this->route('summary.resource-type-item.list', $parameters);
    }

    protected function fetchOptionsForSummaryResourceTypeItemCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('summary.resource-type-item.list.options', $parameters);
    }

    protected function getToSummaryCategoryList(array $parameters = []): TestResponse
    {
        return $this->route('summary.category.list', $parameters);
    }

    protected function fetchOptionsForSummaryCategoryCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('summary.category.list.options', $parameters);
    }

    protected function getToSummarySubcategoryList(array $parameters = []): TestResponse
    {
        return $this->route('summary.subcategory.list', $parameters);
    }

    protected function fetchOptionsForSummarySubcategoryCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('summary.subcategory.list.options', $parameters);
    }

    protected function getToSummaryResourceList(array $parameters = []): TestResponse
    {
        return $this->route('summary.resource.list', $parameters);
    }

    protected function fetchOptionsForSummaryResourceCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('summary.resource.list.options', $parameters);
    }

    protected function getToSummaryResourceTypeList(array $parameters = []): TestResponse
    {
        return $this->route('summary.resource-type.list', $parameters);
    }

    protected function fetchOptionsForSummaryResourceTypeCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('summary.resource-type.list.options', $parameters);
    }

    protected function getToSummaryItemList(array $parameters = []): TestResponse
    {
        return $this->route('summary.item.list', $parameters);
    }

    protected function fetchOptionsForSummaryItemCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('summary.item.list.options', $parameters);
    }

    protected function getToRequestErrorLogList(array $parameters = []): TestResponse
    {
        return $this->route('request.error-log.list', $parameters);
    }

    protected function fetchOptionsForRequestErrorLogCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('request.error-log.list.options', $parameters);
    }

    protected function postToRequestErrorLogCreate(array $payload): TestResponse
    {
        return $this->post(route('request.error-log.create'), $payload);
    }

    protected function quickCreateRequestErrorLog(array $override = []): void
    {
        $payload = [
            'method' => 'GET',
            'expected_status_code' => 200,
            'returned_status_code' => 500,
            'request_uri' => '/v3/' . $this->faker->word(),
            'source' => 'app',
        ];

        foreach ($override as $k => $v) {
            $payload[$k] = $v;
        }

        $this->postToRequestErrorLogCreate($payload)->assertStatus(204);
    }

    protected function deleteToItemSubcategoryDelete(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_subcategory_id
    ): TestResponse
    {
        return $this->delete(
            route(
                'item-subcategory.delete',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id,
                    'item_category_id' => $item_category_id,
                    'item_subcategory_id' => $item_subcategory_id
                ]
            )
        );
    }

    protected function getToPermittedUserList(array $parameters = []): TestResponse
    {
        return $this->route('permitted-user.list', $parameters);
    }

    protected function getToCategoryShow(array $parameters = []): TestResponse
    {
        return $this->route('category.show', $parameters);
    }

    protected function getToCategoryList(array $parameters = []): TestResponse
    {
        return $this->route('category.list', $parameters);
    }

    protected function getToItemShow(array $parameters = []): TestResponse
    {
        return $this->route('item.show', $parameters);
    }

    protected function getToItemList(array $parameters = []): TestResponse
    {
        return $this->route('item.list', $parameters);
    }

    protected function getToResourceShow(array $parameters = []): TestResponse
    {
        return $this->route('resource.show', $parameters);
    }

    protected function getToResourceList(array $parameters = []): TestResponse
    {
        return $this->route('resource.list', $parameters);
    }

    protected function getToResourceTypeList(array $parameters = []): TestResponse
    {
        return $this->route('resource-type.list', $parameters);
    }

    protected function fetchItemType(array $parameters = []): TestResponse
    {
        return $this->route('item-type.show', $parameters);
    }

    protected function getToPermittedUserShow(array $parameters = []): TestResponse
    {
        return $this->route('permitted-user.show', $parameters);
    }

    protected function fetchRandomUser()
    {
        return User::query()->where('id', '!=', 1)->inRandomOrder()->first();
    }

    protected function getToResourceTypeShow(array $parameters = []): TestResponse
    {
        return $this->route('resource-type.show', $parameters);
    }

    protected function getToSubcategoryShow(array $parameters = []): TestResponse
    {
        return $this->route('subcategory.show', $parameters);
    }

    protected function getToSubcategoryList(array $parameters = []): TestResponse
    {
        return $this->route('subcategory.list', $parameters);
    }

    protected function fetchOptionsForCategory(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('category.show.options', $parameters);
    }

    protected function fetchOptionsForCategoryCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('category.list.options', $parameters);
    }

    protected function fetchOptionsForCreatePassword(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.create-password.options', $parameters);
    }

    protected function fetchOptionsForItem(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item.show.options', $parameters);
    }

    protected function fetchOptionsForItemCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('item.list.options', $parameters);
    }

    protected function fetchOptionsForRegister(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.register.options', $parameters);
    }

    protected function fetchOptionsForMigrateBudgetProRequestDelete(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.migrate.budget-pro.request-delete.options', $parameters);
    }

    protected function fetchOptionsForResource(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('resource.show.options', $parameters);
    }

    protected function fetchOptionsForResourceCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('resource.list.options', $parameters);
    }

    protected function fetchOptionsForResourceType(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('resource-type.show.options', $parameters);
    }

    protected function fetchOptionsForResourceTypeCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('resource-type.list.options', $parameters);
    }

    protected function fetchOptionsForSubcategory(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('subcategory.show.options', $parameters);
    }

    protected function fetchOptionsForSubcategoryCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('subcategory.list.options', $parameters);
    }

    protected function fetchOptionsForUpdatePassword(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.update-password.options', $parameters);
    }

    protected function fetchOptionsForUpdateProfile(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.update-profile.options', $parameters);
    }

    protected function fetchOptionsForCheck(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.check.options', $parameters);
    }

    protected function fetchOptionsForCreateNewPassword(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.create-new-password.options', $parameters);
    }

    protected function fetchOptionsForForgotPassword(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.forgot-password.options', $parameters);
    }

    protected function getToAuthUser(array $parameters = []): TestResponse
    {
        return $this->route('auth.user.show', $parameters);
    }

    protected function fetchOptionsForAuthUser(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.options', $parameters);
    }

    protected function getToPermittedResourceTypeList(array $parameters = []): TestResponse
    {
        return $this->route('auth.user.permitted-resource-types.list', $parameters);
    }

    protected function fetchOptionsForPermittedResourceTypeCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.permitted-resource-types.list.options', $parameters);
    }

    protected function getToPermittedResourceTypeShow(array $parameters = []): TestResponse
    {
        return $this->route('auth.user.permitted-resource-types.show', $parameters);
    }

    protected function fetchOptionsForPermittedResourceType(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.permitted-resource-types.show.options', $parameters);
    }

    protected function getToPermittedResourceTypeResourcesList(array $parameters = []): TestResponse
    {
        return $this->route('auth.user.permitted-resource-types-resources.list', $parameters);
    }

    protected function fetchOptionsForPermittedResourceTypeResourcesCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.permitted-resource-types-resources.list.options', $parameters);
    }

    protected function getToPermittedResourceTypeResourceShow(array $parameters = []): TestResponse
    {
        return $this->route('auth.user.permitted-resource-types-resources.show', $parameters);
    }

    protected function fetchOptionsForPermittedResourceTypeResource(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.permitted-resource-types-resources.show.options', $parameters);
    }

    protected function fetchOptionsForAuthRequestDelete(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.request-delete.options', $parameters);
    }

    protected function fetchOptionsForAuthRequestResourceDelete(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.request-resource-delete.options', $parameters);
    }

    protected function fetchOptionsForAuthRequestResourceTypeDelete(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.request-resource-type-delete.options', $parameters);
    }

    protected function getToTokenList(array $parameters = []): TestResponse
    {
        return $this->route('auth.user.token.list', $parameters);
    }

    protected function fetchOptionsForTokenCollection(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.token.list.options', $parameters);
    }

    protected function getToTokenShow(array $parameters = []): TestResponse
    {
        return $this->route('auth.user.token.show', $parameters);
    }

    protected function fetchOptionsForToken(array $parameters = []): TestResponse
    {
        return $this->optionsRoute('auth.user.token.show.options', $parameters);
    }

    protected function route(string $route, array $parameters = []): TestResponse
    {
        return $this->get(route($route, $parameters));
    }

    protected function optionsRoute(string $route, array $parameters = []): TestResponse
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

        if (app()->environment() !== 'testing') {
            dd('Not in \'testing\' environment, skipping tests');
        }

        $this->withoutMiddleware(
            ThrottleRequests::class
        );

        $this->withHeader('X-Internal-Api-Key', 'testing-internal-api-key');

        if (env('APP_KEY') === '' || env('APP_KEY') === null) {
            $this->artisan('key:generate --env=testing');
        }
        
        $this->artisan('migrate:fresh');

        $hash = new \App\HttpRequest\Hash();
        
        // Set the primary user
        $user = new User();
        $user->name = $this->faker->text;
        $user->email = $this->email_for_expected_test_user;
        $user->password = Hash::make($this->password_for_expected_test_user);
        $user->save();
        
        // Create the allocated expense resource type for the primary user
        $resource_type = new ResourceType();
        $resource_type->name = $this->faker->text;
        $resource_type->description = $this->faker->text;
        $resource_type->data = '{"field":true}';
        $resource_type->save();
        
        $resource_type_item_type = new ResourceTypeItemType();
        $resource_type_item_type->resource_type_id = $resource_type->id;
        $resource_type_item_type->item_type_id = $hash->decode('item-type', $this->item_types['allocated-expense']);
        
        $permitted_user = new PermittedUser();
        $permitted_user->resource_type_id = $resource_type->id;
        $permitted_user->user_id = $user->id;
        $permitted_user->added_by = $user->id;
        $permitted_user->save();
    }

    protected function patchToCategoryUpdate(string $resource_type_id, string $category_id, array $payload): TestResponse
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

    protected function patchToItemUpdate(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        array $payload
    ): TestResponse
    {
        return $this->patch(
            route(
                'item.update',
                [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id
                ]
            ),
            $payload
        );
    }

    protected function patchToResourceUpdate(string $resource_type_id, string $resource_id, array $payload): TestResponse
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

    protected function patchToResourceTypeUpdate(string $resource_type_id, array $payload): TestResponse
    {
        return $this->patch(
            route('resource-type.update', ['resource_type_id' => $resource_type_id]),
            $payload
        );
    }

    protected function patchToSubcategoryUpdate(
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
