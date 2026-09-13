<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ResourceTypeTest extends TestCase
{
    /** @test */
    public function allocatedExpenseResourceTypeCollection(): void
    {
        $this->actingAs($this->createUser());

        $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToResourceTypeList(['item-type'=>$this->item_types['allocated-expense'], 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseResourceTypeCollectionPagination(): void
    {
        $this->actingAs($this->createUser());

        $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToResourceTypeList(['offset' => 0, 'limit' => 2, 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', "");
        $response->assertHeader('X-Link-Next', "/v3/resource-types?offset=2&limit=2");

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseResourceTypeCollectionSearchDescription(): void
    {
        $this->actingAs($this->createUser());

        $search_string = $this->faker->text(35);

        $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResourceType(['description' => $search_string]);
        $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToResourceTypeList(['search'=>'description:' . $search_string, 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'description:' . urlencode($search_string));
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseResourceTypeCollectionSearchName(): void
    {
        $this->actingAs($this->createUser());

        $search_string = $this->faker->text(35);

        $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResourceType(['name' => $search_string]);
        $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToResourceTypeList(['search'=>'name:' . $search_string, 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:' . urlencode($search_string));
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseResourceTypeCollectionSortCreated(): void
    {
        $this->actingAs($this->createUser());

        $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResourceType();
        sleep(1); // Ensure the created_at timestamps are different
        $this->quickCreateAllocatedExpenseResourceType(['name' => 'created-last']);

        $response = $this->getToResourceTypeList([
            'sort'=>'created:desc',
            'exclude-public'=>'true'
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

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseResourceTypeCollectionSortName(): void
    {
        $this->actingAs($this->createUser());

        $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResourceType(['name' => 'AAAAAAAAAAAA']);
        $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToResourceTypeList([
            'sort'=>'name:asc',
            'exclude-public'=>'true'
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

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseResourceTypeShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToResourceTypeShow(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function allocatedExpenseResourceTypeShowWithParameterIncludePermittedUsers(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToResourceTypeShow([
            'resource_type_id'=> $resource_type_id,
            'include-permitted-users' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesPermittedUsersSchema($response->content());
    }

    /** @test */
    public function allocatedExpenseResourceTypeShowWithParameterIncludeResource(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->getToResourceTypeShow([
            'resource_type_id'=> $resource_type_id,
            'include-resources' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesResourcesSchema($response->content());
    }

    /** @test */
    public function budgetResourceTypeCollection(): void
    {
        $this->actingAs($this->createUser());

        $this->quickCreateBudgetResourceType();
        $this->quickCreateBudgetResourceType();

        $response = $this->getToResourceTypeList(['item-type'=>$this->item_types['budget'], 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function budgetResourceTypeShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();

        $response = $this->getToResourceTypeShow(['resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function budgetResourceTypeShowWithParameterIncludePermittedUsers(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();

        $response = $this->getToResourceTypeShow([
            'resource_type_id'=> $resource_type_id,
            'include-permitted-users' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesPermittedUsersSchema($response->content());
    }

    /** @test */
    public function budgetResourceTypeShowWithParameterIncludeResource(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();

        $this->quickCreateBudgetResource($resource_type_id);

        $response = $this->getToResourceTypeShow([
            'resource_type_id'=> $resource_type_id,
            'include-resources' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesResourcesSchema($response->content());
    }

    /** @test */
    public function budgetProResourceTypeCollection(): void
    {
        $this->actingAs($this->createUser());

        $this->quickCreateBudgetProResourceType();
        $this->quickCreateBudgetProResourceType();

        $response = $this->getToResourceTypeList(['item-type'=>$this->item_types['budget-pro'], 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function budgetProResourceTypeShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();

        $response = $this->getToResourceTypeShow(['resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function budgetProResourceTypeShowWithParameterIncludePermittedUsers(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();

        $response = $this->getToResourceTypeShow([
            'resource_type_id'=> $resource_type_id,
            'include-permitted-users' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesPermittedUsersSchema($response->content());
    }

    /** @test */
    public function budgetProResourceTypeShowWithParameterIncludeResource(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();

        $this->quickCreateBudgetProResource($resource_type_id);

        $response = $this->getToResourceTypeShow([
            'resource_type_id'=> $resource_type_id,
            'include-resources' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesResourcesSchema($response->content());
    }

    /** @test */
    public function gameResourceTypeCollection(): void
    {
        $this->actingAs($this->createUser());

        $this->quickCreateGameResourceType();
        $this->quickCreateGameResourceType();

        $response = $this->getToResourceTypeList(['item-type'=>$this->item_types['game'], 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function gameResourceTypeShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();

        $response = $this->getToResourceTypeShow(['resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function gameResourceTypeShowWithParameterIncludePermittedUsers(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();

        $response = $this->getToResourceTypeShow([
            'resource_type_id'=> $resource_type_id,
            'include-permitted-users' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesPermittedUsersSchema($response->content());
    }

    /** @test */
    public function gameResourceTypeShowWithParameterIncludeResource(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();

        $this->quickCreateYahtzeeResource($resource_type_id);

        $response = $this->getToResourceTypeShow([
            'resource_type_id'=> $resource_type_id,
            'include-resources' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesResourcesSchema($response->content());
    }

    /** @test */
    public function optionsRequestForAllocatedExpenseResourceType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForResourceType(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);

        // Standard resource type for now, may change in the future
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-type.json');
    }

    /** @test */
    public function optionsRequestForResourceTypeCollection(): void
    {
        $response = $this->fetchOptionsForResourceTypeCollection();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-type-collection.json');
    }

    /** @test */
    public function optionsRequestForBudgetResourceType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();

        $response = $this->fetchOptionsForResourceType(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);

        // Standard resource type for now, may change in the future
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-type.json');
    }

    /** @test */
    public function optionsRequestForBudgetProResourceType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();

        $response = $this->fetchOptionsForResourceType(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);

        // Standard resource type for now, may change in the future
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-type.json');
    }

    /** @test */
    public function optionsRequestForGameResourceType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();

        $response = $this->fetchOptionsForResourceType(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);

        // Standard resource type for now, may change in the future
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-type.json');
    }
}
