<?php

namespace Http\Controllers;

use App\User;
use Tests\TestCase;

final class ItemBudgetTest extends TestCase
{
    /** @test */
    public function budgetItemCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $this->createBudgetItem($resource_type_id, $resource_id);
        $this->createBudgetItem($resource_type_id, $resource_id);
        $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id
        ]);

        $response->assertStatus(200);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesBudgetItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetItemCollectionPagination(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $this->createBudgetItem($resource_type_id, $resource_id);
        $this->createBudgetItem($resource_type_id, $resource_id);
        $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'offset'=>0,
            'limit'=> 2
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', "");
        $response->assertHeader('X-Link-Next', "/v3/resource-types/{$resource_type_id}/resources/{$resource_id}/items?offset=2&limit=2");

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesBudgetItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetItemCollectionSearchName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $this->createBudgetItem($resource_type_id, $resource_id);
        $this->createBudgetItem($resource_type_id, $resource_id, ['name' => 'find-me']);
        $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'search'=>'name:find-me'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:find-me');
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesBudgetItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetItemCollectionSortAmount(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $this->createBudgetItem($resource_type_id, $resource_id, ['amount' => '10000.15']);
        $this->createBudgetItem($resource_type_id, $resource_id, ['amount' => '110000.27']);
        $this->createBudgetItem($resource_type_id, $resource_id, ['amount' => '6.15']);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'sort'=>'amount:asc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'amount:asc');
        $this->assertEquals('6.15', $response->json()[0]['amount']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesBudgetItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetItemCollectionSortCreated(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $this->createBudgetItem($resource_type_id, $resource_id);
        $this->createBudgetItem($resource_type_id, $resource_id);
        sleep(1); // ensure the created_at timestamps are different
        $this->createBudgetItem($resource_type_id, $resource_id, ['name' => 'created-last']);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'sort'=>'created:desc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'created:desc');
        $this->assertEquals('created-last', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesBudgetItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetItemCollectionSortName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $this->createBudgetItem($resource_type_id, $resource_id);
        $this->createBudgetItem($resource_type_id, $resource_id, ['name' => 'AAAAAAAAAAAA']);
        $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'sort'=>'name:asc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');
        $this->assertEquals('AAAAAAAAAAAA', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesBudgetItemSchema($json);
        }
    }

    /** @test */
    public function budgetItemShow(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);
        $item_id = $this->createBudgetItem($resource_type_id, $resource_id);

        $response = $this->fetchItem([
            $resource_type_id,
            $resource_id,
            $item_id
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesBudgetItemSchema($response->content());
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
}
