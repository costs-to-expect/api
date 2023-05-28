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

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/allocated-expense.json');
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

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/allocated-expense-collection.json');
    }

    /** @test */
    public function optionsRequestForBudgetItem(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);
        $item_id = $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItem([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/budget.json');
    }

    /** @test */
    public function optionsRequestForBudgetItemCollection(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $this->createBudgetItem($resource_type_id, $resource_id);
        $this->createBudgetItem($resource_type_id, $resource_id);
        $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/budget-collection.json');
    }

    /** @test */
    public function optionsRequestForBudgetProItem(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);
        $item_id = $this->createBudgetProItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItem([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/budget-pro.json');
    }

    /** @test */
    public function optionsRequestForBudgetProItemCollection(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/budget-pro-collection.json');
    }

    /** @test */
    public function optionsRequestForYahtzeeGameItem(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItem([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/yahtzee.json');
    }

    /** @test */
    public function optionsRequestForYahtzeeGameItemCollection(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);

        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/yahtzee-collection.json');
    }

    /** @test */
    public function optionsRequestForYatzyGameItem(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItem([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/yatzy.json');
    }

    /** @test */
    public function optionsRequestForYatzyGameItemCollection(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);

        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/yatzy-collection.json');
    }
}
