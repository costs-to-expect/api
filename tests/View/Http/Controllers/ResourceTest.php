<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

final class ResourceTest extends TestCase
{
    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseResourceCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $this->createAllocatedExpenseResource($resource_type_id);
        $this->createAllocatedExpenseResource($resource_type_id);
        $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseResourceShow(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->fetchResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceSchema($response->content());
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProResourceCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetProResourceType();
        $this->createBudgetProResource($resource_type_id);
        $this->createBudgetProResource($resource_type_id);
        $this->createBudgetProResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /** @test */
    public function budgetProResourceShow(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $response = $this->fetchResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceSchema($response->content());
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetResourceCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetResourceCollectionPagination(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id,
            'offset' => 0,
            'limit' => 2
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 2);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', "");
        $response->assertHeader('X-Link-Next', '/v3/resource-types/' . $resource_type_id . '/resources?offset=2&limit=2');

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetResourceCollectionPaginationPrevious(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id,
            'offset' => 2,
            'limit' => 2
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 1);
        $response->assertHeader('X-Offset', 2);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', '/v3/resource-types/' . $resource_type_id . '/resources?offset=0&limit=2');
        $response->assertHeader('X-Link-Next', '');

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetResourceCollectionSearchDescription(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $search_string = $this->faker->text(100);

        $resource_type_id = $this->createBudgetResourceType();
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id, ['description' => $search_string]);
        $this->createBudgetResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id,
            'search'=>'description:' . $search_string
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'description:' . urlencode($search_string));
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetResourceCollectionSearchName(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $search_string = $this->faker->text(25);

        $resource_type_id = $this->createBudgetResourceType();
        $this->createBudgetResource($resource_type_id, ['name' => $search_string]);
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id,
            'search'=>'name:' . $search_string
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:' . urlencode($search_string));
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetResourceCollectionSortCreated(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id);
        sleep(1); // Ensure the created_at timestamps are different
        $this->createBudgetResource($resource_type_id, ['name' => 'created-last']);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id,
            'sort'=>'created:desc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'created:desc');
        $response->assertHeader('X-Count', 3);
        $this->assertEquals('created-last', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetResourceCollectionSortDescription(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id, ['description' => 'AAAAAAAAAAAAB']);
        $this->createBudgetResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id,
            'sort'=>'description:desc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'description:desc');
        $response->assertHeader('X-Count', 3);
        $this->assertEquals('AAAAAAAAAAAAB', $response->json()[2]['description']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetResourceCollectionSortName(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $this->createBudgetResource($resource_type_id);
        $this->createBudgetResource($resource_type_id, ['name' => 'AAAAAAAAAAAAB']);
        $this->createBudgetResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id,
            'sort'=>'name:asc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');
        $response->assertHeader('X-Count', 3);
        $this->assertEquals('AAAAAAAAAAAAB', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /** @test */
    public function budgetResourceShow(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $response = $this->fetchResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceSchema($response->content());
    }

    /** @test */
    public function optionsRequestForAllocatedExpenseResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->fetchOptionsForResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }

    /** @test */
    public function optionsRequestForAllocatedExpenseResourceCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }

    /** @test */
    public function optionsRequestForBudgetResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $response = $this->fetchOptionsForResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }

    /** @test */
    public function optionsRequestForBudgetResourceCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }

    /** @test */
    public function optionsRequestForBudgetProResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $response = $this->fetchOptionsForResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }

    /** @test */
    public function optionsRequestForBudgetProResourceCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }

    /** @test */
    public function optionsRequestForYahtzeeResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);

        $response = $this->fetchOptionsForResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }

    /** @test */
    public function optionsRequestForYahtzeeResourceCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }

    /** @test */
    public function optionsRequestForYatzyResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);

        $response = $this->fetchOptionsForResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }

    /** @test */
    public function optionsRequestForYatzyResourceCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function yahtzeeResourceCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createGameResourceType();
        $this->createYahtzeeResource($resource_type_id);
        $this->createYahtzeeResource($resource_type_id);
        $this->createYahtzeeResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /** @test */
    public function yahtzeeResourceShow(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);

        $response = $this->fetchResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceSchema($response->content());
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function yatzyResourceCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createGameResourceType();
        $this->createYatzyResource($resource_type_id);
        $this->createYatzyResource($resource_type_id);
        $this->createYatzyResource($resource_type_id);

        $response = $this->fetchResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceSchema($json);
        }
    }

    /** @test */
    public function yatzyResourceShow(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);

        $response = $this->fetchResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceSchema($response->content());
    }
}
