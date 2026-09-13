<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ItemPartialTransferTest extends TestCase
{
    /** @test */
    public function itemPartialTransferCreate(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $response = $this->postToItemPartialTransferCreate(
            $resource_type_id,
            $from_resource_id,
            $item_id,
            ['resource_id' => $to_resource_id, 'percentage' => 30]
        );

        $response->assertStatus(201);
        $this->assertEquals(30, $response->json('percentage'));
    }

    /** @test */
    public function itemPartialTransferCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $this->quickCreateItemPartialTransfer($resource_type_id, $from_resource_id, $item_id, $to_resource_id, 30);

        $response = $this->getToItemPartialTransferList(['resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesPartialTransferSchema($json);
        }
    }

    /** @test */
    public function itemPartialTransferShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $item_partial_transfer_id = $this->quickCreateItemPartialTransfer($resource_type_id, $from_resource_id, $item_id, $to_resource_id, 30);

        $response = $this->getToItemPartialTransferShow([
            'resource_type_id' => $resource_type_id,
            'item_partial_transfer_id' => $item_partial_transfer_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesPartialTransferSchema($response->content());

        $this->assertStringContainsString("resource-types/{$resource_type_id}/resources/{$from_resource_id}", $response->json('from.uri'));
        $this->assertStringContainsString("resource-types/{$resource_type_id}/resources/{$to_resource_id}", $response->json('to.uri'));
        $this->assertStringContainsString("resource-types/{$resource_type_id}/resources/{$from_resource_id}/items/{$item_id}", $response->json('item.uri'));
    }

    /** @test */
    public function itemPartialTransferShowNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $other_resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $other_from_resource_id = $this->quickCreateAllocatedExpenseResource($other_resource_type_id);
        $other_to_resource_id = $this->quickCreateAllocatedExpenseResource($other_resource_type_id);
        $other_item_id = $this->quickCreateAllocatedExpenseItem($other_resource_type_id, $other_from_resource_id);

        $other_item_partial_transfer_id = $this->quickCreateItemPartialTransfer(
            $other_resource_type_id,
            $other_from_resource_id,
            $other_item_id,
            $other_to_resource_id,
            30
        );

        $response = $this->getToItemPartialTransferShow([
            'resource_type_id' => $resource_type_id,
            'item_partial_transfer_id' => $other_item_partial_transfer_id
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function itemPartialTransferDelete(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $item_partial_transfer_id = $this->quickCreateItemPartialTransfer($resource_type_id, $from_resource_id, $item_id, $to_resource_id, 30);

        $response = $this->deleteToItemPartialTransferDelete($resource_type_id, $item_partial_transfer_id);
        $response->assertStatus(204);

        $response = $this->getToItemPartialTransferShow([
            'resource_type_id' => $resource_type_id,
            'item_partial_transfer_id' => $item_partial_transfer_id
        ]);
        $response->assertStatus(404);
    }

    /** @test */
    public function itemPartialTransferNotSupportedForGame(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();

        $response = $this->getToItemPartialTransferList(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(405);
    }

    /** @test */
    public function optionsRequestForItemPartialTransferCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForItemPartialTransferCollection(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);
    }

    /** @test */
    public function optionsRequestForItemPartialTransfer(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $item_partial_transfer_id = $this->quickCreateItemPartialTransfer($resource_type_id, $from_resource_id, $item_id, $to_resource_id, 30);

        $response = $this->fetchOptionsForItemPartialTransfer([
            'resource_type_id' => $resource_type_id,
            'item_partial_transfer_id' => $item_partial_transfer_id
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function optionsRequestForItemPartialTransferAction(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemPartialTransferAction([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);
        $response->assertStatus(200);
    }
}
