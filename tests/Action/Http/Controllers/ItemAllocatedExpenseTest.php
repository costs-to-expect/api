<?php

namespace Tests\Action\Http\Controllers;

use Tests\TestCase;

final class ItemAllocatedExpenseTest extends TestCase
{
    /** @test */
    public function createAllocatedExpenseItemFailsCurrencyIdInvalid(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->postToItemCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->postToItemCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->postToItemCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

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

        $response->assertStatus(201);
        $this->assertJsonMatchesAllocatedExpenseItemSchema($response->content());
    }

    /** @test */
    public function deleteAllocatedExpenseItemFailsIdNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = '1234asdffgd';

        $response = $this->deleteToItemDelete(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function deleteAllocatedExpenseItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id,$resource_id);

        $response = $this->deleteToItemDelete(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateAllocatedExpenseItemFailsNonExistentField(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

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

    /** @test */
    public function updateAllocatedExpenseItemFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

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
