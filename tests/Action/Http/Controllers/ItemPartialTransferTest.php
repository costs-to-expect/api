<?php

namespace Tests\Action\Http\Controllers;

use Tests\TestCase;

final class ItemPartialTransferTest extends TestCase
{
    /** @test */
    public function createItemPartialTransferFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->postToItemPartialTransferCreate($resource_type_id, $resource_id, $item_id, []);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemPartialTransferFailsPercentageOutOfRange(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $response = $this->postToItemPartialTransferCreate($resource_type_id, $from_resource_id, $item_id, [
            'resource_id' => $to_resource_id,
            'percentage' => 150
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemPartialTransferFailsSameResource(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->postToItemPartialTransferCreate($resource_type_id, $resource_id, $item_id, [
            'resource_id' => $resource_id,
            'percentage' => 30
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemPartialTransferSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $response = $this->postToItemPartialTransferCreate($resource_type_id, $from_resource_id, $item_id, [
            'resource_id' => $to_resource_id,
            'percentage' => 30
        ]);

        $response->assertStatus(201);
        $this->assertEquals(30, $response->json('percentage'));
    }

    /** @test */
    public function deleteItemPartialTransferFailsIdInvalid(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->deleteToItemPartialTransferDelete($resource_type_id, 'ABCDEDFGFG');
        $response->assertStatus(403);
    }

    /** @test */
    public function deleteItemPartialTransferSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $item_partial_transfer_id = $this->quickCreateItemPartialTransfer($resource_type_id, $from_resource_id, $item_id, $to_resource_id, 30);

        $response = $this->deleteToItemPartialTransferDelete($resource_type_id, $item_partial_transfer_id);
        $response->assertStatus(204);
    }
}
