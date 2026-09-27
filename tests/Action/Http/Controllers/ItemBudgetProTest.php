<?php

namespace Tests\Action\Http\Controllers;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ItemBudgetProTest extends TestCase
{
    #[Test]
    public function createBudgetProItemFailsAmountNotFormattedCorrectly(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);

        $response = $this->postToItemCreate(
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

    #[Test]
    public function createBudgetProItemFailsCategoryInvalid(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);

        $response = $this->postToItemCreate(
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

    #[Test]
    public function createBudgetProItemFailsCurrencyInvalid(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);

        $response = $this->postToItemCreate(
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

    #[Test]
    public function createBudgetProItemFailsFrequencyJsonInvalid(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);

        $response = $this->postToItemCreate(
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

    #[Test]
    public function createBudgetProItemFailsNoNameInPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);

        $response = $this->postToItemCreate(
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

    #[Test]
    public function createBudgetProItemFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            []
        );

        $response->assertStatus(422);
    }

    #[Test]
    public function createBudgetProItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);

        $response = $this->postToItemCreate(
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

    #[Test]
    public function deleteBudgetProItemFailsIdNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);
        $item_id = '1234asdffgd';

        $response = $this->deleteToItemDelete(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(403);
    }

    #[Test]
    public function deleteBudgetProItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);
        $item_id = $this->quickCreateBudgetProItem($resource_type_id, $resource_id);

        $response = $this->deleteToItemDelete(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(204);
    }

    #[Test]
    public function updateBudgetProItemFailsNonExistentField(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);
        $item_id = $this->quickCreateBudgetProItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'does_not_exist' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(400);
    }

    #[Test]
    public function updateBudgetProItemFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);
        $item_id = $this->quickCreateBudgetProItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
            $resource_type_id,
            $resource_id,
            $item_id,
            []
        );

        $response->assertStatus(400);
    }

    #[Test]
    public function updateBudgetProItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);
        $item_id = $this->quickCreateBudgetProItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
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
