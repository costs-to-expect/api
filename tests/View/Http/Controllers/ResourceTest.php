<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ResourceTest extends TestCase
{
    /** @test */
    public function allocatedExpenseResourceCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->getToResourceList([
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->getToResourceShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceSchema($response->content());
    }

    /** @test */
    public function budgetProResourceCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $this->quickCreateBudgetProResource($resource_type_id);
        $this->quickCreateBudgetProResource($resource_type_id);
        $this->quickCreateBudgetProResource($resource_type_id);

        $response = $this->getToResourceList([
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);

        $response = $this->getToResourceShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceSchema($response->content());
    }

    /** @test */
    public function budgetResourceCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id);

        $response = $this->getToResourceList([
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
    public function budgetResourceCollectionPagination(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id);

        $response = $this->getToResourceList([
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

    /** @test */
    public function budgetResourceCollectionPaginationPrevious(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id);

        $response = $this->getToResourceList([
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

    /** @test */
    public function budgetResourceCollectionSearchDescription(): void
    {
        $this->actingAs($this->createUser());

        $search_string = $this->faker->text(100);

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id, ['description' => $search_string]);
        $this->quickCreateBudgetResource($resource_type_id);

        $response = $this->getToResourceList([
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

    /** @test */
    public function budgetResourceCollectionSearchName(): void
    {
        $this->actingAs($this->createUser());

        $search_string = $this->faker->text(25);

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $this->quickCreateBudgetResource($resource_type_id, ['name' => $search_string]);
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id);

        $response = $this->getToResourceList([
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

    /** @test */
    public function budgetResourceCollectionSortCreated(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id);
        sleep(1); // Ensure the created_at timestamps are different
        $this->quickCreateBudgetResource($resource_type_id, ['name' => 'created-last']);

        $response = $this->getToResourceList([
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

    /** @test */
    public function budgetResourceCollectionSortDescription(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id, ['description' => 'AAAAAAAAAAAAB']);
        $this->quickCreateBudgetResource($resource_type_id);

        $response = $this->getToResourceList([
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

    /** @test */
    public function budgetResourceCollectionSortName(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetResource($resource_type_id, ['name' => 'AAAAAAAAAAAAB']);
        $this->quickCreateBudgetResource($resource_type_id);

        $response = $this->getToResourceList([
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $resource_id = $this->quickCreateBudgetResource($resource_type_id);

        $response = $this->getToResourceShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceSchema($response->content());
    }

    /** @test */
    public function optionsRequestForAllocatedExpenseResource(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $resource_id = $this->quickCreateBudgetResource($resource_type_id);

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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();

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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetProResource($resource_type_id);

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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();

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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);

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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();

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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYatzyResource($resource_type_id);

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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }

    /** @test */
    public function yahtzeeResourceCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $this->quickCreateYahtzeeResource($resource_type_id);
        $this->quickCreateYahtzeeResource($resource_type_id);
        $this->quickCreateYahtzeeResource($resource_type_id);

        $response = $this->getToResourceList([
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);

        $response = $this->getToResourceShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceSchema($response->content());
    }

    /** @test */
    public function yatzyResourceCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $this->quickCreateYatzyResource($resource_type_id);
        $this->quickCreateYatzyResource($resource_type_id);
        $this->quickCreateYatzyResource($resource_type_id);

        $response = $this->getToResourceList([
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYatzyResource($resource_type_id);

        $response = $this->getToResourceShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceSchema($response->content());
    }
}
