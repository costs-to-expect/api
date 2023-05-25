<?php

namespace Tests\Action\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

final class ItemTest extends TestCase
{
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
}
