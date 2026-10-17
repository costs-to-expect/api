<?php

namespace Tests\Action\Http\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ItemAllocatedExpenseTest extends TestCase
{
    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function createAllocatedExpenseItemReturnsPublishAfter(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $with_date = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'effective_date' => '2026-09-20',
                'publish_after' => '2099-01-31',
                'currency_id' => $this->currency['GBP'],
                'total' => $this->randomMoneyValue(),
            ]
        );

        $with_date->assertStatus(201);
        $this->assertSame('2099-01-31', $with_date->json('publish_after'));

        $without_date = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'effective_date' => '2026-09-20',
                'currency_id' => $this->currency['GBP'],
                'total' => $this->randomMoneyValue(),
            ]
        );

        $without_date->assertStatus(201);
        $this->assertNull($without_date->json('publish_after'));
    }

    #[Test]
    public function updateAllocatedExpenseItemCanSetAndClearPublishAfter(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $this->patchToItemUpdate($resource_type_id, $resource_id, $item_id, ['publish_after' => '2099-01-31'])
            ->assertStatus(204);

        $this->assertSame(
            '2099-01-31',
            $this->getToItemShow([$resource_type_id, $resource_id, $item_id])->json('publish_after')
        );

        $this->patchToItemUpdate($resource_type_id, $resource_id, $item_id, ['publish_after' => null])
            ->assertStatus(204);

        $this->assertNull(
            $this->getToItemShow([$resource_type_id, $resource_id, $item_id])->json('publish_after')
        );
    }
}
