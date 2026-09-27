<?php

namespace Tests\Action\Http\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ItemDataTest extends TestCase
{
    #[Test]
    public function createItemDataFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->postToItemDataCreate($resource_type_id, $resource_id, $item_id, []);

        $response->assertStatus(422);
    }

    #[Test]
    public function createItemDataFailsInvalidJsonValue(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->postToItemDataCreate($resource_type_id, $resource_id, $item_id, [
            'key' => 'score',
            'value' => 'not-json'
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function createItemDataFailsNotSupportedForAllocatedExpense(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->postToItemDataCreate($resource_type_id, $resource_id, $item_id, [
            'key' => 'score',
            'value' => '{"total":1}'
        ]);

        $response->assertStatus(405);
    }

    #[Test]
    public function createItemDataSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->postToItemDataCreate($resource_type_id, $resource_id, $item_id, [
            'key' => 'score',
            'value' => '{"total":123}'
        ]);

        $response->assertStatus(201);
    }

    #[Test]
    public function updateItemDataFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);
        $key = $this->quickCreateItemData($resource_type_id, $resource_id, $item_id, ['key' => 'score', 'value' => '{"total":1}']);

        $response = $this->patchToItemDataUpdate($resource_type_id, $resource_id, $item_id, $key, []);

        $response->assertStatus(400);
    }

    #[Test]
    public function updateItemDataFailsInvalidFieldsInRequest(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);
        $key = $this->quickCreateItemData($resource_type_id, $resource_id, $item_id, ['key' => 'score', 'value' => '{"total":1}']);

        $response = $this->patchToItemDataUpdate($resource_type_id, $resource_id, $item_id, $key, [
            'value' => '{"total":2}',
            'extra' => 'not-allowed'
        ]);

        $response->assertStatus(400);
    }

    #[Test]
    public function updateItemDataSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);
        $key = $this->quickCreateItemData($resource_type_id, $resource_id, $item_id, ['key' => 'score', 'value' => '{"total":1}']);

        $response = $this->patchToItemDataUpdate($resource_type_id, $resource_id, $item_id, $key, [
            'value' => '{"total":2}'
        ]);

        $response->assertStatus(204);

        $response = $this->getToItemDataShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'key' => $key
        ]);
        $this->assertEquals(['total' => 2], $response->json('value'));
    }

    #[Test]
    public function deleteItemDataFailsKeyNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->deleteToItemDataDelete($resource_type_id, $resource_id, $item_id, 'does-not-exist');
        $response->assertStatus(403);
    }

    #[Test]
    public function deleteItemDataSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);
        $key = $this->quickCreateItemData($resource_type_id, $resource_id, $item_id, ['key' => 'score', 'value' => '{"total":1}']);

        $response = $this->deleteToItemDataDelete($resource_type_id, $resource_id, $item_id, $key);
        $response->assertStatus(204);
    }
}
