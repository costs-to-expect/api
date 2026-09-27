<?php

namespace Tests\View\Http\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ItemLogTest extends TestCase
{
    #[Test]
    public function itemLogCollectionEmpty(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->getToItemLogList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 0);
        $this->assertEquals([], $response->json());
    }

    #[Test]
    public function itemLogCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $this->quickCreateItemLog($resource_type_id, $resource_id, $item_id, ['message' => 'Rolled a Yahtzee']);

        $response = $this->getToItemLogList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
        $this->assertEquals('Rolled a Yahtzee', $response->json()[0]['message']);
    }

    #[Test]
    public function itemLogShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $item_log_id = $this->quickCreateItemLog($resource_type_id, $resource_id, $item_id, ['message' => 'Rolled a Yahtzee']);

        $response = $this->getToItemLogShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_log_id' => $item_log_id
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Rolled a Yahtzee', $response->json('message'));
    }

    #[Test]
    public function itemLogShowNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $other_item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);
        $other_item_log_id = $this->quickCreateItemLog($resource_type_id, $resource_id, $other_item_id, ['message' => 'Rolled a Yahtzee']);

        $response = $this->getToItemLogShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_log_id' => $other_item_log_id
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function itemLogNotSupportedForAllocatedExpense(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToItemLogList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(405);
    }

    #[Test]
    public function optionsRequestForItemLogCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemLogCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function optionsRequestForItemLog(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $item_log_id = $this->quickCreateItemLog($resource_type_id, $resource_id, $item_id, ['message' => 'Rolled a Yahtzee']);

        $response = $this->fetchOptionsForItemLog([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_log_id' => $item_log_id
        ]);

        $response->assertStatus(200);
    }
}
