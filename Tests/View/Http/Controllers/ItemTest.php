<?php

namespace Http\Controllers;

use App\User;
use Tests\TestCase;

final class ItemTest extends TestCase
{
    /** @test */
    public function optionsRequestForAllocatedExpenseItem(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);
        $item_id = $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItem([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);

        //$this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/allocated-expense.json');
    }

    /** @test */
    public function optionsRequestForAllocatedExpenseItemCollection(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);

        //$this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/allocated-expense-collection.json');
    }
}
