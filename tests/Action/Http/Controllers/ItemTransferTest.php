<?php

namespace Tests\Action\Http\Controllers;

use Tests\TestCase;

final class ItemTransferTest extends TestCase
{
    /** @test */
    public function createItemTransferFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->postToItemTransferCreate($resource_type_id, $resource_id, $item_id, []);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemTransferFailsSameResource(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->postToItemTransferCreate($resource_type_id, $resource_id, $item_id, [
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemTransferFailsResourceFromDifferentResourceType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $other_resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $other_resource_id = $this->quickCreateAllocatedExpenseResource($other_resource_type_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->postToItemTransferCreate($resource_type_id, $resource_id, $item_id, [
            'resource_id' => $other_resource_id
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemTransferSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $response = $this->postToItemTransferCreate($resource_type_id, $from_resource_id, $item_id, [
            'resource_id' => $to_resource_id
        ]);

        $response->assertStatus(204);
    }
}
