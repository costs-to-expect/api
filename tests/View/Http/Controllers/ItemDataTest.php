<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ItemDataTest extends TestCase
{
    /** @test */
    public function itemDataCollectionEmpty(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->getToItemDataList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 0);
        $this->assertEquals([], $response->json());
    }

    /** @test */
    public function itemDataCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $this->quickCreateItemData($resource_type_id, $resource_id, $item_id, ['key' => 'score', 'value' => '{"total":123}']);

        $response = $this->getToItemDataList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
        $this->assertEquals('score', $response->json()[0]['key']);
        $this->assertEquals(['total' => 123], $response->json()[0]['value']);
    }

    /** @test */
    public function itemDataShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $key = $this->quickCreateItemData($resource_type_id, $resource_id, $item_id, ['key' => 'score', 'value' => '{"total":123}']);

        $response = $this->getToItemDataShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'key' => $key
        ]);

        $response->assertStatus(200);
        $this->assertEquals('score', $response->json('key'));
        $this->assertEquals(['total' => 123], $response->json('value'));
    }

    /** @test */
    public function itemDataShowNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->getToItemDataShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'key' => 'does-not-exist'
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function itemDataNotSupportedForAllocatedExpense(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToItemDataList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(405);
    }

    /** @test */
    public function optionsRequestForItemDataCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemDataCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function optionsRequestForItemData(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $key = $this->quickCreateItemData($resource_type_id, $resource_id, $item_id, ['key' => 'score', 'value' => '{"total":123}']);

        $response = $this->fetchOptionsForItemData([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'key' => $key
        ]);

        $response->assertStatus(200);
    }
}
