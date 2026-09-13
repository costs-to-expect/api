<?php

namespace Tests\Action\Http\Controllers;

use Tests\TestCase;

final class ItemLogTest extends TestCase
{
    /** @test */
    public function createItemLogFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->postToItemLogCreate($resource_type_id, $resource_id, $item_id, []);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemLogFailsInvalidJsonParameters(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->postToItemLogCreate($resource_type_id, $resource_id, $item_id, [
            'message' => 'Rolled a Yahtzee',
            'parameters' => 'not-json'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemLogFailsNotSupportedForAllocatedExpense(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->postToItemLogCreate($resource_type_id, $resource_id, $item_id, [
            'message' => 'Rolled a Yahtzee',
            'parameters' => '{"score":123}'
        ]);

        $response->assertStatus(405);
    }

    /** @test */
    public function createItemLogSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->postToItemLogCreate($resource_type_id, $resource_id, $item_id, [
            'message' => 'Rolled a Yahtzee',
            'parameters' => '{"score":123}'
        ]);

        $response->assertStatus(201);
        $this->assertEquals('Rolled a Yahtzee', $response->json('message'));
        $this->assertEquals(['score' => 123], $response->json('parameters'));
    }
}
