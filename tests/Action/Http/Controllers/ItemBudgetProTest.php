<?php

namespace Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

final class ItemBudgetProTest extends TestCase
{
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
}
