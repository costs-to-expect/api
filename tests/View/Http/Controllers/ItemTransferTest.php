<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ItemTransferTest extends TestCase
{
    /** @test */
    public function itemTransferCreate(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $response = $this->postToItemTransferCreate(
            $resource_type_id,
            $from_resource_id,
            $item_id,
            ['resource_id' => $to_resource_id]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function itemTransferCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $this->postToItemTransferCreate(
            $resource_type_id,
            $from_resource_id,
            $item_id,
            ['resource_id' => $to_resource_id]
        )->assertStatus(204);

        $response = $this->getToItemTransferList(['resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesTransferSchema($json);
        }
    }

    /** @test */
    public function itemTransferShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $this->postToItemTransferCreate(
            $resource_type_id,
            $from_resource_id,
            $item_id,
            ['resource_id' => $to_resource_id]
        )->assertStatus(204);

        $list = $this->getToItemTransferList(['resource_type_id' => $resource_type_id]);
        $item_transfer_id = $list->json()[0]['id'];

        $response = $this->getToItemTransferShow([
            'resource_type_id' => $resource_type_id,
            'item_transfer_id' => $item_transfer_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesTransferSchema($response->content());
    }

    /** @test */
    public function itemTransferShowNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $other_resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $other_from_resource_id = $this->quickCreateAllocatedExpenseResource($other_resource_type_id);
        $other_to_resource_id = $this->quickCreateAllocatedExpenseResource($other_resource_type_id);
        $other_item_id = $this->quickCreateAllocatedExpenseItem($other_resource_type_id, $other_from_resource_id);

        $this->postToItemTransferCreate(
            $other_resource_type_id,
            $other_from_resource_id,
            $other_item_id,
            ['resource_id' => $other_to_resource_id]
        )->assertStatus(204);

        $list = $this->getToItemTransferList(['resource_type_id' => $other_resource_type_id]);
        $other_item_transfer_id = $list->json()[0]['id'];

        $response = $this->getToItemTransferShow([
            'resource_type_id' => $resource_type_id,
            'item_transfer_id' => $other_item_transfer_id
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function itemTransferNotSupportedForGame(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();

        $response = $this->getToItemTransferList(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(405);
    }

    /** @test */
    public function optionsRequestForItemTransferCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForItemTransferCollection(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);
    }

    /** @test */
    public function optionsRequestForItemTransfer(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $from_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $to_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $from_resource_id);

        $this->postToItemTransferCreate(
            $resource_type_id,
            $from_resource_id,
            $item_id,
            ['resource_id' => $to_resource_id]
        )->assertStatus(204);

        $list = $this->getToItemTransferList(['resource_type_id' => $resource_type_id]);
        $item_transfer_id = $list->json()[0]['id'];

        $response = $this->fetchOptionsForItemTransfer([
            'resource_type_id' => $resource_type_id,
            'item_transfer_id' => $item_transfer_id
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function optionsRequestForItemTransferAction(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemTransferAction([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);
        $response->assertStatus(200);
    }
}
