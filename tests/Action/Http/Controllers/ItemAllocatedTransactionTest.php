<?php

namespace Tests\Action\Http\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ItemAllocatedTransactionTest extends TestCase
{
    #[Test]
    public function createAllocatedTransactionItemFailsCurrencyIdInvalid(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'effective_date' => $this->faker->date(),
                'currency_id' => 'epMqeYqPkp',
                'total' => $this->randomMoneyValue(),
                'transaction_type' => 'expense',
            ]
        );

        $response->assertStatus(422);
    }

    #[Test]
    public function createAllocatedTransactionItemFailsNoNameInPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            [
                'description' => $this->faker->text(200),
                'effective_date' => $this->faker->date(),
                'currency_id' => $this->currency['GBP'],
                'total' => $this->randomMoneyValue(),
                'transaction_type' => 'expense',
            ]
        );

        $response->assertStatus(422);
    }

    #[Test]
    public function createAllocatedTransactionItemFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            [
            ]
        );

        $response->assertStatus(422);
    }

    #[Test]
    public function createAllocatedTransactionItemFailsNoTransactionTypeInPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $response = $this->postToItemCreate(
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

        $response->assertStatus(422);
    }

    #[Test]
    public function createAllocatedTransactionItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'effective_date' => $this->faker->date(),
                'currency_id' => $this->currency['GBP'],
                'total' => $this->randomMoneyValue(),
                'transaction_type' => 'income',
            ]
        );

        $response->assertStatus(201);
        $this->assertEquals('income', $response->json('transaction_type'));
        $this->assertJsonMatchesAllocatedTransactionItemSchema($response->content());
    }

    #[Test]
    public function deleteAllocatedTransactionItemFailsIdNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);
        $item_id = '1234asdffgd';

        $response = $this->deleteToItemDelete(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(403);
    }

    #[Test]
    public function deleteAllocatedTransactionItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

        $response = $this->deleteToItemDelete(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(204);
    }

    #[Test]
    public function updateAllocatedTransactionItemFailsNonExistentField(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

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
    public function updateAllocatedTransactionItemFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
            $resource_type_id,
            $resource_id,
            $item_id,
            []
        );

        $response->assertStatus(400);
    }

    #[Test]
    public function updateAllocatedTransactionItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

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
