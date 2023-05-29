<?php

namespace Http\Controllers;

use App\User;
use Tests\TestCase;

final class ItemTest extends TestCase
{
    /** @test */
    public function allocatedExpenseItemCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseItemCollectionFilterEffectiveDate(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $this->createAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2020-09-12']);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2020-10-02']);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2020-10-15']);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2021-10-15']);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'filter'=>'effective_date:2020-10-01:2020-10-30'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Filter', 'effective_date:2020-10-01:2020-10-30');
        $response->assertHeader('X-Total-Count', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseItemCollectionPagination(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseItemCollectionSearchDescription(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id, ['description' => 'search-string']);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'search'=>'description:search-string'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'description:search-string');
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseItemCollectionSearchName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id, ['name' => 'search-string']);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'search'=>'name:search-string'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:search-string');
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseItemCollectionSortName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id, ['name' => 'AAAAAAAAAAAA']);
        $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseItemShow(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);
        $item_id = $this->createAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->fetchItem([
            $resource_type_id,
            $resource_id,
            $item_id
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesAllocatedExpenseItemSchema($response->content());
    }

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

    /** @test */
    public function yahtzeeGameItemCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);

        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesGameItemSchema($json);
        }
    }

    /** @test */
    public function yahtzeeGameItemShow(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchItem([
            $resource_type_id,
            $resource_id,
            $item_id
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesGameItemSchema($response->content());
    }

    /** @test */
    public function yatzyGameItemCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);

        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesGameItemSchema($json);
        }
    }

    /** @test */
    public function yatzyGameItemShow(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);
        $item_id = $this->createYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->fetchItem([
            $resource_type_id,
            $resource_id,
            $item_id
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesGameItemSchema($response->content());
    }
}
