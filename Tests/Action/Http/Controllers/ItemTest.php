<?php

namespace Tests\Action\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

final class ItemTest extends TestCase
{
    /** @test */
    public function createAllocatedExpenseItemFailsCurrencyIdInvalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'effective_date' => $this->faker->date(),
                'currency_id' => 'epMqeYqPkp',
                'total' => $this->randomMoneyValue(),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseItemFailsNoNameInPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'description' => $this->faker->text(200),
                'effective_date' => $this->faker->date(),
                'currency_id' => $this->currency['GBP'],
                'total' => $this->randomMoneyValue(),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseItemFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'effective_date' => $this->faker->date(),
                'currency_id' => $this->currency['GBP'],
                'total' => $this->randomMoneyValue(),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesAllocatedExpenseItemSchema($response->content());
    }

    /** @test */
    public function createBudgetItemFailsAmountNotFormattedCorrectly(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => number_format($this->faker->randomFloat(2, 0.01, 99999999999.99), 2, '.', ','),
                'currency_id' => $this->currency['GBP'],
                'category' => 'income',
                'start_date' => $this->faker->date(),
                'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetItemFailsCategoryInvalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => $this->randomMoneyValue(),
                'currency_id' => $this->currency['GBP'],
                'category' => 'not-income',
                'start_date' => $this->faker->date(),
                'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetItemFailsCurrencyInvalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => $this->randomMoneyValue(),
                'currency_id' => 'epMqeYqPko',
                'category' => 'income',
                'start_date' => $this->faker->date(),
                'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetItemFailsFrequencyJsonInvalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => $this->randomMoneyValue(),
                'currency_id' => $this->currency['GBP'],
                'category' => 'income',
                'start_date' => $this->faker->date(),
                'frequency' => '{"field"=>true}',
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetItemFailsNoNameInPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => $this->randomMoneyValue(),
                'currency_id' => $this->currency['GBP'],
                'category' => 'income',
                'start_date' => $this->faker->date(),
                'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetItemFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => $this->randomMoneyValue(),
                'currency_id' => $this->currency['GBP'],
                'category' => 'income',
                'start_date' => $this->faker->date(),
                'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesBudgetItemSchema($response->content());
    }

    /** @test */
    public function createBudgetProItemFailsAmountNotFormattedCorrectly(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => number_format($this->faker->randomFloat(2, 0.01, 99999999999.99), 2, '.', ','),
                'currency_id' => $this->currency['GBP'],
                'category' => 'income',
                'start_date' => $this->faker->date(),
                'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetProItemFailsCategoryInvalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => $this->randomMoneyValue(),
                'currency_id' => $this->currency['GBP'],
                'category' => 'not-income',
                'start_date' => $this->faker->date(),
                'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetProItemFailsCurrencyInvalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => $this->randomMoneyValue(),
                'currency_id' => 'epMqeYqPko',
                'category' => 'income',
                'start_date' => $this->faker->date(),
                'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetProItemFailsFrequencyJsonInvalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => $this->randomMoneyValue(),
                'currency_id' => $this->currency['GBP'],
                'category' => 'income',
                'start_date' => $this->faker->date(),
                'frequency' => '{"field"=>true}',
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetProItemFailsNoNameInPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => $this->randomMoneyValue(),
                'currency_id' => $this->currency['GBP'],
                'category' => 'income',
                'start_date' => $this->faker->date(),
                'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetProItemFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createBudgetProItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'account' => Str::uuid()->toString(),
                'description' => $this->faker->text(200),
                'amount' => $this->randomMoneyValue(),
                'currency_id' => $this->currency['GBP'],
                'category' => 'income',
                'start_date' => $this->faker->date(),
                'frequency' => json_encode(['type'=>'monthly', 'day'=>null, 'exclusions' => []], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesBudgetProItemSchema($response->content());
    }

    /** @test */
    public function createYahtzeeGameItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesGameItemSchema($response->content());
    }

    /** @test */
    public function createYatzyGameItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);

        $response = $this->createItem(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesGameItemSchema($response->content());
    }

    /** @test */
    public function deleteAllocatedExpenseItemFailsIdNotFound(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);
        $item_id = '1234asdffgd';

        $response = $this->deleteItem(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function deleteAllocatedExpenseItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);
        $item_id = $this->createAllocatedExpenseItem($resource_type_id,$resource_id);

        $response = $this->deleteItem(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetItemFailsIdNotFound(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);
        $item_id = '1234asdffgd';

        $response = $this->deleteItem(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function deleteBudgetItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);
        $item_id = $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->deleteItem(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetProItemFailsIdNotFound(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);
        $item_id = '1234asdffgd';

        $response = $this->deleteItem(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function deleteBudgetProItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);
        $item_id = $this->createBudgetProItem($resource_type_id, $resource_id);

        $response = $this->deleteItem(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteYahtzeeGameItemFailsNotFound(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);
        $item_id = '1234567890';

        $response = $this->deleteItem(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function deleteYahtzeeGameItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->deleteItem(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteYatzyGameItemFailsNotFound(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);
        $item_id = '1234567890';

        $response = $this->deleteItem(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function deleteYatzyGameItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);
        $item_id = $this->createYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->deleteItem(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateAllocatedExpenseItemFailsNonExistentField(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);
        $item_id = $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'does_not_exist' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseItemFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);
        $item_id = $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);
        $item_id = $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateBudgetItemFailsNonExistentField(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);
        $item_id = $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'does_not_exist' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateBudgetItemFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);
        $item_id = $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateBudgetItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);
        $item_id = $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateBudgetProItemFailsNonExistentField(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);
        $item_id = $this->createBudgetProItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'does_not_exist' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateBudgetProItemFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);
        $item_id = $this->createBudgetProItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateBudgetProItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);
        $item_id = $this->createBudgetProItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateYahtzeeGameItemFailsNonExistentField(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'i_do_not_exist' => json_encode(['turns'=>[], 'data'=>null], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateYahtzeeGameItemFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateYahtzeeGameItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'game' => json_encode(['turns'=>[], 'data'=>null], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateYatzyGameItemFailsNonExistentField(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);
        $item_id = $this->createYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'i_do_not_exist' => json_encode(['turns'=>[], 'data'=>null], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateYatzyGameItemFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);
        $item_id = $this->createYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateYatzyGameItemSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->updateItem(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'game' => json_encode(['turns'=>[], 'data'=>null], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(204);
    }
}
