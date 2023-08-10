<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

final class ItemBudgetProTest extends TestCase
{
    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProItemCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesBudgetProItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProItemCollectionExcludeDeleted(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id, ['deleted'=>1]); // Show not be returned
        $this->createBudgetProItem($resource_type_id, $resource_id, ['deleted'=>1]); // Show not be returned
        $this->createBudgetProItem($resource_type_id, $resource_id, ['deleted'=>1]); // Show not be returned

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesBudgetProItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProItemCollectionPagination(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesBudgetProItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProItemCollectionSearchName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id, ['name' => 'find-me']);
        $this->createBudgetProItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesBudgetProItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProItemCollectionSortAmount(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $this->createBudgetProItem($resource_type_id, $resource_id, ['amount' => '10000.15']);
        $this->createBudgetProItem($resource_type_id, $resource_id, ['amount' => '110000.27']);
        $this->createBudgetProItem($resource_type_id, $resource_id, ['amount' => '6.15']);

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

            $this->assertJsonMatchesBudgetProItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProItemCollectionSortCreated(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id);
        sleep(1); // ensure the created_at timestamps are different
        $this->createBudgetProItem($resource_type_id, $resource_id, ['name' => 'created-last']);

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

            $this->assertJsonMatchesBudgetProItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProItemCollectionSortName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $this->createBudgetProItem($resource_type_id, $resource_id);
        $this->createBudgetProItem($resource_type_id, $resource_id, ['name' => 'AAAAAAAAAAAA']);
        $this->createBudgetProItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesBudgetProItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProItemCollectionWithParameterIncludeDeleted(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $this->createBudgetProItem($resource_type_id, $resource_id, ['deleted' => 1]);
        $this->createBudgetProItem($resource_type_id, $resource_id, ['deleted' => 1]);
        $this->createBudgetProItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'include-deleted' => true
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Parameters', 'include-deleted:1');
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesBudgetProItemSchema($json);
        }
    }

    /** @test */
    public function budgetProItemShow(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);
        $item_id = $this->createBudgetProItem($resource_type_id, $resource_id);

        $response = $this->fetchItem([
            $resource_type_id,
            $resource_id,
            $item_id
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesBudgetProItemSchema($response->content());
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
}
